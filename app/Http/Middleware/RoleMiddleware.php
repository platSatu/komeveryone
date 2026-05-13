<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
{
    // Coba log info awal middleware jalan
    Log::info('Middleware cekrolekom dijalankan.');

    // Coba ambil user
    $user = Auth::guard('kom')->user();

    // Log data user yang sedang login
    Log::info('User login:', ['user' => $user]);

    // Log role yang diminta dan role user
    Log::info('Role yang dibutuhkan:', $roles);
    Log::info('Role user:', [$user ? $user->role : null]);

    // Validasi role
    if (!$user || !in_array($user->role, $roles)) {
        Log::warning('Akses ditolak: user tidak memiliki role yang sesuai.');
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }

    return $next($request);
}
}
