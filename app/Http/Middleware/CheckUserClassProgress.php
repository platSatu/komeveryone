<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Kmd\KmdUserClassTutorial;

class CheckUserClassProgress
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        //$user = Auth::guard('kom')->user();

        if (!$user) {
            Log::warning('User not authenticated.');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Sesi login telah berakhir. Silakan login ulang.')
                ], 401);
            }

            return redirect()->route('kom.login');
        }

        $progress = KmdUserClassTutorial::where('user_id', $user->id)
            ->where('status_progress', 'pending')
            ->whereIn('step', ['pilih', 'pertanyaan', 'donasi'])
            ->first();

        if (!$progress) {
            return $next($request);
        }

        // ✅ Skip validasi untuk progress yang dibuat sebelum tanggal cutoff
        $cutoffDate = now()->createFromFormat('Y-m-d H:i:s', '2025-10-09 00:00:00');

        if ($progress->created_at->lessThan($cutoffDate)) {
            return $next($request);
        }

        $currentRoute = $request->route()->getName();

        // kom_v02: lewat batas waktu (tidak jadi memberikan persembahan) → hapus pendaftaran,
        // pertanyaan & persembahan yang belum lunas, lalu kembalikan ke daftar jadwal kelas tutorial.
        // Sebelum menghapus, status Midtrans dicek dulu (jika sudah dibayar → diproses lunas).
        $checkout = app(\App\Services\Kmd\CheckoutTutorialService::class);
        if ($checkout->sudahKedaluwarsa($progress)) {
            $hasil = $checkout->bersihkan($progress);

            if ($hasil === \App\Services\Kmd\CheckoutTutorialService::LUNAS) {
                return $next($request);
            }

            if ($hasil === \App\Services\Kmd\CheckoutTutorialService::DIHAPUS) {
                // middleware ini berjalan sebelum kom.bahasa → set bahasa pesan sesuai peserta
                if ($user->language_code) {
                    app()->setLocale($user->language_code);
                }
                $pesan = __('Waktu memberikan persembahan sudah habis, jadi pendaftaran kelas tutorial dibatalkan. Silakan pilih jadwal kelas tutorial lagi.');
                $tujuan = $progress->category_diklat
                    ? route('kom.diklat_tutor.index')
                    : route('kom.kelas_tutorial.index', ['modul_id' => $progress->modul_id, 'titik' => $progress->video_id ?: 'akhir']);

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $pesan, 'redirect' => $tujuan], 409);
                }

                return redirect($tujuan)->with('error', $pesan);
            }
            // TUNGGU: pembayaran transfer/VA sedang diproses bank → tetap di halaman persembahan
        }

        // Step: PILIH
        if ($progress->step === 'pilih') {
            $allowedRoutes = ['kom.kelas_tutorial.index'];

            if (!in_array($currentRoute, $allowedRoutes)) {
                return redirect()->route('kom.kelas_tutorial.index');
            }
        }

        // Step: PERTANYAAN
        if ($progress->step === 'pertanyaan') {
            $allowedRoutes = [
                'kom.kelas_tutorial.pertanyaan.form',
                'kom.kelas_tutorial.pertanyaan-kom-100.store', // Sesuaikan jika perlu
                'kom.persembahan.batal', // kom_v02: batalkan pendaftaran & pilih jadwal lain
            ];

            if (!in_array($currentRoute, $allowedRoutes)) {

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Anda belum menyelesaikan tahapan pertanyaan.'),
                        'redirect' => route('kom.kelas_tutorial.pertanyaan.form'),
                    ], 403);
                }

                return redirect()->route('kom.kelas_tutorial.pertanyaan.form');
            }
        }

        // Step: DONASI
        if ($progress->step === 'donasi') {
            $allowedRoutes = [
                'kom.form.persembahan',
                'kom.form.persembahan.store',
                'kom.persembahan.token',
                'kom.persembahan.batal', // kom_v02: batalkan pendaftaran & pilih jadwal lain
                'kom.persembahan.cek',   // kom_v02: cek status pembayaran BB (Bizbos)
            ];


            if (!in_array($currentRoute, $allowedRoutes)) {
                Log::warning("🔁 Redirect karena belum menyelesaikan step 'donasi'. User ID: {$user->id}, Current route: {$currentRoute}");

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Anda belum menyelesaikan tahap persembahan.'),
                        'redirect' => route('kom.form.persembahan'),
                    ], 403);
                }

                return redirect()->route('kom.form.persembahan');
            }

        }

        return $next($request);
    }
}
