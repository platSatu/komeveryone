<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomVideo;
use App\Models\Kom\KomModul;

class KomVideoController extends Controller
{
    public function index()
    {
        $videos = KomVideo::with('komModul')->orderBy('urutan_video')->get();
        return view('kom_digital.video.index', compact('videos'));
    }

    public function create()
    {
        $moduls = KomModul::all();
        return view('kom_digital.video.create', compact('moduls'));
    }

    public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'videos' => 'nullable|file|mimes:mp4,mkv,avi,flv,webm',  // Validasi tipe video
        'urutan_video' => 'required|integer',
        'durasi_video' => 'nullable|string',
        'kom_modul_id' => 'required|exists:kom_moduls,id',
    ]);

    // Menyimpan video jika ada file yang diupload
    $videoPath = null;
    if ($request->hasFile('videos')) {
        // Mengambil file yang diupload
        $videoFile = $request->file('videos');
        
        // Menyimpan file ke direktori public/videos
        $videoPath = $videoFile->move(public_path('videos'), $videoFile->getClientOriginalName());
    }

    // Membuat data KomVideo
    KomVideo::create([
        'name' => $request->name,
        'description' => $request->description,
        'videos' => $videoPath, // Menyimpan path file video
        'url_video' => $request->url_video,
        'urutan_video' => $request->urutan_video,
        'durasi_video' => $request->durasi_video,
        'status' => $request->status, // Jika status ada di form
        'kom_modul_id' => $request->kom_modul_id,
    ]);

    // Redirect ke halaman index dengan pesan sukses
    return redirect()->route('kom.video.index')->with('success', 'Video berhasil ditambahkan.');
}


    public function edit($id)
    {
        $video = KomVideo::findOrFail($id);
        $moduls = KomModul::all();
        return view('kom_digital.video.edit', compact('video', 'moduls'));
    }

    public function update(Request $request, $id)
    {
        $video = KomVideo::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'videos' => 'nullable|string',
            'url_video' => 'nullable|url',
            'urutan_video' => 'required|integer',
            'durasi_video' => 'nullable|string',
            'kom_modul_id' => 'required|exists:kom_moduls,id',
        ]);

        $video->update($request->only(
            'name',
            'description',
            'videos',
            'url_video',
            'urutan_video',
            'durasi_video',
            'status',
            'kom_modul_id'
        ));

        return redirect()->route('kom.video.index')->with('success', 'Video berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $video = KomVideo::findOrFail($id);
        $video->delete();

        return redirect()->route('kom.video.index')->with('success', 'Video berhasil dihapus.');
    }
}
