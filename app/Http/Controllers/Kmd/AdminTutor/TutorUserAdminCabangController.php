<?php

namespace App\Http\Controllers\Kmd\AdminCabang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Services\AdminCabang\UserCabangService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Kmd\KomUser;
 use App\Rules\CustomDbRule;

class UserAdminCabangController extends Controller
{
    protected $service;

    public function __construct(UserCabangService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the users
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'role']);
        $data = $this->service->all($filters);

        return view('a_kom_digital_pusat.admin_cabang.user.index', compact('data', 'filters'));
    }


    /**
     * Show form for creating a new user
     */
    public function create()
    {
        return view('a_kom_digital_pusat.admin_cabang.user.create');
    }

    /**
     * Store a newly created user
     */
   

public function store(Request $request)
{
    try {
        // Log semua input mentah dari user
        Log::info('UserCabangController@store - Incoming request data', $request->all());

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                new CustomDbRule('mysql2', 'kmd_users', 'email', 'unique'),
            ],
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'asal_gereja' => 'nullable|string|max:255',
            'tahun_lahir' => 'nullable|integer',
            'image' => 'nullable|string|max:255',
            'status' => 'nullable|string',
        ]);

        // Tambahkan cabang_id otomatis dari user login
        $data['cabang_id'] = Auth::user()->cabang_id;

        // Log setelah validasi berhasil
        Log::info('User CabangController@store - Validated data', $data);

        // Create user via service
        $this->service->create($data);

        Log::info('User CabangController@store - User created successfully');

        return redirect()->route('kom.admin_cabang_users.index')
            ->with('success', 'User  berhasil ditambahkan.');

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Log error validasi
        Log::warning('User CabangController@store - Validation failed', [
            'errors' => $e->errors(),
        ]);

        return back()
            ->withErrors($e->validator)
            ->withInput();
    } catch (\Exception $e) {
        // Log exception tidak terduga
        Log::error('User CabangController@store - Exception occurred', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return back()
            ->with('error', 'Terjadi kesalahan saat menyimpan data.')
            ->withInput();
    }
}



    /**
     * Show the form for editing a user
     */
    public function edit($encryptedId)
{
    try {
        // Decrypt dulu untuk dapat ID asli
        $id = Crypt::decryptString($encryptedId);

        // Cari user berdasar ID asli
        $user = $this->service->find($id);

        // Cek hak akses, misal cabang_id harus sama dengan user login
        if ($user->cabang_id !== Auth::user()->cabang_id) {
            abort(403, 'Unauthorized access.');
        }

        return view('a_kom_digital_pusat.admin_cabang.user.edit', compact('user'));
    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
        abort(403, 'ID tidak valid.');
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, 'Data tidak ditemukan.');
    }
}



    /**
     * Update the specified user
     */
    public function update(Request $request, $encryptedId)
    {
        Log::info('UserCabangController@update - Start');

        try {
            Log::info('Menerima encrypted ID:', ['encryptedId' => $encryptedId]);


            $id = Crypt::decryptString($encryptedId);

            Log::info('Decrypted ID:', ['id' => $id]);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                // unique:<table>,<column>,<ignore_value>,<primary_key_column>
                'email' => 'required|email|unique:kom_users,email,' . $id . ',id',
                'password' => 'nullable|string|min:6',
                'role' => 'required|string',
                'phone' => 'nullable|string|max:20',
                'asal_gereja' => 'nullable|string|max:255',
                'tahun_lahir' => 'nullable|integer',
                'image' => 'nullable|string|max:255',
                'status' => 'nullable|string',
            ]);

            Log::info('Data berhasil divalidasi:', $validatedData);

            $this->service->update($id, $validatedData);

            Log::info('User berhasil diperbarui', ['id' => $id]);

            return redirect()->route('kom.admin_cabang_users.index')
                ->with('success', 'User berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validasi gagal pada update user:', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Gagal mendekripsi ID user:', ['message' => $e->getMessage()]);
            abort(403, 'Invalid ID');
        } catch (\Exception $e) {
            Log::error('Exception saat update user:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Terjadi kesalahan saat memperbarui user.');
        }
    }
    /**
     * Remove the specified user
     */
    public function destroy($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        $this->service->delete($id);

        return redirect()->route('kom.admin_cabang_users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function toggleStatus($encryptedId)
    {
        // Dekripsi ID
        $id = decrypt($encryptedId);

        // Ambil user yang sesuai cabang admin_cabang yang login
        $user = KomUser::where('id', $id)
            ->where('cabang_id', Auth::user()->cabang_id)
            ->firstOrFail();

        // Toggle status: active <-> tidak_aktif
        $user->status = $user->status === 'aktif' ? 'tidak_aktif' : 'aktif';

        $user->save();

        return redirect()->back()->with('success', 'Status user berhasil diperbarui.');
    }

    public function detail($encryptedId)
    {
        $userId = Crypt::decryptString($encryptedId);

        $details = $this->service->getUserDetails($userId);

        return view('a_kom_digital_pusat.admin_cabang.user.detail', compact('details'));
    }



}
