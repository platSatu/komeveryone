<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * kom_v02 — batas waktu memberikan persembahan (checkout).
 *
 * Diisi saat peserta masuk langkah persembahan. Lewat batas waktu (+ tenggang),
 * pendaftaran yang belum dibayar dihapus otomatis supaya peserta tidak "terkunci"
 * di halaman persembahan. Hanya menambah kolom; data lama tidak diubah.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kmd_user_class_tutorial') && ! Schema::hasColumn('kmd_user_class_tutorial', 'checkout_expires_at')) {
            Schema::table('kmd_user_class_tutorial', function (Blueprint $table) {
                $table->timestamp('checkout_expires_at')->nullable()->after('status_progress');
                $table->index(['status_progress', 'step'], 'kuct_status_step_idx');
            });
        }
    }

    public function down(): void
    {
        // Sengaja dikosongkan (kolom tambahan aman dibiarkan).
    }
};
