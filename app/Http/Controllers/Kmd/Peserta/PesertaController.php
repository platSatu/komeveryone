<?php

namespace App\Http\Controllers\Kmd\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Peserta\PesertaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Kmd\KmdProgressModul;
use App\Models\Kmd\KmdUserVideoProgress;
use App\Models\Kmd\KmdUserTutorialStatus;
use App\Models\Kmd\KmdUserClassTutorial;
use App\Models\Kmd\KmdClassTutorial;
use App\Models\Kmd\KmdCertificates;
use App\Models\Kmd\KmdClass;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Crypt;
use App\Models\KmdUserReview;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;





class PesertaController extends Controller
{
    protected $pesertaService;

    public function __construct(PesertaService $pesertaService)
    {
        $this->pesertaService = $pesertaService;

        // Pastikan user sudah login untuk semua method

    }

    public function kelas_tutorial()
    {
        try {
            $data = $this->pesertaService->getUserClassTutorial();

            Log::info('User accessed kelas tutorial', [
                'user_id' => Auth::id(),
                'total_kelas' => $data->count()
            ]);

            return view('a_kom_digital_pusat.peserta.kelas_tutorial', compact('data'));
        } catch (\Exception $e) {
            Log::error('Error in kelas_tutorial', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data kelas tutorial.');
        }
    }

    public function join($zoomMeetingId)
    {
        $meeting = KmdClassTutorial::where('zoom_meeting_id', $zoomMeetingId)->firstOrFail();

        return view('a_kom_digital_pusat.peserta.join', [
            'meetingId' => $meeting->zoom_meeting_id,
            'passcode' => $meeting->passcode ?? '',
            'topic' => $meeting->topic,
        ]);
    }



    public function generateSignature(Request $request)
    {
        $meetingNumber = $request->input('meeting_number');
        $role = $request->input('role'); // 0 = peserta, 1 = host

        $sdkKey = env('ZOOM_SDK_KEY');
        $sdkSecret = env('ZOOM_SDK_SECRET');

        $time = round(microtime(true) * 1000) - 30000;

        $payload = [
            'sdkKey' => $sdkKey,
            'mn' => $meetingNumber,
            'role' => $role,
            'iat' => $time,
            'exp' => $time + 2 * 60 * 1000,
            'appKey' => $sdkKey,
            'tokenExp' => $time + 2 * 60 * 1000
        ];

        $signature = rtrim(strtr(base64_encode(JWT::encode($payload, $sdkSecret, 'HS256')), '+/', '-_'), '=');

        return response()->json(['signature' => $signature]);
    }


    public function pertanyaanSaya(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $data = $this->pesertaService->getPertanyaanUser($perPage);

            Log::info('User accessed pertanyaan', [
                'user_id' => Auth::id(),
                'total_pertanyaan' => $data->total()
            ]);

            return view('a_kom_digital_pusat.peserta.pertanyaan', compact('data'));
        } catch (\Exception $e) {
            Log::error('Error in pertanyaanSaya', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data pertanyaan.');
        }
    }


    /**
 * Show edit form for pertanyaan
 */
public function editPertanyaanSaya($id)
{
    try {
        $pertanyaan = $this->pesertaService->getPertanyaanById($id);

        // Check if question exists
        if (!$pertanyaan) {
            return redirect()->back()->with('error', 'Pertanyaan tidak ditemukan.');
        }

        // Check if user owns this question
        if ($pertanyaan->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit pertanyaan ini.');
        }

        // Check if already edited (max 1x edit)
        if ($pertanyaan->edit_count >= 1) {
            return redirect()->back()->with('error', 'Pertanyaan sudah tidak dapat diedit lagi. Maksimal edit adalah 1x.');
        }

        Log::info('User accessed edit pertanyaan', [
            'user_id' => Auth::id(),
            'pertanyaan_id' => $id
        ]);

        return view('a_kom_digital_pusat.peserta.edit_pertanyaan', compact('pertanyaan'));
    } catch (\Exception $e) {
        Log::error('Error in editPertanyaanSaya', [
            'user_id' => Auth::id(),
            'pertanyaan_id' => $id,
            'error' => $e->getMessage()
        ]);

        return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data pertanyaan.');
    }
}

/**
 * Update pertanyaan
 */
public function updatePertanyaanSaya(Request $request, $id)
    {
        try {
            Log::info('=== START updatePertanyaanSaya ===', [
                'user_id' => Auth::id(),
                'pertanyaan_id' => $id
            ]);

            // Validasi input
            $validatedData = $request->validate([
                'pertanyaan' => 'required|string|max:1000|min:5',
            ], [
                'pertanyaan.required' => 'Pertanyaan harus diisi.',
                'pertanyaan.min' => 'Pertanyaan minimal 5 karakter.',
                'pertanyaan.max' => 'Pertanyaan maksimal 1000 karakter.',
            ]);

            // Ambil data pertanyaan
            $pertanyaan = $this->pesertaService->getPertanyaanById($id);

            if (!$pertanyaan) {
                Log::warning('Pertanyaan not found', ['pertanyaan_id' => $id]);
                return redirect()->back()->with('error', 'Pertanyaan tidak ditemukan.');
            }

            if ($pertanyaan->user_id !== Auth::id()) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit pertanyaan ini.');
            }

            if ($pertanyaan->edit_count >= 1) {
                return redirect()->back()->with('error', 'Pertanyaan sudah tidak dapat diedit lagi. Maksimal edit adalah 1x.');
            }

            // Siapkan data update
            $updateData = [
                'pertanyaan' => $validatedData['pertanyaan'],
                'edit_count' => $pertanyaan->edit_count + 1,
                'tgl_edit' => now()
            ];

            Log::info('Updating pertanyaan with data', [
                'pertanyaan_id' => $id,
                'update_data' => $updateData
            ]);

            // Update via service
            $this->pesertaService->updatePertanyaan($id, $updateData);

            Log::info('Pertanyaan updated successfully', [
                'user_id' => Auth::id(),
                'pertanyaan_id' => $id
            ]);

            return redirect()
                ->route('kom.peserta.pertanyaan')
                ->with('success', 'Pertanyaan berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('=== ERROR in updatePertanyaanSaya ===', [
                'user_id' => Auth::id(),
                'pertanyaan_id' => $id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui pertanyaan.')
                ->withInput();
        }
    }


    public function persembahanSaya(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $data = $this->pesertaService->getDonasiUser($perPage);

            Log::info('User accessed persembahan', [
                'user_id' => Auth::id(),
                'total_donasi' => $data->total()
            ]);

            return view('a_kom_digital_pusat.peserta.persembahan', compact('data'));
        } catch (\Exception $e) {
            Log::error('Error in persembahanSaya', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data persembahan.');
        }
    }

    public function bukuSaya(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $data = $this->pesertaService->getBooksUser($perPage);

            Log::info('User accessed buku', [
                'user_id' => Auth::id(),
                'total_books' => $data->total()
            ]);

            return view('a_kom_digital_pusat.peserta.buku', compact('data'));
        } catch (\Exception $e) {
            Log::error('Error in bukuSaya', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data buku.');
        }
    }

    /**
     * Halaman Progress Peserta
     */
    public function progressSaya()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }

