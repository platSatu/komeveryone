<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KelasTutorial;
use App\Models\Kom\KomPertanyaanUser;
use App\Models\Kom\KomZoom;
use App\Models\Kom\KomDonasi;
use App\Models\Kom\KomBook;
use Illuminate\Support\Facades\Auth;

class PesertaController extends Controller
{
    public function index()
    {
        $user = Auth::guard('kom')->user();

        // Ambil kelas dengan status aktif dan pending
        $kelas = KelasTutorial::where('kom_user_id', $user->id)
            ->whereIn('status', ['aktif', 'pending'])
            ->get();

        return view('kom_digital.peserta.index', compact('kelas'));
    }



    public function pertanyaanSaya()
    {
        $user = Auth::guard('kom')->user();

        $pertanyaan = KomPertanyaanUser::with(['modul', 'kelas', 'rayon', 'schedule'])
            ->where('kom_user_id', $user->id)
            ->orderByDesc('tgl_pertanyaan')
            ->paginate(10); // ← menampilkan 10 data per halaman

        return view('kom_digital.peserta.pertanyaan', compact('pertanyaan'));
    }

    //     public function zoomSaya()
// {
//     $user = auth('kom')->user();

    //     $donasi = KomDonasi::where('kom_user_id', $user->id)
//         ->where('payment_status', 'paid')
//         ->get();

    //     if ($donasi->isEmpty()) {
//         return view('kom_digital.peserta.zoom', ['zoom' => collect(), 'checked' => false]);
//     }

    //     $scheduleIds = $donasi->pluck('kom_schedules_id')->filter()->unique();
//     $modulIds    = $donasi->pluck('kom_moduls_id')->filter()->unique();
//     $classIds    = $donasi->pluck('kom_class_id')->filter()->unique();
//     $rayonIds    = $donasi->pluck('kom_rayons_id')->filter()->unique();

    //    $zoom = KomZoom::with(['modul', 'kelas', 'rayon', 'schedule'])
//     ->whereIn('kom_schedules_id', $scheduleIds)
//     ->whereIn('kom_moduls_id', $modulIds)
//     ->whereIn('kom_class_id', $classIds)
//     ->whereIn('rayons_id', $rayonIds)
//     ->where('status', 'aktif')
//     ->paginate(10);


    //     return view('kom_digital.peserta.zoom', [
//         'zoom' => $zoom,
//         'checked' => true
//     ]);
// }
    public function zoomSaya()
    {
        $user = auth('kom')->user();

        $donasi = KomDonasi::where('kom_user_id', $user->id)
            ->where('payment_status', 'paid')
            ->get();

        if ($donasi->isEmpty()) {
            return view('kom_digital.peserta.zoom', ['zoom' => collect(), 'checked' => false]);
        }

        // Ambil semua ID donasi yang sudah paid
        $donasiIds = $donasi->pluck('id');

        // Ambil histori Zoom berdasarkan donasi yang dibayar oleh user ini
        $zoom = \App\Models\Kom\KomZoomHistory::with(['zoom.modul', 'zoom.kelas', 'zoom.rayon', 'zoom.schedule'])
            ->whereIn('kom_donasi_id', $donasiIds)
            ->orderByDesc('sent_at')
            ->paginate(10);

        return view('kom_digital.peserta.zoom', [
            'zoom' => $zoom,
            'checked' => true
        ]);
    }





    public function donasiSaya()
    {
        $user = Auth::guard('kom')->user();

        $donasi = KomDonasi::with(['komModul', 'komSchedule', 'rayon']) // include relasi biar gampang di blade
            ->where('kom_user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10); // bisa diganti ke get() jika tidak butuh pagination

        return view('kom_digital.peserta.donasi', compact('donasi'));
    }

    public function bukuSaya()
    {
        $user = Auth::guard('kom')->user();

        $buku = KomBook::with(['komUser', 'komClass', 'komCertificate'])
            ->where('kom_user_id', $user->id)
            ->orderByDesc('tgl_aktifasi')
            ->paginate(10);

        return view('kom_digital.peserta.buku', compact('buku'));
    }


    public function videoProgressSaya()
    {
        $user = auth('kom')->user();

        $progress = \App\Models\Kom\KomUserVideoProgress::with(['video'])
            ->where('kom_user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(10);

        return view('kom_digital.peserta.video_progress', compact('progress'));
    }

    public function sertifikatSaya()
    {
        $user = auth('kom')->user();

        // Di tahap ini hanya menampilkan view statis
        return view('kom_digital.peserta.sertifikat', [
            'user' => $user,
        ]);
    }



}
