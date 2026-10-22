<?php

namespace App\Http\Controllers\Kmd\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kmd\KmdDonasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use App\Mail\ThankYouDonasiMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Kmd\KmdUserClassTutorial;


class PersembahanController extends Controller
{
    public function __construct()
    {
        // kom_v02 (keamanan): dulu server key & client key DICATAT ke laravel.log di setiap request.
        // kom_v02: kunci dibaca dari config/midtrans.php (aman dipakai setelah `php artisan optimize`).
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production', true); // false = sandbox
        Config::$isSanitized = config('midtrans.is_sanitized', true);
        Config::$is3ds = config('midtrans.is_3ds', true);
    }

    public function formPersembahan()
    {
        // kom_v02: batas waktu memberikan persembahan (hitung mundur di halaman)
        $progress = KmdUserClassTutorial::where('user_id', Auth::id())
            ->where('status_progress', 'pending')
            ->where('step', 'donasi')
            ->latest()
            ->first();
        $batasWaktu = $progress ? app(\App\Services\Kmd\CheckoutTutorialService::class)->batasWaktu($progress) : null;

        // $joinKelas = session('join_kelas');
        // if (!$joinKelas) {
        //     return redirect()->route('kom.kelas_tutorial.index')->with('error', __('Silakan pilih kelas tutorial terlebih dahulu.'));
        // }

        // kom_v02: payment gateway aktif (Midtrans / BB Bizbos) — diatur superadmin
        $gatewaySetting = app(\App\Services\Payment\PaymentGatewaySetting::class);

        return view('a_kom_digital_pusat.dashboard.persembahan', [
            'gateway' => $gatewaySetting->aktif(),
            'namaGateway' => $gatewaySetting->nama(),
            'bizbosBaseUrl' => $gatewaySetting->bizbosBaseUrl(),
            'batasWaktu' => $batasWaktu,
            'menitBatas' => app(\App\Services\Kmd\CheckoutTutorialService::class)->menitPersembahan(),
        ]);
    }

    /**
     * kom_v02: peserta membatalkan pendaftaran kelas tutorial yang belum dibayar,
     * lalu kembali memilih jadwal (pertanyaan & persembahan yang belum lunas ikut dihapus).
     */
    public function batalkanPendaftaran(Request $request)
    {
        $checkout = app(\App\Services\Kmd\CheckoutTutorialService::class);

        $progress = KmdUserClassTutorial::where('user_id', Auth::id())
            ->where('status_progress', 'pending')
            ->whereIn('step', ['pilih', 'pertanyaan', 'donasi'])
            ->latest()
            ->first();

        if (! $progress) {
            return redirect()->route('kom.kelas_tutorial.index');
        }

        $hasil = $checkout->bersihkan($progress, true);

        if ($hasil === \App\Services\Kmd\CheckoutTutorialService::TUNGGU) {
            return back()->with('error', __('Pembayaran Anda sedang diproses oleh bank, jadi pendaftaran belum bisa dibatalkan. Silakan tunggu beberapa menit.'));
        }

        if ($hasil === \App\Services\Kmd\CheckoutTutorialService::LUNAS) {
            return redirect()->route('kom.peserta.kelas_tutorial')
                ->with('success', __('Pembayaran Anda ternyata sudah berhasil. Pendaftaran kelas tutorial Anda aktif.'));
        }

        $tujuan = $progress->category_diklat
            ? route('kom.diklat_tutor.index')
            : route('kom.kelas_tutorial.index', ['modul_id' => $progress->modul_id, 'titik' => $progress->video_id ?: 'akhir']);

        return redirect($tujuan)->with('success', __('Pendaftaran dibatalkan. Silakan pilih jadwal kelas tutorial lagi.'));
    }

    // public function generateSnapToken(Request $request)
    // {
    //     $user = Auth::user();
    //     $joinKelas = session('join_kelas');
    //     if (!$joinKelas) {
    //         return response()->json(['error' => __('Silakan pilih kelas tutorial terlebih dahulu.')], 400);
    //     }

    //     $amount = 40000;
    //     $fee = 5000;
    //     $total = $amount + $fee;
    //     $orderId = uniqid('order_');

