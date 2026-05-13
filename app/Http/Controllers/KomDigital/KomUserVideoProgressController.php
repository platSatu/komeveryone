<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomUserVideoProgress;
use App\Models\Kom\KomUser;
use App\Models\Kom\KomVideo;

class KomUserVideoProgressController extends Controller
{
    public function index()
    {
        $progress = KomUserVideoProgress::with(['user', 'video'])->latest()->get();
        return view('kom_digital.user_video_progress.index', compact('progress'));
    }

    public function create()
    {
        $users = KomUser::all();
        $videos = KomVideo::all();
        return view('kom_digital.user_video_progress.create', compact('users', 'videos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kom_user_id' => 'required|exists:kom_users,id',
            'kom_video_id' => 'required|exists:kom_videos,id',
            'is_locked' => 'nullable|boolean',
        ]);

        KomUserVideoProgress::create([
            'kom_user_id' => $request->kom_user_id,
            'kom_video_id' => $request->kom_video_id,
            'is_locked' => $request->has('is_locked') ? $request->is_locked : true,
        ]);

        return redirect()->route('kom.user_video_progress.index')->with('success', 'Progress berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $progress = KomUserVideoProgress::findOrFail($id);
        $users = KomUser::all();
        $videos = KomVideo::all();
        return view('kom_digital.user_video_progress.edit', compact('progress', 'users', 'videos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kom_user_id' => 'required|exists:kom_users,id',
            'kom_video_id' => 'required|exists:kom_videos,id',
            'is_locked' => 'nullable|boolean',
        ]);

        $progress = KomUserVideoProgress::findOrFail($id);
        $progress->update([
            'kom_user_id' => $request->kom_user_id,
            'kom_video_id' => $request->kom_video_id,
            'is_locked' => $request->has('is_locked') ? $request->is_locked : true,
        ]);

        return redirect()->route('kom.user_video_progress.index')->with('success', 'Progress berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $progress = KomUserVideoProgress::findOrFail($id);
        $progress->delete();

        return redirect()->route('kom.user_video_progress.index')->with('success', 'Progress berhasil dihapus.');
    }
}
