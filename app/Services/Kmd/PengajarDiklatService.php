<?php

namespace App\Services\Kmd;

use App\Models\Kmd\KmdModuls;
use App\Models\Kmd\KmdPengajarDiklatManual;
use App\Models\Kmd\KmdUserClassTutorial;
use App\Models\Kmd\KomUser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Siapa saja yang boleh dipilih sebagai pengajar Kelas Tutorial (29 Okt 2026).
 *
 * Satu aturan yang dipakai di semua tempat (Buat Kelas, ganti modul, Edit, dan
 * pengecekan saat Simpan) — sebelumnya tiga tempat itu memakai aturan berbeda dan
 * bisa menampilkan SEMUA pengajar.
 *
 *   Modul 110 & 130 → kategori Doktrinal
 *   Modul 120 & 140 → kategori Praktikal
 *
 * Pengajar muncul kalau role-nya 'pengajar' DAN:
 *   - sudah SELESAI Diklat Tutor kategori itu (status 'selesai', diisi saat
 *     Admin Tutor menekan End Zoom kelas diklat), ATAU
 *   - ditandai manual oleh superadmin (menu Diklat Tutor › Pengajar Diklat Manual).
 */
class PengajarDiklatService
{
    public const DOKTRINAL = 'Doktrinal';

    public const PRAKTIKAL = 'Praktikal';

    public const CATEGORIES = [self::DOKTRINAL, self::PRAKTIKAL];

    /** Modul KOM 100 (Bahasa Indonesia) → kategori diklat pengajarnya. */
    public const MODUL_CATEGORY = [
        '3d6d23ce-d56a-4bc8-a7fa-bee7dd9368be' => self::DOKTRINAL, // Modul 110
        '36b0ed83-fb3e-476a-8af0-73d0610de12a' => self::DOKTRINAL, // Modul 130
        'd191a090-53af-46dc-958d-5fbe0b883967' => self::PRAKTIKAL, // Modul 120
        '6d119ce4-8097-4311-b250-1b4b3f600685' => self::PRAKTIKAL, // Modul 140
    ];

    /**
     * Kategori diklat untuk sebuah modul. Salinan modul bahasa lain (kom_v02)
     * mengikuti modul asalnya lewat source_modul_id. Null = modul tanpa kategori.
     */
    public function categoryForModul(?string $modulId): ?string
    {
        if (! $modulId) {
            return null;
        }

        if (isset(self::MODUL_CATEGORY[$modulId])) {
            return self::MODUL_CATEGORY[$modulId];
        }

        $sourceId = KmdModuls::whereKey($modulId)->value('source_modul_id');

        return $sourceId ? (self::MODUL_CATEGORY[$sourceId] ?? null) : null;
    }

    /** ID user yang lulus diklat kategori ini (selesai diklat ATAU ditandai manual). */
    public function qualifiedUserIds(string $category): Collection
    {
        $fromDiklat = KmdUserClassTutorial::query()
            ->where('category_diklat', $category)
            ->where('status', 'selesai')
            ->distinct()
            ->pluck('user_id');

        // Aman sebelum migration tabel manual dijalankan: dilewati saja.
        $manual = self::manualTableExists()
            ? KmdPengajarDiklatManual::where('category_diklat', $category)->pluck('user_id')
            : collect();

        return $fromDiklat->merge($manual)->unique()->values();
    }

    /**
     * Pengajar yang boleh dipilih untuk kategori ini, urut nama.
     * $tetapTampilkanId: pengajar yang sudah terpilih di kelas (halaman Edit) tetap
     * ditampilkan walau belum memenuhi syarat, supaya datanya tidak hilang.
     */
    public function pengajarsForCategory(?string $category, ?string $tetapTampilkanId = null): Collection
    {
        $ids = $category ? $this->qualifiedUserIds($category) : collect();

        if ($tetapTampilkanId) {
            $ids = $ids->push($tetapTampilkanId)->unique();
        }

        if ($ids->isEmpty()) {
            return collect();
        }

        return KomUser::where('role', 'pengajar')
            ->whereIn('id', $ids->all())
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function pengajarsForModul(?string $modulId, ?string $tetapTampilkanId = null): Collection
    {
        return $this->pengajarsForCategory($this->categoryForModul($modulId), $tetapTampilkanId);
    }

    /** Pengajar ini boleh mengajar kelas tutorial untuk modul ini? */
    public function isQualifiedForModul(string $userId, ?string $modulId): bool
    {
        $category = $this->categoryForModul($modulId);

        return $category !== null
            && KomUser::whereKey($userId)->where('role', 'pengajar')->exists()
            && $this->qualifiedUserIds($category)->contains($userId);
    }

    private static ?bool $manualTable = null;

    public static function manualTableExists(): bool
    {
        return self::$manualTable ??= Schema::connection('mysql')->hasTable('kmd_pengajar_diklat_manual');
    }

    /** Pesan untuk dropdown/validasi kalau tidak ada pengajar. */
    public function emptyMessage(?string $category): string
    {
        return $category
            ? __('Belum ada pengajar yang lulus Diklat Tutor :kategori.', ['kategori' => $category])
            : __('Modul ini belum punya kategori Diklat Tutor (Doktrinal/Praktikal).');
    }
}
