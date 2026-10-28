<?php

namespace App\Console\Commands;

use App\Services\Kmd\NotifikasiKelasService;
use Illuminate\Console\Command;

/**
 * kom_v02 — pengingat persembahan (tiap 5 menit) & pengingat kelas tutorial (H-3, H-2, hari H, 1 jam).
 * Dijadwalkan tiap menit (routes/console.php). Aman dijalankan berulang: tiap email hanya terkirim sekali.
 *
 *   php artisan kom:kirim-notifikasi --simulasi   → lihat calon penerima tanpa mengirim apa pun
 */
class KirimNotifikasiKelas extends Command
{
    protected $signature = 'kom:kirim-notifikasi {--simulasi : Tampilkan calon penerima tanpa mengirim email}';

    protected $description = 'Kirim email pengingat persembahan & pengingat kelas tutorial';

    public function handle(NotifikasiKelasService $notifikasi): int
    {
        $simulasi = (bool) $this->option('simulasi');

        if (! $simulasi && ! $notifikasi->aktif()) {
            $this->line('Notifikasi nonaktif (KOM_NOTIFIKASI_AKTIF=false).');

            return self::SUCCESS;
        }

        if ($simulasi) {
            $notifikasi->simulasi();
        }

        $bayar = $notifikasi->pengingatBayar();
        $kelas = $notifikasi->pengingatKelas();

        if ($simulasi) {
            $this->table(['Penerima', 'Subjek'], $notifikasi->hasilSimulasi);
            $this->info("SIMULASI — tidak ada email yang dikirim. Pengingat persembahan: {$bayar}, pengingat kelas: {$kelas}.");
        } else {
            $this->info("Pengingat persembahan: {$bayar}, pengingat kelas: {$kelas}.");
        }

        return self::SUCCESS;
    }
}
