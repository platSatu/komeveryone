<?php

namespace App\Http\Middleware;

use App\Models\Kmd\Language;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * kom_v02 — Bahasa tampilan mengikuti bahasa belajar user.
 *
 * 1. Mengatur bahasa aplikasi (app()->setLocale) sesuai kmd_users.language_code.
 * 2. Peserta yang BELUM memilih bahasa (user baru) melihat POP-UP "Pilih Bahasa"
 *    (Bahasa Indonesia terpilih sebagai bawaan) sebelum bisa memakai halaman lain.
 *    User lama sudah otomatis 'id' (lihat migration), jadi tidak terganggu.
 */
class KomLanguageMiddleware
{
    /** Halaman yang tetap boleh dibuka walau bahasa belum dipilih. */
    protected array $except = [
        'kom.bahasa.index',
        'kom.bahasa.store',
        'kom.logout',
        'kom.profile_kom',
        'kom.profile_kom.update',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $locale = $user?->language_code ?: Language::defaultCode();
        app()->setLocale($locale);

        if ($user && $user->role === 'peserta' && empty($user->language_code)) {
            $route = $request->route()?->getName();

            if (! in_array($route, $this->except, true)) {
                // kom_v02: halaman biasa (GET) tetap dibuka, tetapi layout menampilkan POP-UP
                // "Pilih Bahasa" yang wajib diisi (tidak bisa ditutup). Bahasa Indonesia terpilih
                // sebagai bawaan. Aksi lain (POST / AJAX) tetap ditahan sampai bahasa dipilih.
                if ($request->isMethod('GET') && ! $request->expectsJson()) {
                    \Illuminate\Support\Facades\View::share('komPopupPilihBahasa', true);

                    return $next($request);
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Silakan pilih bahasa belajar terlebih dahulu.'),
                        'redirect' => route('kom.bahasa.index'),
                    ], 403);
                }

                return redirect()->route('kom.bahasa.index');
            }
        }

        return $next($request);
    }
}
