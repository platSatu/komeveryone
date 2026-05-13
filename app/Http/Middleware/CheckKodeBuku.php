<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckKodeBuku
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah session kode_buku ada
        if (!session()->has('kode_buku')) {
            // Jika tidak ada, redirect ke halaman sebelumnya dengan pesan error
            return redirect()->route('form.cek')->with('error', 'Anda harus memasukkan kode buku yang valid untuk mengakses halaman ini.');
        }

        // Jika ada, lanjutkan ke route berikutnya
        return $next($request);
    }
}
