<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah ada locale yang diset di session, jika ada gunakan itu
        $locale = session('locale', config('app.locale'));

        // Tentukan bahasa yang digunakan aplikasi
        app()->setLocale($locale);

        // Lanjutkan proses request
        return $next($request);
    }
}
