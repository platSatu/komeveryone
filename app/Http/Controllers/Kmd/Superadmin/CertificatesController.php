<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kmd\Superadmin\KmdCertificateRequest; // buat validasi nanti
use App\Services\Superadmin\KmdCertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Kmd\KomUser;
use App\Models\KmdCertificates;

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
        Log::info("Superadmin mencoba mengakses sertifikat user ID: $id");

        // 1. Ambil data user
        $user = KomUser::findOrFail($id);
        Log::info("Data user berhasil diambil", ['user' => $user->toArray()]);

        // 2. Ambil sertifikat terbaru user
        $sertifikat = KmdCertificates::where('user_id', $id)->latest()->first();

        if (!$sertifikat) {
            Log::warning("Sertifikat tidak ditemukan untuk user ID: $id");
            return redirect()->back()->with('error', 'User belum memiliki sertifikat.');
        }

        Log::info("Sertifikat ditemukan", ['certificate_id' => $sertifikat->id, 'no_certificate' => $sertifikat->no_certificate]);

        // 3. Generate URL verifikasi
        $qrLink = url('/kom-digital/verifikasi/' . $sertifikat->id);
        Log::info("QR link dibuat", ['qr_link' => $qrLink]);

        // 4. Siapkan folder dan path file QR
        $publicPath = public_path('kmdqr');
        $fileName = "kom_{$sertifikat->id}.png";
        $filePath = $publicPath . DIRECTORY_SEPARATOR . $fileName;

        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0755, true);
            Log::info("Folder QR dibuat", ['path' => $publicPath]);
        }

        // 5. Generate QR code jika belum ada
        if (!file_exists($filePath)) {
            QrCode::format('png')
                ->size(300)
                ->margin(4)
                ->errorCorrection('H')
                ->backgroundColor(255, 255, 255)
                ->generate($qrLink, $filePath);

            Log::info("QR code berhasil digenerate", ['file' => $filePath]);
        } else {
            Log::info("QR code sudah tersedia, tidak digenerate ulang", ['file' => $filePath]);
        }

        // 6. Siapkan URL publik ke QR code
        $qrPathPublic = url('kmdqr/' . $fileName);

        // 7. Kirim data ke view
        return view('a_kom_digital_pusat.superadmin.sertifikat.show', [
            'certificate_number' => $sertifikat->no_certificate,
            'certificate_id' => $sertifikat->id,
            'qr_link' => $qrPathPublic,
            'status' => $sertifikat->status,
            'nama' => $user->name,
            'dibuatOleh' => 'Sub Divisi KOM DIGITAL',
            'tanggalPelaksanaan' => Carbon::parse($sertifikat->date_publish)->format('d-m-Y'),
            'tanggalCetak' => now()->format('d-m-Y'),
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // Error jika user tidak ditemukan
        Log::error("User dengan ID $id tidak ditemukan", [
            'exception' => $e->getMessage(),
            'line' => $e->getLine(),
        ]);

        return redirect()->back()->with('error', 'User tidak ditemukan.');
        
    } catch (\Exception $e) {
        // Error umum lainnya
        Log::error('Error saat menampilkan sertifikat', [
            'user_id' => $id,
            'error_message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        // Untuk mode produksi, lebih aman tampilkan pesan umum
        // Saat development, bisa tambahkan ->with('error', $e->getMessage())
        return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat sertifikat. Silakan coba lagi.');
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
