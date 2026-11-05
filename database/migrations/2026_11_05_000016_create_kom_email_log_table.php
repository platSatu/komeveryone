<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 5 Nov 2026 — riwayat semua email aplikasi (tab "Riwayat Terkirim" di Admin KOM > Kirim Ulang Email).
 * Diisi otomatis oleh App\Support\KomEmailLogRecorder. Isi email TIDAK disimpan, hanya judulnya.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kom_email_log')) {
            Schema::create('kom_email_log', function (Blueprint $table) {
                $table->id();
                $table->string('jenis', 191);              // kelas Mailable / Notification
                $table->string('penerima', 191);
                $table->string('judul', 255)->nullable();
                $table->string('status', 20)->index();     // antre | terkirim | gagal
                $table->uuid('job_uuid')->nullable()->index();
                $table->string('error', 500)->nullable();
                $table->timestamp('terkirim_at')->nullable();
                $table->timestamps();

                $table->index('created_at');
                $table->index('penerima', 'kom_email_log_penerima_index');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kom_email_log');
    }
};
