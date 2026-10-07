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
 * 2. Peserta yang BELUM memilih bahasa (user baru) diarahkan ke halaman
 *    "Pilih Bahasa Belajar" sebelum bisa membuka halaman lain.
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
