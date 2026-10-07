<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * kom_v02: dibungkus Schema::hasTable() agar aman dijalankan di database
     * production yang tabelnya sudah ada (tidak ada tabel `migrations` di VPS).
     */
    public function up(): void
    {
        if (! Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->text('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // SENGAJA DIKOSONGKAN (kom_v02).
        // Tabel ini sudah ada di database production sebelum migration ini tercatat.
        // Rollback TIDAK boleh menghapus tabel berisi data production.
        // Schema::dropIfExists('personal_access_tokens');
    }
};
