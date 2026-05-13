<?php

namespace App\Http\Controllers\Kmd\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;

class ZoomSdkController extends Controller
{
    /**
     * Tampilkan halaman Zoom SDK
     */
    public function index()
    {
        return view('a_kom_digital_pusat.dashboard.zoom.sdk.index');
    }

    /**
     * Generate SDK Signature untuk join meeting
     */
    public function generateSignature(Request $request)
    {
        Log::info('[Zoom SDK] Memulai generate signature...', [
            'request' => $request->all()
        ]);

        $request->validate([
            'meeting_number' => 'required|string',
            'role' => 'required|in:0,1'
        ]);

        $sdkKey = env('ZOOM_SDK_KEY');
        $sdkSecret = env('ZOOM_SDK_SECRET');

        if (!$sdkKey || !$sdkSecret) {
            Log::error('[Zoom SDK] Konfigurasi SDK tidak lengkap.', [
                'ZOOM_SDK_KEY' => $sdkKey,
                'ZOOM_SDK_SECRET' => $sdkSecret
            ]);

            return response()->json([
                'error' => 'ZOOM_SDK_KEY atau ZOOM_SDK_SECRET belum diatur di .env'
            ], 500);
        }

        $meetingNumber = $request->input('meeting_number');
        $role = (int) $request->input('role');
        $iat = time();
        $exp = $iat + 3600;

        $payload = [
            'sdkKey' => $sdkKey,
            'mn' => $meetingNumber,
            'role' => $role,
            'iat' => $iat,
            'exp' => $exp,
            'tokenExp' => $exp,
        ];

        Log::info('[Zoom SDK] Payload untuk JWT:', $payload);

        try {
            $signature = JWT::encode($payload, $sdkSecret, 'HS256');

            Log::info('[Zoom SDK] Signature berhasil dibuat.', [
                'signature' => $signature
            ]);

            return response()->json([
                'signature' => $signature
            ]);
        } catch (\Exception $e) {
            Log::error('[Zoom SDK] Gagal membuat signature', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return response()->json([
                'error' => 'Gagal membuat signature: ' . $e->getMessage()
            ], 500);
        }
    }
}
