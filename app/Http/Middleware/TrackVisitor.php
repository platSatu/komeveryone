<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\VisitorHistory;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Mendapatkan informasi yang diperlukan
        $ipAddress = $request->ip(); // Mendapatkan IP pengunjung
        $browser = $request->header('User-Agent'); // Mendapatkan informasi browser
        $pageUrl = $request->url(); // URL halaman yang dikunjungi
        $method = $request->method(); // HTTP Method (GET, POST, dll.)
        $referrer = $request->header('Referer'); // Referrer URL (jika ada)

        // Menyimpan data pengunjung ke dalam database sebelum response
        $visitorHistory = VisitorHistory::create([
            'ip_address' => $ipAddress,
            'browser' => $browser,
            'page_url' => $pageUrl,
            'visited_at' => now('Asia/Jakarta'), // Waktu saat ini sesuai timezone Asia/Jakarta
            'method' => $method, // HTTP method (GET, POST, dll.)
            'referrer' => $referrer, // Referrer URL
        ]);

        // Mendapatkan response setelah middleware selesai dan mendapatkan status code
        $response = $next($request);

        // Mendapatkan status code setelah response
        $statusCode = $response->getStatusCode(); // Status code dari response

        // Update status code pada visitor history
        $visitorHistory->update(['status_code' => $statusCode]);

        // Cek jika URL yang diakses mencurigakan
        if ($this->isSuspiciousRequest($pageUrl)) {
            // Menyimpan log jika URL yang diakses mencurigakan
            \Log::warning('Suspicious access attempt', [
                'ip' => $ipAddress,
                'user_agent' => $browser,
                'url' => $pageUrl,
                'method' => $method,
                'referrer' => $referrer,
                'status_code' => $statusCode,
            ]);
        }

        // Mengembalikan response
        return $response;
    }

    /**
     * Fungsi untuk mendeteksi request mencurigakan.
     * Misalnya, mencoba mengakses URL yang tidak ada atau endpoint yang tidak sah.
     */
    private function isSuspiciousRequest($url)
    {
        // Daftar pola URL mencurigakan yang perlu diperiksa
        $suspiciousPatterns = [
            'admin',
            'login',
            'register',
            'unauthorized',
            'phpinfo', // Bisa jadi indikasi pencarian informasi server
            '/system',
            '/config',
        ];

        // Mengecek apakah URL yang diakses mencurigakan
        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($url, $pattern) !== false) {
                return true; // Menandakan bahwa ini adalah request mencurigakan
            }
        }

        return false; // Jika tidak ada pola mencurigakan
    }
}
