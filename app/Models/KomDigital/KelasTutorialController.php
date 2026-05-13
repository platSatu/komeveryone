<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KelasTutorial;
use App\Models\Kom\KomUser;

class KelasTutorialController extends Controller
{
    public function index()
    {
        $data = KelasTutorial::latest()->get();
        return view('kom_digital.bookingkelas.index', compact('data'));
    }

    public function create()
    {
        return view('kom_digital.bookingkelas.create');
    }

    public function edit($id)
    {
        $kelas = KelasTutorial::findOrFail($id);
        return view('kom_digital.bookingkelas.edit', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kom_schedules_id' => 'required|exists:kom_jadwal,id',
            'kom_moduls_id' => 'required|exists:kom_moduls,id',
            'kom_class_id' => 'required|exists:kom_class,id',
            'rayons_id' => 'required|exists:rayons,id',
            'tgl_booking' => 'required|date',
        ]);

        $userId = auth('kom')->id();

        KelasTutorial::create([
            'kom_user_id' => $userId,
            'kom_schedules_id' => $request->kom_schedules_id,
            'kom_moduls_id' => $request->kom_moduls_id,
            'kom_class_id' => $request->kom_class_id,
            'rayons_id' => $request->rayons_id,
            'tgl_booking' => $request->tgl_booking,
            'status' => 'aktif',
        ]);

        KomUser::where('id', $userId)->update([
            'rayon_id' => $request->rayons_id
        ]);

        return redirect()->route('kom.pertanyaan_peserta.create', $request->kom_schedules_id)
            ->with('success', 'Kelas berhasil dibooking!');
    }

    public function updateStatus($id)
    {
        $kelas = KelasTutorial::findOrFail($id);

        $kelas->update([
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Status peserta berhasil diubah menjadi pending.');
    }

}
