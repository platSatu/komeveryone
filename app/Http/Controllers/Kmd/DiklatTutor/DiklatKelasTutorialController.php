<?php

namespace App\Http\Controllers\Kmd\DiklatTutor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kmd\KmdClassTutorial;
use App\Models\Kmd\KmdModuls;
use App\Models\Kmd\Cabang;
use App\Models\Kmd\KomUser;
use App\Models\Kmd\KmdUserVideoProgress;
use App\Models\Kmd\KmdProgressModul;
use App\Models\Kmd\UserClassProgress;
use App\Models\Kmd\KmdUserClassTutorial;
use Illuminate\Support\Facades\Auth;
use App\Rules\CustomDbRule;
use App\Services\AdminCabang\KelasTutorService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;



class DiklatKelasTutorialController extends Controller
{

    public function index(Request $request)
    {
        $userId = Auth::id(); // ambil user yang login

        // Cek apakah user ini punya progress modul
        $hasProgressData = KmdProgressModul::where('user_id', $userId)->exists();

        $selectedCategory = $request->filled('category_diklat') ? $request->category_diklat : null;

        if (!$hasProgressData) {
            // Paginator kosong agar Blade aman
            $kelasTutorials = new LengthAwarePaginator([], 0, 9, 1);
            $rayons = collect();
            $moduls = collect();

            return view('a_kom_digital_pusat.diklat_tutor.kelasTutorial', compact(
                'kelasTutorials',
                'rayons',
                'moduls',
                'selectedCategory'
            ))->with('error', 'Anda belum memiliki progress modul, silakan lengkapi terlebih dahulu.');
        }

        // Query builder, jangan pakai get() dulu
        $query = KmdClassTutorial::where('status_diklat', 'active')
            ->whereIn('category_diklat', ['Praktikal', 'Doktrinal']);

        if ($selectedCategory) {
            $query->whereRaw('LOWER(TRIM(category_diklat)) = ?', [strtolower(trim($selectedCategory))]);
        }

        $kelasTutorials = $query->paginate(9)->appends($request->query());
        $rayons = Cabang::all();
        $moduls = KmdModuls::all();

        return view('a_kom_digital_pusat.diklat_tutor.kelasTutorial', compact(
            'kelasTutorials',
            'rayons',
            'moduls',
            'selectedCategory'
        ));
    }


    // public function index(Request $request)
    // {
    //     $userId = Auth::id(); 


    //     $hasProgressData = KmdProgressModul::where('user_id', $userId)->exists();

    //     $selectedCategory = $request->filled('category_diklat') ? $request->category_diklat : null;

    //     if (!$hasProgressData) {

    //         $kelasTutorials = new LengthAwarePaginator([], 0, 9, 1);
    //         $rayons = collect();
    //         $moduls = collect();

    //         return view('a_kom_digital_pusat.diklat_tutor.kelasTutorial', compact(
    //             'kelasTutorials',
    //             'rayons',
    //             'moduls',
    //             'selectedCategory'
    //         ))->with('error', 'Anda belum memiliki progress modul, silakan lengkapi terlebih dahulu.');
    //     }

    //     Query normal jika user punya progress modul
    //     $query = KmdClassTutorial::where('status_diklat', 'active');
    //     $query = KmdClassTutorial::where('status_diklat', 'active')
    //         ->whereNotNull('category_diklat')
    //         ->where('category_diklat', '!=', '');
    //     $query = KmdClassTutorial::where('status_diklat', 'active')
    //         ->whereIn('category_diklat', ['Praktikal', 'Doktrinal'])
    //         ->get();



    //     if ($selectedCategory) {
    //         $query->whereNotNull('category_diklat')
    //             ->whereRaw('LOWER(TRIM(category_diklat)) = ?', [strtolower(trim($selectedCategory))]);
    //     }

    //     $kelasTutorials = $query->paginate(9)->appends($request->query());
    //     $rayons = Cabang::all();
    //     $moduls = KmdModuls::all();

    //     return view('a_kom_digital_pusat.diklat_tutor.kelasTutorial', compact(
    //         'kelasTutorials',
    //         'rayons',
    //         'moduls',
    //         'selectedCategory'
    //     ));
    // }



    public function joinClassDiklat(Request $request)
    {
        Log::info('joinClass called', ['request' => $request->all()]);

        $request->validate([
            'class_tutorial_id' => ['required', new CustomDbRule('mysql2', 'kmd_class_tutorial', 'id', 'exists')],
        ], [
            'class_tutorial_id.required' => 'Kolom Kelas Tutorial wajib diisi.',
        ]);

        $user = Auth::user();

        $komUser = KomUser::find($user->id);

        if (!$komUser || $komUser->jenis !== 'K1') {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum memenuhi syarat untuk ikut diklat. Jenis sim harus K1.',
            ], 422);
        }

        //✅ Cek apakah user sudah menyelesaikan semua 27 video
        $videoCount = KmdUserVideoProgress::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        if ($videoCount < 27) {
            return response()->json([
                'success' => false,
                'message' => "Anda baru menyelesaikan {$videoCount} video. Harus menyelesaikan 27 video sebelum ikut diklat.",
            ], 422);
        }

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

        // 🚫 Cek apakah user sudah pernah ikut kelas yang sama
        $existingSameClass = KmdUserClassTutorial::where('user_id', $user->id)
            ->where('class_tutorial_id', $kelasTutorial->id)
            ->first();

        if ($existingSameClass) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah terdaftar di kelas ini. Silakan lanjutkan progres yang sudah ada.',
            ], 422);
        }

        // 🚫 Cek apakah user sudah punya category_diklat lain sebelumnya
        // $existingDifferentCategory = KmdUserClassTutorial::where('user_id', $user->id)
        //     ->where('category_diklat', '!=', $kelasTutorial->category_diklat)
        //     ->first();
        $existingDifferentCategory = KmdUserClassTutorial::where('user_id', $user->id)
            ->where('category_diklat', '!=', $kelasTutorial->category_diklat)
            ->where('step', '!=', 'selesai')
            ->first();

        if ($existingDifferentCategory) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah terdaftar di kategori diklat lain. Anda hanya boleh mengikuti kelas dalam kategori yang sama.',
            ], 422);
        }


        // Cek apakah user sudah pernah ikut kelas ini (belum selesai)
        $existingProgress = KmdUserClassTutorial::where('user_id', $user->id)
            ->where('class_tutorial_id', $kelasTutorial->id)
            ->where('step', '!=', 'selesai')
            ->first();

        if ($existingProgress) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memilih kelas ini, silakan lanjut ke pertanyaan.',
                'redirect_url' => route('kom.diklat_tutor.pertanyaan.form'),
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
            'category_diklat' => $kelasTutorial->category_diklat,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('Progress berhasil dibuat', ['progress_id' => $progress->id]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil ikut kelas, silakan lanjut ke pertanyaan.',
            'redirect_url' => route('kom.diklat_tutor.pertanyaan.form'),
            'data' => [
                'progress_id' => $progress->id,
                'class_tutorial_id' => $kelasTutorial->id,
                'pengajar_id' => $kelasTutorial->pengajar_id,
            ],
        ]);
    }


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
