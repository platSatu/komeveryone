<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kmd\Superadmin\VideoRequest;
use App\Models\Kmd\KmdModuls;
use App\Services\Superadmin\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    protected $service;

    public function __construct(VideoService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        $data = $this->service->getAll($filters);

        return view('a_kom_digital_pusat.superadmin.video.index', compact('data', 'filters'));
    }

    public function create()
    {
        $moduls = KmdModuls::all();
        return view('a_kom_digital_pusat.superadmin.video.create', compact('moduls'));
    }

    public function store(VideoRequest $request)
    {
        try {
            $this->service->store($request->validated());
            return redirect()->route('kom.super_videos.index')->with('success', 'Video berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan video: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan.');
        }
    }

    public function edit($id)
    {
        try {
            $video = $this->service->find(Crypt::decrypt($id));
            $moduls = KmdModuls::all();

            return view('a_kom_digital_pusat.superadmin.video.edit', compact('video', 'moduls'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit video: ' . $e->getMessage());
            return redirect()->route('kom.super_video.index')->with('error', 'Data tidak ditemukan.');
        }
    }

    public function update(VideoRequest $request, $id)
    {
        try {
            $this->service->update(Crypt::decrypt($id), $request->validated());
            return redirect()->route('kom.super_videos.index')->with('success', 'Video berhasil diupdate.');
        } catch (\Exception $e) {
            Log::error('Gagal update video: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan.');
        }
    }

    public function destroy($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $this->service->delete($id);
            return redirect()->route('kom.super_videos.index')->with('success', 'Video berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal hapus video: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan.');
        }
    }

}
