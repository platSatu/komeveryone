<?php

namespace App\Services\Kmd;

use App\Models\Kmd\KmdModuls;
use App\Models\Kmd\KmdUserClass;
use App\Models\Kmd\KomUser;
use Illuminate\Database\Eloquent\Builder;

/**
 * kom_v02 — Menentukan kelas (rangkaian) yang sedang dibuka user.
 *
 * Urutan:
 *  1. Kelas yang terakhir dibuka user dari katalog (disimpan di session)
 *  2. Kelas yang masih berjalan (status active)
 *  3. Kelas terakhir yang sudah selesai (supaya materi tetap bisa dibuka ulang)
 */
class KelasAktifService
{
    public const SESSION_KEY = 'kom_kelas_aktif_id';

    public function forUser(?KomUser $user): ?KmdUserClass
    {
        if (! $user) {
            return null;
        }

        $sessionId = session(self::SESSION_KEY);
        if ($sessionId) {
            $fromSession = KmdUserClass::with('kelas')
                ->where('id', $sessionId)
                ->where('user_id', $user->id)
                ->first();

            if ($fromSession) {
                return $fromSession;
            }
        }

        return KmdUserClass::with('kelas')
            ->where('user_id', $user->id)
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderByDesc('enrolled_at')
            ->first();
    }

    public function set(KmdUserClass $userClass): void
    {
        session([self::SESSION_KEY => $userClass->id]);
    }

    /** Modul milik kelas + bahasa rangkaian ini. */
    public function modulQuery(KmdUserClass $userClass): Builder
    {
        return KmdModuls::query()
            ->where('kom_class_id', $userClass->class_id)
            ->where('language_code', $userClass->language_code);
    }
}
