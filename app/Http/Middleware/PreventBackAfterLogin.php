<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class PreventBackAfterLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Blokir akses ke halaman login, register, dan forget password jika sudah login
            if ($request->is('login') || $request->is('register') || $request->is('password/reset')) {
                return redirect()->route('home'); // Redirect ke halaman utama setelah login
            }
        }

        // Jika user sudah logout
        if (!Auth::check()) {
            // Menghindari user kembali ke halaman yang dilindungi setelah logout
            if ($request->is('dashboard') || $request->is('profile') || $request->is('settings')) {
                return redirect()->route('login'); // Redirect ke halaman login
            }

            // Mengatur header cache agar halaman tertentu tidak disimpan setelah logout
            $response = $next($request);
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
            return $response;
        }

        return $next($request);
    }
}
