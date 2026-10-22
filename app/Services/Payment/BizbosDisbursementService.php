<?php

namespace App\Services\Payment;

use App\Models\Kmd\KmdTarikSaldo;
use App\Models\Kmd\KmdUserRekening;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * kom_v02 — Tarik dana admin cabang lewat Disbursement Bizbos.
 *
 * Alur: admin cabang mengajukan (pending, saldo kelas tertahan) → superadmin "Setujui & Kirim"
 * → Bizbos memotong saldo & mentransfer ke rekening → webhook payout.* / tombol Cek Status
 * memperbarui status. Status akhir dari Bizbos saja (bukan dari tombol).
 *  - success      → completed
 *  - failed       → failed (saldo Bizbos dikembalikan, saldo kelas kembali)
 *  - lainnya      → approved (sedang diproses / dicek tim Bizbos)
 */
class BizbosDisbursementService
{
    public function __construct(protected BizbosClient $client, protected PaymentGatewaySetting $setting)
    {
    }

    public function aktif(): bool
    {
        return $this->setting->payoutAktif();
    }

    /** @return array<string, string> bank_code => bank_name */
    public function banks(): array
    {
        return Cache::remember('kom_bizbos_banks_v1', 3600, fn () => collect($this->client->banks())
            ->mapWithKeys(fn ($b) => [$b['bank_code'] => $b['bank_name']])
            ->sort()
            ->all());
    }

    /** Cek rekening ke bank lewat Bizbos (nama pemilik harus sama) lalu tandai terverifikasi. */
    public function verifikasi(KmdUserRekening $rekening, string $bankCode): KmdUserRekening
    {
        $bankName = $this->banks()[$bankCode] ?? throw new RuntimeException('Bank tidak dikenali. Pilih bank dari daftar.');

        $akun = $this->client->daftarRekening([
            'external_id' => $rekening->bizbosExternalId(),
            'bank_code' => $bankCode,
            'account_number' => $rekening->no_rek,
            'account_name' => $rekening->nama_rek,
            'label' => trim(($rekening->user->name ?? 'Admin cabang') . ' · ' . ($rekening->cabang->name ?? '')),
        ]);

        if (empty($akun['id'])) {
            throw new RuntimeException('Bizbos tidak mengembalikan data rekening. Coba lagi.');
        }

        $rekening->forceFill([
            'nama_bank' => $bankName,
            'bank_code' => $bankCode,
            'bizbos_account_id' => $akun['id'],
            'bizbos_verified_name' => $akun['account_name'] ?? $rekening->nama_rek,
            'bizbos_verified_at' => now(),
        ])->save();

        return $rekening;
    }

    /** Setujui & kirim pengajuan pending. Aman diklik ulang (idempoten di Bizbos). */
    public function kirim(string $id, string $adminId): KmdTarikSaldo
    {
        if (! $this->aktif()) {
            throw new RuntimeException('Tarik dana lewat Bizbos belum diaktifkan di menu Payment Gateway.');
        }

        $tarik = DB::transaction(function () use ($id, $adminId) {
            $tarik = KmdTarikSaldo::whereKey($id)->lockForUpdate()->firstOrFail();

            if ($tarik->status !== 'pending') {
                throw new RuntimeException('Pengajuan ini sudah diproses (' . $tarik->status_label . ').');
            }

            $rekening = KmdUserRekening::where('user_id', $tarik->user_id)->first();
            if (! $rekening || ! $rekening->terverifikasi()) {
                throw new RuntimeException('Rekening admin cabang belum diverifikasi ke Bizbos. Minta admin cabang verifikasi di menu Info Rekening.');
            }

            $tarik->forceFill(['bizbos_status' => 'mengirim', 'gagal_alasan' => null, 'approved_by' => $adminId])->save();

            return $tarik;
        });

        try {
            $payout = $this->client->buatPayout([
                'external_id' => $tarik->bizbosExternalId(),
                'payout_account_id' => KmdUserRekening::where('user_id', $tarik->user_id)->value('bizbos_account_id'),
                'amount' => (int) round((float) $tarik->jumlah),
                'description' => 'Tarik dana KOM ' . ($tarik->cabang->name ?? ''),
            ]);
        } catch (BizbosException $e) {
            // 4xx = Bizbos menolak, kiriman TIDAK dibuat → kembali pending, boleh diperbaiki / ditolak.
            if ($e->status >= 400 && $e->status < 500 && $e->status !== 408) {
                $tarik->forceFill(['bizbos_status' => null, 'gagal_alasan' => mb_substr($e->getMessage(), 0, 255)])->save();
            }
            throw $e;
        }

        return $this->sinkron($tarik->fresh(), $payout);
    }

