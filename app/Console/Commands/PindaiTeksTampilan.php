<?php

namespace App\Console\Commands;

use App\Services\Kmd\TeksTampilanService;
use Illuminate\Console\Command;

/**
 * kom_v02 — php artisan kom:pindai-teks
 * Menambahkan teks tampilan baru ke menu "Terjemahan Tampilan" (semua bahasa non-utama).
 */
class PindaiTeksTampilan extends Command
{
    protected $signature = 'kom:pindai-teks';

    protected $description = 'Pindai teks __() di aplikasi dan siapkan baris terjemahan untuk setiap bahasa';

    public function handle(TeksTampilanService $service): int
    {
        $baru = $service->sinkron();
        $this->info("Selesai. {$baru} baris terjemahan baru disiapkan.");

        return self::SUCCESS;
    }
}
