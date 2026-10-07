<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * kom_v02 — Sertifikat per kelas & per bahasa.
 *
 * Sebelumnya sertifikat hanya terikat ke user (1 user = 1 sertifikat).
 * Dengan KOM 200 dst, sertifikat harus tahu milik kelas mana (untuk validasi
 * prasyarat) dan bahasa apa (format nomor & template berbeda per bahasa).
 *
 * Data lama: language_code = 'id' (default kolom); class_id diambil dari
 * pendaftaran kelas user (kmd_user_classes, hasil backfill kode buku).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kmd_certificates', function (Blueprint $table) {
            if (! Schema::hasColumn('kmd_certificates', 'class_id')) {
                $table->char('class_id', 36)->nullable()->after('user_id');
                $table->index(['user_id', 'class_id']);
            }
            if (! Schema::hasColumn('kmd_certificates', 'language_code')) {
                $table->string('language_code', 10)->default('id')->after('class_id');
            }
        });

        DB::statement("
            UPDATE kmd_certificates c
            JOIN kmd_user_classes uc ON uc.certificate_id = c.id
            SET c.class_id = uc.class_id
            WHERE c.class_id IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('kmd_certificates', function (Blueprint $table) {
            if (Schema::hasColumn('kmd_certificates', 'language_code')) {
                $table->dropColumn('language_code');
            }
            if (Schema::hasColumn('kmd_certificates', 'class_id')) {
                $table->dropIndex(['user_id', 'class_id']);
                $table->dropColumn('class_id');
            }
        });
    }
};
