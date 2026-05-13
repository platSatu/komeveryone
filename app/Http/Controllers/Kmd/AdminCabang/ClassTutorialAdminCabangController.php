<?php

namespace App\Http\Controllers\Kmd\AdminCabang;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KmdModuls;
use App\Models\Kmd\KmdClassTutorial;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

// Services
use App\Services\AdminCabang\KelasTutorService;
use App\Services\AdminCabang\KelasService;
use App\Services\AdminCabang\AbsensiService;

// Requests
use App\Http\Requests\Kmd\AdminCabang\KelasTutorRequest;
use App\Http\Requests\Kmd\AdminCabang\ReviewRequest;
use App\Http\Requests\Kmd\AdminCabang\AbsensiRequest;

// Models
use App\Models\Kmd\KmdClass;
use App\Models\Kmd\KomUser;

class ClassTutorialAdminCabangController extends Controller
{
    public function __construct(
        protected KelasTutorService $kelasTutorService,
        protected KelasService $kelasService,
        protected AbsensiService $absensiService
    ) {
    }

    // public function index(): View
    // {
    //     $data = $this->kelasTutorService->getAllByUser(Auth::id());
    //     return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.index', compact('data'));
    // }
    // public function index(): View
    // {
    //     $data = $this->kelasTutorService->getAllByUser(Auth::id());
    //     $pertanyaans = $this->kelasTutorService->getPertanyaanByKelasUser(Auth::id());
    //     $zoomData = [];

    //     foreach ($data as $kelas) {
    //         if (!$kelas->zoom_meeting_id) {
    //             $zoomData[$kelas->id] = [
    //                 'registrants' => null,
    //                 'recordings' => null,
    //             ];
    //             continue;
    //         }

    //         try {
    //             $registrants = $this->kelasTutorService->getZoomRegistrants($kelas->zoom_meeting_id);
    //             $recordings = $this->kelasTutorService->getZoomCloudRecordings($kelas->zoom_meeting_id);
    //         } catch (\Exception $e) {
    //             \Log::error("Gagal ambil data Zoom untuk meeting {$kelas->zoom_meeting_id}: " . $e->getMessage());
    //             $registrants = null;
    //             $recordings = null;
    //         }

    //         $zoomData[$kelas->id] = [
    //             'registrants' => $registrants,
    //             'recordings' => $recordings,
    //         ];
    //     }

    //     return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.index', compact('data', 'zoomData', 'pertanyaans'));
    // }
    public function index(): View
    {
        $data = $this->kelasTutorService->getAllByUser(Auth::id());
        $pertanyaans = $this->kelasTutorService->getPertanyaanByKelasUser(Auth::id());

        $zoomData = [];

        foreach ($data as $kelas) {
            if (!$kelas->zoom_meeting_id) {
                $zoomData[$kelas->id] = [
                    'registrants' => null,
                    'recordings' => null,
                ];
                continue;
            }

            try {
                $registrants = $this->kelasTutorService->getZoomRegistrants($kelas->zoom_meeting_id);
                $recordings = $this->kelasTutorService->getZoomCloudRecordings($kelas->zoom_meeting_id);
            } catch (\Exception $e) {
                \Log::error("Gagal ambil data Zoom: " . $e->getMessage());
                $registrants = null;
                $recordings = null;
            }

            $zoomData[$kelas->id] = [
                'registrants' => $registrants,
                'recordings' => $recordings,
            ];
        }

        return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.index', compact('data', 'zoomData', 'pertanyaans'));
    }

    public function addPesertaZoom($kelasId)
    {
        return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.addPesertaZoom');
    }

    public function addPesertaZoomSubmit(Request $request, $kelasId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Cari kelas tutorial berdasarkan ID
        $kelas = KmdClassTutorial::find($kelasId);

        if (!$kelas || !$kelas->zoom_meeting_id) {
            return redirect()->back()->with('error', 'Kelas atau Zoom Meeting ID tidak ditemukan.');
        }

        try {
            // Panggil service untuk register peserta ke Zoom
            $this->kelasTutorService->registerZoomRegistrant(
                $kelas->zoom_meeting_id,
                $request->email,
                $request->name
            );

            return redirect()->route('kom.kelas_tutor_cabang.index')
                ->with('success', 'Peserta berhasil ditambahkan ke Zoom meeting.');
        } catch (\Exception $e) {
            \Log::error('Gagal registrasi peserta ke Zoom: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Gagal menambahkan peserta: ' . $e->getMessage());
        }
    }



