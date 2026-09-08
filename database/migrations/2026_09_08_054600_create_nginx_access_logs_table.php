<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel penyimpanan hasil parse access.log nginx, diisi oleh
 * App\Console\Commands\ImportNginxLog (bukan oleh aplikasi/middleware).
 * Ditambahkan 8 September 2026 (permintaan user).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nginx_access_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->string('method', 10)->nullable();
            $table->string('path', 2048)->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->unsignedBigInteger('bytes_sent')->nullable();
            $table->string('referrer', 500)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('requested_at');
            $table->timestamps();

            $table->index('requested_at');
            $table->index('ip_address');
            $table->index('status_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nginx_access_logs');
    }
};