    /** Tanyakan status ke Bizbos. Kalau kiriman belum pernah dibuat (404) → kembali pending. */
    public function cek(string $id): KmdTarikSaldo
    {
        $tarik = KmdTarikSaldo::findOrFail($id);

        try {
            $payout = $this->client->payout($tarik->bizbosExternalId());
        } catch (BizbosException $e) {
            if ($e->status === 404 && ! $tarik->bizbos_payout_id && $tarik->status === 'pending') {
                $tarik->forceFill(['bizbos_status' => null])->save();

                return $tarik;
            }
            throw $e;
        }

        return $this->sinkron($tarik, $payout);
    }

    /** Samakan status lokal dengan data payout dari Bizbos (webhook / API). Idempoten. */
    public function sinkron(KmdTarikSaldo $tarik, array $payout): KmdTarikSaldo
    {
        if (($payout['external_id'] ?? null) !== $tarik->bizbosExternalId()
            || (int) ($payout['amount'] ?? -1) !== (int) round((float) $tarik->jumlah)) {
            Log::critical('[Bizbos Payout] Data payout tidak cocok dengan pengajuan — cek manual.', [
                'tarik_id' => $tarik->id, 'external_id' => $payout['external_id'] ?? null, 'amount' => $payout['amount'] ?? null,
            ]);
            throw new RuntimeException('Data dari Bizbos tidak cocok dengan pengajuan ini.');
        }

        return DB::transaction(function () use ($tarik, $payout) {
            $tarik = KmdTarikSaldo::whereKey($tarik->id)->lockForUpdate()->firstOrFail();

            if (in_array($tarik->status, ['completed', 'failed'], true)) {
                return $tarik; // sudah final
            }

            if ($tarik->status === 'rejected') {
                Log::critical('[Bizbos Payout] Pengajuan sudah DITOLAK tetapi ada kiriman di Bizbos — cek manual.', [
                    'tarik_id' => $tarik->id, 'payout_id' => $payout['id'] ?? null, 'status' => $payout['status'] ?? null,
                ]);

                return $tarik;
            }

            $statusBizbos = (string) ($payout['status'] ?? '');
            $tarik->forceFill([
                'status' => match ($statusBizbos) {
                    'success' => 'completed',
                    'failed' => 'failed',
                    default => 'approved',
                },
                'bizbos_payout_id' => $payout['id'] ?? $tarik->bizbos_payout_id,
                'bizbos_status' => $statusBizbos ?: $tarik->bizbos_status,
                'gagal_alasan' => $statusBizbos === 'failed' ? mb_substr((string) ($payout['failure_reason'] ?? 'Transfer gagal.'), 0, 255) : null,
                'approved_at' => $tarik->approved_at ?? now(),
            ])->save();

            return $tarik;
        });
    }

    /** Tolak pengajuan pending yang belum pernah dikirim ke Bizbos. Saldo kelas kembali. */
    public function tolak(string $id, string $adminId, string $alasan): KmdTarikSaldo
    {
        return DB::transaction(function () use ($id, $adminId, $alasan) {
            $tarik = KmdTarikSaldo::whereKey($id)->lockForUpdate()->firstOrFail();

            if ($tarik->status !== 'pending') {
                throw new RuntimeException('Hanya pengajuan yang menunggu persetujuan yang bisa ditolak.');
            }
            if ($tarik->sudahKeBizbos()) {
                throw new RuntimeException('Pengajuan ini sudah dikirim ke Bizbos. Klik "Cek Status" dulu.');
            }

            $tarik->forceFill([
                'status' => 'rejected',
                'rejected_reason' => mb_substr($alasan, 0, 255),
                'approved_by' => $adminId,
                'approved_at' => now(),
            ])->save();

            return $tarik;
        });
    }
}
