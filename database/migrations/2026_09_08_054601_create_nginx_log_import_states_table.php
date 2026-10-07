<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nyimpan posisi baca terakhir (byte offset + inode) file access.log
 * nginx per path, supaya App\Console\Commands\ImportNginxLog bisa jalan
 * berulang-ulang (cron) tanpa nge-import baris yang sama dua kali, dan
 * otomatis reset kalau file di-rotate logrotate (inode berubah).
 * Ditambahkan 8 September 2026 (permintaan user).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('nginx_log_import_states')) {
            Schema::create('nginx_log_import_states', function (Blueprint $table) {
                $table->id();
                $table->string('log_path', 500)->unique();
                $table->unsignedBigInteger('last_inode')->nullable();
                $table->unsignedBigInteger('last_offset')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('nginx_log_import_states');
    }
};
