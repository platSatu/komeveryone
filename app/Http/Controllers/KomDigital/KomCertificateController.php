<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomUser;
use App\Models\Kom\KomCertificate;
use Illuminate\Support\Facades\Auth;

class KomCertificateController extends Controller
{
    public function index()
    {
        $certificates = KomCertificate::latest()->paginate(10);
        return view('kom_digital.certificates.index', compact('certificates'));
    }

    // Tampilkan form tambah sertifikat
    public function create()
    {
        return view('kom_digital.certificates.create');
    }

    // Simpan data sertifikat baru
    public function store(Request $request)
    {
        $request->validate([
            'no_sertifikat' => 'required|unique:kom_certificates,no_sertifikat',
            'tgl_sertifikat' => 'required|date',
            'status' => 'required|boolean',
        ]);

        KomCertificate::create([
            'no_sertifikat' => $request->no_sertifikat,
            'tgl_sertifikat' => $request->tgl_sertifikat,
            'status' => $request->status,
        ]);

        return redirect()->route('kom.certificates.index')->with('success', 'Sertifikat berhasil ditambahkan.');
    }

    // Menampilkan form edit sertifikat
    public function edit($id)
    {
        $certificate = KomCertificate::findOrFail($id);
        return view('kom_digital.certificates.edit', compact('certificate'));
    }

    // Update data sertifikat
    public function update(Request $request, $id)
    {
        $certificate = KomCertificate::findOrFail($id);

        $request->validate([
            'no_sertifikat' => 'required|unique:kom_certificates,no_sertifikat,' . $certificate->id,
            'tgl_sertifikat' => 'required|date',
            'status' => 'required|boolean',
        ]);

        $certificate->update([
            'no_sertifikat' => $request->no_sertifikat,
            'tgl_sertifikat' => $request->tgl_sertifikat,
            'status' => $request->status,
        ]);

        return redirect()->route('kom.certificates.index')->with('success', 'Sertifikat berhasil diperbarui.');
    }

    // Hapus sertifikat
    public function destroy($id)
    {
        $certificate = KomCertificate::findOrFail($id);
        $certificate->delete();

        return redirect()->route('kom.certificates.index')->with('success', 'Sertifikat berhasil dihapus.');
    }


}
