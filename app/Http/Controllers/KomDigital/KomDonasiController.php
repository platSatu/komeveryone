<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomDonasi;
use App\Models\Kom\KomUser;
use App\Models\Kom\KomSchedule;
use App\Models\Kom\KomModul;
use App\Models\Kom\KomPertanyaanUser;
use App\Models\Rayon;
use Illuminate\Support\Facades\Http;
use Midtrans\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Midtrans\Notification;
use App\Models\Kom\KomZoom;
use App\Models\Kom\KomZoomHistory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;


class KomDonasiController extends Controller
{
    public function __construct()
    {
        // Set Midtrans Config


        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production', true);
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function indexCabang()
    {
        $user = auth('kom')->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $rayonId = $user->rayon_id;

        if (!$rayonId) {
            return view('kom_digital.donasi.indexCabang', [
                'groupedDonasi' => [],
                'totalDonasi' => 0,
                'totalDonasiCabang' => 0,
                'totalFeeKOM' => 0,
            ]);
        }

        // Ambil semua jadwal dalam rayon ini
        $schedules = KomSchedule::where('rayon_id', $rayonId)->with(['komModul', 'komClass'])->get();

        $groupedDonasi = [];

        foreach ($schedules as $schedule) {
            $donasi = KomDonasi::with('komUser')
                ->where('kom_rayons_id', $rayonId)
                ->where('kom_schedules_id', $schedule->id)
                ->get();

            $totalPaid = $donasi->where('payment_status', 'paid')->sum('paid_amount_value');

            if ($donasi->isNotEmpty()) {
                $groupedDonasi[] = [
                    'schedule' => $schedule,
                    'donasi' => $donasi,
                    'total_paid' => $totalPaid,
                ];
            }
        }

        $totalDonasi = KomDonasi::where('kom_rayons_id', $rayonId)
            ->where('payment_status', 'paid')
            ->sum('donate');

        $totalDonasiCabang = $totalDonasi / 1.11;  // 89% untuk cabang
        $totalFeeKOM = $totalDonasi * 0.11;        // 11% fee aplikasi

        return view('kom_digital.donasi.indexCabang', compact('groupedDonasi', 'totalDonasi', 'totalDonasiCabang', 'totalFeeKOM'));
    }



    public function index()
    {

        return view('kom_digital.donasi.form');
    }


    public function store(Request $request)
    {
        Log::info('[DONASI] Mulai proses donasi');

        DB::beginTransaction();

        try {
            $user = auth('kom')->user();
            $orderId = 'DONASI-' . strtoupper(Str::random(10));

            // Ambil pertanyaan terakhir user (jika ada)
            $lastPertanyaan = KomPertanyaanUser::where('kom_user_id', $user->id)
                ->latest('created_at')
                ->first();


            $donate = 40000; // yang masuk ke cabang
            $fee = 5000;     // 11% fee (bisa 35000 * 0.11 kalau mau dinamis)
            $total = $donate + $fee;

            $donation = KomDonasi::create([
                'partner_service_id' => 1,
                'customer_no' => 'CUST-' . strtoupper(Str::random(8)),
                'virtual_account_no' => 'VA-' . strtoupper(Str::random(10)),
                'virtual_account_name' => $user->name ?? 'Donatur Umum',
                'trx_id' => 'TRX-' . strtoupper(Str::random(10)),
                'payment_request_id' => $orderId,
                'paid_amount_value' => $total,
                'fee' => $fee,
                'donate' => $donate,
                'paid_amount_currency' => 'IDR',
                'virtual_account_email' => $user->email ?? 'donatur@example.com',
                'virtual_account_phone' => $user->phone ?? '08123456789',
                'payment_status' => 'pending',
                'donation_type' => 'KOM',
                'kom_user_id' => $user->id,
                'kom_rayons_id' => $lastPertanyaan->rayons_id ?? null,
                'kom_schedules_id' => $lastPertanyaan->kom_schedules_id ?? null,
                'kom_moduls_id' => $lastPertanyaan->kom_moduls_id ?? null,
            ]);

            // Generate Snap Token dari Midtrans
            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $donation->paid_amount_value,
                ],
                'item_details' => [
                    [
                        'id' => $donation->id,
                        'price' => $donation->paid_amount_value,
                        'quantity' => 1,
                        'name' => 'Donasi ' . $donation->donation_type,
                    ]
                ],
                'customer_details' => [
                    'first_name' => $donation->virtual_account_name,
                    'email' => $donation->virtual_account_email,
                    'phone' => $donation->virtual_account_phone,
                ],
            ]);

