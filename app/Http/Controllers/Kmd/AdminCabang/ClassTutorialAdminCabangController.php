<?php

namespace App\Http\Controllers\Kmd\AdminCabang;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KmdModuls;
use App\Models\Kmd\KmdClassTutorial;
use App\Models\Kmd\KmdUserClassTutorial;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

// Services
use App\Services\AdminCabang\KelasTutorService;
use App\Services\AdminCabang\KelasService;
use App\Services\AdminCabang\AbsensiService;
use App\Services\Kmd\PengajarDiklatService;

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
    public function index(Request $request): View
    {
        // 4 Nov 2026: Admin KOM (superadmin) melihat kelas tutorial SEMUA cabang (filter per cabang);
        // admin cabang tetap hanya kelas miliknya, persis seperti sebelumnya.
        $isSuperadmin = KelasTutorService::isSuperadmin();
        $cabangDipilih = $isSuperadmin ? (string) $request->query('cabang', '') : '';
        $cabangs = $isSuperadmin ? \App\Models\Kmd\Cabang::orderBy('name')->get(['id', 'name']) : collect();

        if ($isSuperadmin) {
            $data = $this->kelasTutorService->getAllForSuperadmin($cabangDipilih ?: null);
            $pertanyaans = $this->kelasTutorService->getPertanyaanByKelasIds($data->getCollection()->pluck('id'));
        } else {
            $data = $this->kelasTutorService->getAllByUser(Auth::id());
            $pertanyaans = $this->kelasTutorService->getPertanyaanByKelasUser(Auth::id());
        }

        $zoomData = [];

        foreach ($data as $kelas) {
            if (!$kelas->zoom_meeting_id) {
                $zoomData[$kelas->id] = [
                    'registrants' => null,
                    'recordings' => null,
                ];
                continue;
            }

            // kom_v02 (performa): data Zoom disimpan 10 menit supaya halaman tidak memanggil API Zoom
            // setiap kali dibuka. Kalau Zoom gagal, tidak disimpan (dicoba lagi saat halaman dibuka lagi).
            try {
                $zoom = Cache::remember(self::zoomCacheKey($kelas->zoom_meeting_id), now()->addMinutes(10), fn () => [
                    'registrants' => $this->kelasTutorService->getZoomRegistrants($kelas->zoom_meeting_id),
                    'recordings' => $this->kelasTutorService->getZoomCloudRecordings($kelas->zoom_meeting_id),
                ]);
                $registrants = $zoom['registrants'];
                $recordings = $zoom['recordings'];
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

        return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.index', compact('data', 'zoomData', 'pertanyaans', 'isSuperadmin', 'cabangs', 'cabangDipilih'));
    }

    /** Kunci cache data Zoom (peserta & rekaman) per meeting. */
    private static function zoomCacheKey($meetingId): string
    {
        return 'kom:zoom-kelas:'.$meetingId;
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
            Cache::forget(self::zoomCacheKey($kelas->zoom_meeting_id)); // daftar peserta langsung terlihat

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




    // public function create(): View
    // {
    //     $user = Auth::user();

    //     $pengajars = KomUser::where('cabang_id', $user->cabang_id)
    //         ->where('role', 'pengajar')
    //         ->get();

    //     $moduls = KmdModuls::all();
    //     $kelas = KmdClass::all();

    //     return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.create', compact('pengajars', 'moduls', 'kelas'));
    // }
    public function create(PengajarDiklatService $pengajarDiklat)
    {
        $moduls = KmdModuls::with('kelas')->orderBy('kom_class_id')->orderBy('language_code')->orderBy('order')->get(); // kom_v02
        $kelas = KmdClass::all();

        // Modul dari isian sebelumnya / request, fallback ke modul pertama.
        $selectedModulId = old('modul_id', request('modul_id', optional($moduls->first())->id));
        $selectedModul = $moduls->firstWhere('id', $selectedModulId);

        // 29 Okt 2026: pengajar sesuai kategori diklat modul (110 & 130 Doktrinal,
        // 120 & 140 Praktikal) — aturan yang sama dengan saat modul diganti & Edit.
        // Tidak ada lagi "tampilkan semua pengajar" kalau kosong.
        $category = $pengajarDiklat->categoryForModul($selectedModulId);
        $pengajars = $pengajarDiklat->pengajarsForCategory($category);
        $pengajarKosong = $pengajarDiklat->emptyMessage($category);

        // 4 Nov 2026: Admin KOM memilih dulu cabang (admin cabang pemilik kelas)
        $adminCabangs = KelasTutorService::isSuperadmin() ? $this->daftarAdminCabang() : collect();

        return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.create', compact(
            'pengajars',
            'moduls',
            'kelas',
            'selectedModul',
            'selectedModulId',
            'category',
            'pengajarKosong',
            'adminCabangs'
        ));
    }

/**
 * kom_v02: daftar titik kelas tutorial untuk satu modul (dropdown "Titik Kelas Tutorial").
 * value kosong = Akhir Modul.
 */
public function getTitikByModul(Request $request, \App\Services\Kmd\TitikTutorialService $titikService)
{
    $modul = KmdModuls::find($request->query('modul_id'));
    if (! $modul) {
        return response()->json([]);
    }

    return response()->json($titikService->titikModul($modul)->map(fn ($t) => [
        'value' => $t['video_id'] ?? '',
        'label' => $t['label'] . ($t['video'] ? ' — ' . $t['video']->name : ''),
    ])->values());
}

public function getPengajarsByModul(Request $request, PengajarDiklatService $pengajarDiklat)
{
    // 29 Okt 2026: satu aturan untuk semua tempat, lihat App\Services\Kmd\PengajarDiklatService.
    $category = $pengajarDiklat->categoryForModul($request->query('modul_id'));

    return response()->json([
        'kategori' => $category,
        'pesan_kosong' => $pengajarDiklat->emptyMessage($category),
        'pengajar' => $pengajarDiklat->pengajarsForCategory($category)->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
        ])->values(),
    ]);
}

    /**
     * 4 Nov 2026: pilihan "Cabang" untuk Admin KOM = akun admin cabang aktif beserta cabangnya.
     * Kelas dibuat atas nama admin cabang itu, jadi muncul di menu admin cabang tersebut dan
     * notifikasinya (peserta baru, pembatalan, dll.) sampai ke admin cabang yang benar.
     */
    private function daftarAdminCabang()
    {
        return KomUser::with('cabang:id,name')
            ->where('role', 'admin_cabang')
            ->whereNotNull('cabang_id')
            ->whereIn('status', ['aktif', '1']) // sama dengan pengecekan status di menu Daftar User
            ->get(['id', 'name', 'email', 'cabang_id'])
            ->sortBy(fn ($u) => strtolower((optional($u->cabang)->name ?? '').' '.$u->name))
            ->values();
    }

    public function store(KelasTutorRequest $request, PengajarDiklatService $pengajarDiklat): RedirectResponse
    {
        $user = Auth::user();

        // 4 Nov 2026: Admin KOM membuat kelas untuk cabang yang dipilih (atas nama admin cabangnya)
        if (KelasTutorService::isSuperadmin()) {
            $pemilik = $this->daftarAdminCabang()->firstWhere('id', (string) $request->input('admin_cabang_id'));
            if (! $pemilik) {
                return back()->withInput()->withErrors(['admin_cabang_id' => __('Pilih cabang tujuan kelas tutorial ini.')]);
            }
            $user = $pemilik;
        }

        // 29 Okt 2026: pengajar wajib sudah lulus Diklat Tutor kategori modul ini
        // (dicek juga di server, bukan hanya di dropdown).
        if (! $pengajarDiklat->isQualifiedForModul((string) $request->input('pengajar_id'), $request->input('modul_id'))) {
            return back()->withInput()->withErrors([
                'pengajar_id' => __('Pengajar yang dipilih belum lulus Diklat Tutor untuk kategori modul ini.'),
            ]);
        }

        try {
            $tutorial = $this->kelasTutorService->create($user->id, $request->all());

            return redirect()->route('kom.kelas_tutor_cabang.index')
                ->with('success', 'Tutorial dan Zoom meeting berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membuat tutorial: ' . $e->getMessage()])
                ->withInput();
        }
    }


    // public function edit(string $encryptedId): View
    // {
    //     try {
    //         $data = $this->kelasTutorService->find($encryptedId);

    //         if ($data->user_id !== Auth::id()) {
    //             abort(403, 'Unauthorized');
    //         }

    //         $moduls = KmdModuls::all();
    //         $kelas = KmdClass::all();
    //         $pengajars = KomUser::where('role', 'pengajar')
    //             ->where('cabang_id', Auth::user()->cabang_id)
    //             ->get();

    //         return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.edit', compact('data', 'moduls', 'kelas', 'pengajars'));
    //     } catch (DecryptException $e) {
    //         abort(403, 'ID tidak valid.');
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         abort(404, 'Data tidak ditemukan.');
    //     }
    // }

    public function edit(string $encryptedId, PengajarDiklatService $pengajarDiklat): View
    {
        try {
            $data = $this->kelasTutorService->find($encryptedId);

            if ($data->user_id !== Auth::id() && ! KelasTutorService::isSuperadmin()) {
                abort(403, 'Unauthorized');
            }
            $data->loadMissing(['user', 'rayon']); // 4 Nov 2026: info cabang di form edit Admin KOM

            $moduls = KmdModuls::with('kelas')->orderBy('kom_class_id')->orderBy('language_code')->orderBy('order')->get(); // kom_v02
            $kelas = KmdClass::all();

            $selectedModulId = $data->modul_id ?? optional($moduls->first())->id;
            $selectedModul = $moduls->firstWhere('id', $selectedModulId);

            // 29 Okt 2026: aturan sama dengan Buat Kelas. Pengajar yang sudah terpilih
            // di kelas ini tetap ditampilkan supaya data lama tidak hilang.
            $category = $pengajarDiklat->categoryForModul($selectedModulId);
            $pengajars = $pengajarDiklat->pengajarsForCategory($category, $data->pengajar_id);
            $pengajarKosong = $pengajarDiklat->emptyMessage($category);

            return view('a_kom_digital_pusat.admin_cabang.kelas_tutorial.edit', compact(
                'data',
                'pengajars',
                'moduls',
                'kelas',
                'selectedModul',
                'selectedModulId',
                'category',
                'pengajarKosong'
            ));
        } catch (DecryptException $e) {
            abort(403, 'ID tidak valid.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Data tidak ditemukan.');
        }
    }




    public function update(KelasTutorRequest $request, string $encryptedId, PengajarDiklatService $pengajarDiklat): RedirectResponse
    {
        try {
            // Samakan dengan alur create: perubahan jadwal/topic/durasi ikut
            // di-PATCH ke meeting Zoom yang sudah ada, bukan cuma update DB lokal.
            // kom_v02: modul / titik tutorial tidak boleh diganti jika sudah ada peserta
            $lama = $this->kelasTutorService->find($encryptedId);
            $titikBerubah = $lama->modul_id !== $request->input('modul_id')
                || ($lama->video_id ?: null) !== ($request->input('video_id') ?: null);
            if ($titikBerubah && KmdUserClassTutorial::where('class_tutorial_id', $lama->id)->exists()) {
                return redirect()->back()->withInput()
                    ->withErrors(['video_id' => __('Modul / titik kelas tutorial tidak bisa diganti karena sudah ada peserta yang mendaftar.')]);
            }

            // 29 Okt 2026: kalau pengajar atau modul diganti, pengajar baru wajib lulus
            // Diklat Tutor kategori modulnya. Kelas lama yang tidak diganti tetap bisa disimpan.
            $pengajarBerubah = (string) $lama->pengajar_id !== (string) $request->input('pengajar_id');
            if (($pengajarBerubah || $lama->modul_id !== $request->input('modul_id'))
                && ! $pengajarDiklat->isQualifiedForModul((string) $request->input('pengajar_id'), $request->input('modul_id'))) {
                return redirect()->back()->withInput()->withErrors([
                    'pengajar_id' => __('Pengajar yang dipilih belum lulus Diklat Tutor untuk kategori modul ini.'),
                ]);
            }

            // 4 Nov 2026: kuota tidak boleh kurang dari peserta lunas + yang sedang mendaftar
            try {
                app(\App\Services\Kmd\KuotaTutorialService::class)->pastikanKuotaCukup($lama, $request->input('quota'));
            } catch (\InvalidArgumentException $e) {
                return redirect()->back()->withInput()->withErrors(['quota' => $e->getMessage()]);
            }

            $this->kelasTutorService->editZoomMeeting($encryptedId, $request->validated());

            return redirect()->route('kom.kelas_tutor_cabang.index')
                ->with('success', 'Kelas tutorial berhasil diperbarui dan Zoom meeting sudah diupdate.');
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
