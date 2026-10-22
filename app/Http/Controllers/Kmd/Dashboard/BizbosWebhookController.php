<?php

namespace App\Http\Controllers\Kmd\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KmdDonasi;
use App\Services\Payment\BizbosClient;
use App\Services\Payment\BizbosPembayaranService;
use App\Services\Payment\PaymentGatewaySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * kom_v02 — Penerima webhook BB (Bizbos): POST /api/bizbos/webhook
 *
 * Daftarkan URL ini di Bizbos › Invoice › API & Integrasi › URL Webhook.
 * Aturan (dokumentasi Bizbos): cek signature dari RAW body + timestamp ≤ 5 menit,
 * balas 2xx secepatnya, dan idempoten (webhook bisa datang lebih dari sekali).
 */
class BizbosWebhookController extends Controller
{
    public function __invoke(Request $request, BizbosClient $client, BizbosPembayaranService $pembayaran)
    {
        $raw = $request->getContent();

        if (! $client->webhookSah($raw, $request->header('X-Bizbos-Timestamp'), $request->header('X-Bizbos-Signature'))) {
            Log::warning('[Bizbos Webhook] Signature tidak valid.', ['ip' => $request->ip()]);

            return response('invalid signature', 401);
        }

        $event = json_decode($raw, true) ?: [];
        $nama = $event['event'] ?? (string) $request->header('X-Bizbos-Event');
        $invoice = $event['data'] ?? [];

        if ($nama === 'ping') {
            return response('OK', 200);
        }

        $externalId = $invoice['external_id'] ?? null;
        $donasi = $externalId
            ? KmdDonasi::where('order_id', $externalId)->where('payment_gateway', PaymentGatewaySetting::BIZBOS)->first()
            : null;

        if (! $donasi) {
            if ($nama === 'invoice.paid') {
                Log::critical('[Bizbos Webhook] Invoice LUNAS tetapi data persembahan tidak ditemukan — cek manual di Bizbos.', [
                    'external_id' => $externalId, 'invoice_id' => $invoice['id'] ?? null, 'total' => $invoice['total'] ?? null,
                ]);
            }

            // tetap 200 supaya Bizbos tidak mengulang terus untuk data yang memang tidak ada
            return response('OK', 200);
        }

        try {
            if (in_array($nama, ['invoice.paid', 'invoice.expired', 'invoice.cancelled'], true)) {
                $pembayaran->sinkron($donasi, $invoice);
            }
        } catch (\Throwable $e) {
            Log::error('[Bizbos Webhook] Gagal memproses event.', ['event' => $nama, 'donasi_id' => $donasi->id, 'error' => $e->getMessage()]);

            return response('error', 500); // Bizbos akan mencoba ulang
        }

        return response('OK', 200);
    }
}