            DB::commit();

            Log::info('[DONASI] Donasi berhasil dibuat', [
                'donation_id' => $donation->id,
                'snap_token' => $snapToken,
            ]);

            return view('kom_digital.donasi.payment', [
                'snapToken' => $snapToken,
                'donation' => $donation,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('[DONASI] Gagal memproses donasi', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Gagal memproses donasi: ' . $e->getMessage());
        }
    }

    //     public function store(Request $request)
// {
//     Log::info('[DONASI] Mulai proses donasi');
//     Log::info('Server Key: ' . env('MIDTRANS_SERVER_KEY'));
//     Log::info('Client Key: ' . env('MIDTRANS_CLIENT_KEY'));

    //     DB::beginTransaction();

    //     try {
//         $user = auth('kom')->user();
//         $orderId = 'DONASI-' . strtoupper(Str::random(10));

    //         // Ambil pertanyaan terakhir user (jika ada)
//         $lastPertanyaan = KomPertanyaanUser::where('kom_user_id', $user->id)
//             ->latest('created_at')
//             ->first();

    //         // Nilai donasi & fee
//         $donate = 35000; // yang masuk ke cabang
//         $fee = 3850;     // 11% fee (bisa 35000 * 0.11 kalau mau dinamis)
//         $total = $donate + $fee;

    //         // Simpan ke database
//         $donation = KomDonasi::create([
//             'partner_service_id'      => 1,
//             'customer_no'             => 'CUST-' . strtoupper(Str::random(8)),
//             'virtual_account_no'      => 'VA-' . strtoupper(Str::random(10)),
//             'virtual_account_name'    => $user->name ?? 'Donatur Umum',
//             'trx_id'                  => 'TRX-' . strtoupper(Str::random(10)),
//             'payment_request_id'      => $orderId,
//             'paid_amount_value'       => $total,
//             'paid_amount_currency'    => 'IDR',
//             'virtual_account_email'   => $user->email ?? 'donatur@example.com',
//             'virtual_account_phone'   => $user->phone ?? '08123456789',
//             'payment_status'          => 'pending',
//             'donation_type'           => 'KOM',
//             'kom_user_id'             => $user->id,
//             'kom_rayons_id'           => $lastPertanyaan->rayons_id ?? null,
//             'kom_schedules_id'        => $lastPertanyaan->kom_schedules_id ?? null,
//             'kom_moduls_id'           => $lastPertanyaan->kom_moduls_id ?? null,
//             'donate'                  => $donate,
//             'fee'                     => $fee,
//         ]);

    //         // Generate Snap Token dari Midtrans
//         $snapToken = Snap::getSnapToken([
//             'transaction_details' => [
//                 'order_id'     => $orderId,
//                 'gross_amount' => $total,
//             ],
//             'item_details' => [
//                 [
//                     'id'       => $donation->id,
//                     'price'    => $total,
//                     'quantity' => 1,
//                     'name'     => 'Donasi ' . $donation->donation_type,
//                 ]
//             ],
//             'customer_details' => [
//                 'first_name' => $donation->virtual_account_name,
//                 'email'      => $donation->virtual_account_email,
//                 'phone'      => $donation->virtual_account_phone,
//             ],
//         ]);

    //         DB::commit();

    //         Log::info('[DONASI] Donasi berhasil dibuat', [
//             'donation_id' => $donation->id,
//             'snap_token'  => $snapToken,
//         ]);

    //         return view('kom_digital.donasi.payment', [
//             'snapToken' => $snapToken,
//             'donation'  => $donation,
//         ]);

    //     } catch (\Exception $e) {
//         DB::rollBack();

    //         Log::error('[DONASI] Gagal memproses donasi', [
//             'error_message' => $e->getMessage(),
//             'trace'         => $e->getTraceAsString(),
//         ]);

    //         return back()->with('error', 'Gagal memproses donasi: ' . $e->getMessage());
//     }
// }
    public function callbackMidtrans(Request $request)
    {
        \Log::info('[MIDTRANS CALLBACK] Callback diterima.', [
            'request' => $request->all()
        ]);

        try {
            $notif = new \Midtrans\Notification();

            $transaction = $notif->transaction_status;
            $order_id = $notif->order_id;

            \Log::info('[MIDTRANS CALLBACK] Status transaksi: ' . $transaction . ', Order ID: ' . $order_id);

            $donasi = \App\Models\Kom\KomDonasi::where('payment_request_id', $order_id)->first();

            if (!$donasi) {
                \Log::error('[DONASI] Data donasi tidak ditemukan untuk order ID: ' . $order_id);
                return response()->json(['error' => 'Data donasi tidak ditemukan'], 404);
            }

            \Log::info('[DONASI] Data donasi ditemukan', [
                'id' => $donasi->id,
                'kom_rayons_id' => $donasi->kom_rayons_id,
                'email' => $donasi->virtual_account_email
            ]);

            if (in_array($transaction, ['settlement', 'capture'])) {
                $donasi->update(['payment_status' => 'paid']);
                \Log::info('[DONASI] Pembayaran ditandai sebagai PAID untuk order ID: ' . $order_id);

                // Cari Zoom berdasarkan kom_rayons_id dari donasi
                $zoom = \App\Models\Kom\KomZoom::where('rayons_id', $donasi->kom_rayons_id)
                    ->where('status', 'aktif')
                    ->first();

                if ($zoom) {
                    \Log::info('[ZOOM] Zoom ditemukan untuk kom_rayons_id: ' . $donasi->kom_rayons_id, [
                        'zoom_id' => $zoom->id,
                        'description' => $zoom->description
                    ]);

                    // ✅ Simpan histori Zoom peserta
                    try {
                        \App\Models\Kom\KomZoomHistory::create([
                            'kom_user_id' => $donasi->kom_user_id,
                            'kom_donasi_id' => $donasi->id,
                            'kom_zoom_id' => $zoom->id,
                            'zoom_link' => $zoom->description, // atau gunakan $zoom->zoom_link jika ada field khusus
                            'sent_at' => now(),
                        ]);
                        \Log::info('[ZOOM HISTORY] Link Zoom disimpan ke histori peserta.');
                    } catch (\Exception $e) {
                        \Log::error('[ZOOM HISTORY] Gagal menyimpan histori Zoom', [
                            'message' => $e->getMessage()
                        ]);
                    }

                    // Kirim email Zoom
                    try {
                        // kom_v02 (performa): lewat antrean, supaya callback pembayaran tidak menunggu server email.
                        Mail::to($donasi->virtual_account_email)->queue(new \App\Mail\ZoomLinkMail($zoom->description));
                        \Log::info('[EMAIL] Link Zoom masuk antrean untuk: ' . $donasi->virtual_account_email);
                    } catch (\Exception $e) {
                        \Log::error('[EMAIL] Gagal mengirim email Zoom ke: ' . $donasi->virtual_account_email, [
                            'message' => $e->getMessage()
                        ]);
                    }
                } else {
                    \Log::warning('[ZOOM] Tidak ditemukan Zoom aktif untuk kom_rayons_id: ' . $donasi->kom_rayons_id);
                }
            } else {
                \Log::info('[DONASI] Status bukan capture/settlement, tidak diproses lebih lanjut.');
            }

            return response()->json(['status' => 'OK']);
        } catch (\Exception $e) {
            \Log::error('[MIDTRANS CALLBACK ERROR]', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Terjadi kesalahan saat memproses callback.'], 500);
        }
    }

    // public function callbackMidtrans(Request $request)
    // {
    //     \Log::info('[MIDTRANS CALLBACK] Callback diterima.', [
    //         'request' => $request->all()
    //     ]);

    //     try {
    //         $notif = new \Midtrans\Notification();

    //         $transaction = $notif->transaction_status;
    //         $order_id = $notif->order_id;

    //         \Log::info('[MIDTRANS CALLBACK] Status transaksi: ' . $transaction . ', Order ID: ' . $order_id);

    //         $donasi = KomDonasi::where('payment_request_id', $order_id)->first();

    //         if (!$donasi) {
    //             \Log::error('[DONASI] Data donasi tidak ditemukan untuk order ID: ' . $order_id);
    //             return response()->json(['error' => 'Data donasi tidak ditemukan'], 404);
    //         }

    //         \Log::info('[DONASI] Data donasi ditemukan', [
    //             'id' => $donasi->id,
    //             'kom_rayons_id' => $donasi->kom_rayons_id,
    //             'email' => $donasi->virtual_account_email
    //         ]);

    //         if (in_array($transaction, ['settlement', 'capture'])) {
    //             $donasi->update(['payment_status' => 'paid']);
    //             \Log::info('[DONASI] Pembayaran ditandai sebagai PAID untuk order ID: ' . $order_id);

    //             // Cari Zoom berdasarkan kom_rayons_id dari donasi
    //             $zoom = \App\Models\Kom\KomZoom::where('rayons_id', $donasi->kom_rayons_id)
    //                 ->where('status', 'aktif')
    //                 ->first();

    //             if ($zoom) {
    //                 \Log::info('[ZOOM] Zoom ditemukan untuk kom_rayons_id: ' . $donasi->kom_rayons_id, [
    //                     'zoom_id' => $zoom->id,
    //                     'description' => $zoom->description
    //                 ]);

    //                 try {
    //                     Mail::to($donasi->virtual_account_email)->send(new \App\Mail\ZoomLinkMail($zoom->description));
    //                     \Log::info('[EMAIL] Link Zoom berhasil dikirim ke: ' . $donasi->virtual_account_email);
    //                 } catch (\Exception $e) {
    //                     \Log::error('[EMAIL] Gagal mengirim email Zoom ke: ' . $donasi->virtual_account_email, [
    //                         'message' => $e->getMessage()
    //                     ]);
    //                 }
    //             } else {
    //                 \Log::warning('[ZOOM] Tidak ditemukan Zoom aktif untuk kom_rayons_id: ' . $donasi->kom_rayons_id);
    //             }
    //         } else {
    //             \Log::info('[DONASI] Status bukan capture/settlement, tidak diproses lebih lanjut.');
    //         }

    //         return response()->json(['status' => 'OK']);
    //     } catch (\Exception $e) {
    //         \Log::error('[MIDTRANS CALLBACK ERROR]', [
    //             'message' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return response()->json(['error' => 'Terjadi kesalahan saat memproses callback.'], 500);
    //     }
    // }


    public function sukses()
    {
        return view('kom_digital.donasi.sukses');
    }

    public function getSnapToken(Request $request)
    {
        $clientId = 'BRN-0211-1747887056999';
        $secretKey = 'SK-hTuxrZB4sOyj7JIgy6X4';
        $apiUrl = 'https://api-sandbox.doku.com/checkout/v1/payment';

        $invoiceNumber = 'INV-' . now()->timestamp;

        $bodyArray = [
            "order" => [
                "amount" => 80000,
                "invoice_number" => $invoiceNumber,
                "currency" => "IDR",
                "callback_url" => "https://gbigatsu.id/kom-digital/donasi/callback/success",
                "callback_url_cancel" => "https://gbigatsu.id/kom-digital/donasi/callback/cancel",
                "callback_url_result" => "https://gbigatsu.id/kom-digital/donasi/callback/result",
                "language" => "EN",
                "auto_redirect" => true,
                "disable_retry_payment" => true,
                "recover_abandoned_cart" => true,
                "expired_recovered_cart" => 2,
                "line_items" => [
                    [
                        "id" => "001",
                        "name" => "Fresh flowers",
                        "quantity" => 1,
                        "price" => 40000,
                        "sku" => "FF01",
                        "category" => "gift-and-flowers",
                        "url" => "https://merchant.com/products/flowers",
                        "image_url" => "https://merchant.com/images/flowers.jpg",
                        "type" => "product"
                    ],
                    [
                        "id" => "002",
                        "name" => "T-shirt",
                        "quantity" => 1,
                        "price" => 40000,
                        "sku" => "T01",
                        "category" => "clothing",
                        "url" => "https://merchant.com/products/tshirt",
                        "image_url" => "https://merchant.com/images/tshirt.jpg",
                        "type" => "product"
                    ]
                ]
            ],
            "payment" => [
                "payment_due_date" => 60,
                "type" => "SALE",
                "payment_method_types" => [
                    "VIRTUAL_ACCOUNT_BCA",
                    "QRIS",
                    "EMONEY_OVO"
                ]
            ],
            "customer" => [
                "id" => "JC-01",
                "name" => "Zolanda",
                "last_name" => "Anggraeni",
                "phone" => "628121212121",
                "email" => "zolanda@example.com",
                "address" => "Taman Setiabudi",
                "postcode" => "120129",
                "state" => "Jakarta",
                "city" => "Jakarta Selatan",
                "country" => "ID"
            ],
            "shipping_address" => [
                "first_name" => "Joe",
                "last_name" => "Doe",
                "address" => "Jalan DOKU no 15",
                "city" => "Jakarta",
                "postal_code" => "11923",
                "phone" => "081312345678",
                "country_code" => "IDN"
            ],
            "billing_address" => [
                "first_name" => "Joe",
                "last_name" => "Doe",
                "address" => "Jalan DOKU no 15",
                "city" => "Jakarta",
                "postal_code" => "11923",
                "phone" => "081312345678",
                "country_code" => "IDN"
            ],
            "additional_info" => [
                "allow_tenor" => [0, 3, 6],
                "doku_wallet_notify_url" => "https://gbigatsu.id/kom-digital/donasi/wallet-notify",
                "override_notification_url" => "https://gbigatsu.id/kom-digital/donasi/payment-notification"
            ]
        ];

        $body = json_encode($bodyArray, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $digest = base64_encode(hash('sha256', $body, true));
        $requestId = (string) \Illuminate\Support\Str::uuid();
        $timestamp = \Carbon\Carbon::now('UTC')->toIso8601ZuluString();  // PENTING: pakai format dengan Z
        $requestTarget = '/checkout/v1/payment';

        $signatureRaw = "Client-Id:$clientId\nRequest-Id:$requestId\nRequest-Timestamp:$timestamp\nRequest-Target:$requestTarget\nDigest:$digest";
        $signature = base64_encode(hash_hmac('sha256', $signatureRaw, $secretKey, true));

        \Log::info('Signature Raw: ' . $signatureRaw);
        \Log::info('Digest: ' . $digest);
        \Log::info('Signature: ' . $signature);

        $headers = [
            'Client-Id' => $clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Request-Target' => $requestTarget,
            'Digest' => $digest,
            'Signature' => 'HMACSHA256=' . $signature,
            'Content-Type' => 'application/json'
        ];

        $response = \Illuminate\Support\Facades\Http::withHeaders($headers)->post($apiUrl, $bodyArray);

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Gagal mendapatkan URL pembayaran dari DOKU',
                'doku_response' => $response->json()
            ], 500);
        }

        $responseData = $response->json();
        if (!isset($responseData['payment']['url'])) {
            return response()->json([
                'error' => 'URL pembayaran tidak ditemukan dalam response',
                'doku_response' => $responseData
            ], 500);
        }

        return response()->json(['paymentUrl' => $responseData['payment']['url']]);
    }


