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
        Log::info('🛡️ Middleware triggered.');
        $user = Auth::user();
        //$user = Auth::guard('kom')->user();

        if (!$user) {
            Log::warning('User not authenticated.');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi login telah berakhir. Silakan login ulang.'
                ], 401);
            }

            return redirect()->route('kom.login');
        }

        $progress = KmdUserClassTutorial::where('user_id', $user->id)
            ->where('status_progress', 'pending')
            ->whereIn('step', ['pilih', 'pertanyaan', 'donasi'])
            ->first();

        if (!$progress) {
            Log::info("No active progress found for user_id={$user->id}. Passing request without redirect.");
            return $next($request);
        }

        // ✅ Skip validasi untuk progress yang dibuat sebelum tanggal cutoff
        $cutoffDate = now()->createFromFormat('Y-m-d H:i:s', '2025-10-09 00:00:00');

        if ($progress->created_at->lessThan($cutoffDate)) {
            Log::info("⏩ Progress lama (dibuat {$progress->created_at}), dilewati middleware. User ID: {$user->id}, Progress ID: {$progress->id}");
            return $next($request);
        }

        $currentRoute = $request->route()->getName();
        Log::info("📍 Current route: $currentRoute | User ID: {$user->id} | Step progress: {$progress->step}");

        // Step: PILIH
        if ($progress->step === 'pilih') {
            $allowedRoutes = ['kom.kelas_tutorial.index'];

            if (!in_array($currentRoute, $allowedRoutes)) {
                Log::info("🔁 Redirect karena belum menyelesaikan step 'pilih'. User ID: {$user->id}, Current route: {$currentRoute}");
                return redirect()->route('kom.kelas_tutorial.index');
            }
        }

        // Step: PERTANYAAN
        if ($progress->step === 'pertanyaan') {
            $allowedRoutes = [
                'kom.kelas_tutorial.pertanyaan.form',
                'kom.kelas_tutorial.pertanyaan-kom-100.store', // Sesuaikan jika perlu
            ];

            if (!in_array($currentRoute, $allowedRoutes)) {
                Log::info("🔁 Redirect karena belum menyelesaikan step 'pertanyaan'. User ID: {$user->id}, Current route: {$currentRoute}");

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda belum menyelesaikan tahapan pertanyaan.',
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
            ];

            Log::info("➡️ Step 'donasi' detected for user_id={$user->id}. Checking allowed routes.");

            if (!in_array($currentRoute, $allowedRoutes)) {
                Log::warning("🔁 Redirect karena belum menyelesaikan step 'donasi'. User ID: {$user->id}, Current route: {$currentRoute}");
                Log::debug('Allowed routes: ' . implode(', ', $allowedRoutes));

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda belum menyelesaikan tahap persembahan.',
                        'redirect' => route('kom.form.persembahan'),
                    ], 403);
                }

                return redirect()->route('kom.form.persembahan');
            }

            Log::info("✅ User ID {$user->id} allowed to access route {$currentRoute} on step 'donasi'.");
        }

        return $next($request);
    }
}