    //     // Simpan data donasi dulu tanpa token
    //     $donasi = KmdDonasi::create([
    //         'user_id' => $user->id,
    //         'userclasstutorial_id' => $joinKelas['userclasstutorial_id'],
    //         'modul_id' => $joinKelas['modul_id'],
    //         'class_id' => $joinKelas['class_id'],
    //         'cabang_id' => $joinKelas['rayon_id'],
    //         'order_id' => $orderId,
    //         'amount' => $amount,
    //         'fee_payment_gateway' => $fee,
    //         'payment_type' => 'midtrans',
    //         'transaction_time' => now(),
    //         'transaction_status' => 'pending',
    //         'payment_token' => null,
    //         'status' => 'pending',
    //     ]);

    //     $params = [
    //         'transaction_details' => [
    //             'order_id' => $orderId,
    //             'gross_amount' => $total,
    //         ],
    //         'customer_details' => [
    //             'first_name' => $user->name ?? 'User ',
    //             'email' => $user->email,
    //             'phone' => $user->phone ?? '',
    //         ],
    //     ];

    //     try {
    //         $snapToken = Snap::getSnapToken($params);

    //         // Update token di DB
    //         $donasi->update(['payment_token' => $snapToken]);

    //         return response()->json(['snap_token' => $snapToken]);
    //     } catch (\Exception $e) {
    //         // Jika error, hapus data transaksi yang sudah dibuat
    //         $donasi->delete();

    //         Log::error('Midtrans Error: ' . $e->getMessage());
    //         return response()->json(['error' => 'Gagal membuat transaksi: ' . $e->getMessage()], 500);
    //     }
    // }
    // public function generateSnapToken(Request $request)
    // {
    //     // Fallback untuk cek apakah fungsi ini terpanggil di server logs biasa
    //     error_log('generateSnapToken: function called');

    //     try {
    //         Log::info('generateSnapToken: function called');

    //         $user = Auth::guard('kom')->user();

    //         if (!$user) {
    //             Log::warning('generateSnapToken: User belum login saat request generate snap token.');
    //             return response()->json(['error' => 'User belum login.'], 401);
    //         }

    //         Log::info("generateSnapToken: Request dari user_id={$user->id}, nama={$user->name}");

    //         // Ambil progress donasi yang masih pending
    //         $progress = KmdUserClassTutorial::where('user_id', $user->id)
    //             ->where('status_progress', 'pending')
    //             ->where('step', 'donasi')
    //             ->latest()
    //             ->first();

    //         if (!$progress) {
    //             Log::warning("generateSnapToken: Tidak menemukan progress donasi yang pending untuk user_id={$user->id}");
    //             return response()->json(['error' => __('Silakan pilih kelas tutorial terlebih dahulu.')], 400);
    //         }

    //         Log::info("generateSnapToken: Progress ditemukan id={$progress->id} untuk user_id={$user->id}");

    //         $amount = 40000;
    //         $fee = 5000;
    //         $total = $amount + $fee;

    //         // Cari donasi yang masih pending untuk progress ini
    //         $donasi = KmdDonasi::where('user_id', $user->id)
    //             ->where('userclasstutorial_id', $progress->id)
    //             ->where('status', 'pending')
    //             ->latest()
    //             ->first();

    //         // if ($donasi && !empty($donasi->payment_token)) {
    //         //     Log::info("generateSnapToken: Ditemukan donasi pending id={$donasi->id}, menggunakan token lama.");
    //         //     return response()->json(['snap_token' => $donasi->payment_token]);
    //         // }
    //         if ($donasi) {
    //             // Cek ke Midtrans status transaksi real-time (opsional, tapi akurat)
    //             try {
    //                 $midtransStatus = \Midtrans\Transaction::status($donasi->order_id);
    //                 $transactionStatus = $midtransStatus->transaction_status;

    //                 Log::info("generateSnapToken: Cek status Midtrans untuk order_id={$donasi->order_id} => status={$transactionStatus}");

    //                 // Update status transaksi di database jika berbeda
    //                 if ($donasi->transaction_status !== $transactionStatus) {
    //                     $donasi->update(['transaction_status' => $transactionStatus]);
    //                 }

