<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kmd\Cabang;
use App\Models\Kmd\KmdModuls;
use App\Models\Kmd\KmdUserReview;
use App\Models\Kmd\KomUser;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * kom_v02 — Admin KOM (superadmin): Review Pengajar SEMUA cabang.
 *
 * Sama seperti menu "Review Pengajar" di admin cabang, tetapi tidak dibatasi cabang.
 *  - index : ringkasan per pengajar (jumlah review, jumlah kelas, rata-rata nilai) + filter
 *  - show  : detail satu pengajar, review dikelompokkan per kelas tutorial
 *
 * Nilai review disimpan sebagai teks (sangat_baik … sangat_kurang) → dikonversi 5 … 1
 * langsung di SQL supaya tetap ringan walaupun review sudah ribuan.
 */
class ReviewPengajarController extends Controller
{
    /** Kolom penilaian di kmd_user_review => label */
    public const KRITERIA = [
        'isi_pengajaran' => 'Isi Pengajaran',
        'kebenaran_teologis' => 'Kebenaran Teologis',
        'pengajar_sampaikan_seluruh_materi' => 'Menyampaikan Seluruh Materi',
        'pengajar_sampaikan_materi_utama' => 'Menyampaikan Materi Utama',
        'pembukaan_materi' => 'Pembukaan Materi',
        'kesinambungan_materi' => 'Kesinambungan Materi',
        'kesaksian_pribadi' => 'Kesaksian Pribadi',
        'materi_dipahami' => 'Materi mudah dipahami',
        'sesuai_dengan_materi' => 'Sesuai dengan Materi',
    ];

    public const NILAI = [
        'sangat_baik' => 5,
        'baik' => 4,
        'cukup' => 3,
        'kurang' => 2,
        'sangat_kurang' => 1,
    ];

    public function index(Request $request): View
    {
        $filters = $this->filters($request);

        $base = $this->baseQuery($filters);

        // Rata-rata keseluruhan per kriteria
        $selectAvg = collect(array_keys(self::KRITERIA))
            ->map(fn ($k) => 'AVG(' . $this->skor('r.' . $k) . ') as ' . $k)
            ->implode(', ');

        $ringkasan = (clone $base)
            ->selectRaw('COUNT(*) as total_review, COUNT(DISTINCT r.pengajar_id) as total_pengajar, COUNT(DISTINCT ct.id) as total_kelas, ' . $selectAvg)
            ->first();

        $rataKriteria = collect(self::KRITERIA)->map(fn ($label, $k) => round((float) ($ringkasan->$k ?? 0), 2));
        $rataSemua = $this->rataRata($rataKriteria);

        // Ringkasan per pengajar
        $perPengajar = (clone $base)
            ->selectRaw('r.pengajar_id, COUNT(*) as total_review, COUNT(DISTINCT ct.id) as total_kelas, '
                . 'COUNT(DISTINCT ct.rayon_id) as total_cabang, MAX(r.created_at) as review_terakhir, '
                . 'AVG(' . $this->skorRataBaris('r') . ') as rata_rata')
            ->groupBy('r.pengajar_id')
            ->when($filters['urut'] === 'terendah', fn ($q) => $q->orderBy('rata_rata'))
            ->when($filters['urut'] === 'tertinggi', fn ($q) => $q->orderByDesc('rata_rata'))
            ->when($filters['urut'] === 'terbanyak', fn ($q) => $q->orderByDesc('total_review'))
            ->orderByDesc('review_terakhir')
            ->paginate(15)
            ->withQueryString();

        $pengajar = KomUser::whereIn('id', $perPengajar->pluck('pengajar_id')->filter())
            ->get(['id', 'name', 'email', 'cabang_id'])
            ->keyBy('id');

        return view('a_kom_digital_pusat.superadmin.review_pengajar.index', [
            'filters' => $filters,
            'ringkasan' => $ringkasan,
            'rataKriteria' => $rataKriteria,
            'rataSemua' => $rataSemua,
            'perPengajar' => $perPengajar,
            'pengajar' => $pengajar,
            'kriteria' => self::KRITERIA,
            'cabangList' => Cabang::orderBy('name')->get(['id', 'name']),
            'modulList' => KmdModuls::orderBy('name')->get(['id', 'name']),
            'pengajarList' => $this->daftarPengajar(),
        ]);
    }

