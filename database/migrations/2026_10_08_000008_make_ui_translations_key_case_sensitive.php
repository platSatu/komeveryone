<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * kom_v02 — Kolom ui_translations.key dibuat peka huruf besar/kecil (utf8mb4_bin).
 *
 * Teks "Nonaktif" dan "nonaktif" adalah dua teks tampilan berbeda, tetapi collation
 * default (utf8mb4_unicode_ci) menganggapnya sama → error "Duplicate entry" saat pindai teks.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ui_translations')) {
            DB::statement('ALTER TABLE ui_translations MODIFY `key` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ui_translations')) {
            DB::statement('ALTER TABLE ui_translations MODIFY `key` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
        }
    }
};
