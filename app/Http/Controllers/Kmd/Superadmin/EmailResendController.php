<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kmd\KomUser;
use App\Services\Superadmin\EmailResendService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Services\Superadmin\EmailResendException;
use Throwable;

/**
 * Admin KOM > Kirim Ulang Email (3 November 2026): daftar email gagal / masih di antrean, kirim ulang
 * per baris atau sekaligus, kirim manual ke satu email, dan tombol kirim dari menu Daftar User.
 * Logika ada di EmailResendService.
 */
class EmailResendController extends Controller
{
    public function __construct(private readonly EmailResendService $emails)
    {
    }

    public function index(): View
    {
        return view('a_kom_digital_pusat.superadmin.email_resend.index', [
            'failed' => $this->emails->failedPage(),
            'pending' => $this->emails->pendingPage(),
            'counts' => $this->emails->counts(),
        ]);
    }

    /** Form "Kirim ke email tertentu". */
    public function sendManual(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'type' => ['required', Rule::in([EmailResendService::TYPE_VERIFICATION, EmailResendService::TYPE_RESET])],
        ], [], ['type' => 'jenis email']);

        $user = KomUser::where('email', trim($data['email']))->first();

        if (! $user) {
            return back()->withInput()->with('error', 'Tidak ada user dengan email '.$data['email'].'.');
        }

        return $this->run(fn () => $this->emails->sendToUser($user, $data['type']));
    }

    /** Tombol di menu Daftar User. */
    public function sendToUser(Request $request, string $encryptedId): RedirectResponse
    {
        $request->validate(['type' => ['required', Rule::in([EmailResendService::TYPE_VERIFICATION, EmailResendService::TYPE_RESET])]]);

        try {
            $user = KomUser::findOrFail(Crypt::decryptString($encryptedId));
        } catch (DecryptException) {
            abort(404);
        }

        return $this->run(fn () => $this->emails->sendToUser($user, $request->input('type')));
    }

    public function resendFailed(string $uuid): RedirectResponse
    {
        return $this->run(fn () => $this->emails->resendFailed($uuid));
    }

    public function resendAllFailed(Request $request): RedirectResponse
    {
        $request->validate(['type' => ['required', Rule::in([EmailResendService::TYPE_VERIFICATION, EmailResendService::TYPE_RESET])]]);

        return $this->run(function () use ($request) {
            $r = $this->emails->resendAllFailed($request->input('type'));
            $label = $request->input('type') === EmailResendService::TYPE_VERIFICATION ? 'verifikasi' : 'reset password';

            return "Link {$label} baru dikirim ke {$r['sent']} user"
                .($r['skipped'] ? ", {$r['skipped']} dilewati (user tidak ada / sudah terverifikasi)" : '')
                .". {$r['cleared']} data gagal dibersihkan dari daftar.";
        });
    }

    public function resendPending(int $id): RedirectResponse
    {
        return $this->run(fn () => $this->emails->resendPending($id));
    }

    public function destroyFailed(string $uuid): RedirectResponse
    {
        return $this->emails->deleteFailed($uuid)
            ? back()->with('success', 'Data email gagal dihapus dari daftar.')
            : back()->with('error', 'Data email gagal ini sudah tidak ada.');
    }

    /** Jalankan aksi; pesan EmailResendException ditampilkan apa adanya, error lain dicatat ke log. */
    private function run(callable $action): RedirectResponse
    {
        try {
            return back()->with('success', $action());
        } catch (EmailResendException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Kirim ulang email gagal: '.$e->getMessage(), ['exception' => $e]);

            return back()->with('error', 'Gagal mengirim ulang email. Detail tercatat di log.');
        }
    }
}
