<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * kom_v02 — Tarik dana admin cabang lewat Disbursement Bizbos.
 * Hanya MENAMBAH kolom (nullable); data lama tidak diubah.
 * Kolom status yang masih ENUM diubah ke VARCHAR supaya status "failed" bisa disimpan.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kmd_user_rekening')) {
            Schema::table('kmd_user_rekening', function (Blueprint $table) {
                foreach ([
                    'bank_code' => fn () => $table->string('bank_code', 20)->nullable(),
                    'bizbos_account_id' => fn () => $table->string('bizbos_account_id', 64)->nullable(),
                    'bizbos_verified_name' => fn () => $table->string('bizbos_verified_name', 150)->nullable(),
                    'bizbos_verified_at' => fn () => $table->timestamp('bizbos_verified_at')->nullable(),
                ] as $kolom => $tambah) {
                    if (! Schema::hasColumn('kmd_user_rekening', $kolom)) {
                        $tambah();
                    }
                }
            });
        }

        if (Schema::hasTable('kmd_tarik_saldo')) {
            Schema::table('kmd_tarik_saldo', function (Blueprint $table) {
                foreach ([
                    'approved_by' => fn () => $table->char('approved_by', 36)->nullable(),
                    'approved_at' => fn () => $table->timestamp('approved_at')->nullable(),
                    'rejected_reason' => fn () => $table->string('rejected_reason', 255)->nullable(),
                    'bizbos_payout_id' => fn () => $table->string('bizbos_payout_id', 64)->nullable(),
                    'bizbos_status' => fn () => $table->string('bizbos_status', 30)->nullable(),
                    'gagal_alasan' => fn () => $table->string('gagal_alasan', 255)->nullable(),
                ] as $kolom => $tambah) {
                    if (! Schema::hasColumn('kmd_tarik_saldo', $kolom)) {
                        $tambah();
                    }
                }
            });

            $status = collect(Schema::getColumns('kmd_tarik_saldo'))->firstWhere('name', 'status');
            if ($status && ($status['type_name'] ?? '') === 'enum') {
                Schema::table('kmd_tarik_saldo', function (Blueprint $table) use ($status) {
                    $table->string('status', 20)->nullable((bool) $status['nullable'])->default('pending')->change();
                });
            }
        }
    }

    public function down(): void
    {
        // Sengaja tidak menghapus kolom supaya riwayat pengiriman dana tidak hilang.
    }
};
