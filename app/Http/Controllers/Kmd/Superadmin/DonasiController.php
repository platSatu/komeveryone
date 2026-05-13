<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Services\Superadmin\DonasiService;
use App\Models\Kmd\KmdUserClassTutorial;
use App\Models\Kmd\KmdModuls;
use App\Models\Kmd\KmdClass;
use App\Models\Kmd\Cabang;
use App\Models\Kmd\KomUser;


class DonasiController extends Controller
{
    protected $service;

    public function __construct(DonasiService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
{
    $filters = $request->only([
        'user_id',
        'modul_id',
        'class_id',
        'cabang_id',
        'transaction_status',
        'search', // ✅ tambahkan search
    ]);

    $donasi = $this->service->getAll($filters);

    return view('a_kom_digital_pusat.superadmin.donasi.index', compact('donasi', 'filters'));
}

public function search(Request $request)
    {
        $search = $request->input('q');

        $results = KomUser::where('name', 'like', "%{$search}%")
            ->select('id', 'name')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json($results);
    }


    public function create()
{
    return view('a_kom_digital_pusat.superadmin.donasi.create', [
        'users' => KomUser::all(),
        'userClassTutorials' => KmdUserClassTutorial::all(),
        'moduls' => KmdModuls::all(),
        'classes' => KmdClass::all(),
        'cabangs' => Cabang::all(),
    ]);
}

    public function store(Request $request)
    {
        try {
            $this->service->store($request->all());
            return redirect()->route('donasi.index')->with('success', 'Data donasi berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan donasi: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $donasi = $this->service->find($decryptedId);
            return view('a_kom_digital_pusat.superadmin.donasi.edit', compact('donasi'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit donasi: ' . $e->getMessage());
            return redirect()->route('donasi.index')->with('error', 'Data tidak ditemukan.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->service->update(Crypt::decrypt($id), $request->all());
            return redirect()->route('kom.super_persembahan.index')->with('success', 'Data donasi berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate donasi: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete(Crypt::decrypt($id));
            return redirect()->route('donasi.index')->with('success', 'Data donasi berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus donasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
