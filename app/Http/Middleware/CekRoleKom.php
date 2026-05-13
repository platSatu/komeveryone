<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekRoleKom
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return redirect()->route('kom.login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Cek apakah role user ada dalam daftar role yang diizinkan
        if (!in_array($user->role, $roles)) {
            return redirect()->route('kom.dashboard_kom')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Jika role bukan superadmin, cek status aktif
        if ($user->role !== 'superadmin') {
            $statusAktifValues = [true, 'aktif', 1];

            if (!in_array($user->status, $statusAktifValues, true)) {
                return redirect()->route('kom.dashboard_kom')->with('error', 'Status akun Anda tidak aktif atau belum disetujui.');
            }
        }

        return $next($request);
    }
}
