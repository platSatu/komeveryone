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
        // Debug log untuk memastikan key terbaca
        Log::info('Server Key: ' . env('MIDTRANS_SERVER_KEY'));
        Log::info('Client Key: ' . env('MIDTRANS_CLIENT_KEY'));

        // Set konfigurasi Midtrans langsung dari .env
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', true); // false = sandbox
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        Config::$is3ds = env('MIDTRANS_IS_3DS', true);
    }

    public function formPersembahan()
    {
        // $joinKelas = session('join_kelas');
        // if (!$joinKelas) {
        //     return redirect()->route('kom.kelas_tutorial.index')->with('error', 'Silakan pilih kelas tutorial terlebih dahulu.');
        // }

        return view('a_kom_digital_pusat.dashboard.persembahan');
    }

    // public function generateSnapToken(Request $request)
    // {
    //     $user = Auth::user();
    //     $joinKelas = session('join_kelas');
    //     if (!$joinKelas) {
    //         return response()->json(['error' => 'Silakan pilih kelas tutorial terlebih dahulu.'], 400);
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
    public function generateSnapToken(Request $request)
    {
        // Fallback untuk cek apakah fungsi ini terpanggil di server logs biasa
        error_log('generateSnapToken: function called');

        try {
            Log::info('generateSnapToken: function called');

            $user = Auth::guard('kom')->user();

            if (!$user) {
                Log::warning('generateSnapToken: User belum login saat request generate snap token.');
                return response()->json(['error' => 'User belum login.'], 401);
            }

            Log::info("generateSnapToken: Request dari user_id={$user->id}, nama={$user->name}");

            // Ambil progress donasi yang masih pending
            $progress = KmdUserClassTutorial::where('user_id', $user->id)
                ->where('status_progress', 'pending')
                ->where('step', 'donasi')
                ->latest()
                ->first();

            if (!$progress) {
                Log::warning("generateSnapToken: Tidak menemukan progress donasi yang pending untuk user_id={$user->id}");
                return response()->json(['error' => 'Silakan pilih kelas tutorial terlebih dahulu.'], 400);
            }

            Log::info("generateSnapToken: Progress ditemukan id={$progress->id} untuk user_id={$user->id}");

            $amount = 100000;
            $fee = 0;
            $total = $amount + $fee;

            // Cari donasi yang masih pending untuk progress ini
            $donasi = KmdDonasi::where('user_id', $user->id)
                ->where('userclasstutorial_id', $progress->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            // if ($donasi && !empty($donasi->payment_token)) {
            //     Log::info("generateSnapToken: Ditemukan donasi pending id={$donasi->id}, menggunakan token lama.");
            //     return response()->json(['snap_token' => $donasi->payment_token]);
            // }
            if ($donasi) {
                // Cek ke Midtrans status transaksi real-time (opsional, tapi akurat)
                try {
                    $midtransStatus = \Midtrans\Transaction::status($donasi->order_id);
                    $transactionStatus = $midtransStatus->transaction_status;

                    Log::info("generateSnapToken: Cek status Midtrans untuk order_id={$donasi->order_id} => status={$transactionStatus}");

                    // Update status transaksi di database jika berbeda
                    if ($donasi->transaction_status !== $transactionStatus) {
                        $donasi->update(['transaction_status' => $transactionStatus]);
                    }

                    // Jika status masih bisa digunakan (pending/token masih aktif), gunakan token lama
                    if (in_array($transactionStatus, ['pending'])) {
                        Log::info("generateSnapToken: Transaksi masih pending, gunakan token lama.");
                        return response()->json(['snap_token' => $donasi->payment_token]);
                    }

                    // Jika expired, denied, atau cancel → lanjut generate token baru
                    Log::info("generateSnapToken: Transaksi sebelumnya tidak valid (status={$transactionStatus}), buat token baru.");
                } catch (\Exception $e) {
                    Log::warning("generateSnapToken: Gagal cek status Midtrans, asumsikan donasi tidak valid. Error: " . $e->getMessage());
                    // Jika tidak bisa cek status (misal order_id tidak valid), anggap donasi tidak valid → lanjut generate baru
                }
            }


            // Tidak ditemukan atau token kosong → buat baru
            $orderId = uniqid('order_');

            Log::info("generateSnapToken: Membuat data donasi baru, order_id={$orderId}, total={$total}");

            $donasi = KmdDonasi::create([
                'user_id' => $user->id,
                'userclasstutorial_id' => $progress->id,
                'modul_id' => $progress->modul_id,
                'class_id' => $progress->class_id,
                'cabang_id' => $progress->rayon_id,
                'order_id' => $orderId,
                'amount' => $amount,
                'fee_payment_gateway' => $fee,
                'payment_type' => 'midtrans',
                'transaction_time' => now(),
                'transaction_status' => 'pending',
                'payment_token' => null,
                'status' => 'pending',
                'category_diklat' => $progress->category_diklat ?? null, // <-- tambahkan di sini
            ]);

            $params = [
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

            Log::debug('generateSnapToken: Params yang dikirim ke Midtrans', $params);

            // Ambil token dari Midtrans
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            Log::info("generateSnapToken: Snap token berhasil didapat, update ke DB donasi_id={$donasi->id}");

            // Simpan token ke DB
            $donasi->update(['payment_token' => $snapToken]);

            return response()->json(['snap_token' => $snapToken]);

        } catch (\Exception $e) {
            Log::error('generateSnapToken: Midtrans Error: ' . $e->getMessage() . (isset($donasi) ? ' | Donasi ID: ' . $donasi->id : ''));

            // Jangan hapus donasi yang sudah dibuat karena masih bisa dipakai ulang

            return response()->json(['error' => 'Gagal membuat transaksi: ' . $e->getMessage()], 500);
        }
    }






    public function storePersembahan(Request $request)
    {
        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;

        $donasi = KmdDonasi::where('order_id', $orderId)->first();
        if (!$donasi) {
            return redirect()->back()->with('error', 'Donasi tidak ditemukan.');
        }

        $donasi->transaction_status = $transactionStatus;
        $donasi->status = in_array($transactionStatus, ['capture', 'settlement']) ? 'success' : 'pending';
        $donasi->save();

        return redirect()->route('kom.kelas_tutorial.index')->with('success', 'Status pembayaran diperbarui.');
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
            'request' => $request->all()
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
                Log::error('[DONASI] Data donasi TIDAK DITEMUKAN untuk order ID: ' . $orderId);
                return response()->json(['error' => 'Data donasi tidak ditemukan'], 404);
            }

            Log::info('[DONASI] Data donasi ditemukan', [
                'donasi_id' => $donasi->id,
                'user_id' => $donasi->user_id,
                'email' => optional($donasi->user)->email,
                'status_lama' => $donasi->status
            ]);

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
            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                $userClassTutorial = $donasi->userclasstutorial;

                if ($userClassTutorial) {
                    // Panggil handlePaidStatus()
                    app(KelasTutorialController::class)->handlePaidStatus($userClassTutorial->id);

                    // Kirim email ucapan + link Zoom (jika sudah tersedia)*
                    try {
                        $userEmail = optional($donasi->user)->email;
                        $joinUrl = $userClassTutorial->zoom_join_url;

                        if ($userEmail && $joinUrl) {
                            Mail::to($userEmail)->send(new ThankYouDonasiMail($donasi, $joinUrl));
                            Log::info('[EMAIL] Link Zoom berhasil dikirim ke: ' . $userEmail);
                        } else {
                            Log::warning('[EMAIL] Tidak kirim email karena data tidak lengkap.', [
                                'email' => $userEmail,
                                'join_url' => $joinUrl
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('[EMAIL] Gagal mengirim email link Zoom.', [
                            'message' => $e->getMessage()
                        ]);
                    }
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
