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
        if (! Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration')->index();
            });
        }

        if (! Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration')->index();
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
        // Schema::dropIfExists('cache');
        // Schema::dropIfExists('cache_locks');
    }
};
