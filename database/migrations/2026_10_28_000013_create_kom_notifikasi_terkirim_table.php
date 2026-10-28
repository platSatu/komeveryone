<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * kom_v02 — penanda email notifikasi yang sudah dikirim (App\Services\Kmd\NotifikasiKelasService),
 * supaya setiap notifikasi hanya terkirim SEKALI walaupun jadwal berjalan tiap menit.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kom_notifikasi_terkirim')) {
            Schema::create('kom_notifikasi_terkirim', function (Blueprint $table) {
                $table->id();
                // mis. "pengingat-kelas:h3:{kelas_id}:{email}"
                $table->string('kunci', 191)->unique();
                $table->timestamp('created_at')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kom_notifikasi_terkirim');
    }
};
