<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'kom.session' => \App\Http\Middleware\KomSessionMiddleware::class,
        ]);
    })

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'userprogress' => \App\Http\Middleware\CheckUserClassProgress::class,
        ]);
    })
    
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'kelaskom' => \App\Http\Middleware\KelasKomMiddleware::class,
        ]);
    })

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'cekrolekom' => \App\Http\Middleware\CekRoleKom::class,
            // kom_v02: bahasa tampilan mengikuti bahasa belajar user
            'kom.bahasa' => \App\Http\Middleware\KomLanguageMiddleware::class,
        ]);
    })
    
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })

    // Ditambahkan 8 September 2026: daftarkan App\Http\Middleware\TrackVisitor
    // (sebelumnya file middleware-nya sudah ada tapi TIDAK PERNAH didaftarkan
    // di sini -- jadi belum pernah aktif sama sekali) ke grup 'web' global,
    // sumber data buat halaman "Statistik Pengunjung" di menu Superadmin.
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', \App\Http\Middleware\TrackVisitor::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
