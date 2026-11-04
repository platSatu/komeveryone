<?php

namespace App\Services\Kmd;

use App\Models\Kmd\KmdClassTutorial;
use App\Models\Kmd\KmdUserClassTutorial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * 4 Nov 2026 — kuota kelas tutorial DITAHAN SEMENTARA selama peserta sedang proses
 * (pilih jadwal → isi pertanyaan → persembahan).
 *
 * Tidak ada kolom / tabel baru. Angka di kmd_class_tutorial tetap seperti sebelumnya:
 *   quota      = kapasitas
 *   jml_ikut   = peserta LUNAS          (+1 hanya di KelasTutorialController::handlePaidStatus)
 *   sisa_quota = quota - jml_ikut       (-1 hanya di handlePaidStatus)
 * "Ditahan" = pendaftaran yang belum lunas dan belum lewat batas waktunya (+ tenggang bank),
 * dihitung langsung dari kmd_user_class_tutorial. Begitu waktunya habis / dibatalkan / dihapus
 * jadwal otomatis, tahanannya lepas sendiri — tidak ada yang perlu dikembalikan.
 *
 *   tempat tersedia = sisa_quota - jumlah ditahan
 *
 * Karena tidak ada refund: peserta baru tidak bisa memilih jadwal kalau tempat tersedia 0, dan
 * peserta hanya bisa membuat pembayaran kalau tahanannya masih dapat tempat (urut siapa duluan).
 */
class KuotaTutorialService
{
    public function __construct(private readonly CheckoutTutorialService $checkout)
    {
    }

    /** Pendaftaran yang sedang menahan tempat di kelas ini. */
    public function queryDitahan(string $kelasId): Builder
    {
        return $this->ditahan()->where('class_tutorial_id', $kelasId);
    }

    /** Kondisi "masih menahan tempat" (tanpa filter kelas). */
    private function ditahan(): Builder
    {
        $tenggang = max(0, (int) config('kom.checkout.tenggang_menit', 5));
        $menitPertanyaan = (int) config('kom.checkout.batas_pertanyaan_menit', 30);
        $menitPersembahan = $this->checkout->menitPersembahan();

        return KmdUserClassTutorial::query()
            ->where('status_progress', 'pending')
            ->where(function (Builder $q) use ($tenggang, $menitPertanyaan, $menitPersembahan) {
                // langkah persembahan: sampai checkout_expires_at (+ tenggang untuk VA/transfer yang terlambat)
                $q->where(function (Builder $d) use ($tenggang, $menitPersembahan) {
                    $d->where('step', 'donasi')->where(function (Builder $w) use ($tenggang, $menitPersembahan) {
                        $w->where('checkout_expires_at', '>', now()->subMinutes($tenggang))
                            ->orWhere(fn (Builder $n) => $n->whereNull('checkout_expires_at')
                                ->where('updated_at', '>', now()->subMinutes($menitPersembahan + $tenggang)))
                            // pembayaran sudah dibuat di gateway (mis. VA yang masih diproses bank): tetap ditahan
                            // sampai jadwal otomatis memastikan statusnya (lunas → masuk kuota, batal → dihapus).
                            ->orWhereExists(fn ($d) => $d->select(DB::raw(1))->from('kmd_donations')
                                ->whereColumn('kmd_donations.userclasstutorial_id', 'kmd_user_class_tutorial.id')
                                ->where('kmd_donations.status', '!=', 'paid'));
                    });
                })
                // langkah pilih / pertanyaan: sejak update terakhir + batas pertanyaan (sama dengan batasWaktu)
                ->orWhere(fn (Builder $p) => $p->whereIn('step', ['pilih', 'pertanyaan'])
                    ->where('updated_at', '>', now()->subMinutes($menitPertanyaan + $tenggang)));
            });
    }

    public function jumlahDitahan(string $kelasId, ?string $kecualiBookingId = null): int
    {
        return $this->queryDitahan($kelasId)
            ->when($kecualiBookingId, fn (Builder $q) => $q->where('id', '!=', $kecualiBookingId))
            ->count();
    }

