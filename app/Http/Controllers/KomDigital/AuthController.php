<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
//use App\Models\Kom\KomUser;
use App\Models\Kmd\KomUser;
use App\Models\Rayon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\KomUserVerificationMail;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Http;
use App\Rules\CustomDbRule;
use Illuminate\Support\Facades\DB;




class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('kom_auth.login');
    }

    public function SyaratKetentuan()
    {
        return view('kom_auth.snk');
    }


    public function generateNikUntukSemuaUser()
    {
        $users = DB::connection('mysql2')
            ->table('kmd_users')
            ->whereNull('nik')
            ->orWhere('nik', '')
            ->get();

        $jumlah = 0;

        foreach ($users as $user) {
            // Generate NIK yang unik di koneksi mysql2
            $nikBaru = $this->generateRandomNpk('kmd_users', 'nik', 'D', 'mysql2');

            // Update user dengan NIK baru
            DB::connection('mysql2')
                ->table('kmd_users')
                ->where('id', $user->id)
                ->update(['nik' => $nikBaru]);

            $jumlah++;
        }

        return response()->json([
            'message' => "Berhasil generate NIK untuk {$jumlah} user di database mysql2.",
        ]);
    }

    // Fungsi helper untuk generate NIK unik
    // private function generateRandomNpk($tableName = 'kmd_users', $column = 'nik', $prefix = 'D', $connection = 'mysql2')
    // {
    //     do {
    //         $randomNumber = mt_rand(100000, 999999);
    //         $npk = $prefix . $randomNumber;

    //             DB::table($tableName)
    //             ->table($tableName)
    //             ->where($column, $npk)
    //             ->exists();
    //     } while ($exists);

    //     return $npk;
    // }
    private function generateRandomNpk(
    $tableName = 'kmd_users',
    $column = 'nik',
    $prefix = 'D'
) {
    do {
        $randomNumber = mt_rand(100000, 999999);
        $npk = $prefix . $randomNumber;

        $exists = DB::table($tableName)
            ->where($column, $npk)
            ->exists();

    } while ($exists);

    return $npk;
}

    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //     Log::debug('Login attempt initiated', ['email' => $credentials['email']]);

    //     // Ambil user dari model KomUser
    //     $user = KomUser::where('email', $credentials['email'])->first();

    //     // Cek apakah user ditemukan
    //     if (!$user) {
    //         Log::warning('Login failed: email not found', ['email' => $credentials['email']]);
    //         return back()->withErrors(['email' => 'Email tidak ditemukan.']);
    //     }

    //     // Cek password
    //     if (!Hash::check($credentials['password'], $user->password)) {
    //         Log::warning('Login failed: wrong password', ['email' => $credentials['email']]);
    //         return back()->withErrors(['email' => 'Password salah.']);
    //     }

    //     // Cek status aktif
    //     if ($user->status !== 'aktif') {
    //         Log::info('Login blocked: account not active', [
    //             'email' => $user->email,
    //             'status' => $user->status,
    //         ]);

    //         return back()->with('error', 'Akun Anda belum aktif. Silakan cek email untuk verifikasi atau hubungi admin.');
    //     }

    //     // Lakukan login jika semua valid
    //     if (Auth::guard('user')->attempt($credentials)) {
    //         Log::info('Login successful', ['user_id' => $user->id, 'email' => $user->email]);
    //         return redirect()->route('kom.dashboard_kom');
    //     }

    //     // Jika sampai sini, berarti Auth gagal walau status dan password cocok
    //     Log::error('Login failed at Auth::guard(kom)->attempt()', [
    //         'email' => $credentials['email'],
    //         'user_id' => $user->id,
    //         'guard' => 'user'
    //     ]);

    //     return back()->withErrors(['email' => 'Gagal login. Silakan coba lagi.']);
    // }
    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    Log::debug('Login attempt initiated', [
        'email' => $credentials['email']
    ]);

    // 🔍 cari user
    $user = KomUser::where('email', $credentials['email'])->first();

    if (!$user) {
        Log::warning('Login failed: email not found', [
            'email' => $credentials['email']
        ]);

        return back()->withErrors([
            'email' => 'Email tidak ditemukan.'
        ]);
    }

    // 🔐 cek password
    if (!Hash::check($credentials['password'], $user->password)) {
        Log::warning('Login failed: wrong password', [
            'email' => $credentials['email']
        ]);

        return back()->withErrors([
            'email' => 'Password salah.'
        ]);
    }

    // 🚫 cek status akun
    // kom_v02: data lama yang tersimpan "1" (dari tombol status superadmin versi lama) juga dianggap aktif
    if (! in_array((string) $user->status, ['aktif', '1'], true)) {
        Log::info('Login blocked: account not active', [
            'email' => $user->email,
            'status' => $user->status,
        ]);

        return back()->with('error', 'Akun Anda belum aktif.');
    }

    // 🔥 LOGIN SESSION (INI YANG WAJIB)
    Auth::login($user);
    $request->session()->regenerate();

    Log::info('Login successful', [
        'user_id' => $user->id,
        'email' => $user->email
    ]);

    return redirect()->route('kom.dashboard_kom');
}




    public function logout()
    {
        Auth::logout();
        return redirect()->route('kom.login');
    }

    // public function showRegisterForm()
    // {
    //     $rayons = Rayon::where('status', true)->get(); 
    //     return view('kom_auth.register', compact('rayons'));
    // }
    public function showRegisterForm(Request $request)
    {
        $url = 'https://hmministry.net/api/cabang_api.php';

        try {
            $raw = $this->getApiResponse($url);

            if (!$raw) {
                \Log::error('Gagal mengambil data dari API: ' . $url);
                $rayonList = [];
            } else {
                $rayonList = json_decode($raw, true);

                if (!is_array($rayonList)) {
                    \Log::error('Response API bukan JSON valid: ' . $raw);
                    $rayonList = [];
                }
            }
        } catch (\Exception $e) {
            \Log::error('Exception saat ambil data API: ' . $e->getMessage());
            $rayonList = [];
        }

        return view('kom_auth.register', compact('rayonList'));
    }

    // public function showRegisterFormTutor(Request $request)
    // {
    //     return view('kom_auth.register_tutor');
    // }

    // public function showRegisterFormPengajar(Request $request)
    // {
    //     return view('kom_auth.register_pengajar');
    // }
    public function showRegisterFormTutor(Request $request)
    {
        return view('kom_auth.register_tutor');
    }

    public function showRegisterFormPengajar(Request $request)
    {
        if (!$request->session()->has('sim')) {
            return redirect()->route('kom.register.tutor')->with('error', 'Silahkan mulai dari cek no sim.');
        }

        return view('kom_auth.register_pengajar');
    }


    // public function cekSim(Request $request)
    // {
    //     try {
    //         Log::info('cekSim called', ['input' => $request->all()]); // log data input request

    //         $request->validate([
    //             'name' => 'required|string'
    //         ]);

    //         $noKartu = $request->input('name');
    //         $url = 'https://hmministry.net/api/sim_guru_api.php';

    //         Log::info('Fetching data from API', ['url' => $url]);

    //         $response = $this->getApiResponse($url);

    //         if (!$response) {
    //             Log::error('Failed to get API response', ['url' => $url]);
    //             return response()->json(['error' => 'Gagal mengakses API.'], 500);
    //         }

    //         Log::info('API response received', ['response' => $response]);

    //         $data = json_decode($response, true);

    //         if ($data === null) {
    //             Log::error('Invalid JSON response from API', ['response' => $response]);
    //             return response()->json(['error' => 'Response API tidak valid JSON.'], 500);
    //         }

    //         // Cari data berdasar no_sim
    //         $result = collect($data['data'] ?? [])->firstWhere('no_sim', $noKartu);

    //         if ($result) {
    //             Log::info('SIM data found', ['no_sim' => $noKartu, 'data' => $result]);
    //             return response()->json(['success' => true, 'sim_data' => $result]);
    //         }

    //         Log::info('SIM data not found', ['no_sim' => $noKartu]);
    //         return response()->json(['success' => false, 'message' => 'No SIM tidak ditemukan.']);

    //     } catch (\Exception $e) {
    //         // Log error dengan trace lengkap
    //         Log::error('Error cekSim: ' . $e->getMessage(), [
    //             'exception' => $e,
    //             'input' => $request->all()
    //         ]);
    //         return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
    //     }
    // }
    public function cekSim(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $noKartu = $request->input('name');
        $url = 'https://hmministry.net/api/sim_guru_api.php';


        \Log::info('[SIM CHECK] Mengirim request ke API SIM', [
            'url' => $url,
            'no_kartu' => $noKartu
        ]);

        $response = $this->getApiResponseSim($url);

        if (!$response) {
            \Log::error('[SIM CHECK] Gagal mendapatkan response dari API.');
            return redirect()->back()->with('error', 'Gagal mengakses API.');
        }

        \Log::debug('[SIM CHECK] Response dari API SIM:', ['response' => $response]);

        $data = json_decode($response, true);

        if ($data === null) {
            \Log::error('[SIM CHECK] Response tidak bisa di-decode JSON.', [
                'raw_response' => $response
            ]);
            return redirect()->back()->with('error', 'Response API tidak valid.');
        }

        $result = collect($data['data'] ?? [])->firstWhere('no_sim', $noKartu);

        if ($result) {
            \Log::info('[SIM CHECK] Data SIM ditemukan', $result);

            if (isset($result['jenis']) && strtoupper($result['jenis']) === 'K1') {
                session(['sim' => $result]);

                \Log::info('[SIM CHECK] SIM valid dan jenis K1. Redirect ke registrasi pengajar.');

                return redirect()->route('kom.register.pengajar')
                    ->with('success', 'Data SIM ditemukan dan valid, silakan lanjut registrasi.');
            } else {
                \Log::warning('[SIM CHECK] Jenis SIM bukan K1.', ['jenis' => $result['jenis'] ?? null]);
                return redirect()->back()->with('error', 'Jenis SIM tidak memenuhi syarat (harus K1).');
            }
        }

        \Log::warning('[SIM CHECK] No SIM tidak ditemukan dalam data API.', [
            'no_sim_dicek' => $noKartu,
            'jumlah_data_diterima' => count($data['data'] ?? [])
        ]);

        return redirect()->back()->with('error', 'No SIM tidak ditemukan.');
    }
    public function getApiResponseSim($url)
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'api_key' => 'KEY_ABC12345',
            ]),
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            \Log::error('[SIM CHECK] cURL error saat mengakses API SIM.', [
                'error' => curl_error($ch)
            ]);
            curl_close($ch);
            return false;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        \Log::info('[SIM CHECK] API response status code: ' . $httpCode);

        curl_close($ch);

        return $response;
    }


    private function getApiResponse($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            \Log::error('cURL error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        return $response;
    }

    /**
     * kom_v02: daftar nama CABANG gereja untuk pilihan "Asal Gereja" di form register peserta.
     * Sumber: API HM Ministry (cabang_api.php). Disimpan di cache 6 jam supaya form tetap cepat
     * walau API sedang lambat; kalau API gagal, hasil terakhir yang berhasil tetap dipakai.
     */
    public function rayonSearch(Request $request)
    {
        return $this->cariDaftarGereja(
            'kom_daftar_cabang_gereja',
            'https://hmministry.net/api/cabang_api.php',
            'nmcabang',
            (string) $request->query('q', ''),
            true
        );
    }

    /** kom_v02: daftar nama CK / Rayon untuk form register pengajar (rayon_api.php). */
    public function asalRayon(Request $request)
    {
        return $this->cariDaftarGereja(
            'kom_daftar_rayon_gereja',
            'https://hmministry.net/api/rayon_api.php',
            'nmrayon',
            (string) $request->query('q', ''),
            false
        );
    }

    /**
     * Ambil daftar nama dari API HM Ministry (pakai cache), saring sesuai ketikan,
     * kembalikan format Select2: {"results":[{"id":"...","text":"..."}]}
     */
    protected function cariDaftarGereja(string $cacheKey, string $url, string $kolom, string $cari, bool $denganLainnya)
    {
        $daftar = \Illuminate\Support\Facades\Cache::get($cacheKey);

        if (! is_array($daftar) || $daftar === []) {
            try {
                $response = Http::timeout(20)->withOptions(['verify' => false])->get($url);
                $json = $response->successful() ? $response->json() : null;

                if (is_array($json)) {
                    $daftar = collect($json)
                        ->pluck($kolom)
                        ->filter(fn ($n) => is_string($n) && trim($n) !== '')
                        ->map(fn ($n) => trim($n))
                        ->unique()
                        ->sort(SORT_NATURAL | SORT_FLAG_CASE)
                        ->values()
                        ->all();

                    if ($daftar) {
                        \Illuminate\Support\Facades\Cache::put($cacheKey, $daftar, now()->addHours(6));
                        \Illuminate\Support\Facades\Cache::forever($cacheKey . '_cadangan', $daftar);
                    }
                } else {
                    Log::warning('API daftar gereja gagal', ['url' => $url, 'status' => $response->status()]);
                }
            } catch (\Throwable $e) {
                Log::warning('API daftar gereja tidak bisa dihubungi', ['url' => $url, 'pesan' => $e->getMessage()]);
            }

            // API gagal → pakai hasil terakhir yang pernah berhasil
            if (! is_array($daftar) || $daftar === []) {
                $daftar = \Illuminate\Support\Facades\Cache::get($cacheKey . '_cadangan', []);
            }
        }

        $cari = trim($cari);
        $hasil = collect($daftar)
            ->filter(fn ($n) => $cari === '' || stripos($n, $cari) !== false)
            ->take(100)
            ->map(fn ($n) => ['id' => $n, 'text' => $n])
            ->values();

        if ($denganLainnya) {
            $hasil->push(['id' => 'other', 'text' => 'Lainnya (tidak ada di daftar)']);
        }

        return response()->json(['results' => $hasil]);
    }

    public function registerTutor(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                new CustomDbRule('mysql', 'kmd_users', 'email', 'unique', 'nik')
            ],
            'no_sim' => [
                'required',
                'string',
                new CustomDbRule('mysql', 'kmd_users', 'no_sim', 'unique', 'nik')
            ],

            'password' => 'required|confirmed|min:6',
            //'rayon_id' => 'required|exists:rayons,id',
            'tahun_lahir' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'idnkp' => 'required|string',
            'statactive' => 'required|string',
            'jenis' => 'required|string',
            
        ], [
            'name.required' => 'Name wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
            //'rayon_id.required' => 'Rayon wajib dipilih.',
            //'rayon_id.exists' => 'Rayon yang dipilih tidak valid.',
            'tahun_lahir.required' => 'Tahun lahir wajib diisi.',
            'tahun_lahir.digits' => 'Tahun lahir harus 4 digit.',
            'tahun_lahir.integer' => 'Tahun lahir harus berupa angka.',
            'tahun_lahir.min' => 'Tahun lahir tidak valid.',
            'tahun_lahir.max' => 'Tahun lahir tidak boleh melebihi tahun sekarang.',
            'no_sim.unique' => 'Nomor SIM sudah terdaftar.', // <-- custom message
        ]);

        // Generate token verifikasi email
        $token = Str::random(64);
        $nik = $this->generateRandomNpk();


        // Simpan user ke database
        $komUser = KomUser::create([
            'nik' => $nik,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'pengajar',
            'status' => 'tidak_aktif',
            //'rayon_id' => $request->rayon_id,
            'asal_gereja' => $request->asal_gereja,
            'email_verification_token' => $token,
            'email_verification_sent_at' => now(),
            'tahun_lahir' => $request->tahun_lahir,
            'idnkp' => $request->idnkp,
            'statactive' => $request->statactive,
            'jenis' => $request->jenis,
            'no_sim' => $request->no_sim,
        ]);
        Log::info('Token verifikasi yang dibuat: ' . $token);
        Log::info('Token di dalam $komUser: ' . $komUser->email_verification_token);

        // Kirim email verifikasi
        try {
            // kom_v02 (performa): dikirim lewat queue → pendaftaran tidak menunggu server email
            Mail::to($komUser->email)->queue(new KomUserVerificationMail($komUser));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email verifikasi: ' . $e->getMessage());
            return redirect()->route('kom.login')->with('error', 'Registrasi berhasil, tapi gagal mengirim email verifikasi. Silakan hubungi admin.');
        }

        return redirect()->route('kom.login')
            ->with('success', 'Registrasi berhasil! Silakan cek email untuk verifikasi.')
            ->with('sim', session('sim')); // Keep data sim di flash session;
    }


    public function register(Request $request)
    {
        // Validasi input
        // $request->validate([
        //     'name' => 'required|string|max:100',
        //     'email' => [
        //         'required',
        //         'email',
        //         new CustomDbRule('mysql', 'kmd_users', 'email', 'unique')
        //     ],
        //     'password' => 'required|confirmed|min:6',
        //     //'rayon_id' => 'required|exists:rayons,id',
        //     'tahun_lahir' => 'required|digits:4|integer|min:1900|max:' . date('Y'),

        // ], [
        //     'name.required' => 'Name wajib diisi.',
        //     'email.required' => 'Email wajib diisi.',
        //     'email.email' => 'Format email tidak valid.',
        //     'email.unique' => 'Email sudah terdaftar.',
        //     'password.required' => 'Password wajib diisi.',
        //     'password.confirmed' => 'Konfirmasi password tidak cocok.',
        //     'password.min' => 'Password minimal 6 karakter.',
        //     //'rayon_id.required' => 'Rayon wajib dipilih.',
        //     //'rayon_id.exists' => 'Rayon yang dipilih tidak valid.',
        //     'tahun_lahir.required' => 'Tahun lahir wajib diisi.',
        //     'tahun_lahir.digits' => 'Tahun lahir harus 4 digit.',
        //     'tahun_lahir.integer' => 'Tahun lahir harus berupa angka.',
        //     'tahun_lahir.min' => 'Tahun lahir tidak valid.',
        //     'tahun_lahir.max' => 'Tahun lahir tidak boleh melebihi tahun sekarang.',
        // ]);
        $request->validate([
    'name' => 'required|string|max:100',
    'email' => 'required|email|unique:kmd_users,email',
    'password' => 'required|confirmed|min:6',
    //'rayon_id' => 'required|exists:rayons,id',
    'tahun_lahir' => 'required|digits:4|integer|min:1900|max:' . date('Y'),

], [
    'name.required' => 'Name wajib diisi.',

    'email.required' => 'Email wajib diisi.',
    'email.email' => 'Format email tidak valid.',
    'email.unique' => 'Email sudah terdaftar.',

    'password.required' => 'Password wajib diisi.',
    'password.confirmed' => 'Konfirmasi password tidak cocok.',
    'password.min' => 'Password minimal 6 karakter.',

    //'rayon_id.required' => 'Rayon wajib dipilih.',
    //'rayon_id.exists' => 'Rayon yang dipilih tidak valid.',

    'tahun_lahir.required' => 'Tahun lahir wajib diisi.',
    'tahun_lahir.digits' => 'Tahun lahir harus 4 digit.',
    'tahun_lahir.integer' => 'Tahun lahir harus berupa angka.',
    'tahun_lahir.min' => 'Tahun lahir tidak valid.',
    'tahun_lahir.max' => 'Tahun lahir tidak boleh melebihi tahun sekarang.',
]);

        // Generate token verifikasi email
        $token = Str::random(64);
        $nik = $this->generateRandomNpk();


        // Simpan user ke database
        $komUser = KomUser::create([
            'nik' => $nik,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'peserta',
            'status' => 'tidak_aktif',
            //'rayon_id' => $request->rayon_id,
            'asal_gereja' => $request->asal_gereja,
            'email_verification_token' => $token,
            'email_verification_sent_at' => now(),
            'tahun_lahir' => $request->tahun_lahir,

        ]);
        Log::info('Token verifikasi yang dibuat: ' . $token);
        Log::info('Token di dalam $komUser: ' . $komUser->email_verification_token);

        // Kirim email verifikasi
        try {
            // kom_v02 (performa): dikirim lewat queue → pendaftaran tidak menunggu server email
            Mail::to($komUser->email)->queue(new KomUserVerificationMail($komUser));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email verifikasi: ' . $e->getMessage());
            return redirect()->route('kom.login')->with('error', 'Registrasi berhasil, tapi gagal mengirim email verifikasi. Silakan hubungi admin.');
        }

        return redirect()->route('kom.login')
            ->with('success', 'Registrasi berhasil! Silakan cek email untuk verifikasi.');


    }

    public function verifyEmail($token)
    {
        $user = KomUser::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('kom.login')->with('error', 'Token tidak valid.');
        }

        if ($user->email_verified_at) {
            return redirect()->route('kom.login')->with('info', 'Email sudah diverifikasi sebelumnya.');
        }

        $tokenLifetime = 10 * 60; // 10 menit dalam detik

        // Gunakan email_verification_sent_at, bukan created_at
        $tokenCreatedTime = strtotime($user->email_verification_sent_at);
        $now = time();

        if (($now - $tokenCreatedTime) > $tokenLifetime) {
            return redirect()->route('kom.login')->with('error', 'Token verifikasi sudah kadaluarsa. 
        <a href="' . route('kom.resend_verification', ['email' => $user->email]) . '">Kirim ulang email verifikasi</a>');
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
            'status' => 'aktif',
        ]);

        return redirect()->route('kom.login')->with('success', 'Email berhasil diverifikasi. Silakan login.');
    }



    public function resendVerification($email)
    {
        $user = KomUser::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('kom.login')->with('error', 'Email tidak ditemukan.');
        }

        if ($user->email_verified_at) {
            return redirect()->route('kom.login')->with('info', 'Email sudah diverifikasi sebelumnya.');
        }

        // Buat token baru
        $token = Str::random(64);

        $user->update([
            'email_verification_token' => $token,
            'email_verification_sent_at' => now(), // reset waktu token agar token lifetime dihitung ulang
        ]);

        try {
            Mail::to($user->email)->queue(new KomUserVerificationMail($user)); // kom_v02: lewat queue
            return redirect()->route('kom.login')->with('success', 'Link verifikasi baru telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            Log::error('Gagal mengirim ulang email verifikasi: ' . $e->getMessage());
            return redirect()->route('kom.login')->with('error', 'Gagal mengirim ulang email verifikasi. Coba lagi nanti.');
        }
    }


    public function showForgotPasswordForm()
    {
        return view('kom_auth.forgot-password');
    }

    // Kirim email reset password
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = KomUser::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        $token = Str::random(64);

        \DB::table('kom_password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        try {
            Mail::to($user->email)->queue(new ResetPasswordMail($token, $user->email)); // kom_v02: lewat queue
            return back()->with('success', 'Link reset password telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email reset password. Silakan coba lagi.');
        }
    }

    // Tampilkan form reset password (dengan token dan email)
    public function showResetForm(Request $request, $token)
    {
        return view('kom_auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email')
        ]);
    }

    // Reset password user
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $record = \DB::table('kom_password_resets')->where('email', $request->email)->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Token reset password tidak valid atau sudah kadaluarsa.']);
        }

        if (!Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'Token reset password tidak valid.']);
        }

        if (\Carbon\Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => 'Token sudah kadaluarsa. Silakan minta reset ulang.']);
        }

        $user = KomUser::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        \DB::table('kom_password_resets')->where('email', $request->email)->delete();

        return redirect()->route('kom.login')->with('success', 'Password berhasil diubah, silakan login.');
    }


    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $users = KomUser::query();

        if ($search) {
            $users = $users->where(function ($query) use ($search) {
                $query->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $users = $users->where('role', $role);
        }

        $users = $users->paginate(10);

        return view('kom_digital.user.index', compact('users', 'search', 'role'));
    }


    public function create()
    {
        $rayons = Rayon::where('status', true)->get(); // hanya rayon aktif
        return view('kom_digital.user.create', compact('rayons'));
    }

    // Fungsi untuk menyimpan data user baru
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100',
            'email' => 'required|email|unique:kom_users',
            'password' => 'required|confirmed|min:6',
            'rayon_id' => 'required|exists:rayons,id',
        ], [
            'username.required' => 'Username wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
            'rayon_id.required' => 'Rayon wajib dipilih.',
            'rayon_id.exists' => 'Rayon yang dipilih tidak valid.',
        ]);

        $token = Str::random(64);

        KomUser::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'rayon_id' => $request->rayon_id,
            'asal_cabang' => $request->asal_cabang,
            'email_verification_token' => $token,
        ]);

        return redirect()->route('kom.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    // Fungsi untuk menampilkan form edit user
    public function edit($id)
    {
        $user = KomUser::findOrFail($id);
        $rayons = Rayon::where('status', true)->get();
        return view('kom_digital.user.edit', compact('user', 'rayons'));
    }

    // Fungsi untuk update data user
    public function update(Request $request, $id)
    {
        $user = KomUser::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:100',
            'email' => 'required|email|unique:kom_users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:6',
            'rayon_id' => 'required|exists:rayons,id',
        ], [
            'username.required' => 'Username wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
            'rayon_id.required' => 'Rayon wajib dipilih.',
            'rayon_id.exists' => 'Rayon yang dipilih tidak valid.',
        ]);

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'rayon_id' => $request->rayon_id,
            'role' => $request->role,
        ]);

        return redirect()->route('kom.users.index')->with('success', 'User berhasil diperbarui.');
    }

    // Fungsi untuk menghapus user
    public function destroy($id)
    {
        $user = KomUser::findOrFail($id);
        $user->delete();

        return redirect()->route('kom.users.index')->with('success', 'User berhasil dihapus.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|boolean'
        ]);

        $user = KomUser::findOrFail($id);
        $user->status = $request->boolean('status') ? 'aktif' : 'tidak_aktif'; // kom_v02: samakan dengan login
        $user->save();

        return redirect()->route('kom.users.index')->with('success', 'Status user berhasil diperbarui.');
    }

    public function getSiswaByRayon(Request $request)
    {
        // Ambil user login dari guard 'kom'
        $user = auth('kom')->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $rayonId = $user->rayon_id;
        $query = KomUser::where('rayon_id', $rayonId);

        // Filter berdasarkan role jika ada
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter pencarian (nama/email)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $siswa = $query->get();
        $rayon = Rayon::find($rayonId);

        return view('kom_digital.siswa.by_rayon', [
            'siswa' => $siswa,
            'rayonId' => $rayonId,
            'rayonName' => $rayon ? $rayon->name : 'Tidak Diketahui',
            'selectedRole' => $request->role,
            'searchTerm' => $request->search,
        ]);
    }


    public function addUserByRayon()
    {
        return view('kom_digital.siswa.addSiswa');
    }



    public function storeUserByRayon(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100|unique:kom_users,username',
            'email' => 'required|email|unique:kom_users,email',
            'password' => 'required|confirmed|min:6',
            'role' => 'required|in:pengajar', // Pastikan tetap validasi role
        ], [
            'username.required' => 'Username wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        $userLogin = Auth::guard('kom')->user();

        if (!$userLogin) {
            return redirect()->back()->with('error', 'Akses tidak sah. Silakan login ulang.');
        }

        $token = Str::random(64);

        KomUser::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'rayon_id' => $userLogin->rayon_id, // Diambil otomatis dari user login
            'email_verification_token' => $token,
        ]);

        return redirect()->route('kom.listPengajar')->with('success', 'User berhasil ditambahkan.');
    }


    public function toggleStatus($id)
    {
        $user = KomUser::findOrFail($id);

        if ($user->status === 'aktif') {
            $user->status = 'tidak_aktif';
        } else {
            $user->status = 'aktif';
        }

        $user->save();

        return redirect()->back()->with('success', 'Status user berhasil diupdate.');
    }


    public function profileKom()
    {
        $user = Auth::user(); // ambil user yang login melalui guard 'kom'

        return view('kom_digital.profile.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password' => ['nullable', 'string', 'min:6'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            if ($user->image && file_exists(public_path('kom/profile/' . $user->image))) {
                unlink(public_path('kom/profile/' . $user->image));
            }

            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('kom/profile'), $filename);

            $user->image = $filename;
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }


    public function listPengajar()
    {
        // Ambil rayon_id dari user yang login via guard 'kom'
        $rayonId = Auth::guard('kom')->user()->rayon_id;

        // Ambil semua user yang role-nya 'pengajar' dan rayon_id-nya sama
        $pengajars = KomUser::where('role', 'pengajar')
            ->where('rayon_id', $rayonId)
            ->get();

        return view('kom_digital.siswa.listPengajar', compact('pengajars'));
    }


    //     public function apiLogin(Request $request)
// {
//     try {
//         $request->validate([
//             'email' => 'required|email',
//             'password' => 'required',
//         ]);

    //         $user = KomUser::where('email', $request->email)->first();

    //         if (!$user) {
//             return response()->json([
//                 'message' => 'Email tidak terdaftar.',
//             ], 404);
//         }

    //         if (!Hash::check($request->password, $user->password)) {
//             return response()->json([
//                 'message' => 'Password yang Anda masukkan salah.',
//             ], 401);
//         }

    //         // Optional: login secara manual ke guard 'kom_api'
//         Auth::guard('kom_api')->login($user);

    //         // Optional: hapus token lama
//         $user->tokens()->delete();

    //         $token = $user->createToken('kom_api_token')->plainTextToken;

    //         return response()->json([
//             'access_token' => $token,
//             'token_type' => 'Bearer',
//             'user' => $user,
//         ]);

    //     } catch (\Illuminate\Validation\ValidationException $ve) {
//         return response()->json([
//             'message' => 'Validasi gagal.',
//             'errors' => $ve->errors(),
//         ], 422);

    //     } catch (\Throwable $e) {
//         Log::error('[LOGIN API ERROR]', [
//             'message' => $e->getMessage(),
//             'trace' => $e->getTraceAsString(),
//             'file' => $e->getFile(),
//             'line' => $e->getLine(),
//         ]);

    //         return response()->json([
//             'message' => 'Terjadi kesalahan pada server.',
//             'error' => $e->getMessage(),
//         ], 500);
//     }
// }

    public function __invoke(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get credentials from request
        $credentials = $request->only('email', 'password');

        //if auth failed
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Anda salah'
            ], 401);
        }

        //if auth success
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api')->user(),
            'token' => $token
        ], 200);
    }
}


