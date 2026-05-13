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
        ]);
    })
    
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
