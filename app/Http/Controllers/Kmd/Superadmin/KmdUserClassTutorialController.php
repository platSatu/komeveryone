<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kmd\KmdUserClassTutorial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rule;

use App\Models\Kmd\KmdPertanyaanUser;
use App\Models\Kmd\KmdDonasi;
use App\Models\Kmd\KmdClassTutorial;
use Illuminate\Support\Facades\DB;
use App\Rules\CustomDbRule;

class KmdUserClassTutorialController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $data = KmdUserClassTutorial::with([
            'user',
            'classTutorial',
            'modul',
            'kelas',
            'rayon',
            'pengajar',
        ])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('classTutorial', function ($q) use ($search) {
                    $q->where('topic', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('a_kom_digital_pusat.superadmin.userclasstutorial.index', compact('data', 'search'));
    }

    // public function destroy($id): RedirectResponse
    // {
    //     $item = KmdUserClassTutorial::findOrFail($id);
    //     $item->delete();

    //     return redirect()
    //         ->back()
    //         ->with('success', 'Data berhasil dihapus.');
    // }
    public function destroy($id): RedirectResponse
    {
        DB::beginTransaction();

        try {
            // Ambil data utama
            $userClass = KmdUserClassTutorial::findOrFail($id);

            // Ambil ID user & kelas tutorial
            $userId = $userClass->user_id;
            $kelasTutorialId = $userClass->class_tutorial_id;

            // Hapus data terkait dari tabel lain
            KmdPertanyaanUser::where('user_id', $userId)
                ->where('kelas_tutorial_id', $kelasTutorialId)
                ->delete();

            KmdDonasi::where('user_id', $userId)
                ->where('userclasstutorial_id', $id)
                ->delete();

            // Update quota di KmdClassTutorial (+1 karena user keluar)
            // Update data kuota di kelas tutorial
            $kelas = KmdClassTutorial::find($kelasTutorialId);
            if ($kelas) {
                // quota tidak diubah karena itu kapasitas tetap
                if ($kelas->jml_ikut > 0) {
                    $kelas->decrement('jml_ikut', 1);
                }
                $kelas->increment('sisa_quota', 1);
            }


            // Hapus data utama
            $userClass->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Data user class tutorial dan data terkait berhasil dihapus serta quota diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $data = KmdUserClassTutorial::with('classTutorial')->findOrFail($id);

        // Hanya kelas aktif, diurutkan dari start_time terdekat
        $kelasTutorials = KmdClassTutorial::where('status', 'inactive')
           // ->orWhere('status_diklat', 'active')
            ->orderBy('created_at', 'desc') 
            //->orderBy('start_time', 'asc')
            ->get();


        return view('a_kom_digital_pusat.superadmin.userclasstutorial.edit', compact('data', 'kelasTutorials'));
    }




    public function update(Request $request, $id)
    {

        // $request->validate([        
        //     'class_tutorial_id' => [
        //         'required', 
        //         new CustomDbRule('mysql2', 'kmd_user_class_tutorial', 'class_tutorial_id')
        //     ],
        // ]);


        $request->validate([
            'class_tutorial_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Check exists di connection 'mysql2' dengan query eksplisit
                    $count = DB::connection('mysql2')->table('kmd_class_tutorial')->where('id', $value)->count();
                    if ($count == 0) {
                        $fail('Kelas tutorial yang dipilih tidak valid.');
                    }
                },
                // Jika CustomDbRule masih error, hapus sementara untuk test
                // new CustomDbRule('mysql2', 'kmd_user_class_tutorial', 'class_tutorial_id'),
            ],
        ], [
            'class_tutorial_id.required' => 'Silakan pilih kelas tutorial.',
        ]);


        // Ambil data user_class_tutorial lama
        $userClass = KmdUserClassTutorial::findOrFail($id);
        $oldKelasId = $userClass->class_tutorial_id;
        $newKelasId = $request->class_tutorial_id;

        // Jika tidak ada perubahan kelas, langsung kembali
        if ($oldKelasId === $newKelasId) {
            return redirect()->back()->with('info', 'Tidak ada perubahan kelas.');
        }

        $userId = $userClass->user_id;

        // Update data kelas di tabel user_class_tutorial
        $userClass->class_tutorial_id = $newKelasId;
        $userClass->save();

        // Update data di tabel KmdPertanyaanUser
        KmdPertanyaanUser::where('user_id', $userId)
            ->where('kelas_tutorial_id', $oldKelasId)
            ->update(['kelas_tutorial_id' => $newKelasId]);

        // Update data di tabel KmdDonasi
        // KmdDonasi::where('user_id', $userId)
        //     ->where('userclasstutorial_id', $userClass->id)
        //     ->update(['class_id' => $newKelasId]);

        // --- Update Quota Kelas ---
        $oldClass = KmdClassTutorial::find($oldKelasId);
        $newClass = KmdClassTutorial::find($newKelasId);

        // if ($oldClass) {
        //     $oldClass->decrement('jml_ikut');
        //     $oldClass->increment('sisa_quota');
        // }

        // if ($newClass) {
        //     $newClass->increment('jml_ikut');
        //     $newClass->decrement('sisa_quota');
        // }
        if ($oldClass) {
            DB::connection('mysql2')->table('kmd_class_tutorial')
                ->where('id', $oldKelasId)
                ->update([
                    'jml_ikut' => DB::raw('jml_ikut - 1'),
                    'sisa_quota' => DB::raw('sisa_quota + 1')
                ]);
        }

        if ($newClass) {
            DB::connection('mysql2')->table('kmd_class_tutorial')
                ->where('id', $newKelasId)
                ->update([
                    'jml_ikut' => DB::raw('jml_ikut + 1'),
                    'sisa_quota' => DB::raw('sisa_quota - 1')
                ]);
        }


        return redirect()
            ->route('kom.super_userclasstutorial.index')
            ->with('success', 'User berhasil dipindahkan ke kelas baru dan data terkait telah diperbarui.');
    }

}
