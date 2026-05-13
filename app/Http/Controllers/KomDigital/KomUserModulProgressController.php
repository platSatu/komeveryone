<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomUserModulProgress;
use App\Models\Kom\KomUser;
use App\Models\Kom\KomModul;
use App\Models\Kom\KomVideo;

class KomUserModulProgressController extends Controller
{
    public function index()
    {
        $progresses = KomUserModulProgress::with(['user', 'modul', 'lastVideo'])->latest()->get();
        return view('kom_digital.user_modul_progress.index', compact('progresses'));
    }

    public function create()
    {
        $users = KomUser::all();
        $moduls = KomModul::all();
        $videos = KomVideo::all();
        return view('kom_digital.user_modul_progress.create', compact('users', 'moduls', 'videos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kom_user_id'     => 'required|exists:kom_users,id',
            'kom_modul_id'    => 'required|exists:kom_moduls,id',
            'last_video_id'   => 'nullable|exists:kom_videos,id',
            'video_terakhir'  => 'required|integer|min:0',
        ]);

        KomUserModulProgress::create($request->only([
            'kom_user_id', 'kom_modul_id', 'last_video_id', 'video_terakhir'
        ]));

        return redirect()->route('kom.user_modul_progress.index')->with('success', 'Progress berhasil disimpan.');
    }

    public function edit($id)
    {
        $progress = KomUserModulProgress::findOrFail($id);
        $users = KomUser::all();
        $moduls = KomModul::all();
        $videos = KomVideo::all();
        return view('kom_digital.user_modul_progress.edit', compact('progress', 'users', 'moduls', 'videos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kom_user_id'     => 'required|exists:kom_users,id',
            'kom_modul_id'    => 'required|exists:kom_moduls,id',
            'last_video_id'   => 'nullable|exists:kom_videos,id',
            'video_terakhir'  => 'required|integer|min:0',
        ]);

        $progress = KomUserModulProgress::findOrFail($id);
        $progress->update($request->only([
            'kom_user_id', 'kom_modul_id', 'last_video_id', 'video_terakhir'
        ]));

        return redirect()->route('kom.user_modul_progress.index')->with('success', 'Progress berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $progress = KomUserModulProgress::findOrFail($id);
        $progress->delete();

        return redirect()->route('kom.user_modul_progress.index')->with('success', 'Progress berhasil dihapus.');
    }
}
