<?php

namespace App\Http\Middleware;

use App\Services\Kmd\KelasAktifService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Akses halaman belajar video.
 *
 * kom_v02: syaratnya sekarang user TERDAFTAR di sebuah kelas (kmd_user_classes),
 * hasil input kode buku per kelas. User lama sudah otomatis terdaftar di KOM 100
 * (backfill dari kode buku aktif), jadi aksesnya tetap sama seperti sebelumnya.
 */
class KelasKomMiddleware
{
    public function __construct(protected KelasAktifService $kelasAktif)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            return redirect('/login');
        }

        $userClass = $this->kelasAktif->forUser($user);

        // Cadangan untuk akun lama yang punya buku aktif tapi belum tercatat di kelas
        $hasActiveBook = $user->books()->where('status', 'active')->exists();

        if (! $userClass && ! $hasActiveBook) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Anda belum terdaftar di kelas mana pun. Silakan pilih kelas terlebih dahulu.'),
                    'redirect' => route('kom.katalog.index'),
                ], 403);
            }

            return redirect()->route('kom.katalog.index')
                ->with('error', __('Anda belum terdaftar di kelas mana pun. Silakan pilih kelas terlebih dahulu.'));
        }

        return $next($request);
    }
}
