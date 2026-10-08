<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * kom_v02 — Terjemahan teks tampilan (menu, tombol, judul, pesan).
 *
 * Disimpan di database (bukan file lang/) supaya superadmin bisa menambah /
 * mengubah terjemahan untuk bahasa baru tanpa programmer.
 *
 * Contoh baris:
 *   key = 'menu.kelas_tutorial', language_code = 'id', value = 'Kelas Tutorial'
 *   key = 'menu.kelas_tutorial', language_code = 'en', value = 'Tutorial Class'
 *
 * Jika terjemahan suatu bahasa belum diisi, aplikasi memakai Bahasa Indonesia.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ui_translations')) {
            Schema::create('ui_translations', function (Blueprint $table) {
                $table->char('id', 36)->primary(); // UUID, konsisten dengan tabel kmd_*
                $table->string('group', 50)->default('general'); // menu, tombol, pesan, ...
                $table->string('key', 191)->collation('utf8mb4_bin'); // peka huruf besar/kecil
                $table->string('language_code', 10);
                $table->text('value')->nullable();
                $table->timestamps();

                $table->unique(['key', 'language_code']);
                $table->index(['language_code', 'group']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ui_translations');
    }
};
