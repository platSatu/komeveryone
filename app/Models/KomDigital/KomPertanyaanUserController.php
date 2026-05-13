<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomPertanyaanUser;
use App\Models\Kom\KomSchedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class KomPertanyaanUserController extends Controller
{
    public function indexUserPertanyaan()
    {
        $user = auth('kom')->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Ambil semua pertanyaan milik siswa yang berada dalam rayon yang sama dengan user login
        $pertanyaans = KomPertanyaanUser::where('rayons_id', $user->rayon_id)
            ->orderBy('tgl_pertanyaan', 'desc')
            ->paginate(10); // Paginate 10 items per page

        return view('kom_digital.pertanyaan_user.indexUser', [
            'pertanyaans' => $pertanyaans
        ]);
    }



    public function index()
    {
        $data = KomPertanyaanUser::latest()->get();
        return view('kom_digital.pertanyaan.index', compact('data'));
    }

    public function create($id)
    {
        // Mendapatkan jadwal berdasarkan ID
        $schedule = KomSchedule::findOrFail($id);

        // Menampilkan form pertanyaan dengan data jadwal
        return view('kom_digital.pertanyaan_user.create', compact('schedule'));
    }

    public function store(Request $request)
    {
        // Validasi input untuk tiga pertanyaan
        $request->validate([
            'pertanyaan_1' => 'required|string',
            'pertanyaan_2' => 'required|string',
            'pertanyaan_3' => 'required|string',
        ]);

        try {
            // Ambil ID user yang sedang login
            $komUserId = Auth::guard('kom')->id();

            if (!$komUserId) {
                return response()->json(['error' => 'Anda belum login'], 401);
            }

            // Daftar pertanyaan yang akan disimpan
            $pertanyaanList = [
                $request->pertanyaan_1,
                $request->pertanyaan_2,
                $request->pertanyaan_3
            ];

            // Simpan data pertanyaan
            foreach ($pertanyaanList as $item) {
                KomPertanyaanUser::create([
                    'kom_user_id' => $komUserId,
                    'kom_schedules_id' => $request->kom_schedules_id,
                    'kom_class_id' => $request->kom_class_id,
                    'kom_moduls_id' => $request->kom_moduls_id,
                    'rayons_id' => $request->rayons_id,
                    'pertanyaan' => $item,
                    'tgl_pertanyaan' => now(),
                    'status' => 'pending',
                ]);
            }

            // Setelah berhasil, arahkan ke halaman donasi
            return redirect()->route('kom.donasi.index')->with('success', 'Pertanyaan berhasil dikirim.');

        } catch (\Exception $e) {
            // Jika terjadi kesalahan
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        $data = KomPertanyaanUser::findOrFail($id);
        return view('kom_digital.pertanyaan.edit', compact('data'));
    }


}
