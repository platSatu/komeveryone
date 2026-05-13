<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Menambahkan header X-Content-Type-Options untuk mencegah browser menebak MIME type
        // Untuk meningkatkan keamanan dengan menghindari content sniffing.
        header('X-Content-Type-Options: nosniff');

        // Menambahkan header X-Frame-Options untuk melarang pemuatan halaman dalam frame atau iframe
        // untuk mencegah serangan clickjacking.
        header('X-Frame-Options: DENY');

        // Menambahkan header Content-Security-Policy (CSP) untuk melindungi dari XSS dan serangan terkait.
        // Ini adalah contoh dasar, kamu dapat menyesuaikan dengan lebih spesifik.
        header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'");

        // Menambahkan header X-XSS-Protection untuk mengaktifkan perlindungan XSS di browser.
        header('X-XSS-Protection: 1; mode=block');

        // Menambahkan header Strict-Transport-Security untuk memastikan hanya koneksi HTTPS.
        // Menghindari serangan man-in-the-middle (MITM) dengan HTTP downgrade.
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

        // Menambahkan header Referrer-Policy untuk membatasi informasi yang dikirim dalam header referer.
        header('Referrer-Policy: no-referrer-when-downgrade');

        // Menambahkan header Permissions-Policy untuk membatasi akses ke fitur browser tertentu.
        header('Permissions-Policy: geolocation=(), microphone=()');

        // Lanjutkan dengan request
        return $next($request);
    }
}
