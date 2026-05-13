<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\Superadmin\KmdProgressModulService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Models\Kmd\KomUser;
use App\Models\Kmd\KmdModuls;

class ProgressModulController extends Controller
{
    protected $service;

    public function __construct(KmdProgressModulService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status']);
        $data = $this->service->getAll($filters);

        return view('a_kom_digital_pusat.superadmin.progress_modul.index', compact('data', 'filters'));
    }

    public function create()
    {
        $users = KomUser::all();
        $moduls = KmdModuls::all();
        return view('a_kom_digital_pusat.superadmin.progress_modul.create', compact('users', 'moduls'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:kmd_users,id',
            'moduls_id' => 'required|exists:kmd_moduls,id',
            'status' => 'required|string|in:active,inactive',
        ]);

        try {
            $this->service->store($validated);
            return redirect()->route('kom.progress_modul.index')->with('success', 'Progress modul berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan progress modul: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        try {
            $id = Crypt::decrypt($id);
            $data = $this->service->find($id);
            $users = KomUser::all();
            $moduls = KmdModuls::all();

            return view('a_kom_digital_pusat.superadmin.progress_modul.edit', compact('data', 'users', 'moduls'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit progress modul: ' . $e->getMessage());
            return redirect()->route('kom.progress_modul.index')->with('error', 'Terjadi kesalahan.');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:kmd_users,id',
            'moduls_id' => 'required|exists:kmd_moduls,id',
            'status' => 'required|string|in:active,inactive',
        ]);

        try {
            $this->service->update(Crypt::decrypt($id), $validated);
            return redirect()->route('kom.progress_modul.index')->with('success', 'Data berhasil diupdate.');
        } catch (\Exception $e) {
            Log::error('Gagal update progress modul: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal mengupdate data.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete(Crypt::decrypt($id));
            return redirect()->route('kom.progress_modul.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal hapus progress modul: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus.');
        }
    }
}
