<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomJawaban;
use App\Models\Kom\KomPertanyaan;
use Illuminate\Pagination\LengthAwarePaginator;


class KomJawabanController extends Controller
{
    public function index()
{
    // Ambil semua jawaban lengkap dengan relasi pertanyaan
    $allJawabans = KomJawaban::with('pertanyaan')
        ->get()
        ->groupBy('pertanyaan_id');

    // Ubah jadi koleksi pertanyaan yang terisi jawabannya
    $grouped = $allJawabans->map(function ($jawabans, $pertanyaanId) {
        return [
            'pertanyaan' => $jawabans->first()->pertanyaan,
            'jawabans' => $jawabans
        ];
    })->values(); // Reset key jadi 0,1,2,...

    // Manual pagination (karena kita sudah groupBy collection)
    $perPage = 5;
    $currentPage = request()->get('page', 1);
    $currentItems = $grouped->slice(($currentPage - 1) * $perPage, $perPage);
    $paginated = new LengthAwarePaginator(
        $currentItems,
        $grouped->count(),
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    return view('kom_digital.jawaban.index', ['pertanyaanJawaban' => $paginated]);
}


    public function create()
    {
        $pertanyaans = KomPertanyaan::all();
        return view('kom_digital.jawaban.create', compact('pertanyaans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan_id' => 'required|exists:kom_pertanyaan,id',
            'jawaban' => 'required|string|max:1000',
            'is_benar' => 'required|boolean',
        ]);

        KomJawaban::create([
            'pertanyaan_id' => $request->pertanyaan_id,
            'jawaban' => $request->jawaban,
            'is_benar' => $request->is_benar,
        ]);

        return redirect()->route('kom.jawaban.index')->with('success', 'Jawaban berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $jawaban = KomJawaban::findOrFail($id);
        $pertanyaans = KomPertanyaan::all();
        return view('kom_digital.jawaban.edit', compact('jawaban', 'pertanyaans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pertanyaan_id' => 'required|exists:kom_pertanyaan,id',
            'jawaban' => 'required|string|max:1000',
            'is_benar' => 'required|boolean',
        ]);

        $jawaban = KomJawaban::findOrFail($id);
        $jawaban->update([
            'pertanyaan_id' => $request->pertanyaan_id,
            'jawaban' => $request->jawaban,
            'is_benar' => $request->is_benar,  // Pastikan nilai is_benar tetap 0 atau 1
        ]);

        return redirect()->route('kom.jawaban.index')->with('success', 'Jawaban berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $jawaban = KomJawaban::findOrFail($id);
        $jawaban->delete();

        return redirect()->route('kom.jawaban.index')->with('success', 'Jawaban berhasil dihapus.');
    }
}
