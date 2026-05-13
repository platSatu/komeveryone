<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomZoom;
use App\Models\Kom\KomUser;
use App\Models\Kom\KomModul;
use App\Models\Kom\KomClass;
use App\Models\Kom\KomSchedule;
use App\Models\Rayon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class KomZoomController extends Controller
{
    public function index()
{
    $user = Auth::guard('kom')->user();

    if (!$user) {
        abort(403, 'Unauthorized');
    }

    $query = KomZoom::with(['user', 'modul', 'kelas', 'schedule', 'rayon'])
        ->where('kom_user_id', $user->id); // Ambil hanya data milik user yang login

    $data = $query->get();

    return view('kom_digital.zoom.index', compact('data'));
}



    public function toggleStatus($id)
    {
        $zoom = \App\Models\KomZoom::findOrFail($id);

        $zoom->status = $zoom->status === 'aktif' ? 'nonaktif' : 'aktif';
        $zoom->save();

        return redirect()->back()->with('success', 'Status berhasil diperbarui.');
    }


   public function create()
{
    $user = Auth::guard('kom')->user();

    if (!$user) {
        abort(403, 'Unauthorized');
    }

    $rayonId = $user->rayon_id;

    // Ambil semua schedule dari rayon user
    $schedules = KomSchedule::where('rayon_id', $rayonId)->get();

    // Ambil ID dari data terhubung lewat schedule
    $modulIds = $schedules->pluck('kom_modul_id')->unique();
    $kelasIds = $schedules->pluck('kom_class_id')->unique();
    $userIds = $schedules->pluck('pengajar_id')->unique();

    // Filter berdasarkan schedule yang relevan dengan rayon user
    $moduls = KomModul::whereIn('id', $modulIds)->get();
    $kelas = KomClass::whereIn('id', $kelasIds)->get();
    $users = KomUser::whereIn('id', $userIds)->get();

    // Rayon user
    $rayons = Rayon::where('id', $rayonId)->get();

    return view('kom_digital.zoom.create', compact('users', 'moduls', 'kelas', 'schedules', 'rayons'));
}






    public function store(Request $request)
    {
        $request->validate([
            'kom_moduls_id' => 'required',
            'kom_class_id' => 'required',
            'kom_schedules_id' => 'required',
            'description' => 'nullable|string',
            'status' => 'required|string',
        ]);

        try {
            $user = auth('kom')->user();

            KomZoom::create([
                'kom_user_id' => $user->id,
                'rayons_id' => $user->rayon_id,
                'kom_moduls_id' => $request->kom_moduls_id,
                'kom_class_id' => $request->kom_class_id,
                'kom_schedules_id' => $request->kom_schedules_id,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            return redirect()
                ->route('kom.zoom.index')
                ->with('success', 'Data Zoom berhasil ditambahkan.');

        } catch (\Exception $e) {
            // Logging error lengkap ke laravel.log
            Log::error('Gagal menyimpan data KomZoom', [
                'user_id' => auth('kom')->id(),
                'request' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data Zoom: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $zoom = KomZoom::findOrFail($id);
        $zoom->delete();

        return redirect()->route('kom.zoom.index')->with('success', 'Data Zoom berhasil dihapus.');
    }
}
