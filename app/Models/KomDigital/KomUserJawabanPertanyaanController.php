<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomUserJawabanPertanyaan;
use App\Models\Kom\KomUser;
use App\Models\Kom\KomPertanyaan;
use App\Models\Kom\KomJawaban;

class KomUserJawabanPertanyaanController extends Controller
{
    public function index()
    {
        $jawabans = KomUserJawabanPertanyaan::with(['user', 'pertanyaan', 'jawaban'])->latest()->paginate(10);
        return view('kom_digital.user_jawaban_pertanyaan.index', compact('jawabans'));
    }


    public function create()
    {
        $users = KomUser::all();
        $pertanyaans = KomPertanyaan::all();
        $jawabans = KomJawaban::all();
        return view('kom_digital.user_jawaban_pertanyaan.create', compact('users', 'pertanyaans', 'jawabans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kom_user_id' => 'required|exists:kom_users,id',
            'pertanyaan_id' => 'required|exists:kom_pertanyaan,id',
            'jawaban_id' => 'required|exists:kom_jawaban,id',
            'jawaban_benar' => 'nullable|boolean',
        ]);

        KomUserJawabanPertanyaan::create([
            'kom_user_id' => $request->kom_user_id,
            'pertanyaan_id' => $request->pertanyaan_id,
            'jawaban_id' => $request->jawaban_id,
            'jawaban_benar' => $request->has('jawaban_benar') ? $request->jawaban_benar : false,
        ]);

        return redirect()->route('kom.user_jawaban_pertanyaan.index')->with('success', 'Jawaban berhasil disimpan.');
    }

    public function edit($id)
    {
        $jawabanUser = KomUserJawabanPertanyaan::findOrFail($id);
        $users = KomUser::all();
        $pertanyaans = KomPertanyaan::all();
        $jawabans = KomJawaban::all();

        return view('kom_digital.user_jawaban_pertanyaan.edit', compact('jawabanUser', 'users', 'pertanyaans', 'jawabans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kom_user_id' => 'required|exists:kom_users,id',
            'pertanyaan_id' => 'required|exists:kom_pertanyaan,id',
            'jawaban_id' => 'required|exists:kom_jawaban,id',
            'jawaban_benar' => 'nullable|boolean',
        ]);

        $jawabanUser = KomUserJawabanPertanyaan::findOrFail($id);
        $jawabanUser->update([
            'kom_user_id' => $request->kom_user_id,
            'pertanyaan_id' => $request->pertanyaan_id,
            'jawaban_id' => $request->jawaban_id,
            'jawaban_benar' => $request->has('jawaban_benar') ? $request->jawaban_benar : false,
        ]);

        return redirect()->route('kom.user_jawaban_pertanyaan.index')->with('success', 'Jawaban berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jawabanUser = KomUserJawabanPertanyaan::findOrFail($id);
        $jawabanUser->delete();

        return redirect()->route('kom.user_jawaban_pertanyaan.index')->with('success', 'Jawaban berhasil dihapus.');
    }
}
