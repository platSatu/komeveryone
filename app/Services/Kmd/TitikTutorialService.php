<?php

namespace App\Services\Kmd;

use App\Models\Kmd\KmdModuls;
use App\Models\Kmd\KmdUserAbsensi;
use App\Models\Kmd\KmdUserClassTutorial;
use App\Models\Kmd\KmdVideo;
use Illuminate\Support\Collection;

/**
 * kom_v02 — "Titik Kelas Tutorial" di dalam modul.
 *
 *   Kelas → Modul → Video 1..n → Kelas Tutorial
 *
 * Aturan:
 *  - AKHIR MODUL selalu ada kelas tutorial (otomatis). Disimpan dengan video_id = NULL.
 *  - TENGAH MODUL: video yang dicentang superadmin (kmd_videos.has_tutorial = 1),
 *    disimpan dengan video_id = id video tsb.
 *  - Centang pada video TERAKHIR diabaikan (sudah otomatis = akhir modul).
 *
 * Status peserta di satu titik:
 *  - belum        : belum pernah memilih jadwal
 *  - proses       : sudah pilih jadwal, rangkaian (pertanyaan / persembahan) belum selesai
 *  - terdaftar    : persembahan selesai, menunggu kelas / absensi
 *  - tidak_hadir  : diabsen tidak hadir → boleh pilih jadwal lain TANPA persembahan lagi
 *  - hadir        : diabsen hadir → titik ini lulus (dihitung untuk sertifikat)
 */
class TitikTutorialService
{
    public const BELUM = 'belum';
    public const PROSES = 'proses';
    public const TERDAFTAR = 'terdaftar';
    public const TIDAK_HADIR = 'tidak_hadir';
    public const HADIR = 'hadir';

    /** Video aktif sebuah modul, urut. */
    public function videoModul(string $modulId): Collection
    {
        return KmdVideo::where('modul_id', $modulId)
            ->where('status', 'active')
            ->orderBy('sort_video')
            ->get();
    }

    /**
     * Semua titik tutorial dalam satu modul, urut.
     *
     * @return Collection<int, array{modul_id:string, video_id:?string, video:?KmdVideo, label:string, setelah_video:?int}>
     */
    public function titikModul(KmdModuls $modul, ?Collection $videos = null): Collection
    {
        $videos ??= $this->videoModul($modul->id);
        $terakhir = $videos->last();

        $titik = $videos
            ->filter(fn (KmdVideo $v) => $v->has_tutorial && (! $terakhir || $v->id !== $terakhir->id))
            ->map(fn (KmdVideo $v) => [
                'modul_id' => $modul->id,
                'video_id' => $v->id,
                'video' => $v,
                'label' => self::label($v),
                'setelah_video' => (int) $v->sort_video,
            ])
            ->values();

        // Akhir modul: selalu ada
        $titik->push([
            'modul_id' => $modul->id,
            'video_id' => null,
            'video' => $terakhir,
            'label' => self::label(null),
            'setelah_video' => $terakhir ? (int) $terakhir->sort_video : null,
        ]);

        return $titik;
    }

    /**
     * Titik tutorial yang tepat SETELAH video ini, atau null jika video ini bukan titik tutorial.
     *
     * @return array{modul_id:string, video_id:?string, label:string}|null
     */
    public function titikSetelahVideo(KmdVideo $video): ?array
    {
        $videos = $this->videoModul($video->modul_id);
        $terakhir = $videos->last();

        if ($terakhir && $terakhir->id === $video->id) {
            return ['modul_id' => $video->modul_id, 'video_id' => null, 'label' => self::label(null)];
        }

        if ($video->has_tutorial) {
            return ['modul_id' => $video->modul_id, 'video_id' => $video->id, 'label' => self::label($video)];
        }

        return null;
    }

    /** Label yang dibaca manusia. */
    public static function label(?KmdVideo $video): string
    {
        if (! $video) {
            return __('Akhir Modul');
        }

        return __('Setelah Video :urutan', ['urutan' => $video->sort_video]);
    }

    /** Batasi query booking/jadwal ke satu titik (NULL-safe). */
    public static function whereTitik($query, string $modulId, ?string $videoId)
    {
        $query->where('modul_id', $modulId);

        return $videoId ? $query->where('video_id', $videoId) : $query->whereNull('video_id');
    }

