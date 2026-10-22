<?php

namespace App\Http\Controllers\Kmd\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kmd\KmdPertanyaanUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Kmd\KmdUserClassTutorial;
use Illuminate\Support\Facades\Log;

class PertanyaanUserController extends Controller
{
    public function formPertanyaan()
    {
        $user = Auth::user();

        $progress = KmdUserClassTutorial::where('user_id', $user->id)
            ->where('status_progress', 'pending') // pastikan status aktif
            ->whereIn('step', ['pilih', 'pertanyaan']) // progress yang valid
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$progress) {
            return redirect()->route('kom.kelas_tutorial.index')
                ->with('error', __('Silakan pilih kelas tutorial terlebih dahulu.'));
        }

        // Update step ke 'pertanyaan' kalau belum
        if ($progress->step !== 'pertanyaan') {
            $progress->step = 'pertanyaan';
            $progress->save();
        }

        return view('a_kom_digital_pusat.dashboard.pertanyaanUser', [
            'progress_id' => $progress->id,
            'user_id' => $user->id,
            'class_tutorial_id' => $progress->class_tutorial_id,
            'class_id' => $progress->class_id,
            'modul_id' => $progress->modul_id,
            'rayon_id' => $progress->rayon_id,
            'pengajar_id' => $progress->pengajar_id,
            'step' => $progress->step,
            'status' => $progress->status,
            'category_diklat' => $progress->category_diklat, 
        ]);
    }


    public function storePertanyaan(Request $request)
    {
        //if (!Auth::guard('kom')->check()) {
        if (!Auth::check()) {    
            Log::warning('🛑 User belum login saat mengirim pertanyaan (AJAX)');
            return response()->json([
                'success' => false,
                'message' => __('Silakan login kembali sebelum mengirim pertanyaan.')
            ], 401);
        }

        //$user = Auth::guard('kom')->user();
        $user = Auth::user();

        // DEBUG: Log isi request sebelum validasi

        try {
            $request->validate([
                'pertanyaan' => 'required|array|min:1|max:2',
                'pertanyaan.*' => 'required|string|max:1000',
                'class_id' => 'required|string',
                'modul_id' => 'required|string',
                'kelas_tutorial_id' => 'required|string',
                'pengajar_id' => 'nullable|string',
                'rayon_id' => 'nullable|string',
                'category_diklat' => 'nullable|string', 
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // DEBUG: Log error validasi
            Log::error('❌ Validasi gagal di storePertanyaan', [
                'user_id' => $user->id,
                'errors' => $e->errors(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('Validasi gagal. Silakan periksa input Anda.'),
                'errors' => $e->errors(),
            ], 422);
        }

        // kom_v02: batas 2 pertanyaan PER KELAS TUTORIAL yang sedang diikuti
        // (sebelumnya maksimal 10 pertanyaan seumur akun → peserta modul berikutnya /
        // tutorial tengah modul pasti tertolak).
        $progressAktif = KmdUserClassTutorial::where('user_id', $user->id)
            ->where('status_progress', 'pending')
            ->where('step', 'pertanyaan')
            ->latest('created_at')
            ->first();

        if (! $progressAktif) {
            return response()->json([
                'success' => false,
                'message' => __('Silakan pilih kelas tutorial terlebih dahulu.'),
            ], 422);
        }

        $batas = 2;
        $count = KmdPertanyaanUser::where('user_id', $user->id)
            ->where('kelas_tutorial_id', $progressAktif->class_tutorial_id)
            ->count();

        // kom_v02 (perbaikan): pertanyaan SUDAH tersimpan tetapi langkah belum pindah ke persembahan
        // (mis. proses sebelumnya terputus) → jangan tolak, cukup lanjutkan ke persembahan.
        if ($count >= $batas) {
            $this->lanjutKePersembahan($progressAktif);

            return response()->json([
                'success' => true,
                'message' => __('Pertanyaan Anda sudah tersimpan sebelumnya. Silakan lanjut ke persembahan.'),
            ]);
        }

        $inputCount = count($request->pertanyaan);
        $maxAllowed = $batas - $count;

        if ($inputCount > $maxAllowed) {
            Log::warning('⚠️ Input pertanyaan melebihi batas maksimal', ['inputCount' => $inputCount, 'maxAllowed' => $maxAllowed]);
            return response()->json(['success' => false, 'message' => __('Anda hanya dapat mengirim maksimal :jumlah pertanyaan lagi.', ['jumlah' => $maxAllowed])], 422);
        }

        $pengajarId = $progressAktif->pengajar_id ?: $request->pengajar_id;
        if (! $pengajarId) {
            Log::error('❌ pengajar_id kosong saat insert. Gagal menyimpan pertanyaan.', [
                'user_id' => $user->id,
                'kelas_tutorial_id' => $progressAktif->class_tutorial_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => __('Data pengajar tidak ditemukan. Silakan refresh halaman atau hubungi admin.')
            ], 422);
        }

        // kom_v02 (perbaikan): simpan pertanyaan + pindah ke langkah persembahan dalam SATU transaksi.
        // Dulu kalau langkah kedua gagal, pertanyaan sudah tersimpan tetapi peserta tertahan di
        // langkah pertanyaan dan kiriman berikutnya ditolak (422 "batas maksimal").
        \Illuminate\Support\Facades\DB::connection('mysql')->transaction(function () use ($request, $user, $progressAktif, $pengajarId) {
            foreach ($request->pertanyaan as $text) {
                // kom_v02: data kelas diambil dari booking di server (bukan dari form)
                KmdPertanyaanUser::create([
                    'user_id' => $user->id,
                    'rayon_id' => $progressAktif->rayon_id ?? $request->rayon_id,
                    'class_id' => $progressAktif->class_id,
                    'modul_id' => $progressAktif->modul_id,
                    'video_id' => $progressAktif->video_id ?: null,
                    'kelas_tutorial_id' => $progressAktif->class_tutorial_id,
                    'pengajar_id' => $pengajarId,
                    'pertanyaan' => $text,
                    'status' => 'active',
                    'tgl_submit' => now(),
                    'category_diklat' => $request->category_diklat,
                ]);
            }

            $this->lanjutKePersembahan($progressAktif);
        });

        return response()->json([
            'success' => true,
            'message' => __('Pertanyaan berhasil dikirim.')
        ]);
    }

    /** kom_v02: pindah ke langkah persembahan & mulai hitung batas waktunya. */
    protected function lanjutKePersembahan(KmdUserClassTutorial $progress): void
    {
        $progress->step = 'donasi';
        $progress->checkout_expires_at = now()->addMinutes(app(\App\Services\Kmd\CheckoutTutorialService::class)->menitPersembahan());
        $progress->save();
    }


}
