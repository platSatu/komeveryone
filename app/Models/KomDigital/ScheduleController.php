<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomSchedule;
use App\Models\Kom\KomUser;
use App\Models\Kom\KomClass;
use App\Models\Kom\KomModul;
use App\Models\Kom\KomZoom;
use App\Models\Kom\KomZoomHistory;
use App\Models\Kom\KelasTutorial;
use App\Models\Kom\KomPertanyaanUser;
use App\Models\Kom\KomDonasi;
use App\Models\Kom\TeacherReview;
use App\Models\Rayon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ScheduleController extends Controller
{

    // public function jadwalPengajar()
    // {
    //     // Ambil semua jadwal aktif
    //     $jadwal = KomSchedule::with([
    //         'kelasTutorial' => function ($query) {
    //             $query->with(['user', 'pertanyaan']);
    //         }
    //     ])
    //         ->where('status', 'aktif')
    //         ->orderBy('datetime')
    //         ->get();

    //     return view('kom_digital.schedules.jadwal_pengajar_kom', compact('jadwal'));
    // }
    // public function jadwalPengajar()
    // {
    //     $user = Auth::guard('kom')->user();

    //     // Cek apakah user login & memiliki role pengajar
    //     if (
    //         !$user ||
    //         !in_array($user->role, ['pengajar', 'admin_kom_pusat', 'admin_kom_cabang'])
    //     ) {
    //         abort(403, 'Anda tidak memiliki akses.');
    //     }


    //     $jadwal = KomSchedule::with([
    //         'kelasTutorial' => function ($query) {
    //             $query->with(['user', 'pertanyaan']);
    //         }
    //     ])
    //         ->withCount('kelasTutorial')
    //         ->where('status', 'aktif')
    //         ->where('rayon_id', $user->rayon_id) // hanya tampilkan jadwal sesuai rayon pengajar
    //         ->orderBy('datetime')
    //         ->get();

    //     return view('kom_digital.schedules.jadwal_pengajar_kom', compact('jadwal'));
    // }

    public function jadwalPengajar()
    {
        $user = Auth::guard('kom')->user();

        // Cek apakah user login & memiliki role pengajar
        if (
            !$user ||
            !in_array($user->role, ['pengajar', 'admin_kom_pusat', 'admin_kom_cabang'])
        ) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $rayonId = $user->rayon_id;

        // Ambil jadwal pengajar beserta kelas tutorial & relasinya
        $jadwal = KomSchedule::with([
            'kelasTutorial' => function ($query) {
                $query->with(['user', 'pertanyaan']);
            }
        ])
            ->withCount('kelasTutorial')
            ->where('status', 'aktif')
            ->where('rayon_id', $rayonId)
            ->orderBy('datetime')
            ->get();

        // Ambil semua link Zoom aktif berdasarkan rayon & jadwal
        $zoomLinks = KomZoom::with('schedule')
            ->whereHas('schedule', function ($query) use ($rayonId) {
                $query->where('rayon_id', $rayonId)
                    ->where('status', 'aktif');
            })
            ->get();

        return view('kom_digital.schedules.jadwal_pengajar_kom', compact('jadwal', 'zoomLinks'));
    }

    public function detailJadwalPengajar($id)
    {
        $jadwal = KomSchedule::with([
            'pengajar',
            'kelasTutorial.user',
            'kelasTutorial.pertanyaan'
        ])->findOrFail($id);

        return view('kom_digital.schedules.jadwal_pengajar', compact('jadwal'));
    }




    public function linkZoomPengajar()
    {
        $user = Auth::guard('kom')->user();

        if (
            !$user ||
            !in_array($user->role, ['pengajar', 'admin_kom_pusat', 'admin_kom_cabang'])
        ) {
            abort(403, 'Anda tidak memiliki akses.');
        }


        $rayonId = $user->rayon_id;

        $zoomLinks = KomZoom::with('schedule') // kita eager load relasi agar bisa akses di blade
            ->whereHas('schedule', function ($query) use ($rayonId) {
                $query->where('rayon_id', $rayonId)
                    ->where('status', 'aktif'); // hanya ambil yang aktif
            })
            ->get();

        return view('kom_digital.schedules.linkzoompengajar', compact('zoomLinks'));
    }






    public function indexAktif()
    {
        $user = auth('kom')->user();

        $schedules = KomSchedule::where('status', 'aktif')
            ->where('rayon_id', $user->rayon_id)
            ->get()
            ->unique(function ($item) {
                return \Carbon\Carbon::parse($item->datetime)->format('Y-m-d');
            })
            ->values();

        return view('kom_digital.schedules.indexAktif', compact('schedules'));
    }


    // Halaman detail jadwal: tampilkan peserta
    public function detail($id)
    {
        $user = auth('kom')->user();

        $schedule = KomSchedule::findOrFail($id);

        $date = \Carbon\Carbon::parse($schedule->datetime)->format('Y-m-d');

        $allSchedulesInDate = KomSchedule::where('status', 'aktif')
            ->where('rayon_id', $user->rayon_id)
            ->whereDate('datetime', $date)
            ->get();

        return view('kom_digital.schedules.detail', compact('allSchedulesInDate', 'date'));
    }



    // public function kelasTutorial()
    // {
    //     $schedules = KomSchedule::with(['komUser', 'rayon', 'komClass', 'komModul'])->get();
    //     return view('kom_digital.schedules.kelastutorial', compact('schedules'));
    // }

   public function kelasTutorial(Request $request)
{
    $user = Auth::guard('kom')->user();

    $keyword = $request->input('search');
    $rayonFilter = $request->input('rayon');

    // 1. Cek apakah user sudah booking kelas dengan status AKTIF
    $sudahBooking = \DB::table('kom_kelas_tutorial')
        ->where('kom_user_id', $user->id)
        ->where('status', 'aktif')
        ->exists();

    // 3. Cek apakah user punya kelas tutorial dengan status NONAKTIF (boleh update rayon & pilih ulang kelas)
    $punyaKelasNonaktif = KelasTutorial::where('kom_user_id', $user->id)
        ->where('status', 'pending')
        ->exists();

    // Query jadwal aktif dengan relasi dan hitung jumlah peserta
    $query = KomSchedule::with(['komUser', 'rayon', 'komClass', 'komModul'])
        ->withCount('kelasTutorial')
        ->where('status', 'aktif');

    // Filter berdasarkan keyword pada nama rayon, nama kelas, atau judul modul
    if ($keyword) {
        $query->where(function($q) use ($keyword) {
            $q->whereHas('rayon', function($q2) use ($keyword) {
                $q2->where('name', 'like', "%{$keyword}%");
            })
            ->orWhereHas('komClass', function($q2) use ($keyword) {
                $q2->where('name', 'like', "%{$keyword}%");
            })
            ->orWhereHas('komModul', function($q2) use ($keyword) {
                $q2->where('title', 'like', "%{$keyword}%");
            });
        });
    }

    // Filter berdasarkan rayon jika ada
    if ($rayonFilter) {
        $query->where('rayon_id', $rayonFilter);
    }

    // Paginate hasil, 8 per halaman
    $schedules = $query->paginate(8)->withQueryString();

    // Ambil daftar rayon yang memiliki jadwal aktif (untuk filter dropdown)
    $rayons = Rayon::whereIn('id', KomSchedule::where('status', 'aktif')->pluck('rayon_id')->unique())->get();

    // Kirim data ke view
    return view('kom_digital.schedules.kelastutorial', compact(
        'schedules',
        'sudahBooking',
        'punyaKelasNonaktif',
        'rayons',
        'keyword',
        'rayonFilter'
    ));
}




    public function indexForUser()
    {
        $user = auth('kom')->user();

        $schedules = KomSchedule::with([
            'komUser',
            'rayon',
            'komClass',
            'komModul',
            'kelasTutorial' => function ($query) {
                $query->with(['user', 'pertanyaan']);
            }
        ])
            ->withCount('kelasTutorial')
            ->where('rayon_id', $user->rayon_id)
            ->orderBy('datetime', 'asc')
            ->get();

        // (Opsional) total semua kelas tutorial dari semua jadwal
        $totalKelasTutorial = $schedules->sum('kelas_tutorial_count');

        return view('kom_digital.schedules.indexByUser', compact('schedules', 'totalKelasTutorial'));
    }

    public function markAsFinished($id)
    {
        $kelas = KelasTutorial::findOrFail($id);

        $kelas->status_kelas = $kelas->status_kelas === 'finish' ? 'ongoing' : 'finish';
        $kelas->save();

        return response()->json([
            'status' => 'success',
            'new_status' => $kelas->status_kelas
        ]);
    }


    // public function toggleStatus($id)
    // {
    //     $jadwal = KomSchedule::findOrFail($id);

    //     // Toggle status aktif <-> nonaktif
    //     $jadwal->status = $jadwal->status === 'aktif' ? 'nonaktif' : 'aktif';
    //     $jadwal->save();

    //     // Update status di kom_kelas_tutorial yang terkait
    //     \DB::table('kom_kelas_tutorial')
    //         ->where('kom_schedules_id', $jadwal->id)
    //         ->update(['status' => $jadwal->status]);

    //     return redirect()->back()->with('success', 'Status berhasil diperbarui.');
    // }
    public function toggleStatus($id)
    {
        $jadwal = KomSchedule::findOrFail($id);

        // Toggle status aktif <-> nonaktif
        $newStatus = $jadwal->status === 'aktif' ? 'nonaktif' : 'aktif';
        $jadwal->status = $newStatus;
        $jadwal->save();

        // Update status di kom_kelas_tutorial terkait
        if ($newStatus === 'nonaktif') {
            // Jika jadwal nonaktif, ubah status kom_kelas_tutorial menjadi pending
            \DB::table('kom_kelas_tutorial')
                ->where('kom_schedules_id', $jadwal->id)
                ->update(['status' => 'pending']);
        } else {
            // Jika jadwal aktif, sesuaikan jika ingin ubah status tutorial kembali ke aktif (opsional)
            \DB::table('kom_kelas_tutorial')
                ->where('kom_schedules_id', $jadwal->id)
                ->update(['status' => 'aktif']);
        }

        return redirect()->back()->with('success', 'Status berhasil diperbarui.');
    }



    public function detailPeserta($scheduleId)
    {
        // Ambil semua data peserta yang mengikuti jadwal tertentu
        $peserta = KelasTutorial::with(['user', 'modul', 'kelas', 'rayon'])
            ->where('kom_schedules_id', $scheduleId)
            ->get();

        // Ambil info jadwal untuk ditampilkan juga di view
        $schedule = KomSchedule::with(['rayon', 'komModul', 'komClass'])->findOrFail($scheduleId);

        return view('kom_digital.schedules.detail_peserta', compact('peserta', 'schedule'));
    }

    public function updateAbsensi(Request $request, $id)
    {
        $request->validate([
            'kehadiran' => 'required|in:hadir,tidak_hadir',
        ]);

        $peserta = KelasTutorial::findOrFail($id);

        if ($request->kehadiran === 'hadir') {
            $peserta->hadir = 'hadir';
            $peserta->tidak_hadir = null;
        } else {
            $peserta->tidak_hadir = 'tidak_hadir';
            $peserta->hadir = null;
        }

        $peserta->save();

        return response()->json(['message' => 'Status kehadiran berhasil diperbarui']);
    }


    public function showReviewForm($komScheduleId)
    {
        \Log::info("Masuk ke showReviewForm dengan komScheduleId: {$komScheduleId}");

        $kelasTutorial = KelasTutorial::where('kom_schedules_id', $komScheduleId)->first();

        if (!$kelasTutorial) {
            \Log::error("KelasTutorial tidak ditemukan untuk kom_schedules_id: {$komScheduleId}");
            abort(404);
        }

        $jadwal = KomSchedule::with('pengajar')->findOrFail($komScheduleId);

        $kelasTutorialId = $kelasTutorial->id;
        $scheduleId = $komScheduleId;
        $pengajar = $jadwal->pengajar; // ini akan mengandung data pengajar

        return view('kom_digital.schedules.review-form', compact('kelasTutorialId', 'scheduleId', 'pengajar'));
    }





    public function storeReview(Request $request)
    {
        $validated = $request->validate([
            'kelas_tutorial_id' => 'required|exists:kom_kelas_tutorial,id',
            'kom_schedule_id' => 'required|exists:kom_jadwal,id',

            'isi_pengajaran' => 'required|in:sangat_baik,baik,cukup,kurang',
            'kebenaran_teologis' => 'required|in:sangat_baik,baik,cukup,kurang',
            'pengajar_sampaikan_seluruh_materi' => 'required|in:sangat_baik,baik,cukup,kurang',
            'pengajar_sampaikan_materi_utama' => 'required|in:sangat_baik,baik,cukup,kurang',
            'pembukaan_materi' => 'required|in:sangat_baik,baik,cukup,kurang',
            'kesinambungan_materi' => 'required|in:sangat_baik,baik,cukup,kurang',
            'kesaksian_pribadi' => 'required|in:sangat_baik,baik,cukup,kurang',
            'materi_dipahami' => 'required|in:sangat_baik,baik,cukup,kurang',
            'sesuai_dengan_materi' => 'required|in:sangat_baik,baik,cukup,kurang',
        ]);

        $validated['kom_user_id'] = auth()->id();

        // Ambil pengajar_id dari jadwal
        $jadwal = KomSchedule::findOrFail($validated['kom_schedule_id']);
        $validated['pengajar_id'] = $jadwal->pengajar_id;

        // Cek apakah user sudah review kelas ini sebelumnya
        $exists = TeacherReview::where('kom_user_id', $validated['kom_user_id'])
            ->where('kelas_tutorial_id', $validated['kelas_tutorial_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'Anda sudah memberikan review untuk kelas ini.']);
        }

        TeacherReview::create($validated);

        return redirect()->route('kom.review-form', $validated['kelas_tutorial_id'])
            ->with('success', 'Terima kasih! Review Anda berhasil disimpan.');
    }

    public function reviewIndex($kelasTutorialId)
    {
        $reviews = TeacherReview::where('kelas_tutorial_id', $kelasTutorialId)->get();

        if ($reviews->isEmpty()) {
            return view('kom_digital.schedules.reviewIndexPengajar', [
                'message' => 'Belum ada review untuk kelas ini.',
                'kelasTutorialId' => $kelasTutorialId,
            ]);
        }

        // Ambil data kelas tutorial
        $kelasTutorial = KelasTutorial::findOrFail($kelasTutorialId);

        // Ambil jadwal berdasarkan kom_schedule_id dari kelas tutorial
        $jadwal = KomSchedule::with('pengajar')->find($kelasTutorial->kom_schedules_id);
        $pengajar = $jadwal?->pengajar;

        $fields = [
            'isi_pengajaran',
            'kebenaran_teologis',
            'pengajar_sampaikan_seluruh_materi',
            'pengajar_sampaikan_materi_utama',
            'pembukaan_materi',
            'kesinambungan_materi',
            'kesaksian_pribadi',
            'materi_dipahami',
            'sesuai_dengan_materi',
        ];

        $ratingValues = [
            'sangat_baik' => 5,
            'baik' => 4,
            'cukup' => 3,
            'kurang' => 2,
            'sangat_kurang' => 1,
        ];

        $averages = [];

        foreach ($fields as $field) {
            $total = 0;
            $count = 0;
            foreach ($reviews as $review) {
                $value = $review->$field;
                if (isset($ratingValues[$value])) {
                    $total += $ratingValues[$value];
                    $count++;
                }
            }
            $averages[$field] = $count > 0 ? round($total / $count, 2) : null;
        }

        $chartLabels = array_map(fn($k) => ucwords(str_replace('_', ' ', $k)), array_keys($averages));
        $chartData = array_values($averages);

        return view('kom_digital.schedules.reviewIndexPengajar', [
            'averages' => $averages,
            'kelasTutorialId' => $kelasTutorialId,
            'reviewsCount' => $reviews->count(),
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'pengajar' => $pengajar, // kirim ke blade
        ]);
    }



    public function hapusPeserta($id)
    {
        $kelas = KelasTutorial::findOrFail($id);
        $kelas->delete();

        return back()->with('success', 'Peserta berhasil dihapus dari kelas.');
    }




    public function createScheduleCabang()
    {
        $users = KomUser::where('status', 1)->get();
        $rayons = Rayon::where('status', 1)->get();
        $classes = KomClass::where('status', 1)->get();
        $moduls = KomModul::where('status', 1)->get();
        return view('kom_digital.schedules.create', compact('users', 'rayons', 'classes', 'moduls'));
    }



    public function index(Request $request)
    {
        // Ambil filter rayon_id jika ada
        $rayonId = $request->input('rayon_id');

        // Query dasar
        $query = KomSchedule::with(['komUser', 'rayon', 'komClass', 'komModul']);

        // Filter jika ada rayon_id
        if ($rayonId) {
            $query->where('rayon_id', $rayonId);
        }

        $schedules = $query->get();

        // Hitung jumlah kelas yang dibuka (asumsinya 1 schedule = 1 kelas dibuka)
        $jumlahKelasDibuka = $schedules->count();

        // Ambil semua rayon (untuk dropdown filter)
        $rayons = \App\Models\Rayon::all();

        return view('kom_digital.schedules.index', compact('schedules', 'rayons', 'jumlahKelasDibuka', 'rayonId'));
    }

    public function create()
    {
        // Ambil user yang sedang login di guard 'kom'
        $user = Auth::guard('kom')->user();

        // Ambil rayon_id dari user login
        $rayonId = $user->rayon_id;

        // Ambil pengajar yang aktif dan rayon_id sesuai dengan user login
        $users = KomUser::where('status', 'aktif')
            ->where('role', 'pengajar')
            ->where('rayon_id', $rayonId)
            ->get();

        $rayons = Rayon::where('status', 1)->get();
        $classes = KomClass::where('status', 1)->get();
        $moduls = KomModul::where('status', 1)->get();

        return view('kom_digital.schedules.create', compact('users', 'rayons', 'classes', 'moduls'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'pengajar_id' => 'required|exists:kom_users,id',
            'kom_class_id' => 'required|exists:kom_class,id',
            'kom_modul_id' => 'required|exists:kom_moduls,id',
            'datetime' => 'required|date',
            'description' => 'nullable|string',
            'quota' => 'required|integer|min:1',
            'status' => 'required|string|in:aktif,nonaktif',
        ]);

        $user = Auth::guard('kom')->user(); // User yang login

        // Pastikan pengajar yang dipilih memang ada dan sesuai rayon (opsional, bisa ditambah)
        $pengajar = KomUser::where('id', $request->pengajar_id)
            ->where('rayon_id', $user->rayon_id)
            ->where('role', 'pengajar')
            ->first();

        if (!$pengajar) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['pengajar_id' => 'Pengajar tidak valid atau tidak berada dalam rayon Anda.']);
        }

        $scheduleData = [
            'kom_user_id' => $user->id,
            'pengajar_id' => $request->pengajar_id,
            'rayon_id' => $user->rayon_id,
            'kom_class_id' => $request->kom_class_id,
            'kom_modul_id' => $request->kom_modul_id,
            'datetime' => $request->datetime,
            'description' => $request->description,
            'quota' => $request->quota,
            'status' => $request->status,
        ];

        KomSchedule::create($scheduleData);

        return redirect()->route('kom.schedulesbyuser.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }



    public function edit($id)
    {
        $schedule = KomSchedule::findOrFail($id);
        $user = Auth::guard('kom')->user();

        $komUsers = KomUser::where('role', 'pengajar')
            ->where('rayon_id', $user->rayon_id)
            ->where('status', 1)
            ->get();

        $rayons = Rayon::where('status', 1)->get();
        $komClasses = KomClass::where('status', 1)->get();
        $komModuls = KomModul::where('status', 1)->get();

        return view('kom_digital.schedules.edit', compact('schedule', 'komUsers', 'rayons', 'komClasses', 'komModuls'));
    }



    public function update(Request $request, $id)
    {
        $schedule = KomSchedule::findOrFail($id);
        $user = Auth::guard('kom')->user();

        $request->validate([
            'pengajar_id' => 'required|exists:kom_users,id',
            'kom_class_id' => 'required|exists:kom_class,id',
            'kom_modul_id' => 'required|exists:kom_moduls,id',
            'datetime' => 'required|date',
            'description' => 'nullable|string',
            'quota' => 'required|integer|min:1',
            'status' => 'required|string|in:aktif,nonaktif,cancel',
        ]);

        // Validasi pengajar_id sesuai rayon dan role pengajar
        $pengajar = KomUser::where('id', $request->pengajar_id)
            ->where('role', 'pengajar')
            ->where('rayon_id', $user->rayon_id)
            ->first();

        if (!$pengajar) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['pengajar_id' => 'Pengajar tidak valid atau tidak berada dalam rayon Anda.']);
        }

        // Update data jadwal
        $schedule->update([
            'pengajar_id' => $request->pengajar_id,
            'rayon_id' => $user->rayon_id,
            'kom_class_id' => $request->kom_class_id,
            'kom_modul_id' => $request->kom_modul_id,
            'datetime' => $request->datetime,
            'description' => $request->description,
            'quota' => $request->quota,
            'status' => $request->status,
        ]);

        // Update status pada tabel kom_kelas_tutorial sesuai schedule id
        \DB::table('kom_kelas_tutorial')
            ->where('kom_schedules_id', $schedule->id)
            ->update(['status' => $schedule->status]);

        return redirect()->route('kom.schedulesbyuser.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    // public function updateRayon(Request $request)
    // {
    //     $request->validate([
    //         'rayons_id' => 'required|exists:rayons,id',
    //     ]);

    //     try {
    //         $komUserId = Auth::guard('kom')->id();
    //         $newRayonId = $request->rayons_id;

    //         // Update KelasTutorial (tanpa filter status)
    //         KelasTutorial::where('kom_user_id', $komUserId)
    //             ->update([
    //                 'rayons_id' => $newRayonId,
    //                 'status' => 'aktif'
    //             ]);

    //         // Update semua KomPertanyaanUser
    //         KomPertanyaanUser::where('kom_user_id', $komUserId)
    //             ->update(['rayons_id' => $newRayonId]);

    //         // Update semua KomDonasi (paid & tipe KOM)
    //         KomDonasi::where('kom_user_id', $komUserId)
    //             ->where('donation_type', 'KOM')
    //             ->where('payment_status', 'paid')
    //             ->update(['kom_rayons_id' => $newRayonId]);

    //         // Update KomZoomHistory berdasarkan kom_donasi milik user
    //         // Ambil kom_donasi yang sudah update kom_rayons_id baru
    //         $donasiIds = KomDonasi::where('kom_user_id', $komUserId)
    //             ->where('donation_type', 'KOM')
    //             ->where('payment_status', 'paid')
    //             ->pluck('id');

    //         KomZoomHistory::where('kom_user_id', $komUserId)
    //             ->whereIn('kom_donasi_id', $donasiIds)
    //             ->update(['kom_rayons_id' => $newRayonId]);

    //         return redirect()->back()->with('success', 'Rayon berhasil diperbarui di semua data terkait.');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Gagal memperbarui rayon: ' . $e->getMessage());
    //     }
    // }
    public function updateRayon(Request $request)
    {
        $request->validate([
            'rayons_id' => 'required|exists:rayons,id',
            'kom_schedules_id' => 'required|exists:kom_jadwal,id',
        ]);


        try {
            $komUserId = Auth::guard('kom')->id();
            $newRayonId = $request->rayons_id;
            $newScheduleId = $request->kom_schedules_id;

            // ✅ Update KelasTutorial
            KelasTutorial::where('kom_user_id', $komUserId)
                ->update([
                    'rayons_id' => $newRayonId,
                    'kom_schedules_id' => $newScheduleId,
                    'status' => 'aktif'
                ]);

            // ✅ Update KomPertanyaanUser
            KomPertanyaanUser::where('kom_user_id', $komUserId)
                ->update([
                    'rayons_id' => $newRayonId,
                    'kom_schedules_id' => $newScheduleId
                ]);

            // ✅ Update KomDonasi
            KomDonasi::where('kom_user_id', $komUserId)
                ->where('donation_type', 'KOM')
                ->where('payment_status', 'paid')
                ->update([
                    'kom_rayons_id' => $newRayonId,
                    'kom_schedules_id' => $newScheduleId
                ]);

            // ✅ Update KomZoomHistory
            $zoom = KomZoom::where('rayons_id', $newRayonId)
                ->where('kom_schedules_id', $newScheduleId)
                ->first();

            if ($zoom) {
                KomZoomHistory::where('kom_user_id', $komUserId)
                    ->update([
                        'kom_zoom_id' => $zoom->id
                    ]);
            }

            KomUser::where('id', $komUserId)->update([
                'rayon_id' => $request->rayons_id
            ]);

            return redirect()->back()->with('success', 'Rayon & jadwal berhasil diperbarui di semua data terkait.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }




}
