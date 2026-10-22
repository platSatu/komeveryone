<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * kom_v02 — Setting payment gateway (Midtrans / BB · Bizbos).
 *
 * 1. kom_payment_settings: setting key-value yang diatur superadmin
 *    (gateway aktif + kredensial Bizbos; secret disimpan terenkripsi).
 * 2. kmd_donations: mencatat persembahan dibayar lewat gateway mana.
 *    Data lama (NULL) = Midtrans, jadi tidak ada data yang diubah.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kom_payment_settings')) {
            Schema::create('kom_payment_settings', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('key', 100)->unique();
                $table->text('value')->nullable();
                $table->boolean('is_secret')->default(false);
                $table->char('updated_by', 36)->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('kmd_donations')) {
            Schema::table('kmd_donations', function (Blueprint $table) {
                if (! Schema::hasColumn('kmd_donations', 'payment_gateway')) {
                    $table->string('payment_gateway', 20)->nullable()->after('payment_type'); // midtrans | bizbos
                }
                if (! Schema::hasColumn('kmd_donations', 'gateway_invoice_id')) {
                    $table->string('gateway_invoice_id', 64)->nullable()->after('payment_gateway');
                }
                if (! Schema::hasColumn('kmd_donations', 'checkout_url')) {
                    $table->text('checkout_url')->nullable()->after('gateway_invoice_id');
                }
            });
        }
    }

    public function down(): void
    {
        // Sengaja tidak menghapus data pembayaran.
    }
};