    /** Tempat yang masih bisa dipilih peserta baru. */
    public function tersedia(KmdClassTutorial $kelas): int
    {
        return max(0, (int) $kelas->sisa_quota - $this->jumlahDitahan($kelas->id));
    }

    /**
     * Tempat tersedia untuk banyak kelas sekaligus (halaman daftar jadwal) — satu query.
     *
     * @param  iterable<KmdClassTutorial>  $kelasList
     * @return array<string, int> [class_tutorial_id => tersedia]
     */
    public function tersediaBanyak(iterable $kelasList): array
    {
        $kelasList = collect($kelasList);
        if ($kelasList->isEmpty()) {
            return [];
        }

        $jumlah = $this->ditahan()
            ->whereIn('class_tutorial_id', $kelasList->pluck('id')->all())
            ->groupBy('class_tutorial_id')
            ->select('class_tutorial_id', DB::raw('COUNT(*) as jumlah'))
            ->pluck('jumlah', 'class_tutorial_id');

        return $kelasList->mapWithKeys(fn ($kelas) => [
            $kelas->id => max(0, (int) $kelas->sisa_quota - (int) ($jumlah[$kelas->id] ?? 0)),
        ])->all();
    }

    /**
     * Kuota minimal saat admin mengubah kuota: peserta lunas + yang sedang mendaftar.
     *
     * @throws \InvalidArgumentException pesan siap tampil
     */
    public function pastikanKuotaCukup(KmdClassTutorial $kelas, $quotaBaru): void
    {
        if ($quotaBaru === null || $quotaBaru === '') {
            return;
        }

        $lunas = (int) $kelas->jml_ikut;
        $ditahan = $this->jumlahDitahan($kelas->id);

        if ((int) $quotaBaru < $lunas + $ditahan) {
            throw new \InvalidArgumentException(__('Kuota minimal :n karena sudah ada :lunas peserta lunas dan :proses peserta sedang mendaftar.', [
                'n' => $lunas + $ditahan, 'lunas' => $lunas, 'proses' => $ditahan,
            ]));
        }
    }

    /**
     * Simpan perubahan kelas tutorial dari form edit. Kalau kuota berubah, sisa_quota dihitung ulang
     * (quota - jml_ikut) — jadi menambah kuota 1 langsung membuka 1 tempat. jml_ikut tidak pernah
     * diambil dari form (hanya berubah lewat pembayaran). Dikunci supaya tidak bentrok dengan
     * pembayaran yang masuk bersamaan (handlePaidStatus memakai kunci yang sama).
     */
    public function simpanKelas(KmdClassTutorial $kelas, array $data): KmdClassTutorial
    {
        return DB::connection('mysql')->transaction(function () use ($kelas, $data) {
            $kunci = KmdClassTutorial::lockForUpdate()->findOrFail($kelas->id);

            unset($data['jml_ikut']);
            if (array_key_exists('quota', $data) && $data['quota'] !== null && $data['quota'] !== '') {
                $this->pastikanKuotaCukup($kunci, $data['quota']);
                $data['sisa_quota'] = max(0, (int) $data['quota'] - (int) $kunci->jml_ikut);
            } else {
                unset($data['sisa_quota']);
            }

            $kunci->update($data);

            return $kunci;
        });
    }

    /**
     * Boleh membuat pembayaran? Ya kalau tahanan peserta ini masih kebagian tempat: jumlah tahanan
     * yang LEBIH DULU dari dia harus lebih kecil dari sisa_quota (urut waktu daftar).
     * Normalnya selalu ya (sudah dicek saat pilih jadwal); bisa tidak kalau kuota dikurangi / peserta
     * dipindah superadmin di tengah jalan.
     */
    public function bolehBayar(KmdUserClassTutorial $booking): bool
    {
        $kelas = KmdClassTutorial::find($booking->class_tutorial_id);
        if (! $kelas) {
            return false;
        }

        $didahului = $this->queryDitahan($kelas->id)
            ->where('id', '!=', $booking->id)
            ->where(fn (Builder $q) => $q->where('created_at', '<', $booking->created_at)
                ->orWhere(fn (Builder $s) => $s->where('created_at', $booking->created_at)->where('id', '<', $booking->id)))
            ->count();

        return $didahului < (int) $kelas->sisa_quota;
    }
}
