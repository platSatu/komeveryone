<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Services\Superadmin\CabangService;

class CabangController extends Controller
{
    protected $service;

    public function __construct(CabangService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $data = $this->service->getAll($filters);

        return view('a_kom_digital_pusat.superadmin.rayon.index', compact('data', 'filters'));
    }

    public function create()
    {
        return view('a_kom_digital_pusat.superadmin.rayon.create');
    }

    public function store(Request $request)
    {
        try {
            $this->service->store($request->all());
            return redirect()->route('rayon.index')->with('success', 'Data berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan cabang: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $data = $this->service->find($decryptedId);

            return view('a_kom_digital_pusat.superadmin.rayon.edit', compact('data'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit cabang: ' . $e->getMessage());
            return redirect()->route('rayon.index')->with('error', 'Data tidak ditemukan.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->service->update(Crypt::decrypt($id), $request->all());
            return redirect()->route('rayon.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate cabang: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete(Crypt::decrypt($id));
            return redirect()->route('rayon.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus cabang: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
