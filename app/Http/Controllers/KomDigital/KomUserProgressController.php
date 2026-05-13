<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomVideo;
use App\Models\Kom\KomPertanyaan;
use App\Models\Kom\KomModul;
use App\Models\Kom\KomUserProgress;


class KomUserProgressController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $progress = KomUserProgress::where('user_id', $userId)
                    ->with(['modul', 'video'])
                    ->get();

        return view('kom_digital.progress.index', compact('progress'));
    }

    // Tampilkan detail progress tertentu (by ID)
    public function show($id)
    {
        $userId = auth()->id();
        $progress = KomUserProgress::where('id', $id)
                    ->where('user_id', $userId)
                    ->with(['modul', 'video', 'pertanyaan'])
                    ->firstOrFail();

        return view('kom_digital.progress.show', compact('progress'));
    }

    // Update status progress (misalnya setelah menjawab soal)
    public function update(Request $request, $id)
    {
        $userId = auth()->id();

        $progress = KomUserProgress::where('id', $id)
                    ->where('user_id', $userId)
                    ->firstOrFail();

        $request->validate([
            'status' => 'required|in:selesai,belum_selesai',
        ]);

        $progress->update([
            'status' => $request->status,
            'waktu_selesai' => $request->status === 'selesai' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Progress berhasil diperbarui.');
    }

    // Hapus progress (optional)
    public function destroy($id)
    {
        $userId = auth()->id();

        $progress = KomUserProgress::where('id', $id)
                    ->where('user_id', $userId)
                    ->firstOrFail();

        $progress->delete();

        return redirect()->route('kom.progress.index')->with('success', 'Progress berhasil dihapus.');
    }
}
