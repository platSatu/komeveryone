<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomVideoPertanyaan;
use App\Models\Kom\KomVideo;
use App\Models\Kom\KomPertanyaan;

class KomVideoPertanyaanController extends Controller
{
    public function index()
{
    // Ambil semua video pertanyaan beserta relasi video dan pertanyaan
    $allData = KomVideoPertanyaan::with(['video', 'pertanyaan'])->latest()->get();

    // Group berdasarkan kom_video_id
    $grouped = $allData->groupBy('kom_video_id');

    return view('kom_digital.video_pertanyaan.index', ['videoPertanyaan' => $grouped]);
}



    public function create()
    {
        $videos = KomVideo::all();
        $pertanyaans = KomPertanyaan::all();
        return view('kom_digital.video_pertanyaan.create', compact('videos', 'pertanyaans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kom_video_id' => 'required|exists:kom_videos,id',
            'pertanyaan_id' => 'required|exists:kom_pertanyaan,id',
        ]);

        KomVideoPertanyaan::create([
            'kom_video_id' => $request->kom_video_id,
            'pertanyaan_id' => $request->pertanyaan_id,
        ]);

        return redirect()->route('kom.video_pertanyaan.index')->with('success', 'Relasi Video & Pertanyaan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $videoPertanyaan = KomVideoPertanyaan::findOrFail($id);
        $videos = KomVideo::all();
        $pertanyaans = KomPertanyaan::all();
        return view('kom_digital.video_pertanyaan.edit', compact('videoPertanyaan', 'videos', 'pertanyaans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kom_video_id' => 'required|exists:kom_videos,id',
            'pertanyaan_id' => 'required|exists:kom_pertanyaans,id',
        ]);

        $videoPertanyaan = KomVideoPertanyaan::findOrFail($id);
        $videoPertanyaan->update([
            'kom_video_id' => $request->kom_video_id,
            'pertanyaan_id' => $request->pertanyaan_id,
        ]);

        return redirect()->route('kom.video_pertanyaan.index')->with('success', 'Relasi Video & Pertanyaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $videoPertanyaan = KomVideoPertanyaan::findOrFail($id);
        $videoPertanyaan->delete();

        return redirect()->route('kom.video_pertanyaan.index')->with('success', 'Relasi Video & Pertanyaan berhasil dihapus.');
    }
}
