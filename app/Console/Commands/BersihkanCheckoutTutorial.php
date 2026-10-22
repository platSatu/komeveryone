<?php

namespace App\Console\Commands;

use App\Services\Kmd\CheckoutTutorialService;
use Illuminate\Console\Command;

/**
 * kom_v02 — hapus pendaftaran kelas tutorial yang tidak jadi dibayar (lewat batas waktu).
 * Dijadwalkan tiap menit di routes/console.php.
 */
class BersihkanCheckoutTutorial extends Command
{
    protected $signature = 'kom:bersihkan-checkout';

    protected $description = 'Hapus pendaftaran kelas tutorial yang tidak dibayar melewati batas waktu (beserta pertanyaan & persembahannya)';

    public function handle(CheckoutTutorialService $checkout): int
    {
        $hasil = $checkout->bersihkanSemua();

        $this->info(sprintf(
            'Dihapus: %d · Ternyata sudah lunas: %d · Menunggu bank: %d',
            $hasil[CheckoutTutorialService::DIHAPUS],
            $hasil[CheckoutTutorialService::LUNAS],
            $hasil[CheckoutTutorialService::TUNGGU],
        ));

        return self::SUCCESS;
    }
}
