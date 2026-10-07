<?php

namespace App\Services\Kmd;

use App\Models\Kmd\KmdUserVideoProgress;
use App\Models\Kmd\KmdVideo;
use App\Models\Kmd\KomUser;

/**
 * kom_v02 — Ringkasan belajar untuk kartu "Lanjutkan Belajar" di Beranda.
 */
class RingkasanBelajarService
{
    public function __construct(protected KelasAktifService $kelasAktif)
    {
    }

    /**
     * @return array|null  null = user belum terdaftar di kelas mana pun
     */
    public function forUser(?KomUser $user): ?array
    {
        $userClass = $this->kelasAktif->forUser($user);
        if (! $userClass) {
            return null;
        }

        $moduls = $this->kelasAktif->modulQuery($userClass)
            ->where('status', 'active')
            ->orderBy('order')
            ->with(['videos' => fn ($q) => $q->where('status', 'active')->orderBy('sort_video')])
            ->get();

        $videos = $moduls->flatMap(fn ($m) => $m->videos->map(fn ($v) => [$v, $m]));
        $videoIds = $videos->map(fn ($pair) => $pair[0]->id);

        $selesaiIds = KmdUserVideoProgress::where('user_id', $user->id)
            ->whereIn('video_id', $videoIds)
            ->where('status', 'completed')
            ->where('answered_question', true)
            ->pluck('video_id')
            ->flip();

        $total = $videos->count();
        $selesai = $selesaiIds->count();

        // Video berikutnya = video pertama (urut modul & video) yang belum selesai
        $berikut = $videos->first(fn ($pair) => ! $selesaiIds->has($pair[0]->id));

        return [
            'user_class' => $userClass,
            'nama_kelas' => $userClass->kelas?->translatedName() ?? '-',
            'kode_kelas' => $userClass->kelas?->code,
            'selesai_kelas' => $userClass->status === 'completed',
            'jumlah_modul' => $moduls->count(),
            'total_video' => $total,
            'video_selesai' => $selesai,
            'persen' => $total > 0 ? (int) round($selesai / $total * 100) : 0,
            'video_berikut' => $berikut ? $berikut[0]->name : null,
            'modul_berikut' => $berikut ? $berikut[1]->name : null,
        ];
    }
}
