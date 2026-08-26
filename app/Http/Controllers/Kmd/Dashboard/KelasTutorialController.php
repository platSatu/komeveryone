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
        $query = KmdClassTutorial::where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('category_diklat')
                    ->orWhere('category_diklat', '');
            });



        // Jika ada filter dari request, tambahkan kondisi
        if ($request->filled('rayon_id')) {
            $query->where('rayon_id', $request->rayon_id);
        }

        if ($request->filled('modul_id')) {
            $query->where('modul_id', $request->modul_id);
        }

        // Pagination, misal 9 per halaman
        $kelasTutorials = $query->paginate(9);

        // Ambil data rayon dan modul untuk filter dropdown
        $rayons = Cabang::all();
        $moduls = KmdModuls::all();

        return view('a_kom_digital_pusat.dashboard.kelasTutorial', compact('kelasTutorials', 'rayons', 'moduls'));
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
    //         'class_tutorial_id.required' => 'Kolom Kelas Tutorial wajib diisi.',
    //     ]);

    //     $user = Auth::user();

    //     $kelasTutorial = KmdClassTutorial::findOrFail($request->class_tutorial_id);

    //     Log::info('Data class tutorial ditemukan', ['kelasTutorial' => $kelasTutorial->toArray()]);

    //     if (empty($kelasTutorial->pengajar_id)) {
    //         Log::error('Pengajar ID kosong di kelas tutorial', ['class_tutorial_id' => $kelasTutorial->id]);
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Pengajar untuk kelas ini belum ditentukan. Silakan hubungi admin.',
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
    //         'message' => 'Berhasil ikut kelas, silakan lanjut ke pertanyaan.',
    //         'redirect_url' => route('kom.kelas_tutorial.pertanyaan.form'),
    //         'data' => [
    //             'progress_id' => $progress->id,
    //             'class_tutorial_id' => $kelasTutorial->id,
    //             'pengajar_id' => $kelasTutorial->pengajar_id,
    //         ],
    //     ]);
    // }
    public function joinClass(Request $request)
    {
        Log::info('joinClass called', ['request' => $request->all()]);

        // $request->validate([
        //     'class_tutorial_id' => ['required', new CustomDbRule('mysql2', 'kmd_class_tutorial', 'id', 'exists')],
        // ], [
        //     'class_tutorial_id.required' => 'Kolom Kelas Tutorial wajib diisi.',
        // ]);
        $request->validate([
            'class_tutorial_id' => 'required|exists:kmd_class_tutorial,id',
        ], [
            'class_tutorial_id.required' => 'Kolom Kelas Tutorial wajib diisi.',
            'class_tutorial_id.exists' => 'Kelas Tutorial tidak ditemukan.',
        ]);

        $user = Auth::user();

        // Ambil data kelas tutorial
        $kelasTutorial = KmdClassTutorial::findOrFail($request->class_tutorial_id);

        Log::info('Data class tutorial ditemukan', ['kelasTutorial' => $kelasTutorial->toArray()]);

        // Cek kuota terlebih dahulu
        if ($kelasTutorial->sisa_quota <= 0) {
            Log::warning('Kuota kelas sudah penuh, pendaftaran ditolak', ['class_tutorial_id' => $kelasTutorial->id]);
            return response()->json([
                'success' => false,
                'message' => 'Kuota kelas sudah penuh, tidak bisa mendaftar lagi.',
            ], 422);
        }

        // Cek apakah pengajar sudah ada
        if (empty($kelasTutorial->pengajar_id)) {
            Log::error('Pengajar ID kosong di kelas tutorial', ['class_tutorial_id' => $kelasTutorial->id]);
            return response()->json([
                'success' => false,
                'message' => 'Pengajar untuk kelas ini belum ditentukan. Silakan hubungi admin.',
            ], 422);
        }

        Log::info('pengajar_id sebelum insert progress', ['pengajar_id' => $kelasTutorial->pengajar_id]);
        
        $existingSameModul = KmdUserClassTutorial::where('user_id', $user->id)
            ->where('modul_id', $kelasTutorial->modul_id)
            ->first();

        if ($existingSameModul) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah terdaftar di modul ini sebelumnya dan tidak dapat mendaftar lagi.',
                'redirect_url' => route('kom.kelas_tutorial.pertanyaan.form'),
            ], 422);
        }

        // Cek apakah user sudah ikut kelas ini yang belum selesai (step != selesai)
        $existingProgress = KmdUserClassTutorial::where('user_id', $user->id)
            ->where('class_tutorial_id', $kelasTutorial->id)
            ->where('step', '!=', 'selesai')
            ->first();

        // Cek apakah user sudah pernah ikut kelas ini (belum selesai)
        $existingProgress = KmdUserClassTutorial::where('user_id', $user->id)
            ->where('class_tutorial_id', $kelasTutorial->id)
            ->where('step', '!=', 'selesai')
            ->first();

        if ($existingProgress) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memilih kelas ini, silakan lanjut ke pertanyaan.',
                'redirect_url' => route('kom.kelas_tutorial.pertanyaan.form'),
            ]);
        }

        // Buat progress baru dengan status pending (belum bayar)
        $progress = KmdUserClassTutorial::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'class_tutorial_id' => $kelasTutorial->id,
            'class_id' => $kelasTutorial->class_id,
            'modul_id' => $kelasTutorial->modul_id,
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
            'message' => 'Berhasil ikut kelas, silakan lanjut ke pertanyaan.',
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


    public function handlePaidStatus($userClassTutorialId)
    {
        $userClassTutorial = KmdUserClassTutorial::findOrFail($userClassTutorialId);

        // 🛑 Skip jika status sudah paid (untuk mencegah callback ganda)
        if ($userClassTutorial->status_progress === 'paid') {
            \Log::info("[handlePaidStatus] Status sudah 'paid', tidak diproses ulang. ID: {$userClassTutorial->id}");
            return;
        }

        // ✅ Update status progress dan step jika belum selesai
        $userClassTutorial->step = 'selesai';
        $userClassTutorial->status_progress = 'paid';
        $userClassTutorial->save();

        // ✅ Update jumlah ikut & sisa kuota
        $classTutorial = $userClassTutorial->classTutorial;
        if ($classTutorial) {
            $classTutorial->jml_ikut = $classTutorial->jml_ikut + 1;
            $classTutorial->sisa_quota = max(0, $classTutorial->sisa_quota - 1); // Hindari minus
            $classTutorial->save();
        }

        $user = $userClassTutorial->user;

        // ✅ Cek kalau belum diregistrasi ke Zoom
        if ($userClassTutorial->zoom_join_url || $userClassTutorial->zoom_registrant_id) {
            \Log::info("[handlePaidStatus] Sudah terdaftar Zoom. ID: {$userClassTutorial->id}");
            return;
        }

        if ($classTutorial && $classTutorial->zoom_meeting_id) {
            try {
                $zoomName = $user->name ?? 'Peserta';

                $registrant = app(KelasTutorService::class)->registerZoomRegistrant(
                    $classTutorial->zoom_meeting_id,
                    $user->email,
                    $zoomName
                );

                $userClassTutorial->zoom_join_url = $registrant['join_url'] ?? null;
                $userClassTutorial->zoom_registrant_id = $registrant['registrant_id'] ?? null;
                $userClassTutorial->save();

                // Kirim email ke user
                Mail::to($user->email)->send(new ZoomLinkEmail($userClassTutorial));
            } catch (\Exception $e) {
                \Log::error('Gagal mendaftarkan ke Zoom setelah pembayaran', [
                    'message' => $e->getMessage()
                ]);
            }
        }
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
            Log::info("joinClass: User {$user->id} sudah terdaftar di kelas {$classTutorial->id}");
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
