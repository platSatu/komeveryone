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
                ->with('error', 'Silakan pilih kelas tutorial terlebih dahulu.');
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
                'message' => 'Silakan login kembali sebelum mengirim pertanyaan.'
            ], 401);
        }

        //$user = Auth::guard('kom')->user();
        $user = Auth::user();
        Log::info('📝 storePertanyaan dipanggil', ['user_id' => $user->id]);

        // DEBUG: Log isi request sebelum validasi
        Log::debug('📥 Isi request untuk storePertanyaan:', $request->all());

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
                'message' => 'Validasi gagal. Silakan periksa input Anda.',
                'errors' => $e->errors(),
            ], 422);
        }

        $count = KmdPertanyaanUser::where('user_id', $user->id)->count();
        Log::info('📊 Jumlah pertanyaan user saat ini', ['count' => $count]);

        $inputCount = count($request->pertanyaan);
        $maxAllowed = 10 - $count;

        Log::info("📋 Jumlah pertanyaan dikirim: $inputCount, Maksimal tersisa: $maxAllowed");

        if ($maxAllowed <= 0) {
            Log::warning('🛑 Batas maksimal pertanyaan tercapai', ['user_id' => $user->id]);
            return response()->json(['success' => false, 'message' => 'Anda sudah mencapai batas maksimal 6 pertanyaan.'], 422);
        }

        if ($inputCount > $maxAllowed) {
            Log::warning('⚠️ Input pertanyaan melebihi batas maksimal', ['inputCount' => $inputCount, 'maxAllowed' => $maxAllowed]);
            return response()->json(['success' => false, 'message' => "Anda hanya dapat mengirim maksimal {$maxAllowed} pertanyaan lagi."], 422);
        }

        foreach ($request->pertanyaan as $index => $text) {
            Log::info("💾 Menyimpan pertanyaan ke-" . ($index + 1), [
                'pertanyaan' => $text,
                'pengajar_id' => $request->pengajar_id,
            ]);

            // Tambahan proteksi untuk pengajar_id walaupun nullable
            if (!$request->pengajar_id) {
                Log::error('❌ pengajar_id kosong saat insert. Gagal menyimpan pertanyaan.', [
                    'user_id' => $user->id,
                    'kelas_tutorial_id' => $request->kelas_tutorial_id,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Data pengajar tidak ditemukan. Silakan refresh halaman atau hubungi admin.'
                ], 422);
            }

            KmdPertanyaanUser::create([
                'user_id' => $user->id,
                'rayon_id' => $request->rayon_id,
                'class_id' => $request->class_id,
                'modul_id' => $request->modul_id,
                'kelas_tutorial_id' => $request->kelas_tutorial_id,
                'pengajar_id' => $request->pengajar_id,
                'pertanyaan' => $text,
                'status' => 'active',
                'tgl_submit' => now(),
                'category_diklat' => $request->category_diklat, 
            ]);
        }

        // Update step progress ke 'donasi'
        $progress = KmdUserClassTutorial::where('user_id', $user->id)
            ->where('status_progress', 'pending')
            ->where('step', 'pertanyaan')
            ->latest('created_at')
            ->first();

        if ($progress) {
            $progress->step = 'donasi';
            $progress->save();
            Log::info('✅ Progress step diperbarui menjadi donasi.', ['progress_id' => $progress->id]);
        } else {
            Log::warning('⚠️ Tidak ditemukan progress aktif dengan step pertanyaan untuk update.', ['user_id' => $user->id]);
        }

        Log::info('✅ Pertanyaan berhasil disimpan untuk user', [
            'user_id' => $user->id,
            'jumlah_pertanyaan' => $inputCount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pertanyaan berhasil dikirim.'
        ]);
    }


}
