<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kmd\{KmdClass, KmdModuls, KomUser};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Services\Superadmin\KmdClassTutorialService;
use App\Http\Requests\Kmd\Superadmin\ClassTutorialRequest;
use Illuminate\Support\Facades\Log;
use App\Models\Kmd\Cabang;
use App\Models\Kmd\KmdClassTutorial;
use Firebase\JWT\JWT;

class ClassTutorialController extends Controller
{
    protected $service;

    public function __construct(KmdClassTutorialService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'rayon_id']);
        $data = $this->service->getAll($filters);
        $rayons = Cabang::all(); // ambil semua rayon untuk dropdown filter
        return view('a_kom_digital_pusat.superadmin.kelastutorial.index', compact('data', 'filters', 'rayons'));
    }


    public function create()
    {
        $users = KomUser::all();
        $pengajars = KomUser::where('role', 'pengajar')->get();
        $classes = KmdClass::all();
        $moduls = KmdModuls::all();
        $rayons = Cabang::all();

        return view('a_kom_digital_pusat.superadmin.kelastutorial.create', compact('users', 'pengajars', 'classes', 'moduls', 'rayons'));
    }

    public function store(ClassTutorialRequest $request)
    {
        try {
            $this->service->store($request->all());
            return redirect()->route('kom.super_kelas_tutorial.index')->with('success', 'Tutorial berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan tutorial: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $data = $this->service->find($id);

            $users = KomUser::all();
            $pengajars = KomUser::where('role', 'pengajar')->get();
            $classes = KmdClass::all();
            $moduls = KmdModuls::all();
            $rayons = Cabang::all();

            return view('a_kom_digital_pusat.superadmin.kelastutorial.edit', [
                'item' => $data,
                'users' => $users,
                'pengajars' => $pengajars,
                'classes' => $classes,
                'moduls' => $moduls,
                'rayons' => $rayons,
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal menampilkan form edit tutorial: ' . $e->getMessage());
            return redirect()->route('kom.super_kelas_tutorial.index')->with('error', 'Terjadi kesalahan saat menampilkan data.');
        }
    }

    public function update(ClassTutorialRequest $request, $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $this->service->update($id, $request->all());
            return redirect()->route('kom.super_kelas_tutorial.index')->with('success', 'Tutorial berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error('Gagal mengupdate tutorial: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate data.');
        }
    }


    public function destroy($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $this->service->delete($id);

            return redirect()->route('kom.super_kelas_tutorial.index')->with('success', 'Tutorial berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus tutorial: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function join($zoomMeetingId)
    {
        $meeting = KmdClassTutorial::where('zoom_meeting_id', $zoomMeetingId)->firstOrFail();

        return view('a_kom_digital_pusat.superadmin.kelastutorial.join', [
            'meetingId' => $meeting->zoom_meeting_id,
            'passcode' => $meeting->passcode ?? '',
            'topic' => $meeting->topic,
        ]);
    }


    public function generateSignature(Request $request)
    {
        $meetingNumber = $request->input('meeting_number');
        $role = $request->input('role'); // 0 = peserta, 1 = host

        $sdkKey = env('ZOOM_SDK_KEY');
        $sdkSecret = env('ZOOM_SDK_SECRET');

        $time = round(microtime(true) * 1000) - 30000;

        $data = base64_encode($sdkKey . $meetingNumber . $time . $role);

        $hash = hash_hmac('sha256', $data, $sdkSecret, true);

        $hashBase64 = base64_encode($hash);

        $rawSignature = $sdkKey . "." . $meetingNumber . "." . $time . "." . $role . "." . $hashBase64;

        $signature = rtrim(strtr(base64_encode($rawSignature), '+/', '-_'), '=');

        return response()->json(['signature' => $signature]);
    }


}
