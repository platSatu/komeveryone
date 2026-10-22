<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * kom_v02 (keamanan) — kunci API untuk endpoint yang berisi data pribadi peserta
 * (mis. /api/books/no-books: kode buku, nama, no SIM, no sertifikat).
 *
 * Pemanggil wajib mengirim header:  X-API-KEY: <isi KOM_API_KEY di .env>
 *
 * Masa transisi: jika KOM_API_KEY belum diisi di .env, request masih DILOLOSKAN
 * (supaya sistem lain yang memakai endpoint ini tidak langsung putus), tapi dicatat
 * di log sebagai peringatan. Setelah KOM_API_KEY diisi, request tanpa kunci ditolak (401).
 */
class KomApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $kunci = (string) config('services.kom_api.key');

        if ($kunci === '') {
            Log::warning('[KomApiKey] KOM_API_KEY belum diisi — endpoint masih terbuka', [
                'path' => $request->path(), 'ip' => $request->ip(),
            ]);

            return $next($request);
        }

        $dikirim = (string) $request->header('X-API-KEY', '');

        if ($dikirim === '' || ! hash_equals($kunci, $dikirim)) {
            Log::warning('[KomApiKey] Akses API ditolak (kunci salah / kosong)', [
                'path' => $request->path(), 'ip' => $request->ip(),
            ]);

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
