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
     * Update 8 September 2026: middleware ini SEBELUMNYA sudah ada tapi
     * tidak pernah didaftarkan di bootstrap/app.php (mati/tidak aktif) --
     * sekarang didaftarkan global di grup 'web'. Ditambahkan filter
     * shouldTrack() supaya statistik "halaman paling banyak dikunjungi"
     * tidak bias oleh request AJAX/polling internal, file asset, atau
     * ping health-check ('/up') yang bukan kunjungan halaman beneran.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->shouldTrack($request)) {
            return $next($request);
        }

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

    /**
     * Hanya catat request GET halaman biasa (bukan AJAX/JSON, bukan file
     * asset, bukan health-check '/up') -- supaya "jumlah pengunjung" &
     * "halaman terpopuler" mencerminkan navigasi halaman beneran oleh
     * manusia, bukan polling AJAX internal atau ping uptime monitor.
     */
    private function shouldTrack(Request $request): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return false;
        }

        if ($request->is('up')) {
            return false;
        }

        if (preg_match('/\.(css|js|map|png|jpe?g|gif|svg|ico|woff2?|ttf|eot|webp|mp4|pdf)$/i', $request->path())) {
            return false;
        }

        return true;
    }
}
