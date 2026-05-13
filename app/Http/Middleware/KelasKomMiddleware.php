<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KelasKomMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Jika belum login, redirect ke login
        if (!$user) {
            return redirect('/login');
        }

        // Cek apakah punya buku dengan status 'active'
        $hasActiveBook = $user->books()->where('status', 'active')->exists();

        if (!$hasActiveBook) {
            return redirect('/')->with('error', 'Akses ditolak. Anda belum memiliki buku aktif.');
        }

        return $next($request);
    }
}
