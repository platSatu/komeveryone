<?php

namespace App\Console\Commands;

use App\Models\NginxAccessLog;
use App\Models\NginxLogImportState;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Import baris BARU dari file access.log nginx (format "combined" bawaan
 * nginx) ke tabel nginx_access_logs, sumber data untuk dashboard
 * Superadmin > Statistik Pengunjung (Log Nginx).
 *
 * PENTING: command ini harus dijalankan/dijadwalkan LANGSUNG DI VPS
 * (lewat scheduler Laravel `php artisan schedule:run` yang dipanggil cron
 * setiap menit, sudah didaftarkan di routes/console.php), karena Claude
 * tidak punya akses SSH ke VPS untuk menjalankannya sendiri.
 *
 * Path file diatur lewat .env NGINX_LOG_PATH (default /var/log/nginx/access.log).
 * User yang menjalankan PHP-FPM/artisan harus punya izin BACA ke file itu.
 *
 * Baca file secara incremental: posisi (byte offset) dan inode terakhir
 * disimpan di tabel nginx_log_import_states per path, supaya command ini
 * aman dipanggil berulang (tidak nge-import baris yang sama dua kali), dan
 * otomatis mulai dari awal lagi kalau file diganti oleh logrotate (inode
 * berubah atau ukuran file jadi lebih kecil dari posisi terakhir).
 *
 * Ditambahkan 8 September 2026 (permintaan user).
 */
class ImportNginxLog extends Command
{
    protected $signature = 'nginx:import-log {--path= : Override path file log (default dari config/nginxlog.php)}';

    protected $description = 'Import baris baru access.log nginx ke tabel nginx_access_logs';

    // Format "combined" default nginx:
    // $remote_addr - $remote_user [$time_local] "$request" $status $body_bytes_sent "$http_referer" "$http_user_agent"
    private const PATTERN = '/^(?<ip>\S+) \S+ \S+ \[(?<time>[^\]]+)\] "(?<request>[^"]*)" (?<status>\d{3}) (?<bytes>\S+) "(?<referrer>[^"]*)" "(?<agent>[^"]*)"/';

    public function handle(): int
    {
        $path = $this->option('path') ?: config('nginxlog.log_path');

        if (! $path || ! is_readable($path)) {
            $this->error("File log tidak ditemukan atau tidak bisa dibaca: {$path}");
            $this->error('Pastikan NGINX_LOG_PATH di .env sudah benar, dan user yang menjalankan PHP/artisan punya izin baca file itu.');

            return self::FAILURE;
        }

        $inode = @fileinode($path) ?: null;
        $size = @filesize($path) ?: 0;

        $state = NginxLogImportState::firstOrNew(['log_path' => $path]);

        // File dirotate (inode beda) atau ukurannya sekarang lebih kecil dari
        // posisi terakhir kita baca -> mulai baca dari awal file lagi.
        if (! $state->exists || $state->last_inode !== $inode || $size < $state->last_offset) {
            $state->last_inode = $inode;
            $state->last_offset = 0;
        }

        $handle = fopen($path, 'r');
        fseek($handle, $state->last_offset);

        $imported = 0;
        $skipped = 0;
        $rows = [];
        $now = now();
        $lastGoodPos = $state->last_offset;

        while (true) {
            $posBeforeLine = ftell($handle);
            $line = fgets($handle);

            if ($line === false) {
                break;
            }

            // Baris terakhir mungkin belum lengkap kalau nginx lagi nulis --
            // jangan diproses, dan JANGAN majukan offset melewatinya, supaya
            // dibaca ulang dari awal baris itu di run berikutnya.
            if (! str_ends_with($line, "\n")) {
                break;
            }

            if (preg_match(self::PATTERN, $line, $m)) {
                [$method, $requestPath] = $this->parseRequestLine($m['request']);

                try {
                    // Format waktu nginx: 08/Sep/2026:12:34:56 +0700
                    $requestedAt = Carbon::createFromFormat('d/M/Y:H:i:s O', $m['time']);
                } catch (\Throwable $e) {
                    $requestedAt = null;
                }

                if ($requestedAt) {
                    $rows[] = [
                        'ip_address' => substr($m['ip'], 0, 45),
                        'method' => $method ? substr($method, 0, 10) : null,
                        'path' => $requestPath ? substr($requestPath, 0, 2048) : null,
                        'status_code' => (int) $m['status'],
                        'bytes_sent' => is_numeric($m['bytes']) ? (int) $m['bytes'] : null,
                        'referrer' => ($m['referrer'] !== '-' && $m['referrer'] !== '') ? substr($m['referrer'], 0, 500) : null,
                        'user_agent' => ($m['agent'] !== '-' && $m['agent'] !== '') ? $m['agent'] : null,
                        'requested_at' => $requestedAt,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $imported++;
                } else {
                    $skipped++;
                }
            } else {
                $skipped++;
            }

            $lastGoodPos = ftell($handle);

            if (count($rows) >= 500) {
                NginxAccessLog::insert($rows);
                $rows = [];
            }
        }

        if ($rows) {
            NginxAccessLog::insert($rows);
        }

        $state->last_offset = $lastGoodPos;
        $state->save();
        fclose($handle);

        $this->info("Selesai: {$imported} baris baru diimport, {$skipped} baris dilewati (format tidak cocok), dari {$path}.");

        return self::SUCCESS;
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function parseRequestLine(string $request): array
    {
        // Contoh: "GET /path?query=1 HTTP/1.1"
        $parts = explode(' ', $request);

        if (count($parts) < 2) {
            return [null, null];
        }

        return [$parts[0], $parts[1]];
    }
}