    //                 // Jika status masih bisa digunakan (pending/token masih aktif), gunakan token lama
    //                 if (in_array($transactionStatus, ['pending'])) {
    //                     Log::info("generateSnapToken: Transaksi masih pending, gunakan token lama.");
    //                     return response()->json(['snap_token' => $donasi->payment_token]);
    //                 }

    //                 // Jika expired, denied, atau cancel → lanjut generate token baru
    //                 Log::info("generateSnapToken: Transaksi sebelumnya tidak valid (status={$transactionStatus}), buat token baru.");
    //             } catch (\Exception $e) {
    //                 Log::warning("generateSnapToken: Gagal cek status Midtrans, asumsikan donasi tidak valid. Error: " . $e->getMessage());
    //                 // Jika tidak bisa cek status (misal order_id tidak valid), anggap donasi tidak valid → lanjut generate baru
    //             }
    //         }


    //         // Tidak ditemukan atau token kosong → buat baru
    //         $orderId = uniqid('order_');

    //         Log::info("generateSnapToken: Membuat data donasi baru, order_id={$orderId}, total={$total}");

    //         $donasi = KmdDonasi::create([
    //             'user_id' => $user->id,
    //             'userclasstutorial_id' => $progress->id,
    //             'modul_id' => $progress->modul_id,
    //             'class_id' => $progress->class_id,
    //             'cabang_id' => $progress->rayon_id,
    //             'order_id' => $orderId,
    //             'amount' => $amount,
    //             'fee_payment_gateway' => $fee,
    //             'payment_type' => 'midtrans',
    //             'transaction_time' => now(),
    //             'transaction_status' => 'pending',
    //             'payment_token' => null,
    //             'status' => 'pending',
    //         ]);

    //         $params = [
    //             'transaction_details' => [
    //                 'order_id' => $orderId,
    //                 'gross_amount' => $total,
    //             ],
    //             'customer_details' => [
    //                 'first_name' => $user->name ?? 'User',
    //                 'email' => $user->email,
    //                 'phone' => $user->phone ?? '',
    //             ],
    //         ];

    //         Log::debug('generateSnapToken: Params yang dikirim ke Midtrans', $params);

    //         // Ambil token dari Midtrans
    //         $snapToken = \Midtrans\Snap::getSnapToken($params);

    //         Log::info("generateSnapToken: Snap token berhasil didapat, update ke DB donasi_id={$donasi->id}");

    //         // Simpan token ke DB
    //         $donasi->update(['payment_token' => $snapToken]);

    //         return response()->json(['snap_token' => $snapToken]);

    //     } catch (\Exception $e) {
    //         Log::error('generateSnapToken: Midtrans Error: ' . $e->getMessage() . (isset($donasi) ? ' | Donasi ID: ' . $donasi->id : ''));

    //         // Jangan hapus donasi yang sudah dibuat karena masih bisa dipakai ulang

