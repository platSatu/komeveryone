<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Cekrole;

class OnlyOneAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Mendapatkan user yang sedang login
        $user = Auth::user();

        // Mendapatkan menu_id dari URL
        $menuId = $request->route('menu_id');  // Pastikan menu_id ada dalam parameter route

        // Jika tidak ada menu_id dalam URL, abort
        if (!$menuId) {
            return abort(403, 'Menu tidak ditemukan!');
        }

        // Memeriksa apakah user memiliki hak akses pada menu_id yang sesuai dan memiliki akses 'list' = 1
        $cekrole = Cekrole::where('user_id', $user->id)
                          ->where('menu_id', $menuId)  // Memastikan menu_id sesuai dengan yang ada di URL
                          ->where('list', 1)           // Cek apakah hak akses `list` = 1
                          ->first();                   // Ambil data pertama yang sesuai

        // Jika tidak ditemukan data cekrole atau tidak ada akses (list != 1)
        if (!$cekrole) {
            return abort(403, 'Anda tidak memiliki akses ke menu ini.');
        }

        // Lanjutkan ke rute jika hak akses sesuai
        return $next($request);
    }
}
