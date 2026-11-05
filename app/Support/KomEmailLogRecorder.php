<?php

namespace App\Support;

use App\Models\Kmd\KomEmailLog;
use Illuminate\Events\Dispatcher;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobQueued;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\Mime\Address;
use Throwable;

/**
 * 5 Nov 2026 — mencatat SEMUA email ke tabel kom_email_log tanpa mengubah kelas email / notifikasinya:
 * masuk antrean (antre) -> diterima server email (terkirim) / gagal (dengan alasan).
 *
 * Didaftarkan sebagai singleton + subscriber di AppServiceProvider. Pencatatan TIDAK BOLEH
 * menggagalkan pengiriman email, jadi semua error di sini hanya dilaporkan ke log.
 */
class KomEmailLogRecorder
{
    /** uuid job antrean yang sedang dijalankan worker (untuk menyambungkan MessageSent ke barisnya). */
    private ?string $currentJob = null;

    private ?bool $tableReady = null;

    public function subscribe(Dispatcher $events): array
    {
        return [
            JobQueued::class => 'queued',
            JobProcessing::class => 'processing',
            JobProcessed::class => 'processed',
            MessageSent::class => 'sent',
            JobExceptionOccurred::class => 'failed',
            JobFailed::class => 'failed',
        ];
    }

    public function queued(JobQueued $event): void
    {
        $this->safely(function () use ($event) {
            if ($email = $this->describe($event->job)) {
                KomEmailLog::create($email + [
                    'job_uuid' => $event->payload()['uuid'] ?? null,
                    'status' => KomEmailLog::ANTRE,
                ]);
            }
        });
    }

    public function processing(JobProcessing $event): void
    {
        $this->currentJob = $event->job->uuid();
    }

    public function processed(): void
    {
        $this->currentJob = null;
    }

    public function sent(MessageSent $event): void
    {
        $this->safely(function () use ($event) {
            $data = [
                'status' => KomEmailLog::TERKIRIM,
                'judul' => Str::limit((string) $event->message->getSubject(), 250, ''),
                'error' => null,
                'terkirim_at' => now(),
            ];

            $updated = $this->currentJob ? KomEmailLog::where('job_uuid', $this->currentJob)->update($data) : 0;

            // Email yang dikirim langsung (tanpa antrean) atau belum tercatat: buat barisnya sekarang.
            if (! $updated) {
                KomEmailLog::create($data + [
                    'jenis' => $event->data['__laravel_mailable'] ?? $event->data['__laravel_notification'] ?? 'Email',
                    'penerima' => $this->recipients(array_map(fn (Address $to) => $to->getAddress(), $event->message->getTo())),
                    'job_uuid' => $this->currentJob,
                ]);
            }
        });
    }

    /** Tiap percobaan gagal & saat antrean menyerah; kalau percobaan berikutnya berhasil, status jadi Terkirim. */
    public function failed(JobExceptionOccurred|JobFailed $event): void
    {
        $this->safely(fn () => KomEmailLog::where('job_uuid', $event->job->uuid())
            ->where('status', '!=', KomEmailLog::TERKIRIM)
            ->update(['status' => KomEmailLog::GAGAL, 'error' => Str::limit($event->exception->getMessage(), 480, '')]));
    }

    /** @return array{jenis: string, penerima: string}|null  null = job ini bukan email. */
    private function describe(mixed $job): ?array
    {
        if ($job instanceof SendQueuedMailable) {
            return ['jenis' => get_class($job->mailable), 'penerima' => $this->recipients(array_column($job->mailable->to, 'address'))];
        }

        if ($job instanceof SendQueuedNotifications && in_array('mail', (array) $job->channels, true)) {
            $to = collect($job->notifiables)->map(function ($notifiable) use ($job) {
                $route = $notifiable->routeNotificationFor('mail', $job->notification);

                // Rute bisa "a@b.c", ["a@b.c" => "Nama"], atau ["a@b.c", ...].
                return is_array($route) ? array_map(fn ($key, $value) => is_string($key) ? $key : $value, array_keys($route), $route) : $route;
            })->flatten()->filter()->all();

            return ['jenis' => get_class($job->notification), 'penerima' => $this->recipients($to)];
        }

        return null;
    }

    private function recipients(array $emails): string
    {
        return Str::limit(implode(', ', $emails) ?: '-', 188, '');
    }

    private function safely(callable $callback): void
    {
        try {
            // Sebelum `php artisan migrate` dijalankan, tabelnya belum ada: lewati tanpa error.
            $this->tableReady ??= Schema::connection('mysql')->hasTable('kom_email_log');

            if ($this->tableReady) {
                $callback();
            }
        } catch (Throwable $e) {
            report($e);
        }
    }
}
