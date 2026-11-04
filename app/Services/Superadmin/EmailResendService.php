<?php

namespace App\Services\Superadmin;

use App\Mail\KomUserVerificationMail;
use App\Mail\ResetPasswordMail;
use App\Models\Kmd\KomUser;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Superadmin > Kirim Ulang Email (3 November 2026).
 *
 * Link verifikasi berlaku 10 menit (AuthController::verifyEmail) dan link reset password 60 menit
 * (AuthController::resetPassword). Email lama yang gagal/tertahan, kalau dikirim apa adanya, isinya
 * link yang sudah kadaluarsa. Jadi untuk dua jenis itu kirim ulang SELALU membuat link baru, persis
 * seperti tombol "kirim ulang verifikasi" / "lupa password" yang dipakai user sendiri.
 * Email jenis lain (undangan Zoom, notifikasi, dll.) tidak punya link kadaluarsa, jadi dikirim ulang
 * apa adanya lewat `queue:retry` bawaan Laravel.
 *
 * Semua email tetap lewat antrean (Mail::queue) yang diproses scheduler tiap menit -- sama dengan
 * alur yang sudah berjalan, tidak ada jalur kirim baru.
 */
class EmailResendService
{
    public const TYPE_VERIFICATION = 'verifikasi';
    public const TYPE_RESET = 'reset_password';
    public const TYPE_OTHER = 'lainnya';

    /** Nama yang ditampilkan untuk tiap kelas email. */
    private const LABELS = [
        KomUserVerificationMail::class => 'Verifikasi Email',
        ResetPasswordMail::class => 'Reset Password',
        'App\Mail\ClassTutorialNotificationMail' => 'Undangan Kelas Tutorial',
        'App\Mail\ZoomLinkMail' => 'Link Zoom',
        'App\Mail\KomNotifikasiMail' => 'Notifikasi / Pengingat',
    ];

    // ------------------------------------------------------------------ kirim ke satu user

    /** Kirim link verifikasi BARU (token & waktu kirim direset, link lama otomatis tidak berlaku). */
    public function sendVerification(KomUser $user): void
    {
        if ($user->email_verified_at) {
            throw new EmailResendException('Email '.$user->email.' sudah terverifikasi, tidak perlu dikirim ulang.');
        }

        $user->update([
            'email_verification_token' => Str::random(64),
            'email_verification_sent_at' => now(),
        ]);

        Mail::to($user->email)->queue(new KomUserVerificationMail($user));
    }

    /** Kirim link reset password BARU (sama dengan AuthController::sendResetLinkEmail). */
    public function sendPasswordReset(KomUser $user): void
    {
        $token = Str::random(64);

        DB::table('kom_password_resets')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        Mail::to($user->email)->queue(new ResetPasswordMail($token, $user->email));
    }

    public function sendToUser(KomUser $user, string $type): string
    {
        if (blank($user->email)) {
            throw new EmailResendException('User ini tidak punya email.');
        }

        if ($type === self::TYPE_VERIFICATION) {
            $this->sendVerification($user);

            return 'Link verifikasi baru dikirim ke '.$user->email.'.';
        }

        if ($type === self::TYPE_RESET) {
            $this->sendPasswordReset($user);

            return 'Link reset password baru dikirim ke '.$user->email.'.';
        }

        throw new EmailResendException('Jenis email tidak dikenal.');
    }

    // ------------------------------------------------------------------ daftar email gagal / tertahan

    public function failedPage(int $perPage = 25): LengthAwarePaginator
    {
        return $this->failedQuery()
            ->orderByDesc('failed_at')
            ->paginate($perPage, ['*'], 'gagal')
            ->through(fn ($row) => $this->describe($row, 'failed'));
    }

    public function pendingPage(int $perPage = 25): LengthAwarePaginator
    {
        return $this->pendingQuery()
            ->orderBy('id')
            ->paginate($perPage, ['*'], 'menunggu')
            ->through(fn ($row) => $this->describe($row, 'pending'));
    }

    /** @return array{failed: int, pending: int} */
    public function counts(): array
    {
        return ['failed' => $this->failedQuery()->count(), 'pending' => $this->pendingQuery()->count()];
    }

    // ------------------------------------------------------------------ kirim ulang

    /** Kirim ulang satu email gagal (failed_jobs.uuid). */
    public function resendFailed(string $uuid): string
    {
        $row = $this->failedQuery()->where('uuid', $uuid)->first();

        if (! $row) {
            throw new EmailResendException('Email gagal ini sudah tidak ada (mungkin sudah dikirim ulang atau dihapus).');
        }

        $item = $this->describe($row, 'failed');

        if ($item->type === self::TYPE_OTHER) {
            // Tanpa link kadaluarsa: kembalikan ke antrean apa adanya (record failed otomatis terhapus).
            Artisan::call('queue:retry', ['id' => [$uuid]]);

            return $item->label.' untuk '.$item->recipients.' dimasukkan lagi ke antrean.';
        }

        $message = $this->sendFresh($item);
        $this->failedQuery()->where('uuid', $uuid)->delete();

        return $message;
    }

    /**
     * Kirim ulang semua email gagal untuk satu jenis (verifikasi / reset password) dengan link baru.
     * Satu user hanya dikirimi satu email walaupun gagalnya berkali-kali.
     *
     * @return array{sent: int, skipped: int, cleared: int}
     */
    public function resendAllFailed(string $type): array
    {
        if (! in_array($type, [self::TYPE_VERIFICATION, self::TYPE_RESET], true)) {
            throw new EmailResendException('Jenis email tidak dikenal.');
        }

        $done = [];
        $result = ['sent' => 0, 'skipped' => 0, 'cleared' => 0];

        foreach ($this->failedQuery()->orderBy('id')->get() as $row) {
            $item = $this->describe($row, 'failed');

            if ($item->type !== $type) {
                continue;
            }

            foreach ($item->emails as $email) {
                $key = strtolower($email);

                if (isset($done[$key])) {
                    continue;
                }

                $done[$key] = true;
                $this->sendFreshTo($type, $email) ? $result['sent']++ : $result['skipped']++;
            }

            $this->failedQuery()->where('id', $row->id)->delete();
            $result['cleared']++;
        }

        return $result;
    }

