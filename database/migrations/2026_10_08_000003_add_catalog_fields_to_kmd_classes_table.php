<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * kom_v02 — Katalog kelas (KOM 100 / 200 / 300 / 400).
 *
 * kmd_classes (tambahan kolom):
 *  - code                  : slug untuk URL, mis. "kom-100" → /app/kelas/kom-100
 *  - sort_order            : urutan tampil di katalog
 *  - prerequisite_class_id : kelas yang wajib lulus dulu (KOM 200 → KOM 100)
 *  - cover_image           : gambar kartu katalog
 *
 * kmd_class_translations (tabel baru):
 *  - nama & deskripsi kelas per bahasa (katalog tampil sesuai bahasa user)
 *
 * Data lama: nama kelas yang ada disalin sebagai terjemahan Bahasa Indonesia.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kmd_classes', function (Blueprint $table) {
            if (! Schema::hasColumn('kmd_classes', 'code')) {
                $table->string('code', 50)->nullable()->unique()->after('name');
            }
            if (! Schema::hasColumn('kmd_classes', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('code');
            }
            if (! Schema::hasColumn('kmd_classes', 'prerequisite_class_id')) {
                $table->char('prerequisite_class_id', 36)->nullable()->after('sort_order');
                $table->index('prerequisite_class_id');
            }
            if (! Schema::hasColumn('kmd_classes', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('prerequisite_class_id');
            }
        });

        if (! Schema::hasTable('kmd_class_translations')) {
            Schema::create('kmd_class_translations', function (Blueprint $table) {
                $table->char('id', 36)->primary();
                $table->char('class_id', 36);
                $table->string('language_code', 10);
                $table->string('name');
                $table->text('description')->nullable();
                $table->timestamps();

                $table->unique(['class_id', 'language_code']);
                $table->index('language_code');
            });
        }

        // ---- Backfill data lama ----
        $now = now();
        foreach (DB::table('kmd_classes')->get() as $class) {
            // code & sort_order dari nama, mis. "KOM 100" → kom-100 / 100
            if (empty($class->code)) {
                $code = Str::slug($class->name);
                if (DB::table('kmd_classes')->where('code', $code)->exists()) {
                    $code = $code.'-'.substr($class->id, 0, 8);
                }
                $digits = (int) preg_replace('/\D/', '', $class->name);

                DB::table('kmd_classes')->where('id', $class->id)->update([
                    'code' => $code,
                    'sort_order' => $class->sort_order ?: $digits,
                ]);
            }

            // Nama kelas yang ada = terjemahan Bahasa Indonesia
            $exists = DB::table('kmd_class_translations')
                ->where('class_id', $class->id)->where('language_code', 'id')->exists();

            if (! $exists) {
                DB::table('kmd_class_translations')->insert([
                    'id' => (string) Str::uuid(),
                    'class_id' => $class->id,
                    'language_code' => 'id',
                    'name' => $class->name,
                    'description' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kmd_class_translations');

        Schema::table('kmd_classes', function (Blueprint $table) {
            foreach (['cover_image', 'prerequisite_class_id', 'sort_order', 'code'] as $col) {
                if (Schema::hasColumn('kmd_classes', $col)) {
                    if ($col === 'prerequisite_class_id') {
                        $table->dropIndex(['prerequisite_class_id']);
                    }
                    if ($col === 'code') {
                        $table->dropUnique(['code']);
                    }
                    $table->dropColumn($col);
                }
            }
        });
    }
};
