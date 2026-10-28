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

    // Email notifikasi kelas tutorial (App\Services\Kmd\NotifikasiKelasService).
    'notifikasi' => [
        // Saklar utama. Mati = tidak ada email notifikasi baru (email terima kasih yang lama tetap jalan).
        'aktif' => (bool) env('KOM_NOTIFIKASI_AKTIF', false),

        // Penerima tetap saat kelas tutorial baru dibuka (selain admin cabang).
        'email_pusat' => env('KOM_EMAIL_NOTIFIKASI', 'natanaeltamto@gmail.com'),

        // Jarak pengingat persembahan yang belum selesai (menit).
        'pengingat_bayar_menit' => (int) env('KOM_PENGINGAT_BAYAR_MENIT', 5),

        // Pengingat "hari H" dikirim mulai jam ini (WIB) pada hari kelas.
        'jam_pengingat_hari_h' => (int) env('KOM_JAM_PENGINGAT_HARI_H', 6),
    ],

];
