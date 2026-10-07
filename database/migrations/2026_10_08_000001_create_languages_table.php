<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * kom_v02 — Master bahasa.
 *
 * Superadmin bisa menambah bahasa & mengaktifkan/menonaktifkannya.
 * Hanya bahasa dengan is_active = 1 yang muncul di pop-up pilih bahasa user.
 * Bahasa default (is_default = 1) dipakai untuk user lama & fallback.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->char('id', 36)->primary(); // UUID, konsisten dengan tabel kmd_*
                $table->string('code', 10)->unique();          // id, en, zh, ...
                $table->string('name', 100);                    // Indonesia, English
                $table->string('native_name', 100)->nullable(); // Bahasa Indonesia, English, 中文
                $table->string('flag', 20)->nullable();         // emoji / kode bendera
                $table->string('certificate_prefix', 50)->nullable();
                $table->boolean('is_active')->default(false);
                $table->boolean('is_default')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        $now = now();

        // Bahasa Indonesia: default & aktif (semua konten lama berbahasa Indonesia)
        if (! DB::table('languages')->where('code', 'id')->exists()) {
            DB::table('languages')->insert([
                'id' => (string) Str::uuid(),
                'code' => 'id', 'name' => 'Indonesia', 'native_name' => 'Bahasa Indonesia',
                'flag' => '🇮🇩', 'is_active' => true, 'is_default' => true, 'sort_order' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // English: disiapkan tapi NONAKTIF sampai superadmin selesai mengisi konten
        if (! DB::table('languages')->where('code', 'en')->exists()) {
            DB::table('languages')->insert([
                'id' => (string) Str::uuid(),
                'code' => 'en', 'name' => 'English', 'native_name' => 'English',
                'flag' => '🇬🇧', 'is_active' => false, 'is_default' => false, 'sort_order' => 2,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