    // public function showPertanyaan($id)
    // {
    //     try {
    //         $decryptedId = Crypt::decryptString($id);

    //         $pertanyaans = $this->kelasTutorService->getPertanyaanByKelasTutorialId($decryptedId);

    //         return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.pertanyaan', compact('pertanyaans'));
    //     } catch (\Exception $e) {
    //         Log::error('Gagal menampilkan pertanyaan: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Gagal memuat pertanyaan.');
    //     }
    // }
    public function showPertanyaan($id) // ⛔️ Jangan pakai ": View"
    {
        try {
            $decryptedId = Crypt::decryptString($id);

            $pertanyaans = $this->kelasTutorService->getPertanyaanByKelasTutorialId($decryptedId);

            return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.pertanyaan', compact('pertanyaans'));
        } catch (\Exception $e) {
    Log::error('Gagal menampilkan pertanyaan: ' . $e->getMessage(), [
        'trace' => $e->getTraceAsString(),
        'id' => $id,
    ]);
    return redirect()->back()->with('error', 'Gagal memuat pertanyaan.');
}

    }

    public function showDonasi($id): View
    {
        try {
            $decryptedId = Crypt::decryptString($id);

            $donasiData = $this->kelasTutorService->getDonasiDetailByKelasTutorial($decryptedId);

            if (!$donasiData) {
                return redirect()->back()->with('error', 'Kelas tutorial tidak ditemukan.');
            }

            return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.donasi', $donasiData);
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan donasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat donasi.');
        }
    }

    public function showReview($id): View
    {
        try {
            $decryptedId = Crypt::decryptString($id);

            $data = $this->kelasTutorService->getReviewByKelasTutorial($decryptedId);

            if (!$data) {
                return redirect()->back()->with('error', 'Kelas tutorial tidak ditemukan.');
            }

            return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.reviewKelas', [
                'kelasTutorial' => $data['kelasTutorial'],
                'reviews' => $data['reviews'],
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan review: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat review.');
        }
    }




    public function create(): View
    {
        $user = Auth::user();

        $pengajars = KomUser::where('cabang_id', $user->cabang_id)
            ->where('role', 'pengajar')
            ->get();

        $moduls = KmdModuls::all();
        $kelas = KmdClass::all();

        return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.create', compact('pengajars', 'moduls', 'kelas'));
    }