    public function show(Request $request, string $pengajarId): View
    {
        $pengajar = KomUser::findOrFail($pengajarId);
        $filters = $this->filters($request);
        $filters['pengajar_id'] = $pengajar->id;

        $base = $this->baseQuery($filters);

        // Ringkasan seluruh hasil filter (bukan hanya halaman ini)
        $selectAvg = collect(array_keys(self::KRITERIA))
            ->map(fn ($k) => 'AVG(' . $this->skor('r.' . $k) . ') as ' . $k)
            ->implode(', ');
        $ringkasan = (clone $base)
            ->selectRaw('COUNT(*) as total_review, COUNT(DISTINCT ct.id) as total_kelas, ' . $selectAvg)
            ->first();
        $rataKriteria = collect(self::KRITERIA)->map(fn ($l, $k) => round((float) ($ringkasan->$k ?? 0), 2));

        // kom_v02: kelas tutorial dipaginasi (10 per halaman) supaya ringan
        $halamanKelas = (clone $base)
            ->selectRaw('ct.id as kelas_tutorial_id, MAX(ct.start_time) as jadwal, MAX(r.created_at) as terakhir')
            ->groupBy('ct.id')
            ->orderByDesc($filters['jenis_tanggal'] === 'kelas' ? 'jadwal' : 'terakhir')
            ->paginate(10)
            ->withQueryString();

        $idKelas = $halamanKelas->pluck('kelas_tutorial_id');
        $adaTanpaKelas = $idKelas->contains(null);

        $idReview = (clone $base)
            ->where(function ($q) use ($idKelas, $adaTanpaKelas) {
                $q->whereIn('ct.id', $idKelas->filter()->values());
                if ($adaTanpaKelas) {
                    $q->orWhereNull('ct.id');
                }
            })
            ->pluck('r.id');

        $reviews = KmdUserReview::with([
                'user:id,name,email',
                'userClassTutorial.classTutorial.modul:id,name',
                'userClassTutorial.classTutorial.kelas:id,name',
                'userClassTutorial.classTutorial.rayon:id,name',
            ])
            ->whereIn('id', $idReview)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn ($r) => optional(optional($r->userClassTutorial)->classTutorial)->id ?? 'lainnya');

        // Urutan mengikuti halaman (paginasi)
        $perKelas = $halamanKelas->getCollection()->map(function ($baris) use ($reviews) {
            $grup = $reviews->get($baris->kelas_tutorial_id ?? 'lainnya', collect());
            $ct = optional(optional($grup->first())->userClassTutorial)->classTutorial;
            $rata = collect(self::KRITERIA)->map(fn ($l, $k) => $this->rataKoleksi($grup, $k));

            return [
                'kelas_tutorial' => $ct,
                'reviews' => $grup,
                'rata_kriteria' => $rata,
                'rata_rata' => $this->rataRata($rata),
                'terakhir' => $grup->max('created_at'),
            ];
        })->filter(fn ($g) => $g['reviews']->isNotEmpty())->values();

