<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pengajar yang ditandai MANUAL oleh superadmin sebagai sudah ikut Diklat Tutor
 * untuk satu kategori (Doktrinal / Praktikal), mis. pengajar lama yang diklatnya
 * dulu tidak lewat aplikasi. Dibaca App\Services\Kmd\PengajarDiklatService
 * bersama data diklat asli (kmd_user_class_tutorial). Sengaja tabel terpisah,
 * supaya tidak membuat baris pendaftaran/persembahan palsu.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kmd_pengajar_diklat_manual')) {
            Schema::create('kmd_pengajar_diklat_manual', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id')->index();
                $table->string('category_diklat', 20);
                $table->string('catatan', 255)->nullable();
                $table->uuid('created_by')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'category_diklat']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kmd_pengajar_diklat_manual');
    }
};
