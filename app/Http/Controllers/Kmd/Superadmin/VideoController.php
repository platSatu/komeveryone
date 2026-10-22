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
        $moduls = KmdModuls::with('kelas')->where('language_code', \App\Services\Kmd\BahasaKelolaService::current())->orderBy('order')->get(); // kom_v02
        return view('a_kom_digital_pusat.superadmin.video.create', compact('moduls'));
    }

    public function store(VideoRequest $request)
    {
        try {
            $this->service->store($this->dataVideo($request));
            return redirect()->route('kom.super_videos.index')->with('success', 'Video berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan video: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan.');
        }
    }

    public function edit($id)
    {
        try {
            $video = $this->service->find($this->resolveId($id));
            $moduls = KmdModuls::with('kelas')->where('language_code', \App\Services\Kmd\BahasaKelolaService::current())->orderBy('order')->get(); // kom_v02

            return view('a_kom_digital_pusat.superadmin.video.edit', compact('video', 'moduls'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit video: ' . $e->getMessage());
            return redirect()->route('kom.super_videos.index')->with('error', 'Data tidak ditemukan.');
        }
    }

    public function update(VideoRequest $request, $id)
    {
        try {
            $videoId = $this->resolveId($id);
            $data = $this->dataVideo($request);

            // kom_v02: centang tutorial tidak boleh dilepas jika sudah ada jadwal tutorial di titik ini
            $lama = $this->service->find($videoId);
            if ($lama->has_tutorial && ! $data['has_tutorial']
                && \App\Models\Kmd\KmdClassTutorial::where('video_id', $videoId)->exists()) {
                return redirect()->back()->withInput()->withErrors(['has_tutorial' => __('Centang Kelas Tutorial tidak bisa dilepas karena sudah ada jadwal kelas tutorial untuk video ini.')]);
            }

            $this->service->update($videoId, $data);
            return redirect()->route('kom.super_videos.index')->with('success', 'Video berhasil diupdate.');
        } catch (\Exception $e) {
            Log::error('Gagal update video: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan.');
        }
    }

    public function destroy($encryptedId)
    {
        try {
            $id = $this->resolveId($encryptedId);
            $this->service->delete($id);
            return redirect()->route('kom.super_videos.index')->with('success', 'Video berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal hapus video: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * kom_v02: checkbox yang tidak dicentang tidak terkirim → set eksplisit false.
     */
    protected function dataVideo(VideoRequest $request): array
    {
        return array_merge($request->validated(), [
            'has_tutorial' => $request->boolean('has_tutorial'),
        ]);
    }

    /**
     * kom_v02: daftar video mengirim ID biasa untuk Edit dan ID terenkripsi untuk Hapus.
     * Terima keduanya (sebelumnya tombol Edit selalu gagal karena ID tidak terenkripsi).
     */
    protected function resolveId(string $id): string
    {
        try {
            return Crypt::decrypt($id);
        } catch (\Throwable $e) {
            return $id;
        }
    }
}
