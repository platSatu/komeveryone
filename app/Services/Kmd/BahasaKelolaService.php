<?php

namespace App\Services\Kmd;

use App\Models\Kmd\Language;

/**
 * kom_v02 — "Bahasa yang sedang dikelola" di halaman superadmin.
 *
 * Superadmin memilih satu bahasa (dropdown di atas halaman Modul, Video,
 * Pertanyaan, Jawaban, Pertanyaan di Video). Semua daftar & form di halaman
 * itu hanya menampilkan / menyimpan data bahasa tersebut.
 */
class BahasaKelolaService
{
    public const SESSION_KEY = 'super_bahasa_kelola';

    public static function current(): string
    {
        $code = session(self::SESSION_KEY);

        if ($code && Language::where('code', $code)->exists()) {
            return $code;
        }

        return Language::defaultCode();
    }

    public static function set(string $code): void
    {
        session([self::SESSION_KEY => $code]);
    }

    public static function currentLanguage(): ?Language
    {
        return Language::where('code', self::current())->first();
    }
}
