<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Superadmin\TarikSaldoService;
use App\Services\Payment\BizbosDisbursementService;
use App\Services\Payment\BizbosException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class TarikSaldoController extends Controller
{
    protected $service;

    public function __construct(TarikSaldoService $service, protected BizbosDisbursementService $payout)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['user_id', 'cabang_id', 'status', 'tanggal_penarikan']);
        $tarikSaldos = $this->service->getAll($filters);

        $payoutAktif = $this->payout->aktif();

        return view('a_kom_digital_pusat.superadmin.tarik_saldo.index', compact('tarikSaldos', 'filters', 'payoutAktif'));
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
            if ($this->service->find(Crypt::decrypt($id))->sudahKeBizbos()) {
                return redirect()->back()->with('error', 'Pengajuan ini dikirim lewat Bizbos. Statusnya hanya berubah dari Bizbos (tombol Cek Status).');
            }

            $this->service->update(Crypt::decrypt($id), $request->all());
            return redirect()->route('kom.super_tarik-saldo.index')->with('success', 'Data tarik saldo berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate tarik saldo: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    /** Setujui & kirim dana lewat Bizbos. Aman diklik ulang (idempoten). */
    public function kirim($id)
    {
        try {
            $tarik = $this->payout->kirim(Crypt::decrypt($id), Auth::id());
        } catch (BizbosException $e) {
            return back()->with('error', $e->status >= 500 || $e->status === 408
                ? 'Bizbos sedang bermasalah, status belum pasti. Klik "Cek Status" sebelum melakukan hal lain.'
                : 'Bizbos menolak: ' . $e->getMessage());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('[Tarik Saldo] Kirim ke Bizbos gagal/terputus.', ['id' => $id, 'error' => $e->getMessage()]);

            return back()->with('error', 'Koneksi ke Bizbos terputus, status belum pasti. Klik "Cek Status" sebelum melakukan hal lain.');
        }

        return back()->with('success', 'Dana dikirim lewat Bizbos. Status: ' . $tarik->status_label . '.');
    }

    public function cek($id)
    {
        try {
            $tarik = $this->payout->cek(Crypt::decrypt($id));
        } catch (\Throwable $e) {
            return back()->with('error', 'Status belum bisa dicek: ' . $e->getMessage());
        }

        return back()->with('success', 'Status terbaru: ' . $tarik->status_label . '.');
    }

    public function tolak(Request $request, $id)
    {
        $data = $request->validate(['alasan' => 'required|string|max:255'], ['alasan.required' => 'Alasan penolakan wajib diisi.']);

        try {
            $this->payout->tolak(Crypt::decrypt($id), Auth::id(), $data['alasan']);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pengajuan ditolak. Saldo kelas kembali ke admin cabang.');
    }

    public function destroy($id)
    {
        try {
            if ($this->service->find(Crypt::decrypt($id))->sudahKeBizbos()) {
                return redirect()->back()->with('error', 'Pengajuan yang sudah dikirim lewat Bizbos tidak bisa dihapus.');
            }

            $this->service->delete(Crypt::decrypt($id));
            return redirect()->route('kom.super_tarik-saldo.index')->with('success', 'Data tarik saldo berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus tarik saldo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
