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
// kom_v02: dinonaktifkan bersama halaman Statistik Pengunjung (Log Nginx); cukup satu statistik (TrackVisitor).
// Schedule::command('nginx:import-log')->everyFiveMinutes()->withoutOverlapping();

// kom_v02: hapus pendaftaran kelas tutorial yang tidak jadi dibayar (lewat batas waktu persembahan),
// supaya peserta tidak terkunci di halaman persembahan. Lokal: `php artisan schedule:work`.
Schedule::command('kom:bersihkan-checkout')->everyMinute()->withoutOverlapping();

// kom_v02: pengingat persembahan (tiap 5 menit) & pengingat kelas tutorial (H-3, H-2, hari H, 1 jam).
// Hanya mengirim kalau KOM_NOTIFIKASI_AKTIF=true; tiap email tercatat supaya tidak terkirim dobel.
Schedule::command('kom:kirim-notifikasi')->everyMinute()->withoutOverlapping();

// kom_v02: email (verifikasi daftar, reset password, dll.) dikirim lewat antrean (QUEUE_CONNECTION=database).
// Tanpa queue worker yang menyala terus, email hanya menumpuk di tabel `jobs` dan tidak pernah terkirim.
// Antrean diproses tiap menit lewat scheduler yang sama (cron `schedule:run`), berhenti sendiri saat antrean kosong.
Schedule::command('queue:work --stop-when-empty --tries=3 --max-time=55')->everyMinute()->withoutOverlapping();
