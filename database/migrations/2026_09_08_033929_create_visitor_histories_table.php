<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel pencatat kunjungan halaman (dipakai App\Http\Middleware\TrackVisitor
 * & App\Models\VisitorHistory) -- sumber data buat halaman "Statistik
 * Pengunjung" di menu Superadmin (jumlah kunjungan per minggu/bulan/tahun +
 * halaman paling banyak dikunjungi).
 *
 * Ditambahkan 8 September 2026 (permintaan user). Middleware TrackVisitor
 * SEBELUMNYA sudah ada di app/Http/Middleware tapi tidak pernah didaftarkan
 * di bootstrap/app.php DAN model App\Models\VisitorHistory yang dipakainya
 * belum pernah dibuat -- jadi middleware itu sebenarnya belum pernah aktif
 * sama sekali sebelum perubahan ini (dicek: tidak ada referensi ke
 * TrackVisitor di bootstrap/app.php maupun routes manapun). Migration ini
 * MURNI menambah tabel baru, tidak menyentuh tabel lain yang sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_histories', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->nullable();
            $table->text('browser')->nullable();
            $table->string('page_url', 500);
            $table->string('method', 10)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->timestamp('visited_at')->useCurrent();
            $table->timestamps();

            $table->index('visited_at');
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_histories');
    }
};
