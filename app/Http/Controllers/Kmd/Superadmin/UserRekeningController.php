<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Services\Superadmin\UserRekeningService;

class UserRekeningController extends Controller
{
    protected $service;

    public function __construct(UserRekeningService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['user_id', 'cabang_id', 'nama_rek']);
        $data = $this->service->getAll($filters);

        return view('a_kom_digital_pusat.superadmin.rekening.index', compact('data', 'filters'));
    }

    public function create()
    {
        return view('a_kom_digital_pusat.superadmin.rekening.create');
    }

    public function store(Request $request)
    {
        try {
            $this->service->store($request->all());
            return redirect()->route('rekening.index')->with('success', 'Data rekening berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan rekening: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $data = $this->service->find($decryptedId);
            return view('a_kom_digital_pusat.superadmin.rekening.edit', compact('data'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit rekening: ' . $e->getMessage());
            return redirect()->route('rekening.index')->with('error', 'Data tidak ditemukan.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->service->update(Crypt::decrypt($id), $request->all());
            return redirect()->route('rekening.index')->with('success', 'Data rekening berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate rekening: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete(Crypt::decrypt($id));
            return redirect()->route('rekening.index')->with('success', 'Data rekening berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus rekening: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
