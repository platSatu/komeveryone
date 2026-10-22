<?php

namespace App\Http\Controllers\Kmd\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kmd\KmdClassTutorial;
use App\Models\Kmd\KmdModuls;
use App\Models\Kmd\Cabang;
use App\Models\Kmd\UserClassProgress;
use App\Models\Kmd\KmdUserClassTutorial;
use Illuminate\Support\Facades\Auth;
use App\Rules\CustomDbRule;
use App\Services\AdminCabang\KelasTutorService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class KelasTutorialController extends Controller
{


    public function index(Request $request)
    {
        //$query = KmdClassTutorial::where('status', 'active');
        $query = KmdClassTutorial::with(['modul', 'kelas', 'rayon', 'titikVideo'])
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('category_diklat')
                    ->orWhere('category_diklat', '');
            });

        // kom_v02: hanya jadwal untuk KELAS + BAHASA rangkaian peserta
        $userClass = app(\App\Services\Kmd\KelasAktifService::class)->forUser(Auth::user());
        $modulQuery = KmdModuls::query()->where('status', 'active')->orderBy('order');
        if ($userClass) {
            $query->where('class_id', $userClass->class_id)
                ->whereHas('modul', fn ($q) => $q->where('language_code', $userClass->language_code));
            $modulQuery->where('kom_class_id', $userClass->class_id)
                ->where('language_code', $userClass->language_code);
        }

        // Jika ada filter dari request, tambahkan kondisi
        if ($request->filled('rayon_id')) {
            $query->where('rayon_id', $request->rayon_id);
        }

        if ($request->filled('modul_id')) {
            $query->where('modul_id', $request->modul_id);
        }

        // kom_v02: titik tutorial (dari halaman belajar). 'akhir' = akhir modul, selain itu id video
        $titikInfo = null;
        if ($request->filled('modul_id') && $request->filled('titik')) {
            $videoId = $request->titik === 'akhir' ? null : $request->titik;
            $videoId ? $query->where('video_id', $videoId) : $query->whereNull('video_id');

            $modulTitik = KmdModuls::find($request->modul_id);
            $titikService = app(\App\Services\Kmd\TitikTutorialService::class);
            $status = $titikService->statusPeserta(Auth::id(), $request->modul_id, $videoId);
            $titikInfo = [
                'modul' => $modulTitik,
                'label' => \App\Services\Kmd\TitikTutorialService::label($videoId ? \App\Models\Kmd\KmdVideo::find($videoId) : null),
                'status' => $status['status'],
                'booking' => $status['booking'],
            ];
        }

        // Pagination, misal 9 per halaman
        $kelasTutorials = $query->orderBy('start_time')->paginate(9);

        // Ambil data rayon dan modul untuk filter dropdown
        $rayons = Cabang::all();
        $moduls = $modulQuery->get();

        return view('a_kom_digital_pusat.dashboard.kelasTutorial', compact('kelasTutorials', 'rayons', 'moduls', 'titikInfo'));
    }

    // 2,3. User memilih kelas, simpan ke KmdUser ClassTutorial dan update jml_ikut & sisa_quota
    // public function joinClass(Request $request)
    // {
    //     $request->validate([
    //         'class_tutorial_id' => ['required', new CustomDbRule('mysql2', 'kmd_class_tutorial', 'id', 'exists')],
    //     ]);

    //     $user = Auth::user();
    //     $classTutorial = KmdClassTutorial::findOrFail($request->class_tutorial_id);

    //     if ($classTutorial->sisa_quota <= 0) {
    //         return response()->json(['success' => false, 'message' => 'Kuota kelas sudah penuh.']);
    //     }

    //     $exists = KmdUserClassTutorial::where('user_id', $user->id)
    //         ->where('class_tutorial_id', $classTutorial->id)
    //         ->exists();

    //     if ($exists) {
    //         return response()->json(['success' => false, 'message' => 'Anda sudah terdaftar di kelas ini.', 'redirect_url' => route('kom.pertanyaan.form')]);
    //     }

    //     $userClassTutorial = KmdUserClassTutorial::create([
    //         'user_id' => $user->id,
    //         'class_tutorial_id' => $classTutorial->id,
    //         'modul_id' => $classTutorial->modul_id,
    //         'class_id' => $classTutorial->class_id,
    //         'rayon_id' => $classTutorial->rayon_id,
    //         'date_booking' => now(),
    //         'status' => 'booked',
    //     ]);

    //     $user->update([
    //         'cabang_id' => $classTutorial->rayon_id
    //     ]);
    //     // Update kuota
    //     $classTutorial->increment('jml_ikut');
    //     $classTutorial->sisa_quota = max(0, $classTutorial->quota - $classTutorial->jml_ikut);
    //     $classTutorial->save();

    //     // Simpan data join kelas ke session
    //     session([
    //         'join_kelas' => [
    //             'userclasstutorial_id' => $userClassTutorial->id,
    //             'kelas_tutorial_id' => $classTutorial->id,
    //             'class_id' => $classTutorial->class_id,
    //             'modul_id' => $classTutorial->modul_id,
    //             'rayon_id' => $classTutorial->rayon_id,
    //             'pengajar_id' => $classTutorial->pengajar_id ?? null,
    //         ]
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Berhasil mendaftar kelas tutorial.',
    //         'redirect_url' => route('kom.pertanyaan.form'),
    //     ]);
    // }


    // public function joinClass(Request $request)
    // {
    //     Log::info('joinClass called', ['request' => $request->all()]);

    //     $request->validate([
    //         'class_tutorial_id' => ['required', new CustomDbRule('mysql2', 'kmd_class_tutorial', 'id', 'exists')],
    //     ], [
    //         'class_tutorial_id.required' => __('Kolom Kelas Tutorial wajib diisi.'),
    //     ]);

    //     $user = Auth::user();

    //     $kelasTutorial = KmdClassTutorial::findOrFail($request->class_tutorial_id);

    //     Log::info('Data class tutorial ditemukan', ['kelasTutorial' => $kelasTutorial->toArray()]);

    //     if (empty($kelasTutorial->pengajar_id)) {
    //         Log::error('Pengajar ID kosong di kelas tutorial', ['class_tutorial_id' => $kelasTutorial->id]);
    //         return response()->json([
    //             'success' => false,
    //             'message' => __('Pengajar untuk kelas ini belum ditentukan. Silakan hubungi admin.'),
    //         ], 422);
    //     }

    //     // Pastikan pengajar_id benar dan bukan string kosong atau null
    //     Log::info('pengajar_id sebelum insert progress', ['pengajar_id' => $kelasTutorial->pengajar_id]);

    //     $existingProgress = KmdUserClassTutorial::where('user_id', $user->id)
    //         ->where('class_tutorial_id', $kelasTutorial->id)
    //         ->where('step', '!=', 'selesai')
    //         ->first();

    //     if ($existingProgress) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Anda sudah memilih kelas ini, silakan lanjut ke pertanyaan.',
    //             'redirect_url' => route('kom.kelas_tutorial.pertanyaan.form'),
    //         ]);
    //     }

    //     $progress = KmdUserClassTutorial::create([
    //         'id' => (string) Str::uuid(),
    //         'user_id' => $user->id,
    //         'class_tutorial_id' => $kelasTutorial->id,
    //         'class_id' => $kelasTutorial->class_id,
    //         'modul_id' => $kelasTutorial->modul_id,
    //         'rayon_id' => $kelasTutorial->rayon_id,
    //         'pengajar_id' => $kelasTutorial->pengajar_id,  // Hanya dari KmdClassTutorial
    //         'step' => 'pertanyaan',
    //         'status_progress' => 'pending',
    //         'status' => 'booked',
    //         'date_booking' => now(),
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

    //     Log::info('Progress berhasil dibuat', ['progress_id' => $progress->id]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => __('Berhasil ikut kelas, silakan lanjut ke pertanyaan.'),
    //         'redirect_url' => route('kom.kelas_tutorial.pertanyaan.form'),
    //         'data' => [
    //             'progress_id' => $progress->id,
    //             'class_tutorial_id' => $kelasTutorial->id,
    //             'pengajar_id' => $kelasTutorial->pengajar_id,
    //         ],
    //     ]);
    // }
    /**
     * kom_v02: satu peserta hanya boleh memproses 1 pendaftaran dalam satu waktu
     * (klik ganda / dua tab bersamaan tidak membuat id pendaftaran dobel).
     */
    public function joinClass(Request $request)
    {
        $kunci = null;
        try {
            $kunci = \Illuminate\Support\Facades\Cache::lock('kom-join-tutorial:' . Auth::id(), 15);
            if (! $kunci->block(5)) {
                $kunci = null;
                return response()->json([
                    'success' => false,
                    'message' => __('Pendaftaran Anda sedang diproses. Silakan tunggu sebentar.'),
                ], 429);
            }
        } catch (\Illuminate\Contracts\Cache\LockTimeoutException $e) {
            return response()->json([
                'success' => false,
                'message' => __('Pendaftaran Anda sedang diproses. Silakan tunggu sebentar.'),
            ], 429);
        } catch (\Throwable $e) {
            // driver cache tidak mendukung lock → tetap lanjut (perilaku lama)
            Log::warning('[JoinClass] Cache lock tidak tersedia: ' . $e->getMessage());
            $kunci = null;
        }

        try {
            return $this->prosesJoinClass($request);
        } finally {
            optional($kunci)->release();
        }
    }

    protected function prosesJoinClass(Request $request)
    {

        // $request->validate([
        //     'class_tutorial_id' => ['required', new CustomDbRule('mysql2', 'kmd_class_tutorial', 'id', 'exists')],
        // ], [
        //     'class_tutorial_id.required' => __('Kolom Kelas Tutorial wajib diisi.'),
        // ]);
        $request->validate([
            'class_tutorial_id' => 'required|exists:kmd_class_tutorial,id',
        ], [
            'class_tutorial_id.required' => __('Kolom Kelas Tutorial wajib diisi.'),
            'class_tutorial_id.exists' => __('Kelas Tutorial tidak ditemukan.'),
        ]);

        $user = Auth::user();

        // Ambil data kelas tutorial
        $kelasTutorial = KmdClassTutorial::findOrFail($request->class_tutorial_id);


        // Cek kuota terlebih dahulu
        if ($kelasTutorial->sisa_quota <= 0) {
            Log::warning('Kuota kelas sudah penuh, pendaftaran ditolak', ['class_tutorial_id' => $kelasTutorial->id]);
            return response()->json([
                'success' => false,
                'message' => __('Kuota kelas sudah penuh, tidak bisa mendaftar lagi.'),
            ], 422);
        }

        // Cek apakah pengajar sudah ada
        if (empty($kelasTutorial->pengajar_id)) {
            Log::error('Pengajar ID kosong di kelas tutorial', ['class_tutorial_id' => $kelasTutorial->id]);
            return response()->json([
                'success' => false,
                'message' => __('Pengajar untuk kelas ini belum ditentukan. Silakan hubungi admin.'),
            ], 422);
        }

        
        // kom_v02: aturan per TITIK tutorial (bukan per modul lagi) — 1 modul bisa punya
        // beberapa kelas tutorial (tengah modul + akhir modul).
        $titikService = app(\App\Services\Kmd\TitikTutorialService::class);
        $statusTitik = $titikService->statusPeserta($user->id, $kelasTutorial->modul_id, $kelasTutorial->video_id ?: null);

        switch ($statusTitik['status']) {
            case \App\Services\Kmd\TitikTutorialService::HADIR:
                return response()->json([
                    'success' => false,
                    'message' => __('Anda sudah hadir di kelas tutorial ini. Tidak perlu mendaftar lagi.'),
                ], 422);

            case \App\Services\Kmd\TitikTutorialService::PROSES:
                return response()->json([
                    'success' => false,
                    'message' => __('Anda sudah memilih jadwal untuk kelas tutorial ini. Silakan selesaikan langkah berikutnya.'),
                    'redirect_url' => route('kom.kelas_tutorial.pertanyaan.form'),
                ], 422);

            case \App\Services\Kmd\TitikTutorialService::TERDAFTAR:
                return response()->json([
                    'success' => false,
                    'message' => __('Anda sudah terdaftar di jadwal kelas tutorial ini. Silakan ikuti kelasnya sesuai jadwal.'),
                    'redirect_url' => route('kom.peserta.kelas_tutorial'),
                ], 422);

            case \App\Services\Kmd\TitikTutorialService::TIDAK_HADIR:
                // Tidak hadir → boleh pilih jadwal pengganti TANPA persembahan lagi
                return $this->jadwalPengganti($user, $kelasTutorial, $statusTitik['booking']);
        }

        // Buat progress baru dengan status pending (belum bayar)
        $progress = KmdUserClassTutorial::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'class_tutorial_id' => $kelasTutorial->id,
            'class_id' => $kelasTutorial->class_id,
            'modul_id' => $kelasTutorial->modul_id,
            'video_id' => $kelasTutorial->video_id ?: null, // kom_v02: titik tutorial
            'rayon_id' => $kelasTutorial->rayon_id,
            'pengajar_id' => $kelasTutorial->pengajar_id,
            'step' => 'pertanyaan',
            'status_progress' => 'pending', // belum bayar
            'status' => 'booked',
            'date_booking' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('Progress berhasil dibuat', ['progress_id' => $progress->id]);

        return response()->json([
            'success' => true,
            'message' => __('Berhasil ikut kelas, silakan lanjut ke pertanyaan.'),
            'redirect_url' => route('kom.kelas_tutorial.pertanyaan.form'),
            'data' => [
                'progress_id' => $progress->id,
                'class_tutorial_id' => $kelasTutorial->id,
                'pengajar_id' => $kelasTutorial->pengajar_id,
            ],
        ]);
    }





    // public function joinClass(Request $request, KelasTutorService $kelasTutorService)
    // {
    //     $request->validate([
    //         'class_tutorial_id' => ['required', new CustomDbRule('mysql2', 'kmd_class_tutorial', 'id', 'exists')],
    //     ]);

    //     $user = Auth::user();
    //     $classTutorial = KmdClassTutorial::findOrFail($request->class_tutorial_id);

    //     if ($classTutorial->sisa_quota <= 0) {
    //         return response()->json(['success' => false, 'message' => 'Kuota kelas sudah penuh.']);
    //     }

    //     $exists = KmdUserClassTutorial::where('user_id', $user->id)
    //         ->where('class_tutorial_id', $classTutorial->id)
    //         ->exists();

    //     if ($exists) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Anda sudah terdaftar di kelas ini.',
    //             'redirect_url' => route('kom.pertanyaan.form')
    //         ]);
    //     }


    //     $userClassTutorial = KmdUserClassTutorial::create([
    //         'user_id' => $user->id,
    //         'class_tutorial_id' => $classTutorial->id,
    //         'modul_id' => $classTutorial->modul_id,
    //         'class_id' => $classTutorial->class_id,
    //         'rayon_id' => $classTutorial->rayon_id,
    //         'date_booking' => now(),
    //         'status' => 'booked',
    //     ]);

    //     $user->update([
    //         'cabang_id' => $classTutorial->rayon_id
    //     ]);


    //     $classTutorial->increment('jml_ikut');
    //     $classTutorial->sisa_quota = max(0, $classTutorial->quota - $classTutorial->jml_ikut);
    //     $classTutorial->save();

    // Registrasi peserta ke Zoom meeting, jika ada zoom_meeting_id
    // try {
    //     if ($classTutorial->zoom_meeting_id) {
    //         $zoomName = $user->name ?? 'Peserta';

    //         \Log::info('Memanggil Zoom registrant service', [
    //             'meeting_id' => $classTutorial->zoom_meeting_id,
    //             'email' => $user->email,
    //             'name' => $zoomName
    //         ]);

    //         $registrant = $kelasTutorService->registerZoomRegistrant(
    //             $classTutorial->zoom_meeting_id,
    //             $user->email,
    //             $zoomName
    //         );

    //         \Log::info('Isi response dari Zoom:', $registrant);

    //         $userClassTutorial->zoom_join_url = $registrant['join_url'] ?? null;
    //         $userClassTutorial->zoom_registrant_id = $registrant['registrant_id'] ?? null;
    //         $userClassTutorial->save();
    //     }
    // } catch (\Exception $e) {
    //     \Log::error('Error registrasi peserta ke Zoom:', [
    //         'message' => $e->getMessage(),
    //         'trace' => $e->getTraceAsString()
    //     ]);
    // }


    // Simpan data join kelas ke session
    //     session([
    //         'join_kelas' => [
    //             'userclasstutorial_id' => $userClassTutorial->id,
    //             'kelas_tutorial_id' => $classTutorial->id,
    //             'class_id' => $classTutorial->class_id,
    //             'modul_id' => $classTutorial->modul_id,
    //             'rayon_id' => $classTutorial->rayon_id,
    //             'pengajar_id' => $classTutorial->pengajar_id ?? null,

    //         ]
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Berhasil mendaftar kelas tutorial.',
    //         'redirect_url' => route('kom.pertanyaan.form'),
    //     ]);
    // }

    // public function handlePaidStatus($userClassTutorialId)
    // {
    //     $userClassTutorial = KmdUserClassTutorial::findOrFail($userClassTutorialId);

    //     // ✅ Update status progress dan step jika belum selesai
    //     if ($userClassTutorial->status_progress !== 'paid') {
    //         $userClassTutorial->step = 'selesai';
    //         $userClassTutorial->status_progress = 'paid';
    //         $userClassTutorial->save();
    //     }

    //     // ✅ Update jumlah ikut & sisa kuota
    //     $classTutorial = $userClassTutorial->classTutorial;
    //     if ($classTutorial) {
    //         $classTutorial->jml_ikut = $classTutorial->jml_ikut + 1;
    //         $classTutorial->sisa_quota = max(0, $classTutorial->sisa_quota - 1); // Hindari minus
    //         $classTutorial->save();
    //     }

    //     $user = $userClassTutorial->user;

    //     // ✅ Cek kalau belum diregistrasi ke Zoom
    //     if ($userClassTutorial->zoom_join_url || $userClassTutorial->zoom_registrant_id) {
    //         return; // Sudah pernah registrasi
    //     }

    //     if ($classTutorial && $classTutorial->zoom_meeting_id) {
    //         try {
    //             $zoomName = $user->name ?? 'Peserta';

    //             $registrant = app(KelasTutorService::class)->registerZoomRegistrant(
    //                 $classTutorial->zoom_meeting_id,
    //                 $user->email,
    //                 $zoomName
    //             );

    //             $userClassTutorial->zoom_join_url = $registrant['join_url'] ?? null;
    //             $userClassTutorial->zoom_registrant_id = $registrant['registrant_id'] ?? null;
    //             $userClassTutorial->save();

    //             // Kirim email ke user
    //             Mail::to($user->email)->send(new ZoomLinkEmail($userClassTutorial));

    //         } catch (\Exception $e) {
    //             \Log::error('Gagal mendaftarkan ke Zoom setelah pembayaran', [
    //                 'message' => $e->getMessage()
    //             ]);
    //         }
    //     }
    // }
    // public function handlePaidStatus($userClassTutorialId)
    // {
    //     try {
    //         $userClassTutorial = KmdUserClassTutorial::findOrFail($userClassTutorialId);
    //         $user = $userClassTutorial->user;
    //         $classTutorial = $userClassTutorial->classTutorial;

    //         \Log::info('[HANDLE PAID] Proses dimulai', [
    //             'user_class_tutorial_id' => $userClassTutorial->id,
    //             'user_id' => $user->id ?? null,
    //             'class_tutorial_id' => $classTutorial->id ?? null,
    //         ]);

    //         // ✅ Update status progress dan step jika belum selesai
    //         if ($userClassTutorial->status_progress !== 'paid') {
    //             $userClassTutorial->step = 'selesai';
    //             $userClassTutorial->status_progress = 'paid';
    //             $userClassTutorial->save();

    //             \Log::info('[HANDLE PAID] Status progress diperbarui ke "paid"', [
    //                 'progress_id' => $userClassTutorial->id
    //             ]);
    //         }

    //         // ✅ Update jumlah ikut & sisa kuota
    //         if ($classTutorial) {
    //             $classTutorial->jml_ikut = $classTutorial->jml_ikut + 1;
    //             $classTutorial->sisa_quota = max(0, $classTutorial->sisa_quota - 1); // Hindari minus
    //             $classTutorial->save();

    //             \Log::info('[HANDLE PAID] Kuota diperbarui', [
    //                 'class_tutorial_id' => $classTutorial->id,
    //                 'jml_ikut' => $classTutorial->jml_ikut,
    //                 'sisa_quota' => $classTutorial->sisa_quota
    //             ]);
    //         }

    //         // ✅ Cek kalau belum diregistrasi ke Zoom
    //         if ($userClassTutorial->zoom_join_url || $userClassTutorial->zoom_registrant_id) {
    //             \Log::info('[HANDLE PAID] User sudah terdaftar Zoom, tidak melakukan registrasi ulang', [
    //                 'user_class_tutorial_id' => $userClassTutorial->id
    //             ]);
    //             return;
    //         }

    //         // ✅ Registrasi Zoom jika meeting ID tersedia
    //         if ($classTutorial && $classTutorial->zoom_meeting_id) {
    //             try {
    //                 $zoomName = $user->name ?? 'Peserta';

    //                 $registrant = app(KelasTutorService::class)->registerZoomRegistrant(
    //                     $classTutorial->zoom_meeting_id,
    //                     $user->email,
    //                     $zoomName
    //                 );

    //                 $userClassTutorial->zoom_join_url = $registrant['join_url'] ?? null;
    //                 $userClassTutorial->zoom_registrant_id = $registrant['registrant_id'] ?? null;
    //                 $userClassTutorial->save();

    //                 \Log::info('[HANDLE PAID] Registrasi Zoom berhasil', [
    //                     'user_id' => $user->id,
    //                     'email' => $user->email,
    //                     'zoom_join_url' => $userClassTutorial->zoom_join_url,
    //                     'zoom_registrant_id' => $userClassTutorial->zoom_registrant_id
    //                 ]);

    //                 // Kirim email ke user
    //                 Mail::to($user->email)->send(new ZoomLinkEmail($userClassTutorial));

    //                 \Log::info('[HANDLE PAID] Email Zoom terkirim', [
    //                     'user_id' => $user->id,
    //                     'email' => $user->email
    //                 ]);

    //             } catch (\Exception $e) {
    //                 \Log::error('[HANDLE PAID] Gagal mendaftarkan ke Zoom atau kirim email', [
    //                     'error' => $e->getMessage(),
    //                     'user_id' => $user->id,
    //                     'email' => $user->email
    //                 ]);
    //             }
    //         }

    //         \Log::info('[HANDLE PAID] Proses selesai untuk user', [
    //             'user_id' => $user->id,
    //             'user_class_tutorial_id' => $userClassTutorial->id
    //         ]);

    //     } catch (\Exception $e) {
    //         \Log::error('[HANDLE PAID] Error saat menjalankan handlePaidStatus', [
    //             'message' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //     }
    // }


    /**
     * kom_v02: peserta TIDAK HADIR memilih jadwal lain untuk titik yang sama.
     * Tidak perlu persembahan lagi (persembahan sebelumnya tetap berlaku) dan
     * pertanyaan sebelumnya ikut dikirim ke pengajar jadwal baru.
     */
    protected function jadwalPengganti($user, KmdClassTutorial $kelasTutorial, KmdUserClassTutorial $bookingLama)
    {
        $booking = \Illuminate\Support\Facades\DB::connection('mysql')->transaction(function () use ($user, $kelasTutorial, $bookingLama) {
            $kelas = KmdClassTutorial::lockForUpdate()->find($kelasTutorial->id);
            if (! $kelas || $kelas->sisa_quota <= 0) {
                return null;
            }

            $booking = KmdUserClassTutorial::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'class_tutorial_id' => $kelas->id,
                'class_id' => $kelas->class_id,
                'modul_id' => $kelas->modul_id,
                'video_id' => $kelas->video_id ?: null,
                'rayon_id' => $kelas->rayon_id,
                'pengajar_id' => $kelas->pengajar_id,
                'step' => 'donasi',
                'status_progress' => 'pending',
                'status' => 'booked',
                'is_rebook' => true,
                'rebook_from_id' => $bookingLama->id,
                'date_booking' => now(),
            ]);

            // Pertanyaan sebelumnya dikirim juga ke pengajar jadwal baru
            $pertanyaanLama = \App\Models\Kmd\KmdPertanyaanUser::where('user_id', $user->id)
                ->where('kelas_tutorial_id', $bookingLama->class_tutorial_id)
                ->get();
            foreach ($pertanyaanLama as $p) {
                \App\Models\Kmd\KmdPertanyaanUser::create([
                    'user_id' => $user->id,
                    'rayon_id' => $kelas->rayon_id,
                    'class_id' => $kelas->class_id,
                    'modul_id' => $kelas->modul_id,
                    'video_id' => $kelas->video_id ?: null,
                    'kelas_tutorial_id' => $kelas->id,
                    'pengajar_id' => $kelas->pengajar_id,
                    'pertanyaan' => $p->pertanyaan,
                    'status' => 'active',
                    'tgl_submit' => now(),
                ]);
            }

            return $booking;
        });

        if (! $booking) {
            return response()->json([
                'success' => false,
                'message' => __('Kuota kelas sudah penuh, tidak bisa mendaftar lagi.'),
            ], 422);
        }

        // Tandai lunas (tanpa pembayaran baru), kurangi kuota & daftarkan ke Zoom
        $this->handlePaidStatus($booking->id);

        Log::info('Jadwal pengganti (tidak hadir) dibuat', [
            'user_id' => $user->id, 'booking_baru' => $booking->id, 'booking_lama' => $bookingLama->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Jadwal pengganti berhasil dipilih. Anda tidak perlu memberikan persembahan lagi. Link Zoom ada di menu Kelas Tutorial Saya.'),
            'redirect_url' => route('kom.peserta.kelas_tutorial'),
            'tanpa_pertanyaan' => true,
        ]);
    }

    /**
     * Tandai pendaftaran kelas tutorial LUNAS.
     *
     * kom_v02 (performa):
     *  - status lunas & kuota diproses LANGSUNG dalam satu transaksi berkunci (lockForUpdate),
     *    supaya callback ganda / dua peserta bersamaan tidak membuat kuota minus atau dobel;
     *  - pendaftaran Zoom + email link Zoom dikerjakan di QUEUE (ProsesTutorialLunasJob),
     *    supaya callback Midtrans tidak ikut lambat saat Zoom / server email lambat.
     *
     * @param  string|null  $donasiId  diisi dari callback persembahan → peserta dikirimi email terima kasih + link Zoom
     * @return bool  true jika baru saja diproses, false jika sudah lunas sebelumnya
     */
    public function handlePaidStatus($userClassTutorialId, ?string $donasiId = null): bool
    {
        $diproses = \Illuminate\Support\Facades\DB::connection('mysql')->transaction(function () use ($userClassTutorialId) {
            $userClassTutorial = KmdUserClassTutorial::lockForUpdate()->findOrFail($userClassTutorialId);

            // 🛑 Skip jika status sudah paid (untuk mencegah callback ganda)
            if ($userClassTutorial->status_progress === 'paid') {
                return false;
            }

            $userClassTutorial->step = 'selesai';
            $userClassTutorial->status_progress = 'paid';
            $userClassTutorial->save();

            $classTutorial = KmdClassTutorial::lockForUpdate()->find($userClassTutorial->class_tutorial_id);
            if ($classTutorial) {
                $classTutorial->jml_ikut = $classTutorial->jml_ikut + 1;
                $classTutorial->sisa_quota = max(0, $classTutorial->sisa_quota - 1); // Hindari minus
                $classTutorial->save();
            }

            return true;
        });

        if ($diproses) {
            // dijalankan worker queue (php artisan queue:work); jika QUEUE_CONNECTION=sync, langsung jalan
            \App\Jobs\Kmd\ProsesTutorialLunasJob::dispatch((string) $userClassTutorialId, $donasiId);
        }

        return $diproses;
    }


    // public function handlePaidStatus($userClassTutorialId)
    // {
    //     $userClassTutorial = KmdUserClassTutorial::findOrFail($userClassTutorialId);

    //     // Cek kalau belum diregistrasi ke Zoom
    //     if ($userClassTutorial->zoom_join_url || $userClassTutorial->zoom_registrant_id) {
    //         return; // Sudah pernah registrasi
    //     }

    //     $classTutorial = $userClassTutorial->classTutorial;
    //     $user = $userClassTutorial->user;

    //     if ($classTutorial->zoom_meeting_id) {
    //         try {
    //             $zoomName = $user->name ?? 'Peserta';

    //             $registrant = app(KelasTutorService::class)->registerZoomRegistrant(
    //                 $classTutorial->zoom_meeting_id,
    //                 $user->email,
    //                 $zoomName
    //             );

    //             $userClassTutorial->zoom_join_url = $registrant['join_url'] ?? null;
    //             $userClassTutorial->zoom_registrant_id = $registrant['registrant_id'] ?? null;
    //             $userClassTutorial->save();

    //             // Kirim email ke user
    //             Mail::to($user->email)->send(new ZoomLinkEmail($userClassTutorial));

    //         } catch (\Exception $e) {
    //             \Log::error('Gagal mendaftarkan ke Zoom setelah pembayaran', [
    //                 'message' => $e->getMessage()
    //             ]);
    //         }
    //     }
    // }


    public function joinClassByUserAndClassTutorial($user, $classTutorial)
    {
        if ($classTutorial->sisa_quota <= 0) {
            Log::warning("joinClass: Kuota kelas sudah penuh untuk kelas_tutorial_id={$classTutorial->id}");
            return false;
        }

        $exists = KmdUserClassTutorial::where('user_id', $user->id)
            ->where('class_tutorial_id', $classTutorial->id)
            ->exists();

        if ($exists) {
            return true; // sudah terdaftar, anggap sukses
        }

        $userClassTutorial = KmdUserClassTutorial::create([
            'user_id' => $user->id,
            'class_tutorial_id' => $classTutorial->id,
            'modul_id' => $classTutorial->modul_id,
            'class_id' => $classTutorial->class_id,
            'rayon_id' => $classTutorial->rayon_id,
            'date_booking' => now(),
            'status' => 'booked',
        ]);

        $user->update([
            'cabang_id' => $classTutorial->rayon_id
        ]);

        // Update kuota
        $classTutorial->increment('jml_ikut');
        $classTutorial->sisa_quota = max(0, $classTutorial->quota - $classTutorial->jml_ikut);
        $classTutorial->save();

        return true;
    }

}
