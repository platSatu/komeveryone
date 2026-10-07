<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * kom_v02 — Bahasa pada konten belajar.
 *
 * Struktur konten:  kelas → modul → video → (question_videos) → soal → jawaban
 *
 *  - kmd_moduls.language_code   : bahasa modul. Video & relasi soal mengikuti modul.
 *  - kmd_questions.language_code: agar soal yang BELUM dihubungkan ke video tetap
 *                                 bisa difilter per bahasa di halaman superadmin.
 *  - source_*_id                : menunjuk ke data asal saat konten DISALIN ke bahasa
 *                                 lain (mis. modul English disalin dari modul Indonesia).
 *                                 Dipakai untuk export/import terjemahan & aturan sesi.
 *
 * Data lama: otomatis 'id' lewat default kolom. Tidak ada data yang diubah/dihapus.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kmd_moduls', function (Blueprint $table) {
            if (! Schema::hasColumn('kmd_moduls', 'language_code')) {
                $table->string('language_code', 10)->default('id')->after('kom_class_id');
                $table->index(['kom_class_id', 'language_code']);
            }
            if (! Schema::hasColumn('kmd_moduls', 'source_modul_id')) {
                $table->char('source_modul_id', 36)->nullable()->after('language_code');
                $table->index('source_modul_id');
            }
        });

        Schema::table('kmd_videos', function (Blueprint $table) {
            if (! Schema::hasColumn('kmd_videos', 'source_video_id')) {
                $table->char('source_video_id', 36)->nullable()->after('modul_id');
                $table->index('source_video_id');
            }
        });

        Schema::table('kmd_questions', function (Blueprint $table) {
            if (! Schema::hasColumn('kmd_questions', 'language_code')) {
                $table->string('language_code', 10)->default('id')->after('questions');
                $table->index('language_code');
            }
            if (! Schema::hasColumn('kmd_questions', 'source_question_id')) {
                $table->char('source_question_id', 36)->nullable()->after('language_code');
                $table->index('source_question_id');
            }
        });

        Schema::table('kmd_answereds', function (Blueprint $table) {
            if (! Schema::hasColumn('kmd_answereds', 'source_answer_id')) {
                $table->char('source_answer_id', 36)->nullable()->after('questions_id');
                $table->index('source_answer_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kmd_answereds', function (Blueprint $table) {
            if (Schema::hasColumn('kmd_answereds', 'source_answer_id')) {
                $table->dropIndex(['source_answer_id']);
                $table->dropColumn('source_answer_id');
            }
        });

        Schema::table('kmd_questions', function (Blueprint $table) {
            if (Schema::hasColumn('kmd_questions', 'source_question_id')) {
                $table->dropIndex(['source_question_id']);
                $table->dropColumn('source_question_id');
            }
            if (Schema::hasColumn('kmd_questions', 'language_code')) {
                $table->dropIndex(['language_code']);
                $table->dropColumn('language_code');
            }
        });

        Schema::table('kmd_videos', function (Blueprint $table) {
            if (Schema::hasColumn('kmd_videos', 'source_video_id')) {
                $table->dropIndex(['source_video_id']);
                $table->dropColumn('source_video_id');
            }
        });

        Schema::table('kmd_moduls', function (Blueprint $table) {
            if (Schema::hasColumn('kmd_moduls', 'source_modul_id')) {
                $table->dropIndex(['source_modul_id']);
                $table->dropColumn('source_modul_id');
            }
            if (Schema::hasColumn('kmd_moduls', 'language_code')) {
                $table->dropIndex(['kom_class_id', 'language_code']);
                $table->dropColumn('language_code');
            }
        });
    }
};
