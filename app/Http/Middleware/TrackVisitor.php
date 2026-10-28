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
        return $next($request);
    }

    /**
     * kom_v02 (performa): dicatat SETELAH halaman terkirim ke pengunjung (terminable middleware),
     * cukup 1x simpan lengkap dengan status code. Sebelumnya 2x tulis database (simpan + update)
     * sebelum halaman selesai, di setiap halaman. Gagal mencatat tidak boleh mengganggu pengunjung.
     */
    public function terminate(Request $request, Response $response): void
    {
        if (! $this->shouldTrack($request)) {
            return;
        }

        $data = [
            'ip_address' => $request->ip(),
            'browser' => $request->header('User-Agent'),
            // Kolom page_url & referrer maksimal 500 karakter.
            'page_url' => \Illuminate\Support\Str::limit($request->url(), 500, ''),
            'method' => $request->method(),
            'referrer' => \Illuminate\Support\Str::limit((string) $request->header('Referer'), 500, '') ?: null,
            'status_code' => $response->getStatusCode(),
            'visited_at' => now('Asia/Jakarta'),
        ];

        try {
            VisitorHistory::create($data);
        } catch (\Throwable $e) {
            report($e);
        }

        if ($this->isSuspiciousRequest($data['page_url'])) {
            \Log::warning('Suspicious access attempt', [
                'ip' => $data['ip_address'],
                'user_agent' => $data['browser'],
                'url' => $data['page_url'],
                'method' => $data['method'],
                'referrer' => $data['referrer'],
                'status_code' => $data['status_code'],
            ]);
        }
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
