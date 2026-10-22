<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * kom_v02 — Kelas tutorial di TENGAH modul.
 *
 * Aturan:
 *  - Akhir modul SELALU ada kelas tutorial (otomatis, tidak disimpan sebagai centang).
 *  - Tengah modul: superadmin mencentang "Ada Kelas Tutorial setelah video ini"
 *    → kmd_videos.has_tutorial = 1.
 *
 * "Titik tutorial" disimpan sebagai video_id pada jadwal / booking / pertanyaan:
 *  - video_id = NULL  → tutorial AKHIR MODUL (sama dengan perilaku lama)
 *  - video_id = <id>  → tutorial setelah video tsb (tengah modul)
 *
 * Karena NULL = akhir modul, SEMUA data lama otomatis tetap berarti "akhir modul".
 * Tidak ada backfill, tidak ada data yang diubah. Hanya menambah kolom.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kmd_videos') && ! Schema::hasColumn('kmd_videos', 'has_tutorial')) {
            Schema::table('kmd_videos', function (Blueprint $table) {
                $table->boolean('has_tutorial')->default(false)->after('sort_video');
            });
        }

        if (Schema::hasTable('kmd_class_tutorial') && ! Schema::hasColumn('kmd_class_tutorial', 'video_id')) {
            Schema::table('kmd_class_tutorial', function (Blueprint $table) {
                $table->char('video_id', 36)->nullable()->after('modul_id');
                $table->index(['modul_id', 'video_id'], 'kct_modul_video_idx');
            });
        }

        if (Schema::hasTable('kmd_user_class_tutorial')) {
            Schema::table('kmd_user_class_tutorial', function (Blueprint $table) {
                if (! Schema::hasColumn('kmd_user_class_tutorial', 'video_id')) {
                    $table->char('video_id', 36)->nullable()->after('modul_id');
                    $table->index(['user_id', 'modul_id', 'video_id'], 'kuct_user_titik_idx');
                }
                if (! Schema::hasColumn('kmd_user_class_tutorial', 'is_rebook')) {
                    // 1 = jadwal pengganti karena tidak hadir (tanpa persembahan lagi)
                    $table->boolean('is_rebook')->default(false)->after('status_progress');
                }
                if (! Schema::hasColumn('kmd_user_class_tutorial', 'rebook_from_id')) {
                    $table->char('rebook_from_id', 36)->nullable()->after('is_rebook');
                }
            });
        }

        if (Schema::hasTable('kmd_pertanyaan_user') && ! Schema::hasColumn('kmd_pertanyaan_user', 'video_id')) {
            Schema::table('kmd_pertanyaan_user', function (Blueprint $table) {
                $table->char('video_id', 36)->nullable()->after('modul_id');
            });
        }
    }

    public function down(): void
    {
        // Sengaja dikosongkan: kolom tambahan aman dibiarkan, dan rollback tidak boleh
        // menghapus data booking / jadwal tutorial peserta.
    }
};
