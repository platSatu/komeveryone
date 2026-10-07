<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * kom_v02 — Baseline struktur tabel kmd_*.
 *
 * Tabel kmd_* awalnya dibuat oleh migration di project lama dan tidak pernah
 * tercatat di project ini. File ini mencatat struktur production (lihat
 * database/schema/kmd_baseline.sql) supaya database baru (developer baru /
 * server baru) bisa dibangun ulang dari kode.
 *
 * AMAN untuk production:
 *  - Jika tabel `kmd_users` sudah ada, migration ini TIDAK melakukan apa pun.
 *  - down() sengaja kosong: rollback tidak boleh menghapus tabel berisi data.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kmd_users')) {
            return; // database sudah berisi (production / salinan production)
        }

        $sql = file_get_contents(database_path('schema/kmd_baseline.sql'));

        DB::unprepared($sql);
    }

    public function down(): void
    {
        // SENGAJA DIKOSONGKAN — lihat keterangan di atas.
    }
};
