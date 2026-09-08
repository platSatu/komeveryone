<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Ditambahkan 8 September 2026 (permintaan user): jadwalkan import access.log
// nginx tiap 5 menit untuk halaman Statistik Pengunjung (Log Nginx). Supaya
// jalan, cron `* * * * * php artisan schedule:run` harus sudah aktif di VPS
// (ini standar bawaan Laravel -- kalau belum ada, tambahkan manual di VPS).
Schedule::command('nginx:import-log')->everyFiveMinutes()->withoutOverlapping();
