<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SettingMenu;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    public function handle(Request $request, Closure $next, $menuId)
    {
        // Pastikan user sudah login
        if (auth()->check()) {
            $user = auth()->user();
            $roleId = $user->role_id;
            $userId = $user->id;

            // Cek apakah pengguna memiliki akses ke menu berdasarkan user_id, role_id, dan menu_id
            $menuAccess = SettingMenu::where('user_id', $userId)
                                      ->where('role_id', $roleId)
                                      ->where('menu_id', $menuId)
                                      ->where('status', 'active')
                                      ->exists();  // Mengecek apakah ada menu yang aktif

            // Jika tidak ada akses, maka redirect atau tampilkan error
            if (!$menuAccess) {
                // Redirect ke halaman yang diinginkan atau bisa berikan error
                return redirect()->route('home')->with('error', 'Anda tidak memiliki akses ke menu ini.');
            }

            // Jika akses diterima, lanjutkan ke request berikutnya
            return $next($request);
        }

        // Jika pengguna belum login, redirect ke halaman login
        return redirect()->route('login');
    }
}