            // Ambil data progress dengan model yang sudah ada
            $progressModul = KmdProgressModul::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $videoProgress = KmdUserVideoProgress::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $tutorialStatus = KmdUserClassTutorial::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('User accessed progress page', [
                'user_id' => $user->id,
                'modul_count' => $progressModul->count(),
                'video_count' => $videoProgress->count(),
                'tutorial_count' => $tutorialStatus->count()
            ]);

            return view('a_kom_digital_pusat.peserta.progress', compact(
                'progressModul',
                'videoProgress',
                'tutorialStatus'
            ));

        } catch (\Exception $e) {
            Log::error('Error in progressSaya', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('a_kom_digital_pusat.peserta.progress', [
                'progressModul' => collect(),
                'videoProgress' => collect(),
                'tutorialStatus' => collect()
            ])->with('error', 'Terjadi kesalahan saat memuat data progress.');
        }
    }



    // public function sertifikatSaya()
    // {
    //     try {
    //         $userId = Auth::id();
    //         $user = Auth::user();

    //         Log::info('User accessed sertifikat', ['user_id' => $userId]);

    //         // Ambil data class_tutorial terakhir user
    //         $classTutorial = KmdUserClassTutorial::where('user_id', $userId)
    //             ->orderBy('created_at', 'desc')
    //             ->first();

    //         if (!$classTutorial) {
    //             return back()->with('error', 'Anda belum terdaftar di kelas manapun.');
    //         }

    //         $classId = $classTutorial->class_id;
    //         $classTutorialId = $classTutorial->id;
    //         $classTutorialCreatedAt = $classTutorial->created_at;

    //         // Ambil semua modul wajib berdasarkan class_id dari admin
    //         $modulWajib = KmdClassTutorial::where('class_id', $classId)
    //             ->pluck('modul_id')
    //             ->unique()
    //             ->toArray();

    //         if (empty($modulWajib)) {
    //             return back()->with('error', 'Modul belum ditentukan untuk kelas ini.');
    //         }

    //         // Ambil semua modul yang sudah diselesaikan user untuk kelas ini
    //         $modulSelesai = KmdUserClassTutorial::where('user_id', $userId)
    //             ->where('class_id', $classId)
    //             ->whereIn('modul_id', $modulWajib)
    //             ->where('status', 'selesai')
    //             ->pluck('modul_id')
    //             ->unique()
    //             ->toArray();

    //         // Cek apakah semua modul wajib sudah diselesaikan
    //         $isCompleted = count(array_intersect($modulWajib, $modulSelesai)) === count($modulWajib);

    //         if (!$isCompleted) {
    //             return view('a_kom_digital_pusat.peserta.esertifikat', [
    //                 'status' => 'belum',
    //                 'modulSelesai' => count($modulSelesai),
    //                 'totalModul' => count($modulWajib),
    //                 'certificate_number' => null,
    //             ]);
    //         }

    //         // Cek apakah sertifikat sudah ada
    //         $existing = KmdCertificates::where('user_id', $userId)->first();

    //         if (!$existing) {
    //             // Ambil tanggal selesai terakhir
    //             $tanggalSelesai = KmdUserClassTutorial::where('user_id', $userId)
    //                 ->where('class_id', $classId)
    //                 ->where('status', 'selesai')
    //                 ->latest('date_booking')
    //                 ->value('date_booking') ?? now();

    //             $bulan = str_pad(Carbon::parse($tanggalSelesai)->month, 2, '0', STR_PAD_LEFT);
    //             $tahun = Carbon::parse($tanggalSelesai)->year;

    //             // Hitung urutan
    //             $lastUrutan = KmdCertificates::count();
    //             $urut = str_pad($lastUrutan + 1, 6, '0', STR_PAD_LEFT);

    //             // Ambil nama kelas dan ekstrak angkanya
    //             $namaKelas = KmdClass::where('id', $classId)->value('name') ?? '000';
    //             $kodeKelas = preg_replace('/\D/', '', $namaKelas);

    //             // Format nomor sertifikat
    //             $nomor = "DSCP0104/{$kodeKelas}/{$bulan}/{$tahun}/{$urut}";

    //             // Simpan sertifikat
    //             $sertifikat = KmdCertificates::create([
    //                 'id' => Str::uuid(),
    //                 'user_id' => $userId,
    //                 'no_certificate' => $nomor,
    //                 'date_publish' => Carbon::now(),
    //                 'status' => 'active',
    //             ]);
    //         } else {
    //             $sertifikat = $existing;
    //             $nomor = $existing->no_certificate;
    //         }

    //         // Buat QR link terenkripsi
    //         //$token = Crypt::encryptString($sertifikat->id);
    //         // $qrLink = url('/kom-digital/verifikasi/' . $token);
    //         $qrLink = url('/kom-digital/verifikasi/' . $sertifikat->id);



    //         // Path simpan QR di public/kmdqr/
    //         $publicPath = public_path('kmdqr');
    //         $fileName = "kom_{$sertifikat->id}.png";
    //         $filePath = $publicPath . DIRECTORY_SEPARATOR . $fileName;

    //         // Buat folder jika belum ada
    //         if (!file_exists($publicPath)) {
    //             mkdir($publicPath, 0755, true);
    //         }

    //         $forceGenerate = request()->query('force');

    //         // Generate dan simpan QR code hanya jika belum ada
    //         if (!file_exists($filePath) || $forceGenerate) {
    //             QrCode::format('png')
    //                 ->size(300)
    //                 ->margin(4)
    //                 ->errorCorrection('H')
    //                 // ->version(5)
    //                 ->backgroundColor(255, 255, 255)
    //                 ->generate($qrLink, $filePath);

    //             Log::info("QR code generated (force: {$forceGenerate}) for certificate {$sertifikat->id}");
    //         } else {
    //             Log::info("QR code skipped (already exists): {$filePath}");
    //         }

    //         // URL langsung ke public/kmdqr
    //         $qrPathPublic = url('kmdqr/' . $fileName);

    //         // Kirim data ke view
    //         return view('a_kom_digital_pusat.peserta.esertifikat', [
    //             'certificate_number' => $nomor,
    //             'certificate_id' => $sertifikat->id,
    //             'qr_link' => $qrPathPublic,
    //             'status' => 'selesai',
    //             'nama' => $user->name,
    //             'dibuatOleh' => 'Sub Divisi KOM DIGITAL',
    //             'tanggalPelaksanaan' => Carbon::parse($sertifikat->date_publish)->format('d-m-Y'),
    //             'tanggalCetak' => now()->format('d-m-Y'),

    //             // Tambahan informasi
    //             'class_tutorial_id' => $classTutorialId,
    //             'class_tutorial_created_at' => $classTutorialCreatedAt,
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error('Error in sertifikatSaya', [
    //             'user_id' => Auth::id(),
    //             'error_message' => $e->getMessage(),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine(),
    //             'trace' => $e->getTraceAsString(), // Jika ingin detail lengkap
    //         ]);

    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat halaman sertifikat.');
    //     }

    // }
    public function sertifikatSaya()
    {
        try {
            $userId = Auth::id();
            $user = Auth::user();

            Log::info('User accessed sertifikat', ['user_id' => $userId]);

            // Ambil data class_tutorial terakhir user
            $classTutorial = KmdUserClassTutorial::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$classTutorial) {
                return back()->with('error', 'Anda belum terdaftar di kelas manapun.');
            }

            $classId = $classTutorial->class_id;
            $classTutorialId = $classTutorial->id;
            $classTutorialCreatedAt = $classTutorial->created_at;

            // Ambil semua modul wajib berdasarkan class_id dari admin
            $modulWajib = KmdClassTutorial::where('class_id', $classId)
                ->pluck('modul_id')
                ->unique()
                ->toArray();

            if (empty($modulWajib)) {
                return back()->with('error', 'Modul belum ditentukan untuk kelas ini.');
            }

            // Ambil seluruh booking peserta untuk modul-modul wajib di kelas ini
            $userClassTutorials = KmdUserClassTutorial::where('user_id', $userId)
                ->where('class_id', $classId)
                ->whereIn('modul_id', $modulWajib)
                ->get();

            // Sertifikat hanya berhak didapat jika admin cabang menandai peserta
            // HADIR (kmd_user_absensi.hadir = true) di setiap sesi/modul wajib.
            // Catatan: status 'selesai' yang di-set otomatis saat admin cabang
            // mengakhiri Zoom (End Meeting) TIDAK dipakai lagi sebagai syarat,
            // karena tidak mencerminkan kehadiran peserta yang sebenarnya.
            $modulHadir = KmdUserAbsensi::where('user_id', $userId)
                ->where('hadir', true)
                ->whereIn('userclasstutorial_id', $userClassTutorials->pluck('id'))
                ->get()
                ->map(function ($absensi) use ($userClassTutorials) {
                    return optional($userClassTutorials->firstWhere('id', $absensi->userclasstutorial_id))->modul_id;
                })
                ->filter()
                ->unique()
                ->toArray();

            // Cek apakah semua modul wajib sudah dihadiri (absensi = hadir)
            $isCompleted = count(array_intersect($modulWajib, $modulHadir)) === count($modulWajib);

            if (!$isCompleted) {
                return view('a_kom_digital_pusat.peserta.esertifikat', [
                    'status' => 'belum',
                    'modulSelesai' => count($modulHadir),
                    'totalModul' => count($modulWajib),
                    'certificate_number' => null,
                ]);
            }

            // Cek apakah sertifikat sudah ada
            $existing = KmdCertificates::where('user_id', $userId)->first();

            if (!$existing) {
                // Ambil tanggal sesi terakhir yang dihadiri (berdasarkan absensi)
                $tanggalSelesai = KmdUserAbsensi::where('user_id', $userId)
                    ->where('hadir', true)
                    ->whereIn('userclasstutorial_id', $userClassTutorials->pluck('id'))
                    ->latest('updated_at')
                    ->value('updated_at') ?? now();

                $bulan = str_pad(Carbon::parse($tanggalSelesai)->month, 2, '0', STR_PAD_LEFT);
                $tahun = Carbon::parse($tanggalSelesai)->year;

                // Hitung urutan
                $lastUrutan = KmdCertificates::count();
                $urut = str_pad($lastUrutan + 1, 6, '0', STR_PAD_LEFT);

                // Ambil nama kelas dan ekstrak angkanya
                $namaKelas = KmdClass::where('id', $classId)->value('name') ?? '000';
                $kodeKelas = preg_replace('/\D/', '', $namaKelas);

                // Format nomor sertifikat
                $nomor = "DSCP0104/{$kodeKelas}/{$bulan}/{$tahun}/{$urut}";

                // Simpan sertifikat
                $sertifikat = KmdCertificates::create([
                    'id' => Str::uuid(),
                    'user_id' => $userId,
                    'no_certificate' => $nomor,
                    'date_publish' => Carbon::now(),
                    'status' => 'active',
                ]);
            } else {
                $sertifikat = $existing;
                $nomor = $existing->no_certificate;
            }

            // Buat QR link terenkripsi
            //$token = Crypt::encryptString($sertifikat->id);
            // $qrLink = url('/kom-digital/verifikasi/' . $token);
            $qrLink = url('/kom-digital/verifikasi/' . $sertifikat->id);



            // Path simpan QR di public/kmdqr/
            $publicPath = public_path('kmdqr');
            $fileName = "kom_{$sertifikat->id}.png";
            $filePath = $publicPath . DIRECTORY_SEPARATOR . $fileName;

            // Buat folder jika belum ada
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $forceGenerate = request()->query('force');

            // Generate dan simpan QR code hanya jika belum ada
            if (!file_exists($filePath) || $forceGenerate) {
                QrCode::format('png')
                    ->size(300)
                    ->margin(4)
                    ->errorCorrection('H')
                    // ->version(5)
                    ->backgroundColor(255, 255, 255)
                    ->generate($qrLink, $filePath);

                Log::info("QR code generated (force: {$forceGenerate}) for certificate {$sertifikat->id}");
            } else {
                Log::info("QR code skipped (already exists): {$filePath}");
            }

            // URL langsung ke public/kmdqr
            $qrPathPublic = url('kmdqr/' . $fileName);

            // Kirim data ke view
            return view('a_kom_digital_pusat.peserta.esertifikat', [
                'certificate_number' => $nomor,
                'certificate_id' => $sertifikat->id,
                'qr_link' => $qrPathPublic,
                'status' => 'selesai',
                'nama' => $user->name,
                'dibuatOleh' => 'Sub Divisi KOM DIGITAL',
                'tanggalPelaksanaan' => Carbon::parse($sertifikat->date_publish)->format('d-m-Y'),
                'tanggalCetak' => now()->format('d-m-Y'),

                // Tambahan informasi
                'class_tutorial_id' => $classTutorialId,
                'class_tutorial_created_at' => $classTutorialCreatedAt,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in sertifikatSaya', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(), // Jika ingin detail lengkap
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat halaman sertifikat.');
        }

    }



    // public function verifikasi($token)
    // {
    //     try {
    //         $id = Crypt::decryptString($token);

    //         $sertifikat = KmdCertificates::with('user')->where('id', $id)->firstOrFail();

    //         return view('a_kom_digital_pusat.peserta.verifikasi', [
    //             'certificate_number' => $sertifikat->no_certificate,
    //             'nama' => $sertifikat->user->name,
    //             'dibuatOleh' => 'Sub Divisi KOM DIGITAL',
    //             'tanggalPelaksanaan' => Carbon::parse($sertifikat->date_publish)->format('d-m-Y'),
    //             'tanggalCetak' => now()->format('d-m-Y'),
    //         ]);

    //     } catch (\Exception $e) {
    //         return abort(404, 'Sertifikat tidak ditemukan atau link tidak valid.');
    //     }
    // }


    // public function formReviewPengajar()
    // {
    //     return view('a_kom_digital_pusat.peserta.formReview');
    // }

    public function formReviewPengajar($id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);

            // Ambil data kelas beserta relasi pengajar-nya (pastikan relasi classTutorial dan pengajar tersedia)
            $kelas = KmdUserClassTutorial::with('classTutorial.pengajar')->findOrFail($decryptedId);

            // Cek apakah relasi ke pengajar ada
            $pengajar_id = optional($kelas->classTutorial)->pengajar_id;

            return view('a_kom_digital_pusat.peserta.formReview', [
                'kelas' => $kelas,
                'userclasstutorial_id' => $kelas->id,
                'pengajar_id' => $pengajar_id,
            ]);

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(403, 'Invalid ID');
        }
    }





    public function storeReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|uuid',
            'pengajar_id' => 'required|uuid',
            'userclasstutorial_id' => 'required|uuid',
            'isi_pengajaran' => 'nullable|string',

            'kebenaran_teologis' => 'required|in:1,2,3,4',
            'pengajar_sampaikan_seluruh_materi' => 'required|in:1,2,3,4',
            'pengajar_sampaikan_materi_utama' => 'required|in:1,2,3,4',
            'pembukaan_materi' => 'required|in:1,2,3,4',
            'kesinambungan_materi' => 'required|in:1,2,3,4',
            'kesaksian_pribadi' => 'required|in:1,2,3,4',
            'materi_dipahami' => 'required|in:1,2,3,4',
            'sesuai_dengan_materi' => 'required|in:1,2,3,4',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cek apakah user sudah review untuk tutorial ini
        $alreadyReviewed = \App\Models\Kmd\KmdUserReview::where('user_id', $request->user_id)
            ->where('userclasstutorial_id', $request->userclasstutorial_id)
            ->exists();

        if ($alreadyReviewed) {
            return redirect()->back()->with('error', 'Anda sudah mengisi review untuk kelas ini.');
        }

        // Simpan data review
        \App\Models\Kmd\KmdUserReview::create([
            'id' => Str::uuid(), // Buat UUID baru
            'user_id' => $request->user_id,
            'pengajar_id' => $request->pengajar_id,
            'userclasstutorial_id' => $request->userclasstutorial_id,
            'isi_pengajaran' => $request->isi_pengajaran,
            'kebenaran_teologis' => $request->kebenaran_teologis,
            'pengajar_sampaikan_seluruh_materi' => $request->pengajar_sampaikan_seluruh_materi,
            'pengajar_sampaikan_materi_utama' => $request->pengajar_sampaikan_materi_utama,
            'pembukaan_materi' => $request->pembukaan_materi,
            'kesinambungan_materi' => $request->kesinambungan_materi,
            'kesaksian_pribadi' => $request->kesaksian_pribadi,
            'materi_dipahami' => $request->materi_dipahami,
            'sesuai_dengan_materi' => $request->sesuai_dengan_materi,
        ]);

        return redirect()->route('kom.peserta.kelas_tutorial')->with('success', 'Terima kasih! Review Anda berhasil dikirim.');
    }


}
