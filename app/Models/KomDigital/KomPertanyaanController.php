<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomPertanyaan;
use App\Models\Kom\KomVideo;

class KomPertanyaanController extends Controller
{
    public function index()
    {
        $pertanyaans = KomPertanyaan::paginate(10);
        return view('kom_digital.pertanyaan.index', compact('pertanyaans'));
    }




    public function create()
    {
        return view('kom_digital.pertanyaan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required|string|max:1000',
        ]);

        KomPertanyaan::create([
            'pertanyaan' => $request->pertanyaan,
        ]);

        return redirect()->route('kom.pertanyaan.index')->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pertanyaan = KomPertanyaan::findOrFail($id);
        return view('kom_digital.pertanyaan.edit', compact('pertanyaan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required|string|max:1000',
        ]);

        $pertanyaan = KomPertanyaan::findOrFail($id);
        $pertanyaan->update([
            'pertanyaan' => $request->pertanyaan,
        ]);

        return redirect()->route('kom.pertanyaan.index')->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pertanyaan = KomPertanyaan::findOrFail($id);
        $pertanyaan->delete();

        return redirect()->route('kom.pertanyaan.index')->with('success', 'Pertanyaan berhasil dihapus.');
    }
}
