<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * kom_v02 — Pendaftaran user ke kelas (rangkaian KOM 100 / 200 / ...).
 *
 * Satu baris = user X mengikuti kelas Y dengan bahasa Z.
 *  - language_code : bahasa rangkaian, TERKUNCI selama status = 'active'
 *  - book_id       : kode buku yang membuka kelas ini
 *  - status        : 'active' (sedang berjalan) | 'completed' (lulus, sertifikat terbit)
 *  - certificate_id: sertifikat kelas ini (dipakai validasi prasyarat kelas berikutnya)
 *
 * Backfill "jejak" user lama:
 *  - Setiap user yang punya kode buku AKTIF di kmd_books dibuatkan pendaftaran
 *    ke kelas buku tsb, bahasa 'id'. User ini langsung masuk tanpa input kode buku lagi.
 *  - Jika user sudah punya sertifikat → status 'completed' + certificate_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kmd_user_classes')) {
            Schema::create('kmd_user_classes', function (Blueprint $table) {
                $table->char('id', 36)->primary();
                $table->char('user_id', 36);
                $table->char('class_id', 36);
                $table->string('language_code', 10)->default('id');
                $table->char('book_id', 36)->nullable();
                $table->string('status', 20)->default('active'); // active | completed
                $table->char('certificate_id', 36)->nullable();
                $table->timestamp('enrolled_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'class_id']);
                $table->index(['user_id', 'status']);
                $table->index('class_id');
                $table->index('book_id');
                $table->index('certificate_id');
            });
        }

        // ---- Backfill dari kode buku aktif (jejak user lama) ----
        // INSERT IGNORE: aman dijalankan ulang; user dgn >1 buku di kelas sama → 1 baris.
        DB::statement("
            INSERT IGNORE INTO kmd_user_classes
                (id, user_id, class_id, language_code, book_id, status,
                 certificate_id, enrolled_at, completed_at, created_at, updated_at)
            SELECT
                UUID(), b.user_id, b.class_id, 'id', b.id,
                CASE WHEN c.id IS NULL THEN 'active' ELSE 'completed' END,
                c.id,
                COALESCE(b.created_at, NOW()),
                CASE WHEN c.id IS NULL THEN NULL ELSE COALESCE(c.created_at, NOW()) END,
                NOW(), NOW()
            FROM kmd_books b
            LEFT JOIN (
                SELECT user_id, MIN(id) AS id, MIN(created_at) AS created_at
                FROM kmd_certificates
                GROUP BY user_id
            ) c ON c.user_id = b.user_id
            WHERE b.status = 'active'
              AND b.user_id IS NOT NULL
              AND b.class_id IS NOT NULL
            ORDER BY b.created_at ASC
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('kmd_user_classes');
    }
};
