<?php

namespace App\Providers;

use App\Translation\DatabaseTranslationLoader;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // kom_v02: terjemahan tampilan diambil juga dari database (ui_translations)
        $this->app->extend('translation.loader', function ($loader) {
            return new DatabaseTranslationLoader($loader);
        });

        // 5 Nov 2026: satu instance per proses, menyimpan job antrean yang sedang dikirim (KomEmailLogRecorder)
        $this->app->singleton(\App\Support\KomEmailLogRecorder::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 5 Nov 2026: catat semua email ke kom_email_log (tab Riwayat Terkirim di Kirim Ulang Email)
        \Illuminate\Support\Facades\Event::subscribe(\App\Support\KomEmailLogRecorder::class);

        // 4 Nov 2026: awalan route halaman Kelas Tutorial (admin cabang / Admin KOM), lihat KelasTutorService::awalanRute()
        \Illuminate\Support\Facades\View::composer('a_kom_digital_pusat.admin_cabang.kelas_tutorial.*', function ($view) {
            $view->with('ktRute', \App\Services\AdminCabang\KelasTutorService::awalanRute());
        });
    }
}
