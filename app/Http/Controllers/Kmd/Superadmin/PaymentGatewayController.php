<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\Payment\BizbosClient;
use App\Services\Payment\PaymentGatewaySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * kom_v02 — Admin KOM: Setting Payment Gateway untuk persembahan kelas tutorial & diklat.
 *  - Midtrans    : kunci tetap di .env (MIDTRANS_SERVER_KEY, MIDTRANS_CLIENT_KEY, MIDTRANS_IS_PRODUCTION)
 *  - BB (Bizbos) : Base URL, API Key, Secret Key, Webhook Secret diisi di sini (secret terenkripsi)
 */
class PaymentGatewayController extends Controller
{
    public function __construct(protected PaymentGatewaySetting $setting)
    {
    }

    public function index()
    {
        return view('a_kom_digital_pusat.superadmin.payment_gateway.index', [
            'pilihan' => $this->setting->get('gateway') ?: PaymentGatewaySetting::MIDTRANS,
            'aktif' => $this->setting->aktif(),
            'daftar' => PaymentGatewaySetting::DAFTAR,
            'midtransLengkap' => $this->setting->midtransLengkap(),
            'midtransProduction' => (bool) config('midtrans.is_production', true),
            'bizbosBaseUrl' => $this->setting->bizbosBaseUrl(),
            'bizbosApiKey' => $this->setting->bizbosApiKey(),
            'adaSecret' => $this->setting->adaNilai('bizbos_secret'),
            'adaWebhookSecret' => $this->setting->adaNilai('bizbos_webhook_secret'),
            'bizbosLengkap' => $this->setting->bizbosLengkap(),
            'webhookUrl' => url('/api/bizbos/webhook'),
            'originPopup' => $this->origin(url('/')),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'gateway' => ['required', Rule::in(array_keys(PaymentGatewaySetting::DAFTAR))],
            'bizbos_base_url' => ['nullable', 'url', 'max:255'],
            'bizbos_api_key' => ['nullable', 'string', 'max:255'],
            'bizbos_secret' => ['nullable', 'string', 'max:255'],
            'bizbos_webhook_secret' => ['nullable', 'string', 'max:255'],
        ], [
            'bizbos_base_url.url' => __('Base URL Bizbos harus berupa alamat lengkap, mis. https://app.bizbos.id'),
        ]);

        // Memilih BB (Bizbos) → semua kredensial wajib ada (yang baru diisi atau yang sudah tersimpan)
        if ($data['gateway'] === PaymentGatewaySetting::BIZBOS) {
            $kurang = [];
            if (! ($data['bizbos_base_url'] ?? $this->setting->bizbosBaseUrl())) $kurang['bizbos_base_url'] = __('Base URL wajib diisi.');
            if (! ($data['bizbos_api_key'] ?? $this->setting->bizbosApiKey())) $kurang['bizbos_api_key'] = __('API Key wajib diisi.');
            if (! ($data['bizbos_secret'] ?? $this->setting->bizbosSecret())) $kurang['bizbos_secret'] = __('Secret Key wajib diisi.');
            if (! ($data['bizbos_webhook_secret'] ?? $this->setting->bizbosWebhookSecret())) $kurang['bizbos_webhook_secret'] = __('Webhook Secret wajib diisi.');

            if ($kurang) {
                return back()->withInput()->withErrors($kurang);
            }
        }

        if ($data['gateway'] === PaymentGatewaySetting::MIDTRANS && ! $this->setting->midtransLengkap()) {
            return back()->withInput()->withErrors(['gateway' => __('Kunci Midtrans di .env belum lengkap (MIDTRANS_SERVER_KEY & MIDTRANS_CLIENT_KEY).')]);
        }

        $lama = $this->setting->aktif();
        $this->setting->simpan($data, Auth::id());

        Log::warning('[PaymentGateway] Setting diubah.', [
            'oleh' => Auth::id(), 'gateway_lama' => $lama, 'gateway_baru' => $data['gateway'],
        ]);

        return redirect()->route('kom.super_payment_gateway.index')
            ->with('success', __('Setting payment gateway berhasil disimpan. Persembahan baru memakai :nama.', [
                'nama' => PaymentGatewaySetting::DAFTAR[$data['gateway']],
            ]));
    }

    /** Tes koneksi & tanda tangan ke Bizbos (GET /api/v1/balance). */
    public function tesBizbos(BizbosClient $client)
    {
        try {
            $saldo = $client->saldo();

            return back()->with('success', __('Koneksi ke Bizbos berhasil. Saldo: Rp :saldo', [
                'saldo' => number_format((float) ($saldo['balance'] ?? 0), 0, ',', '.'),
            ]));
        } catch (\Throwable $e) {
            return back()->with('error', __('Koneksi ke Bizbos gagal: :pesan', ['pesan' => $e->getMessage()]));
        }
    }

    protected function origin(string $url): string
    {
        $p = parse_url($url);

        return ($p['scheme'] ?? 'https') . '://' . ($p['host'] ?? '') . (isset($p['port']) ? ':' . $p['port'] : '');
    }
}