    public function store(KelasTutorRequest $request): RedirectResponse
    {
        $user = Auth::user();

        try {
            $tutorial = $this->kelasTutorService->create($user->id, $request->all());

            return redirect()->route('kom.kelas_tutor_cabang.index')
                ->with('success', 'Tutorial dan Zoom meeting berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membuat tutorial: ' . $e->getMessage()])
                ->withInput();
        }
    }


    public function edit(string $encryptedId): View
    {
        try {
            $data = $this->kelasTutorService->find($encryptedId);

            if ($data->user_id !== Auth::id()) {
                abort(403, 'Unauthorized');
            }

            $moduls = KmdModuls::all();
            $kelas = KmdClass::all();
            $pengajars = KomUser::where('role', 'pengajar')
                ->where('cabang_id', Auth::user()->cabang_id)
                ->get();

            return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.edit', compact('data', 'moduls', 'kelas', 'pengajars'));
        } catch (DecryptException $e) {
            abort(403, 'ID tidak valid.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Data tidak ditemukan.');
        }
    }

    public function update(KelasTutorRequest $request, string $encryptedId): RedirectResponse
    {
        try {
            $this->kelasTutorService->update($encryptedId, $request->validated());

            return redirect()->route('kom.kelas_tutor_cabang.index')
                ->with('success', 'Kelas tutorial berhasil diperbarui.');
        } catch (DecryptException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'ID tidak valid.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }


    public function endMeeting($id, KelasTutorService $kelasTutorService)
    {
        $classTutorial = KmdClassTutorial::findOrFail($id);

        if (!$classTutorial->zoom_meeting_id) {
            return redirect()->back()->with('error', 'Meeting Zoom belum dibuat.');
        }

        try {
            $kelasTutorService->endZoomMeeting($classTutorial->zoom_meeting_id, $classTutorial->id);

            // Opsional: update status lokal di DB
            $classTutorial->update([
                'status' => 'inactive', // atau sesuaikan nama kolom
            ]);

            $kelasTutorService->updatePesertaStatusSelesai($classTutorial->id);


            return redirect()->back()->with('success', 'Meeting Zoom telah diakhiri.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengakhiri meeting Zoom: ' . $e->getMessage());
        }
    }



    public function destroy(string $encryptedId): RedirectResponse
    {
        try {
            $this->kelasTutorService->delete(Auth::id(), $encryptedId);
            return redirect()->back()->with('success', 'Kelas tutorial berhasil dihapus.');
        } catch (DecryptException $e) {
            return redirect()->back()->with('error', 'ID tidak valid.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function absensi(string $encryptedId): View|RedirectResponse
    {
        try {
            $classTutorialId = Crypt::decryptString($encryptedId);

            $data = $this->kelasService->getAbsensiData($classTutorialId);

            return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.absensi', $data);
        } catch (DecryptException $e) {
            return redirect()->route('kom.kelas_tutor_cabang.index')
                ->with('error', 'ID tidak valid.');
        } catch (\Exception $e) {
            return redirect()->route('kom.kelas_tutor_cabang.index')
                ->with('error', 'Data kelas tidak ditemukan: ' . $e->getMessage());
        }
    }

    public function absensiStore(AbsensiRequest $request, string $encryptedId): RedirectResponse
    {
        try {
            $classTutorialId = Crypt::decryptString($encryptedId);

            Log::info('Start storing absensi', [
                'classTutorialId' => $classTutorialId,
                'data' => $request->validated()
            ]);

            $this->kelasService->storeAbsensi($classTutorialId, $request->validated());

            Log::info('Absensi stored successfully', ['classTutorialId' => $classTutorialId]);

            return redirect()->route('kom.kelas_tutor_cabang.index')
                ->with('success', 'Data absensi berhasil disimpan.');
        } catch (DecryptException $e) {
            Log::error('DecryptException: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'ID tidak valid.');
        } catch (\Exception $e) {
            Log::error('Exception saat menyimpan absensi: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan absensi: ' . $e->getMessage());
        }
    }

    public function review(string $encryptedId): View|RedirectResponse
    {
        try {
            // Debug log
            \Log::info('Review method called', ['encrypted_id' => $encryptedId]);

            $classTutorialId = Crypt::decryptString($encryptedId);
            \Log::info('Decrypted ID', ['class_tutorial_id' => $classTutorialId]);

            $data = $this->kelasService->getReviewData($classTutorialId);
            \Log::info('Data retrieved successfully');

            return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.review', $data);
        } catch (DecryptException $e) {
            \Log::error('Decrypt error', ['error' => $e->getMessage()]);
            return redirect()->route('kom.kelas_tutor_cabang.index')
                ->with('error', 'ID tidak valid.');
        } catch (\Exception $e) {
            \Log::error('General error in review', ['error' => $e->getMessage()]);
            return redirect()->route('kom.kelas_tutor_cabang.index')
                ->with('error', 'Data kelas tidak ditemukan: ' . $e->getMessage());
        }
    }


    public function reviewStore(ReviewRequest $request, string $encryptedId): RedirectResponse
    {
        try {
            $classTutorialId = Crypt::decryptString($encryptedId);

            $this->kelasService->storeReview($classTutorialId, $request->validated());

            return redirect()->route('kom.kelas_tutor_cabang.index')
                ->with('success', 'Data review pengajar berhasil disimpan.');
        } catch (DecryptException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'ID tidak valid.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan review: ' . $e->getMessage());
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

}
