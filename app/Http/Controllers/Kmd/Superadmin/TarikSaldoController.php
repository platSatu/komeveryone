<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Superadmin\TarikSaldoService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class TarikSaldoController extends Controller
{
    protected $service;

    public function __construct(TarikSaldoService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['user_id', 'cabang_id', 'status', 'tanggal_penarikan']);
        $tarikSaldos = $this->service->getAll($filters);

        return view('a_kom_digital_pusat.superadmin.tarik_saldo.index', compact('tarikSaldos', 'filters'));
    }

    public function create()
    {
        return view('a_kom_digital_pusat.superadmin.tarik_saldo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string|exists:kmd_users,id',
            'cabang_id' => 'required|string|exists:kmd_cabangs,id',
            'donasi_id' => 'nullable|string|exists:kmd_donations,id',
            'userclasstutorial_id' => 'nullable|string|exists:kmd_user_class_tutorial,id',
            'tanggal_penarikan' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
            'status' => 'required|string',
        ]);

        try {
            $this->service->store($request->all());
            return redirect()->route('kom.super_tarik-saldo.index')->with('success', 'Data tarik saldo berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan tarik saldo: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $tarikSaldo = $this->service->find($decryptedId);
            return view('a_kom_digital_pusat.superadmin.tarik_saldo.edit', compact('tarikSaldo'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit tarik saldo: ' . $e->getMessage());
            return redirect()->route('kom.super_tarik-saldo.index')->with('error', 'Data tidak ditemukan.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|string|exists:kmd_users,id',
            'cabang_id' => 'required|string|exists:kmd_cabangs,id',
            'donasi_id' => 'nullable|string|exists:kmd_donations,id',
            'userclasstutorial_id' => 'nullable|string|exists:kmd_user_class_tutorial,id',
            'tanggal_penarikan' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
            'status' => 'required|string',
        ]);

        try {
            $this->service->update(Crypt::decrypt($id), $request->all());
            return redirect()->route('kom.super_tarik-saldo.index')->with('success', 'Data tarik saldo berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate tarik saldo: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete(Crypt::decrypt($id));
            return redirect()->route('kom.super_tarik-saldo.index')->with('success', 'Data tarik saldo berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus tarik saldo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