    /**
     * Email yang masih menunggu di antrean (tabel jobs). Verifikasi / reset password: job lama dibuang,
     * diganti email dengan link baru. Jenis lain tidak perlu -- akan terkirim sendiri oleh scheduler.
     */
    public function resendPending(int $id): string
    {
        $row = $this->pendingQuery()->where('id', $id)->first();

        if (! $row) {
            throw new EmailResendException('Email ini sudah tidak ada di antrean (kemungkinan sudah terkirim).');
        }

        if ($row->reserved_at) {
            throw new EmailResendException('Email ini sedang diproses pengirim. Tunggu 1-2 menit lalu muat ulang halaman.');
        }

        $item = $this->describe($row, 'pending');

        if ($item->type === self::TYPE_OTHER) {
            throw new EmailResendException('Email ini akan terkirim otomatis oleh antrean, tidak perlu dikirim ulang.');
        }

        // Hapus dulu (hanya kalau belum diambil pengirim) supaya user tidak menerima link lama + link baru.
        $deleted = $this->pendingQuery()->where('id', $id)->whereNull('reserved_at')->delete();

        if (! $deleted) {
            throw new EmailResendException('Email ini baru saja diproses pengirim. Muat ulang halaman.');
        }

        return $this->sendFresh($item);
    }

    public function deleteFailed(string $uuid): bool
    {
        return (bool) $this->failedQuery()->where('uuid', $uuid)->delete();
    }

    // ------------------------------------------------------------------ helper

    private function sendFresh(object $item): string
    {
        $sent = array_filter($item->emails, fn ($email) => $this->sendFreshTo($item->type, $email));

        if ($sent === []) {
            return $item->label.' untuk '.$item->recipients.' tidak dikirim (user tidak ditemukan atau sudah terverifikasi). Data gagal dihapus dari daftar.';
        }

        return ($item->type === self::TYPE_VERIFICATION ? 'Link verifikasi baru' : 'Link reset password baru')
            .' dikirim ke '.implode(', ', $sent).'.';
    }

    /** false = dilewati (user tidak ada / sudah terverifikasi). */
    private function sendFreshTo(string $type, string $email): bool
    {
        $user = KomUser::where('email', $email)->first();

        if (! $user || ($type === self::TYPE_VERIFICATION && $user->email_verified_at)) {
            return false;
        }

        $type === self::TYPE_VERIFICATION ? $this->sendVerification($user) : $this->sendPasswordReset($user);

        return true;
    }

    /** Ringkasan satu baris failed_jobs / jobs: jenis, penerima, waktu, alasan gagal. */
    public function describe(object $row, string $source): object
    {
        $payload = json_decode($row->payload ?? '', true) ?: [];
        $class = (string) ($payload['displayName'] ?? '');
        $command = (string) ($payload['data']['command'] ?? '');

        // Penerima dibaca dari teks serialisasi (tanpa unserialize, jadi aman walau user sudah dihapus).
        preg_match_all('/s:7:"address";s:\d+:"([^"]+)"/', $command, $matches);
        $emails = array_values(array_unique(array_filter($matches[1] ?? [])));

        if ($emails === [] && preg_match('/s:5:"email";s:\d+:"([^"]+)"/', $command, $match)) {
            $emails = [$match[1]];
        }

        $type = match ($class) {
            KomUserVerificationMail::class => self::TYPE_VERIFICATION,
            ResetPasswordMail::class => self::TYPE_RESET,
            default => self::TYPE_OTHER,
        };

        $at = $source === 'failed'
            ? ($row->failed_at ? \Illuminate\Support\Carbon::parse($row->failed_at) : null)
            : \Illuminate\Support\Carbon::createFromTimestamp((int) $row->created_at, config('app.timezone'));

        return (object) [
            'source' => $source,
            'key' => $source === 'failed' ? $row->uuid : $row->id,
            'type' => $type,
            'label' => self::LABELS[$class] ?? class_basename($class ?: 'Email'),
            'emails' => $emails,
            'recipients' => $emails ? implode(', ', $emails) : '-',
            'at' => $at,
            'reason' => $source === 'failed' ? $this->reason((string) $row->exception) : null,
            'attempts' => (int) ($row->attempts ?? 0),
            'processing' => $source === 'pending' && ! empty($row->reserved_at),
        ];
    }

    /** Baris pertama pesan error, dipendekkan (mis. "535 Authentication failed"). */
    private function reason(string $exception): string
    {
        $first = trim(strtok($exception, "\n") ?: '');
        $first = preg_replace('/^[A-Za-z0-9_\\\\]+(Exception|Error): /', '', $first) ?? $first;

        return Str::limit($first, 160);
    }

    /** Hanya job email (Mail::queue membungkusnya dengan SendQueuedMailable). */
    private function failedQuery(): Builder
    {
        return DB::connection(config('queue.failed.database'))
            ->table(config('queue.failed.table', 'failed_jobs'))
            ->where('payload', 'like', '%SendQueuedMailable%');
    }

    private function pendingQuery(): Builder
    {
        return DB::connection(config('queue.connections.database.connection'))
            ->table(config('queue.connections.database.table', 'jobs'))
            ->where('payload', 'like', '%SendQueuedMailable%');
    }
}
