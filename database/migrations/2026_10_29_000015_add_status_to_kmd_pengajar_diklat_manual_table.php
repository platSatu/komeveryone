<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Status aktif/nonaktif pengajar diklat per kategori (29 Okt 2026).
 * Superadmin bisa menonaktifkan pengajar (yang lulus diklat maupun ditandai manual)
 * supaya tidak muncul di form Buat Kelas Tutorial admin cabang.
 *
 *   is_manual = 1 → ditandai manual sudah diklat (baris lama otomatis 1)
 *   is_manual = 0 → baris hanya untuk menyimpan status nonaktif pengajar hasil diklat
 *   is_active = 0 → dinonaktifkan superadmin (alasan & siapa/kapan dicatat)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kmd_pengajar_diklat_manual', function (Blueprint $table) {
            if (! Schema::hasColumn('kmd_pengajar_diklat_manual', 'is_manual')) {
                $table->boolean('is_manual')->default(true)->after('category_diklat');
            }
            if (! Schema::hasColumn('kmd_pengajar_diklat_manual', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_manual');
            }
            if (! Schema::hasColumn('kmd_pengajar_diklat_manual', 'alasan_nonaktif')) {
                $table->string('alasan_nonaktif', 255)->nullable()->after('catatan');
                $table->uuid('nonaktif_by')->nullable()->after('alasan_nonaktif');
                $table->timestamp('nonaktif_at')->nullable()->after('nonaktif_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kmd_pengajar_diklat_manual', function (Blueprint $table) {
            $table->dropColumn(['is_manual', 'is_active', 'alasan_nonaktif', 'nonaktif_by', 'nonaktif_at']);
        });
    }
};
