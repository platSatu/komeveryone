<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kmd\KmdPertanyaanUser;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class KmdPertanyaanPesertaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = KmdPertanyaanUser::with(['user', 'class', 'modul', 'kelasTutorial', 'pengajar', 'rayon']);

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('pertanyaan', 'like', "%{$search}%");
        }

        $pertanyaans = $query->latest()->paginate(10)->withQueryString();

        return view('a_kom_digital_pusat.superadmin.pertanyaan.index', compact('pertanyaans', 'search'));
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $item = KmdPertanyaanUser::findOrFail($decryptedId);
            $item->delete();

            return redirect()->route('pertanyaan.index')->with('success', 'Pertanyaan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus pertanyaan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