        return view('a_kom_digital_pusat.superadmin.review_pengajar.show', [
            'pengajarUser' => $pengajar,
            'filters' => $filters,
            'perKelas' => $perKelas,
            'halamanKelas' => $halamanKelas,
            'totalReview' => (int) ($ringkasan->total_review ?? 0),
            'totalKelas' => (int) ($ringkasan->total_kelas ?? 0),
            'rataKriteria' => $rataKriteria,
            'rataSemua' => $this->rataRata($rataKriteria),
            'kriteria' => self::KRITERIA,
            'cabangList' => Cabang::orderBy('name')->get(['id', 'name']),
            'modulList' => KmdModuls::orderBy('name')->get(['id', 'name']),
        ]);
    }

    // ------------------------------------------------------------------

    protected function filters(Request $request): array
    {
        return [
            'cabang_id' => $request->get('cabang_id'),
            'pengajar_id' => $request->get('pengajar_id'),
            'modul_id' => $request->get('modul_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            // kom_v02: tanggal berdasarkan jadwal kelas tutorial atau tanggal review
            'jenis_tanggal' => $request->get('jenis_tanggal') === 'kelas' ? 'kelas' : 'review',
            // kom_v02: pencarian topik kelas tutorial, modul, cabang, atau nama peserta
            'cari' => trim((string) $request->get('cari', '')) ?: null,
            'urut' => in_array($request->get('urut'), ['terbaru', 'tertinggi', 'terendah', 'terbanyak'], true)
                ? $request->get('urut') : 'terbaru',
        ];
    }

    /** Query dasar review + jadwal kelas tutorial, sudah difilter. */
    protected function baseQuery(array $f): Builder
    {
        return DB::connection('mysql')->table('kmd_user_review as r')
            ->leftJoin('kmd_user_class_tutorial as uct', 'uct.id', '=', 'r.userclasstutorial_id')
            ->leftJoin('kmd_class_tutorial as ct', 'ct.id', '=', 'uct.class_tutorial_id')
            ->when($f['cabang_id'], fn ($q, $v) => $q->where('ct.rayon_id', $v))
            ->when($f['pengajar_id'], fn ($q, $v) => $q->where('r.pengajar_id', $v))
            ->when($f['modul_id'], fn ($q, $v) => $q->where('ct.modul_id', $v))
            ->when($f['date_from'], fn ($q, $v) => $q->whereDate($this->kolomTanggal($f), '>=', $v))
            ->when($f['date_to'], fn ($q, $v) => $q->whereDate($this->kolomTanggal($f), '<=', $v))
            ->when($f['cari'] ?? null, function ($q, $cari) {
                $kata = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $cari) . '%';
                $q->where(function ($w) use ($kata) {
                    $w->where('ct.topic', 'like', $kata)
                      ->orWhereExists(fn ($s) => $s->selectRaw('1')->from('kmd_moduls as m')
                          ->whereColumn('m.id', 'ct.modul_id')->where('m.name', 'like', $kata))
                      ->orWhereExists(fn ($s) => $s->selectRaw('1')->from('kmd_cabangs as c')
                          ->whereColumn('c.id', 'ct.rayon_id')->where('c.name', 'like', $kata))
                      ->orWhereExists(fn ($s) => $s->selectRaw('1')->from('kmd_users as u')
                          ->whereColumn('u.id', 'r.user_id')->where('u.name', 'like', $kata));
                });
            });
    }

    protected function kolomTanggal(array $f): string
    {
        return ($f['jenis_tanggal'] ?? 'review') === 'kelas' ? 'ct.start_time' : 'r.created_at';
    }

    /** SQL: nilai teks → angka (NULL jika kosong / tidak dikenal, tidak ikut dirata-rata). */
    protected function skor(string $kolom): string
    {
        $case = 'CASE ' . $kolom;
        foreach (self::NILAI as $teks => $angka) {
            $case .= " WHEN '" . $teks . "' THEN " . $angka;
        }

        return $case . ' ELSE NULL END';
    }

    /** SQL: rata-rata nilai semua kriteria dalam satu review. */
    protected function skorRataBaris(string $alias): string
    {
        $jumlah = collect(array_keys(self::KRITERIA))->map(fn ($k) => 'COALESCE(' . $this->skor($alias . '.' . $k) . ', 0)')->implode(' + ');
        $banyak = collect(array_keys(self::KRITERIA))->map(fn ($k) => '(CASE WHEN ' . $this->skor($alias . '.' . $k) . ' IS NULL THEN 0 ELSE 1 END)')->implode(' + ');

        return '(' . $jumlah . ') / NULLIF(' . $banyak . ', 0)';
    }

    protected function rataKoleksi($reviews, string $kolom): float
    {
        $nilai = $reviews->map(fn ($r) => self::NILAI[$r->$kolom] ?? null)->filter();

        return $nilai->isEmpty() ? 0.0 : round($nilai->avg(), 2);
    }

    protected function rataRata($rataKriteria): float
    {
        $ada = collect($rataKriteria)->filter(fn ($v) => $v > 0);

        return $ada->isEmpty() ? 0.0 : round($ada->avg(), 2);
    }

    protected function daftarPengajar()
    {
        $id = DB::connection('mysql')->table('kmd_user_review')->distinct()->pluck('pengajar_id')->filter();

        return KomUser::whereIn('id', $id)->orderBy('name')->get(['id', 'name']);
    }
}
