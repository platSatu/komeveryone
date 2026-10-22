<?php

/*
 * kom_v02 — konfigurasi Midtrans.
 *
 * Sebelumnya kunci dibaca langsung dengan env() di PersembahanController. Setelah
 * `php artisan optimize` / `config:cache` (wajib di production), env() di luar folder
 * config mengembalikan NULL → pembayaran gagal. Halaman persembahan juga sudah memakai
 * config('midtrans.client_key') tetapi file ini belum ada.
 */
return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', true), // false = sandbox
    'is_sanitized' => (bool) env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => (bool) env('MIDTRANS_IS_3DS', true),
];
