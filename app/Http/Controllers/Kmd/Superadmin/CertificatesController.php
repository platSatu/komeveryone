<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kmd\Superadmin\KmdCertificateRequest; // buat validasi nanti
use App\Services\Superadmin\KmdCertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Kmd\KomUser;
use App\Models\Kmd\KmdCertificates;
use App\Models\Kmd\KmdUserClassTutorial;

use Carbon\Carbon;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificatesController extends Controller
{
    protected $service;

    public function __construct(KmdCertificateService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $data = $this->service->getAll($filters);

        return view('a_kom_digital_pusat.superadmin.sertifikat.index', compact('data', 'filters'));
    }


    public function show($id)
{
    try {
        Log::info("SUPERADMIN: Mengakses halaman sertifikat certificate ID: {$id}");

        // 1. Ambil sertifikat berdasarkan certificate_id (UUID)
        $sertifikat = KmdCertificates::find($id);

        if (!$sertifikat) {
            Log::warning("SUPERADMIN: Sertifikat dengan ID {$id} tidak ditemukan");
            return redirect()->back()->with('error', 'Sertifikat tidak ditemukan.');
        }

        Log::info("SUPERADMIN: Sertifikat ditemukan", [
            'certificate_id' => $sertifikat->id, 
            'user_id' => $sertifikat->user_id,
            'no_certificate' => $sertifikat->no_certificate
        ]);

        // 2. Ambil data user dari user_id di sertifikat
        $user = KomUser::findOrFail($sertifikat->user_id);
        Log::info("SUPERADMIN: Data user berhasil diambil", ['user_name' => $user->name]);

        // 3. Ambil data class tutorial untuk tanggal pelaksanaan
        $classTutorial = KmdUserClassTutorial::where('user_id', $sertifikat->user_id)
            ->where('status', 'selesai')
            ->latest('date_booking')
            ->first();

        $classTutorialCreatedAt = $classTutorial->created_at ?? $sertifikat->date_publish;

        // 4. Siapkan path QR (TIDAK GENERATE)
        $fileName = "kom_{$sertifikat->id}.png";
        $qrPathPublic = file_exists(public_path('kmdqr/' . $fileName)) 
            ? url('kmdqr/' . $fileName) 
            : null;

        Log::info("SUPERADMIN: QR code siap ditampilkan", ['qr_exists' => $qrPathPublic !== null]);

        // 5. Kirim data ke view
        return view('a_kom_digital_pusat.superadmin.sertifikat.show', [
            'certificate_number' => $sertifikat->no_certificate,
            'certificate_id' => $sertifikat->id,
            'qr_link' => $qrPathPublic,
            'status' => 'selesai',
            'nama' => $user->name,
            'dibuatOleh' => 'Sub Divisi KOM DIGITAL',
            'tanggalPelaksanaan' => Carbon::parse($classTutorialCreatedAt)->format('d-m-Y'),
            'tanggalCetak' => now()->format('d-m-Y'),
            'class_tutorial_created_at' => $classTutorialCreatedAt,
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        Log::error("SUPERADMIN: User tidak ditemukan untuk sertifikat {$id}");
        return redirect()->back()->with('error', 'User tidak ditemukan.');
        
    } catch (\Exception $e) {
        Log::error('SUPERADMIN: Error saat menampilkan sertifikat', [
            'certificate_id' => $id,
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
        ]);
        return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat sertifikat.');
    }
}

    public function create()
    {
        // Bisa ambil data user untuk dropdown misalnya
        // $users = KomUser::all(); // kalau perlu, import KomUser di atas
        return view('a_kom_digital_pusat.superadmin.sertifikat.create');
    }

    public function store(KmdCertificateRequest $request)
    {
        try {
            $this->service->store($request->validated());
            return redirect()->route('kom.super_sertifikat.index')->with('success', 'Data sertifikat berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan sertifikat: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        try {
            $data = $this->service->find($id);
            return view('a_kom_digital_pusat.superadmin.sertifikat.edit', compact('data'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit sertifikat: ' . $e->getMessage());
            return redirect()->route('kom.super_sertifikat.index')->with('error', 'Terjadi kesalahan saat menampilkan form edit.');
        }
    }

    public function update(KmdCertificateRequest $request, $id)
    {
        try {
            $this->service->update($id, $request->validated());
            return redirect()->route('kom.super_sertifikat.index')->with('success', 'Data sertifikat berhasil diupdate.');
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate sertifikat: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate data.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return redirect()->route('kom.super_sertifikat.index')->with('success', 'Data sertifikat berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus sertifikat: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