    //         return response()->json(['error' => 'Gagal membuat transaksi: ' . $e->getMessage()], 500);
    //     }
    // }
 public function generateSnapToken(Request $request)
{
    try {
        Log::info('generateSnapToken: function called');

        //$user = Auth::guard('kom')->user();
         $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => __('Silakan login terlebih dahulu.')], 401);
        }

        Log::info("generateSnapToken: User {$user->id} ({$user->name}) dengan role={$user->role}");

        // Ambil progress donasi yang masih pending
        $progress = KmdUserClassTutorial::where('user_id', $user->id)
            ->where('status_progress', 'pending')
            ->where('step', 'donasi')
            ->latest()
            ->first();

        if (!$progress) {
            return response()->json(['error' => __('Silakan pilih kelas tutorial terlebih dahulu.')], 400);
        }

        // === Tentukan harga berdasarkan role ===
        if ($user->role === 'pengajar') {
            $amount = 100000;
            $fee = 0;
        } else {
            $amount = 40000;
            $fee = 5000;
        }
        $total = $amount + $fee;
        // =======================================

        Log::info("generateSnapToken: Hitung donasi => amount={$amount}, fee={$fee}, total={$total}");

        // 🔹 Jika hanya ingin melihat jumlah tanpa generate token (preview)
        if ($request->boolean('preview', false)) {
            Log::info("generateSnapToken: Mode preview aktif, hanya kirim nominal tanpa generate token.");
            return response()->json([
                'amount' => $amount,
                'fee' => $fee,
                'total' => $total,
            ]);
        }

        // kom_v02: payment gateway aktif (Midtrans / BB Bizbos)
        $gateway = app(\App\Services\Payment\PaymentGatewaySetting::class)->aktif();
        $checkout = app(\App\Services\Kmd\CheckoutTutorialService::class);

        if ($checkout->sudahKedaluwarsa($progress)) {
            return response()->json([
                'error' => __('Waktu memberikan persembahan sudah habis. Silakan pilih jadwal kelas tutorial lagi.'),
                'redirect' => route('kom.kelas_tutorial.index'),
            ], 409);
        }

        if ($gateway === \App\Services\Payment\PaymentGatewaySetting::BIZBOS) {
            return $this->checkoutBizbos($user, $progress, $amount, $fee, $total, $checkout);
        }

        // 🔹 Buat order ID unik
        $orderId = uniqid('order_');

        // 🔹 Simpan donasi ke database
        $donasi = KmdDonasi::create([
            'user_id' => $user->id,
            'userclasstutorial_id' => $progress->id,
            'modul_id' => $progress->modul_id,
            'class_id' => $progress->class_id,
            'cabang_id' => $progress->rayon_id,
            'category_diklat' => $progress->category_diklat ?? null, 
            'order_id' => $orderId,
            'amount' => $amount,
            'fee_payment_gateway' => $fee,
            'payment_type' => 'midtrans',
            'payment_gateway' => \App\Services\Payment\PaymentGatewaySetting::MIDTRANS, // kom_v02
            'transaction_time' => now(),
            'transaction_status' => 'pending',
            'payment_token' => null,
            'status' => 'pending',
        ]);

        // 🔹 Siapkan parameter Midtrans
        $params = [
            // kom_v02: link pembayaran kedaluwarsa bersamaan dengan batas waktu persembahan
            'expiry' => $checkout->expiryMidtrans($progress),
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $user->name ?? 'User',
                'email' => $user->email,
                'phone' => $user->phone ?? '',
            ],
        ];

        // 🔹 Ambil token dari Midtrans
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        $donasi->update(['payment_token' => $snapToken]);

        Log::info("generateSnapToken: Snap token berhasil dibuat dan disimpan untuk user_id={$user->id}");

        // 🔹 Kirim juga jumlah harga, fee, dan total ke frontend
        return response()->json([
            'gateway' => 'midtrans', // kom_v02
            'snap_token' => $snapToken,
            'amount' => $amount,
            'fee' => $fee,
            'total' => $total,
        ]);
    } catch (\Exception $e) {
        Log::error('generateSnapToken: Error => ' . $e->getMessage());
        return response()->json(['error' => __('Gagal membuat transaksi.')], 500);
    }
}








    /**
     * kom_v02: persembahan lewat BB (Bizbos) — buat invoice & kirim link bayar (popup).
     * Invoice yang masih menunggu pembayaran dipakai ulang supaya tidak membuat tagihan ganda.
     */
    protected function checkoutBizbos($user, KmdUserClassTutorial $progress, int $amount, int $fee, int $total, $checkout)
    {
        $bizbos = app(\App\Services\Payment\BizbosPembayaranService::class);

        try {
            $lama = KmdDonasi::where('userclasstutorial_id', $progress->id)
                ->where('payment_gateway', \App\Services\Payment\PaymentGatewaySetting::BIZBOS)
                ->where('status', 'pending')
                ->whereNotNull('checkout_url')
                ->latest()
                ->first();

            if ($lama) {
                $status = $bizbos->sinkron($lama);
                if ($status === \App\Services\Payment\BizbosPembayaranService::PAID) {
                    return response()->json([
                        'gateway' => 'bizbos', 'status' => 'paid',
                        'message' => __('Terima kasih atas persembahan Anda!'),
                        'redirect' => route('kom.peserta.kelas_tutorial'),
                    ]);
                }
                if ($status === \App\Services\Payment\BizbosPembayaranService::UNPAID) {
                    return response()->json([
                        'gateway' => 'bizbos', 'checkout_url' => $lama->checkout_url,
                        'amount' => $amount, 'fee' => $fee, 'total' => $total,
                    ]);
                }
                // expired / cancelled → buat invoice baru di bawah
            }

            $donasi = KmdDonasi::create([
                'user_id' => $user->id,
                'userclasstutorial_id' => $progress->id,
                'modul_id' => $progress->modul_id,
                'class_id' => $progress->class_id,
                'cabang_id' => $progress->rayon_id,
                'category_diklat' => $progress->category_diklat ?? null,
                'order_id' => 'kom-' . str_replace('.', '', uniqid('', true)),
                'amount' => $amount,
                'fee_payment_gateway' => $fee,
                'payment_type' => 'bizbos',
                'payment_gateway' => \App\Services\Payment\PaymentGatewaySetting::BIZBOS,
                'transaction_time' => now(),
                'transaction_status' => 'pending',
                'payment_token' => null,
                'status' => 'pending',
            ]);

            $topik = optional($progress->classTutorial)->topic;
            $deskripsi = $progress->category_diklat
                ? __('Persembahan Diklat Tutor') . ($topik ? ' - ' . $topik : '')
                : __('Persembahan Kelas Tutorial') . ($topik ? ' - ' . $topik : '');

            $menit = (int) ceil(max(60, now()->diffInSeconds($checkout->batasWaktu($progress), false)) / 60);

            try {
                $invoice = $bizbos->buatCheckout($donasi, $user, $total, $menit, $deskripsi, route('kom.form.persembahan', ['cek' => 1]));
            } catch (\Throwable $e) {
                $donasi->delete();
                throw $e;
            }

            return response()->json([
                'gateway' => 'bizbos',
                'checkout_url' => $invoice['checkout_url'] ?? null,
                'amount' => $amount,
                'fee' => $fee,
                'total' => $total,
            ]);
        } catch (\Throwable $e) {
            Log::error('[Bizbos] Gagal membuat invoice persembahan', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return response()->json(['error' => __('Gagal membuat transaksi.')], 502);
        }
    }

    /**
     * kom_v02: dipanggil setelah popup Bizbos ditutup / selesai — cek status ke server Bizbos.
     * (Di localhost webhook tidak selalu sampai, jadi status juga dicek dari sini.)
     */
    public function cekPembayaran()
    {
        $progress = KmdUserClassTutorial::where('user_id', Auth::id())
            ->latest()
            ->first();

        if ($progress && $progress->status_progress === 'paid') {
            return response()->json(['status' => 'paid', 'redirect' => route('kom.peserta.kelas_tutorial')]);
        }

        $donasi = $progress
            ? KmdDonasi::where('userclasstutorial_id', $progress->id)
                ->where('payment_gateway', \App\Services\Payment\PaymentGatewaySetting::BIZBOS)
                ->latest()
                ->first()
            : null;

        if (! $donasi) {
            return response()->json(['status' => 'unpaid']);
        }

        try {
            $status = app(\App\Services\Payment\BizbosPembayaranService::class)->sinkron($donasi);
        } catch (\Throwable $e) {
            Log::warning('[Bizbos] Gagal cek status pembayaran', ['donasi_id' => $donasi->id, 'error' => $e->getMessage()]);

            return response()->json(['status' => 'unknown']);
        }

        return response()->json([
            'status' => $status,
            'redirect' => $status === 'paid' ? route('kom.peserta.kelas_tutorial') : null,
        ]);
    }

    public function storePersembahan(Request $request)
    {
        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;

        $donasi = KmdDonasi::where('order_id', $orderId)->first();
        if (!$donasi) {
            return redirect()->back()->with('error', __('Data persembahan tidak ditemukan.'));
        }

        $donasi->transaction_status = $transactionStatus;
        $donasi->status = in_array($transactionStatus, ['capture', 'settlement']) ? 'success' : 'pending';
        $donasi->save();

        return redirect()->route('kom.kelas_tutorial.index')->with('success', __('Status pembayaran diperbarui.'));
    }

    //    public function callbackMidtrans(Request $request)
// {
//     Log::info('[MIDTRANS CALLBACK] Callback diterima.', [
//         'request' => $request->all()
//     ]);

    //     try {
//         $notification = new Notification();

    //         $transactionStatus = $notification->transaction_status;
//         $orderId = $notification->order_id;
//         $paymentType = $notification->payment_type ?? null;

    //         Log::info('[MIDTRANS CALLBACK] Status transaksi: ' . $transactionStatus . ', Order ID: ' . $orderId);
//         Log::debug('[MIDTRANS CALLBACK] Detail notifikasi Midtrans', [
//             'transaction_time' => $notification->transaction_time,
//             'payment_type' => $paymentType,
//             'gross_amount' => $notification->gross_amount,
//         ]);

    //         // Ambil data donasi dan relasi yang diperlukan (tanpa fraud_status)
//         $donasi = KmdDonasi::with([
//             'user',
//             'userclasstutorial.classTutorial' // pakai relasi yang BENAR
//         ])->where('order_id', $orderId)->first();

    //         if (!$donasi) {
//             Log::error('[DONASI] Data donasi TIDAK DITEMUKAN untuk order ID: ' . $orderId);
//             return response()->json(['error' => 'Data donasi tidak ditemukan'], 404);
//         }

    //         Log::info('[DONASI] Data donasi ditemukan', [
//             'donasi_id' => $donasi->id,
//             'user_id' => $donasi->user_id,
//             'email' => optional($donasi->user)->email,
//             'status_lama' => $donasi->status
//         ]);

    //         // Update status transaksi
//         $updateResult = $donasi->update([
//             'transaction_status' => $transactionStatus,
//             'payment_type' => $paymentType,
//             'status' => in_array($transactionStatus, ['settlement', 'capture']) ? 'paid' : $donasi->status,
//         ]);

    //         if ($updateResult) {
//             Log::info('[DONASI] Status donasi BERHASIL diperbarui.', [
//                 'donasi_id' => $donasi->id,
//                 'status_baru' => $donasi->status,
//             ]);
//         } else {
//             Log::warning('[DONASI] Gagal memperbarui status donasi.', [
//                 'donasi_id' => $donasi->id,
//             ]);
//         }

    //         // Kirim email jika status transaksi berhasil
//         if (in_array($transactionStatus, ['settlement', 'capture'])) {
//             try {
//                 $userEmail = optional($donasi->user)->email;
//                 $userName = optional($donasi->user)->name ?? 'Sahabat KOM Digital';
//                 $joinUrl = optional($donasi->userclasstutorial)->zoom_join_url ?? null;
//                 //$joinUrl = optional($donasi->userclasstutorial)->classTutorial->join_url ?? null;
//                 Log::debug('[EMAIL] Mencoba kirim email ke: ' . ($userEmail ?? 'null'));

    //                 if ($userEmail) {
//                     Log::debug('[EMAIL] Mulai proses kirim email...');



    //                     Mail::to($userEmail)->send(new ThankYouDonasiMail($donasi, $joinUrl));


    //                     Log::debug('[EMAIL] Fungsi Mail::to()->send() selesai dipanggil.');
//                     Log::info('[EMAIL] Ucapan terima kasih BERHASIL dikirim ke: ' . $userEmail);
//                 } else {
//                     Log::warning('[EMAIL] Email pengguna TIDAK TERSEDIA, email tidak dikirim.', [
//                         'donasi_id' => $donasi->id,
//                         'user_id' => $donasi->user_id
//                     ]);
//                 }
//             } catch (\Exception $e) {
//                 Log::error('[EMAIL] GAGAL mengirim ucapan terima kasih.', [
//                     'message' => $e->getMessage(),
//                     'donasi_id' => $donasi->id,
//                     'user_id' => $donasi->user_id
//                 ]);
//             }
//         } else {
//             Log::info('[DONASI] Status transaksi bukan settlement/capture, tidak kirim email.', [
//                 'donasi_id' => $donasi->id,
//                 'status' => $transactionStatus
//             ]);
//         }

    //         return response()->json(['status' => 'OK']);
//     } catch (\Exception $e) {
//         Log::error('[MIDTRANS CALLBACK ERROR]', [
//             'message' => $e->getMessage(),
//             'trace' => $e->getTraceAsString()
//         ]);

    //         return response()->json(['error' => 'Terjadi kesalahan saat memproses callback.'], 500);
//     }
// }
    public function callbackMidtrans(Request $request)
    {
        Log::info('[MIDTRANS CALLBACK] Callback diterima.', [
            'order_id' => $request->input('order_id'),
            'transaction_status' => $request->input('transaction_status'),
        ]);

        try {
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id;
            $paymentType = $notification->payment_type ?? null;

            Log::info('[MIDTRANS CALLBACK] Status transaksi: ' . $transactionStatus . ', Order ID: ' . $orderId);
            Log::debug('[MIDTRANS CALLBACK] Detail notifikasi Midtrans', [
                'transaction_time' => $notification->transaction_time,
                'payment_type' => $paymentType,
                'gross_amount' => $notification->gross_amount,
            ]);

            // Ambil data donasi & relasi
            $donasi = KmdDonasi::with([
                'user',
                'userclasstutorial.classTutorial'
            ])->where('order_id', $orderId)->first();

            if (!$donasi) {
                // kom_v02: jika ternyata LUNAS tapi datanya tidak ada → perlu ditangani admin
                if (in_array($transactionStatus, ['settlement', 'capture'])) {
                    Log::critical('[DONASI] PEMBAYARAN LUNAS tetapi data persembahan tidak ditemukan — cek manual di dashboard Midtrans.', [
                        'order_id' => $orderId, 'gross_amount' => $notification->gross_amount ?? null,
                    ]);
                }
                Log::error('[DONASI] Data donasi TIDAK DITEMUKAN untuk order ID: ' . $orderId);
                return response()->json(['error' => 'Data donasi tidak ditemukan'], 404);
            }

            Log::info('[DONASI] Data donasi ditemukan', [
                'donasi_id' => $donasi->id,
                'user_id' => $donasi->user_id,
                'email' => optional($donasi->user)->email,
                'status_lama' => $donasi->status
            ]);

            // kom_v02: apakah pendaftaran tutorial ini SUDAH diproses lunas sebelumnya (callback ulang dari Midtrans)?
            // Dicek dari status pendaftaran (bukan status donasi) supaya kalau proses sebelumnya gagal di tengah,
            // callback ulang tetap memproses ulang.
            $sudahLunasSebelumnya = optional($donasi->userclasstutorial)->status_progress === 'paid';

            // Update status donasi
            $donasi->update([
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'status' => in_array($transactionStatus, ['settlement', 'capture']) ? 'paid' : $donasi->status,
            ]);

            Log::info('[DONASI] Status donasi diperbarui.', [
                'donasi_id' => $donasi->id,
                'status_baru' => $donasi->status,
            ]);

            // Jika transaksi sukses, proses registrasi Zoom & kirim email
            if (in_array($transactionStatus, ['settlement', 'capture']) && ! $sudahLunasSebelumnya) { // kom_v02: jangan proses / kirim email dua kali
                $userClassTutorial = $donasi->userclasstutorial;

                if ($userClassTutorial) {
                    // kom_v02 (performa): status lunas & kuota langsung diproses; pendaftaran Zoom +
                    // email terima kasih berisi link Zoom dikerjakan di queue (ProsesTutorialLunasJob),
                    // jadi callback Midtrans cepat selesai walaupun Zoom / server email sedang lambat.
                    app(KelasTutorialController::class)->handlePaidStatus($userClassTutorial->id, $donasi->id);
                } else {
                    Log::warning('[DONASI] userclasstutorial tidak ditemukan untuk donasi ID: ' . $donasi->id);
                }
            }

            return response()->json(['status' => 'OK']);
        } catch (\Exception $e) {
            Log::error('[MIDTRANS CALLBACK ERROR]', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Terjadi kesalahan saat memproses callback.'], 500);
        }
    }
    


}
