<?php

/*
 * kom_v02 — pengaturan aplikasi KOM.
 */
return [

    'checkout' => [
        // Batas waktu memberikan persembahan setelah pertanyaan terkirim (menit).
        // Link pembayaran Midtrans juga kedaluwarsa pada waktu yang sama.
        'batas_persembahan_menit' => (int) env('KOM_BATAS_PERSEMBAHAN_MENIT', 10),

        // Batas waktu mengisi 2 pertanyaan setelah memilih jadwal (menit).
        'batas_pertanyaan_menit' => (int) env('KOM_BATAS_PERTANYAAN_MENIT', 30),

        // Tenggang sebelum data dihapus oleh jadwal otomatis (menit), untuk
        // pembayaran transfer/VA yang notifikasinya dari bank sedikit terlambat.
        'tenggang_menit' => (int) env('KOM_TENGGANG_CHECKOUT_MENIT', 5),
    ],

];
