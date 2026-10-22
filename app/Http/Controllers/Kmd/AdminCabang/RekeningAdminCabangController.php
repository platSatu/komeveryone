<?php

namespace App\Http\Controllers\Kmd\AdminCabang;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KmdUserRekening;
use App\Services\Payment\BizbosDisbursementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class RekeningAdminCabangController extends Controller
{
    public function __construct(protected BizbosDisbursementService $payout)
    {
    }

    /**
     * Tampilkan halaman rekening user
     */
    public function index(): View
    {
        $userRekening = KmdUserRekening::with(['user', 'cabang'])->where('user_id', Auth::id())->first();
        [$listBank, $bankError] = $this->listBank();

        return view('a_kom_digital_pusat.admin_cabang.rekening.index', [
            'userRekening' => $userRekening,
            'listBank' => $listBank,
            'payoutAktif' => $this->payout->aktif(),
            'error' => $bankError,
        ]);
    }

    /**
     * Simpan data rekening user (hanya bisa 1x). Kalau tarik dana lewat Bizbos aktif,
     * rekening dicek dulu ke bank — data baru disimpan kalau nama pemiliknya cocok.
     */
    public function store(Request $request)
    {
        $payoutAktif = $this->payout->aktif();

        try {
            $request->validate([
                'nama_rek' => 'required|string|max:100',
                'no_rek' => ['required', 'string', 'max:50', 'regex:/^[0-9]+$/'],
                'nama_bank' => array_merge(['required', 'string', 'max:100'], $payoutAktif ? [Rule::in(array_keys($this->listBank()[0]))] : []),
                'cabang_bank' => 'required|string|max:100',
            ], [
                'nama_rek.required' => 'Nama pemilik rekening harus diisi',
                'nama_rek.max' => 'Nama pemilik rekening maksimal 100 karakter',
                'no_rek.required' => 'Nomor rekening harus diisi',
                'no_rek.regex' => 'Nomor rekening hanya boleh berisi angka',
                'no_rek.max' => 'Nomor rekening maksimal 50 karakter',
                'nama_bank.required' => 'Nama bank harus diisi',
                'nama_bank.in' => 'Pilih bank dari daftar.',
                'nama_bank.max' => 'Nama bank maksimal 100 karakter',
                'cabang_bank.required' => 'Cabang bank harus diisi',
                'cabang_bank.max' => 'Cabang bank maksimal 100 karakter',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Data tidak valid.', 'errors' => $e->errors()], 422);
        }

        $user = Auth::user();

        try {
            $rekening = DB::transaction(function () use ($request, $user, $payoutAktif) {
                DB::table('kmd_users')->where('id', $user->id)->lockForUpdate()->first();

                if (KmdUserRekening::where('user_id', $user->id)->exists()) {
                    throw new RuntimeException('Anda sudah memiliki data rekening. Setiap user hanya dapat menginput rekening sekali.');
                }

                $rekening = new KmdUserRekening([
                    'user_id' => $user->id,
                    'cabang_id' => $user->cabang_id,
                    'nama_rek' => $request->nama_rek,
                    'no_rek' => $request->no_rek,
                    'nama_bank' => $request->nama_bank,
                    'cabang_bank' => $request->cabang_bank,
                ]);

                return $payoutAktif ? $this->payout->verifikasi($rekening, $request->nama_bank) : tap($rekening)->save();
            });
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('[Rekening] Gagal menyimpan rekening.', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan. Silakan coba lagi.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => $payoutAktif
                ? 'Rekening terverifikasi atas nama ' . $rekening->bizbos_verified_name . ' dan berhasil disimpan.'
                : 'Data rekening berhasil disimpan.',
        ]);
    }

    /** Verifikasi rekening lama (tersimpan sebelum tarik dana lewat Bizbos aktif). */
    public function verifikasi(Request $request)
    {
        abort_unless($this->payout->aktif(), 404);

        $data = $request->validate([
            'bank_code' => ['required', 'string', Rule::in(array_keys($this->listBank()[0]))],
        ], ['bank_code.required' => 'Pilih bank dari daftar.', 'bank_code.in' => 'Pilih bank dari daftar.']);

        $rekening = KmdUserRekening::with(['user', 'cabang'])->where('user_id', Auth::id())->firstOrFail();

        try {
            $this->payout->verifikasi($rekening, $data['bank_code']);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('[Rekening] Verifikasi Bizbos gagal.', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);

            return back()->with('error', 'Bizbos belum bisa dihubungi. Silakan coba lagi beberapa saat lagi.');
        }

        return back()->with('success', 'Rekening terverifikasi atas nama ' . $rekening->bizbos_verified_name . '.');
    }

    /** @return array{0: array<string, string>, 1: ?string} [daftar bank, pesan error] */
    private function listBank(): array
    {
        if (! $this->payout->aktif()) {
            return [$this->bankManual(), null];
        }

        try {
            return [$this->payout->banks(), null];
        } catch (\Throwable $e) {
            Log::warning('[Rekening] Daftar bank Bizbos tidak bisa diambil.', ['error' => $e->getMessage()]);

            return [[], 'Daftar bank belum bisa dimuat. Muat ulang halaman beberapa saat lagi.'];
        }
    }

    /**
     * Get list bank populer di Indonesia (dipakai kalau tarik dana lewat Bizbos tidak aktif)
     */
    private function bankManual(): array
    {
        return [
            'BCA' => 'Bank Central Asia (BCA)',
            'BRI' => 'Bank Rakyat Indonesia (BRI)',
            'BNI' => 'Bank Negara Indonesia (BNI)',
            'Mandiri' => 'Bank Mandiri',
            'CIMB' => 'CIMB Niaga',
            'Danamon' => 'Bank Danamon',
            'Permata' => 'Bank Permata',
            'OCBC' => 'OCBC NISP',
            'Maybank' => 'Maybank Indonesia',
            'BTPN' => 'Bank BTPN',
            'Jenius' => 'Jenius (BTPN)',
            'BSI' => 'Bank Syariah Indonesia (BSI)',
            'Muamalat' => 'Bank Muamalat',
            'BCA Syariah' => 'BCA Syariah',
            'BRI Syariah' => 'BRI Syariah',
            'BNI Syariah' => 'BNI Syariah',
            'Mandiri Syariah' => 'Mandiri Syariah',
            'Mega' => 'Bank Mega',
            'Bukopin' => 'Bank Bukopin',
            'HSBC' => 'HSBC Indonesia',
            'Citibank' => 'Citibank',
            'Standard Chartered' => 'Standard Chartered',
            'UOB' => 'United Overseas Bank (UOB)',
            'Panin' => 'Bank Panin',
            'Sinarmas' => 'Bank Sinarmas',
            'Lainnya' => 'Bank Lainnya',
        ];
    }
}