    /**
     * Status peserta di satu titik + booking terakhirnya.
     *
     * @return array{status:string, booking:?KmdUserClassTutorial}
     */
    public function statusPeserta(string $userId, string $modulId, ?string $videoId): array
    {
        $bookings = self::whereTitik(KmdUserClassTutorial::where('user_id', $userId), $modulId, $videoId)
            ->where(fn ($q) => $q->whereNull('category_diklat')->orWhere('category_diklat', ''))
            ->orderByDesc('created_at')
            ->get();

        return $this->hitungStatus($bookings);
    }

    /**
     * Status peserta untuk BANYAK titik sekaligus (1x query) — dipakai halaman belajar.
     *
     * @param  Collection  $titik  hasil titikModul() untuk beberapa modul
     * @return array<string, array{status:string, booking:?KmdUserClassTutorial}>  kunci = self::kunci()
     */
    public function statusPesertaBanyak(string $userId, Collection $titik): array
    {
        $modulIds = $titik->pluck('modul_id')->unique()->values();

        $bookings = KmdUserClassTutorial::where('user_id', $userId)
            ->whereIn('modul_id', $modulIds)
            ->where(fn ($q) => $q->whereNull('category_diklat')->orWhere('category_diklat', ''))
            ->orderByDesc('created_at')
            ->get();

        $absensi = $this->absensiUntuk($bookings);

        $hasil = [];
        foreach ($titik as $t) {
            $milik = $bookings->filter(fn ($b) => $b->modul_id === $t['modul_id'] && ($b->video_id ?: null) === $t['video_id']);
            $hasil[self::kunci($t['modul_id'], $t['video_id'])] = $this->hitungStatus($milik->values(), $absensi);
        }

        return $hasil;
    }

    public static function kunci(string $modulId, ?string $videoId): string
    {
        return $modulId . ':' . ($videoId ?: 'akhir');
    }

    /**
     * Syarat sertifikat sebuah kelas: HADIR di semua titik tutorial semua modul aktif
     * kelas tsb (sesuai bahasa rangkaian peserta).
     *
     * @return array{lulus:bool, total:int, hadir:int, booking_hadir:Collection}
     */
    public function syaratSertifikat(string $userId, Collection $moduls): array
    {
        $titik = $moduls->flatMap(fn (KmdModuls $m) => $this->titikModul($m));
        $status = $this->statusPesertaBanyak($userId, $titik);

        $hadir = collect($status)->filter(fn ($s) => $s['status'] === self::HADIR);

        return [
            'lulus' => $titik->isNotEmpty() && $hadir->count() === $titik->count(),
            'total' => $titik->count(),
            'hadir' => $hadir->count(),
            'booking_hadir' => $hadir->pluck('booking')->filter()->values(),
        ];
    }

    // ------------------------------------------------------------------

    protected function absensiUntuk(Collection $bookings): Collection
    {
        if ($bookings->isEmpty()) {
            return collect();
        }

        return KmdUserAbsensi::whereIn('userclasstutorial_id', $bookings->pluck('id'))
            ->get()
            ->keyBy('userclasstutorial_id');
    }

    /**
     * @param  Collection  $bookings  booking satu titik, terbaru dulu
     */
    protected function hitungStatus(Collection $bookings, ?Collection $absensi = null): array
    {
        if ($bookings->isEmpty()) {
            return ['status' => self::BELUM, 'booking' => null];
        }

        $absensi ??= $this->absensiUntuk($bookings);

        // Pernah hadir di salah satu jadwal → titik lulus
        $hadir = $bookings->first(fn ($b) => optional($absensi->get($b->id))->hadir);
        if ($hadir) {
            return ['status' => self::HADIR, 'booking' => $hadir];
        }

        $terakhir = $bookings->first();

        if ($terakhir->status_progress !== 'paid') {
            return ['status' => self::PROSES, 'booking' => $terakhir];
        }

        if (optional($absensi->get($terakhir->id))->tidak_hadir) {
            return ['status' => self::TIDAK_HADIR, 'booking' => $terakhir];
        }

        return ['status' => self::TERDAFTAR, 'booking' => $terakhir];
    }
}