    // Contoh route callback untuk sukses
    public function callbackSuccess(Request $request)
    {
        return redirect()->route('kom.donasi.index')->with('success', 'Pembayaran berhasil.');
    }

    // ✅ Callback ketika user membatalkan pembayaran
    public function callbackCancel(Request $request)
    {
        return redirect()->route('kom.donasi.index')->with('error', 'Pembayaran dibatalkan.');
    }

    // ✅ Callback hasil pembayaran (biasanya ini di-trigger otomatis oleh DOKU, tapi diarahkan ke user juga)
    public function callbackResult(Request $request)
    {
        Log::info('DOKU callbackResult:', $request->all());
        return redirect()->route('kom.donasi.index')->with('info', 'Pembayaran sedang diproses.');
    }

    // ✅ Notifikasi dari DOKU Wallet (bisa digunakan untuk pembayaran menggunakan DOKU wallet)
    public function dokuWalletNotify(Request $request)
    {
        Log::info('DOKU Wallet Notify:', $request->all());

        // Verifikasi signature (kalau implementasi verifyDokuSignature kamu sudah benar)
        if (!$this->verifyDokuSignature($request)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Optional: Tambahkan logic jika perlu proses tambahan
        return response()->json(['message' => 'OK']);
    }

    // ✅ Callback utama notifikasi pembayaran dari DOKU (penting!)
    public function paymentNotification(Request $request)
    {
        Log::info('DOKU Payment Notification Override:', $request->all());

        if (!$this->verifyDokuSignature($request)) {
            Log::warning('Invalid signature from DOKU');
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $data = $request->all();
        $invoiceNumber = $data['order']['invoice_number'] ?? null;
        $status = $data['transaction']['status'] ?? null;

        if ($invoiceNumber && $status) {
            $donation = KomDonasi::where('invoice_number', $invoiceNumber)->first();

            if ($donation) {
                $donation->payment_status = $status;
                $donation->save();

                Log::info("Donation #$invoiceNumber updated with status $status");
            } else {
                Log::warning("Donation with invoice $invoiceNumber not found");
            }
        } else {
            Log::warning('Missing invoice_number or status in DOKU notification.');
        }

        return response()->json(['message' => 'OK']);
    }

    // 🔐 Signature Verification (pastikan ini kamu implementasi dengan benar)
    private function verifyDokuSignature(Request $request)
    {
        // Simpel dummy verifikasi, kamu bisa ganti dengan logika dari dokumentasi resmi DOKU
        return true;
    }


    // Menampilkan form untuk membuat donasi baru
    public function create()
    {
        $users = KomUser::all();
        $rayons = Rayon::all();
        $schedules = KomSchedule::all();
        $moduls = KomModul::all();

        return view('kom_digital.donasi.create', compact('users', 'rayons', 'schedules', 'moduls'));
    }

    // Menyimpan donasi baru ke database
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'partner_service_id' => 'required|string',
    //         'customer_no' => 'required|string',
    //         'virtual_account_no' => 'required|string',
    //         'virtual_account_name' => 'required|string',
    //         'trx_id' => 'required|string',
    //         'payment_request_id' => 'required|string',
    //         'paid_amount_value' => 'required|numeric',
    //         'paid_amount_currency' => 'required|string',
    //         'virtual_account_email' => 'required|email',
    //         'virtual_account_phone' => 'required|string',
    //         'payment_status' => 'required|string',
    //         'donation_type' => 'required|string',
    //         'kom_user_id' => 'required|exists:kom_users,id',
    //         'kom_rayons_id' => 'required|exists:rayons,id',
    //         'kom_schedules_id' => 'required|exists:kom_schedules,id',
    //         'kom_moduls_id' => 'required|exists:kom_moduls,id',
    //     ]);

    //     KomDonasi::create($request->all());

    //     return redirect()->route('kom.donasi.index')->with('success', 'Donasi berhasil disimpan.');
    // }

    // Menampilkan form untuk mengedit donasi
    public function edit($id)
    {
        $donasi = KomDonasi::findOrFail($id);
        $users = KomUser::all();
        $rayons = Rayon::all();
        $schedules = KomSchedule::all();
        $moduls = KomModul::all();

        return view('kom_digital.donasi.edit', compact('donasi', 'users', 'rayons', 'schedules', 'moduls'));
    }

    // Mengupdate data donasi
    public function update(Request $request, $id)
    {
        $request->validate([
            'partner_service_id' => 'required|string',
            'customer_no' => 'required|string',
            'virtual_account_no' => 'required|string',
            'virtual_account_name' => 'required|string',
            'trx_id' => 'required|string',
            'payment_request_id' => 'required|string',
            'paid_amount_value' => 'required|numeric',
            'paid_amount_currency' => 'required|string',
            'virtual_account_email' => 'required|email',
            'virtual_account_phone' => 'required|string',
            'payment_status' => 'required|string',
            'donation_type' => 'required|string',
            'kom_user_id' => 'required|exists:kom_users,id',
            'kom_rayons_id' => 'required|exists:rayons,id',
            'kom_schedules_id' => 'required|exists:kom_schedules,id',
            'kom_moduls_id' => 'required|exists:kom_moduls,id',
        ]);

        $donasi = KomDonasi::findOrFail($id);
        $donasi->update($request->all());

        return redirect()->route('kom.donasi.index')->with('success', 'Donasi berhasil diperbarui.');
    }

    // Menghapus donasi
    public function destroy($id)
    {
        $donasi = KomDonasi::findOrFail($id);
        $donasi->delete();

        return redirect()->route('kom.donasi.index')->with('success', 'Donasi berhasil dihapus.');
    }

    public function showSaldo()
    {
        $user = Auth::guard('kom')->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Batasi akses hanya untuk admin_cabang
        if (!in_array($user->role, ['admin_cabang', 'admin_kom_pusat'])) {
            abort(403, 'Hanya admin yang bisa mengakses halaman ini.');
        }

        $rayonId = $user->rayon_id;

        // Ambil total saldo dari seluruh donasi paid di rayon ini
        $totalSaldo = KomDonasi::getSaldoByUserRayon($rayonId, null);
        $rayon = Rayon::find($rayonId);

        return view('kom_digital.donasi.totalSaldo', [
            'totalSaldo' => $totalSaldo,
            'userName' => $user->username,
            'rayonName' => $rayon ? $rayon->name : '-',
        ]);
    }

    public function viewMutasiAdminKom(Request $request)
{
    // Ambil nilai filter dari query string
    $rayonId = $request->input('rayon_id');

    // Query dasar
    $query = KomDonasi::with(['komUser', 'komModul', 'komSchedule'])
        ->where('payment_status', 'paid');

    // Filter berdasarkan rayon jika ada
    if ($rayonId) {
        $query->whereHas('komUser', function ($q) use ($rayonId) {
            $q->where('rayon_id', $rayonId);
        });
    }

    // Ambil data
    $donations = $query->orderBy('created_at', 'desc')->get();

    // Hitung total
    $totalDonate = $donations->sum('donate');
    $totalFee = $donations->sum('fee');
    $totalPaid = $donations->sum('paid_amount_value');

    // Ambil daftar rayon untuk pilihan filter
    $rayons = \App\Models\Rayon::all(); // Pastikan model Rayon ada

    return view('kom_digital.peserta.viewSaldoAdminKom', compact(
        'donations',
        'totalDonate',
        'totalFee',
        'totalPaid',
        'rayons',
        'rayonId'
    ));
}



}
