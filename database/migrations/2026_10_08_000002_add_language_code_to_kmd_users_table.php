<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * kom_v02 — Bahasa yang sedang dipakai user.
 *
 * - NULL  : user belum memilih bahasa → tampilkan pop-up pilih bahasa.
 * - Terisi: bahasa rangkaian kelas yang sedang berjalan. Terkunci selama user
 *           masih punya kelas berstatus 'active' di kmd_user_classes.
 *
 * Semua user yang SUDAH ADA diisi 'id' (Bahasa Indonesia), sehingga user lama
 * tidak melihat pop-up dan alurnya tetap seperti sekarang.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('kmd_users', 'language_code')) {
            Schema::table('kmd_users', function (Blueprint $table) {
                $table->string('language_code', 10)->nullable()->after('role');
                $table->index('language_code');
            });
        }

        // Backfill user lama → Bahasa Indonesia (hanya yang masih kosong)
        DB::table('kmd_users')->whereNull('language_code')->update(['language_code' => 'id']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('kmd_users', 'language_code')) {
            Schema::table('kmd_users', function (Blueprint $table) {
                $table->dropIndex(['language_code']);
                $table->dropColumn('language_code');
            });
        }
    }
};
