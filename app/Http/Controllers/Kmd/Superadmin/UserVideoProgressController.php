<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\Superadmin\KmdUserVideoProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Kmd\Superadmin\UserVideoProgressRequest;
use Illuminate\Support\Facades\Crypt;
use App\Models\Kmd\KmdVideo;

class UserVideoProgressController extends Controller
{
    protected $service;

    public function __construct(KmdUserVideoProgressService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->getAll($request->all());
        return view('a_kom_digital_pusat.superadmin.videoprogress.index', compact('data'));
    }

    public function create()
    {
        $videos = KmdVideo::all();
        return view('a_kom_digital_pusat.superadmin.videoprogress.create', compact('videos'));
    }

    public function store(UserVideoProgressRequest $request)
    {
        try {
            $this->service->store($request->validated());
            return redirect()->route('kom.super_user_video_progress.index')->with('success', 'Progress video berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan progress video: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        try {
            $data = $this->service->find(Crypt::decrypt($id));
            $videos = KmdVideo::all();
            return view('a_kom_digital_pusat.superadmin.videoprogress.edit', compact('data', 'videos'));
        } catch (\Exception $e) {
            Log::error('Gagal membuka form edit progress: ' . $e->getMessage());
            return redirect()->route('kom.super_user_video_progress.index')->with('error', 'Data tidak ditemukan.');
        }
    }

    public function update(UserVideoProgressRequest $request, $id)
    {
        try {
            $this->service->update(Crypt::decrypt($id), $request->validated());
            return redirect()->route('kom.super_user_video_progress.index')->with('success', 'Progress berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat update data.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete(Crypt::decrypt($id));
            return redirect()->route('kom.super_user_video_progress.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal hapus progress: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
