<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kmd\Superadmin\KmdModulRequest; // jangan lupa buat request validasinya
use App\Services\Superadmin\KmdModulService;
use Illuminate\Http\Request;
use App\Models\Kmd\KmdClass;
use Illuminate\Support\Facades\Log;

class ModulController extends Controller
{
    protected $service;

    public function __construct(KmdModulService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $data = $this->service->getAll($filters);

        return view('a_kom_digital_pusat.superadmin.modul.index', compact('data', 'filters'));
    }

    public function create()
    {
        $classes = KmdClass::where('status', 'active')->get(); // hanya kelas aktif (opsional)
        return view('a_kom_digital_pusat.superadmin.modul.create', compact('classes'));
    }

    public function store(KmdModulRequest $request)
    {
        try {
            $this->service->store($request->validated());
            return redirect()->route('kom.super_kom-modul.index')->with('success', 'Modul berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan modul: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan modul.');
        }
    }

    public function edit($id)
    {
        try {
            $data = $this->service->find($id);
            return view('a_kom_digital_pusat.superadmin.modul.edit', compact('data'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit modul: ' . $e->getMessage());
            return redirect()->route('kom.super_kom-modul.index')->with('error', 'Terjadi kesalahan saat menampilkan form edit.');
        }
    }

    public function update(KmdModulRequest $request, $id)
    {
        try {
            $this->service->update($id, $request->validated());
            return redirect()->route('kom.super_kom-modul.index')->with('success', 'Modul berhasil diupdate.');
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate modul: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate modul.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return redirect()->route('kom.super_kom-modul.index')->with('success', 'Modul berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus modul: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus modul.');
        }
    }
}
