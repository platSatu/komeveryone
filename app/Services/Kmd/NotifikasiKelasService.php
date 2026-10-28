<?php

namespace App\Services\Kmd;

use App\Mail\KomNotifikasiMail;
use App\Models\Kmd\KmdClassTutorial;
use App\Models\Kmd\KmdDonasi;
use App\Models\Kmd\KmdUserClassTutorial;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Traits\Localizable;
use Throwable;

/**
 * kom_v02 — semua email notifikasi kelas tutorial ada di sini.
 *
 *  1. kelasDibuka()        → email pusat + admin cabang, saat jadwal kelas tutorial dibuat
 *  2. pengingatBayar()     → peserta yang belum menyelesaikan persembahan, tiap 5 menit selama batas waktu
 *  3. terimaKasihPeserta() → peserta, setelah persembahan lunas (dipanggil ProsesTutorialLunasJob)
 *  4. pesertaBergabung()   → admin cabang, setelah peserta lunas & tergabung di kelas
 *  5. pengingatKelas()     → peserta & pengajar: H-3, H-2, hari H, dan 1 jam sebelum kelas
 *
 * Ringan: email masuk antrean (kecuali #3 yang sudah berjalan di dalam job), dan setiap notifikasi
 * diklaim dulu di tabel kom_notifikasi_terkirim, jadi jadwal tiap menit tidak pernah mengirim dobel.
 * #2 dan #5 dijalankan perintah kom:kirim-notifikasi (routes/console.php, tiap menit).
 *
 * Pengaman (diuji langsung di production): #1, #2, #4, #5 hanya jalan kalau KOM_NOTIFIKASI_AKTIF=true,
 * dan `php artisan kom:kirim-notifikasi --simulasi` menampilkan calon penerima tanpa mengirim apa pun.
 * #3 (terima kasih) adalah email yang sudah ada sebelumnya, jadi tetap jalan.
 */
class NotifikasiKelasService
{
    use Localizable;

    private const ZONA = 'Asia/Jakarta';

    /** Mode simulasi: tidak menandai & tidak mengirim, hanya mencatat calon penerima. */
    private bool $simulasi = false;

    /** @var array<int, array{0: string, 1: string}> [email, subjek] hasil simulasi */
    public array $hasilSimulasi = [];

    public function __construct(private readonly CheckoutTutorialService $checkout)
    {
    }

    public function aktif(): bool
    {
        return (bool) config('kom.notifikasi.aktif');
    }

    public function simulasi(): static
    {
        $this->simulasi = true;

        return $this;
    }

    /** 1. Jadwal kelas tutorial baru dibuat. */
    public function kelasDibuka(KmdClassTutorial $kelas): void
    {
        if (! $this->aktif()) {
            return;
        }

        $kelas->loadMissing(['user', 'pengajar', 'modul', 'rayon', 'titikVideo']);

        $penerima = array_merge([config('kom.notifikasi.email_pusat')], $this->emailAdminCabang($kelas));

        foreach ($this->unik($penerima) as $email) {
            if (! $this->klaim("kelas-dibuka:{$kelas->id}:{$email}")) {
                continue;
            }

            $this->antre($email, $this->dalamBahasa('id', fn () => [
                'subjek' => __('Kelas tutorial baru dibuka: :topik', ['topik' => $kelas->topic]),
                'judul' => __('Kelas Tutorial Baru Dibuka'),
                'ikon' => '📚',
                'aksen' => 'biru',
                'sapaan' => __('Shalom, Bapak/Ibu!'),
                'paragraf' => [
                    __('Kabar baik! Satu jadwal kelas tutorial baru saja dibuka dan sudah bisa dipilih oleh peserta.'),
                    __('Berikut ringkasan jadwalnya. Terima kasih sudah melayani dan menyiapkan kelas ini dengan sepenuh hati.'),
                ],
                'detail' => $this->detailKelas($kelas) + [__('Kuota') => __(':n peserta', ['n' => $kelas->quota])],
                'tombol' => ['label' => __('Lihat Jadwal Kelas'), 'url' => $this->rute('kom.kelas_tutor_cabang.index')],
            ]));
        }
    }

    /** 2. Pengingat menyelesaikan persembahan, tiap N menit selama batas waktu checkout. Mengembalikan jumlah email. */
    public function pengingatBayar(): int
    {
        $jeda = max(1, (int) config('kom.notifikasi.pengingat_bayar_menit', 5));
        $sekarang = Carbon::now(self::ZONA);
        $terkirim = 0;

        KmdUserClassTutorial::with(['user', 'classTutorial.pengajar', 'classTutorial.modul', 'classTutorial.titikVideo'])
            ->where('status_progress', 'pending')
            ->where('step', 'donasi')
            ->where('updated_at', '>=', $sekarang->copy()->subDay())
            ->get()
            ->each(function (KmdUserClassTutorial $booking) use ($jeda, $sekarang, &$terkirim) {
                $email = optional($booking->user)->email;
                $batas = $this->checkout->batasWaktu($booking)->setTimezone(self::ZONA);

                if (! $email || ! $booking->classTutorial || $sekarang->gte($batas)) {
                    return;
                }

                $mulai = $batas->copy()->subMinutes($this->checkout->menitPersembahan());
                $ke = intdiv(max(0, $sekarang->getTimestamp() - $mulai->getTimestamp()), $jeda * 60);

                if ($ke < 1 || ! $this->klaim("pengingat-bayar:{$booking->id}:{$ke}")) {
                    return;
                }

                $sisa = max(1, (int) ceil(($batas->getTimestamp() - $sekarang->getTimestamp()) / 60));
                $kelas = $booking->classTutorial;

                $this->antre($email, $this->dalamBahasa($booking->user->language_code, fn () => [
                    'subjek' => __('Tinggal selangkah lagi untuk bergabung di kelas tutorial 🙏'),
                    'judul' => __('Persembahan Anda Belum Selesai'),
                    'ikon' => '⏳',
                    'aksen' => 'oranye',
                    'sapaan' => __('Shalom, :nama!', ['nama' => $booking->user->name]),
                    'paragraf' => [
                        __('Terima kasih sudah memilih jadwal kelas tutorial. Kami melihat persembahan Anda belum selesai. Tidak apa-apa, mungkin tadi ada yang perlu diurus dulu.'),
                        __('Supaya tempat Anda di kelas ini tetap tersimpan, yuk selesaikan persembahan dalam :menit menit ke depan.', ['menit' => $sisa]),
                    ],
                    'detail' => $this->detailKelas($kelas) + [__('Batas waktu') => $batas->format('H.i').' WIB'],
                    'tombol' => ['label' => __('Selesaikan Persembahan'), 'url' => $this->rute('kom.form.persembahan')],
                    'catatan' => __('Kalau waktunya terlewat, pendaftaran akan dibatalkan otomatis dan Anda bisa memilih jadwal lagi kapan saja. Abaikan email ini jika persembahan sudah Anda selesaikan.'),
                ]));
                $terkirim++;
            });

        return $terkirim;
    }

    /** 3. Terima kasih ke peserta setelah lunas. Dikirim langsung (sudah di dalam job antrean). */
    public function terimaKasihPeserta(KmdUserClassTutorial $booking, KmdDonasi $donasi): void
    {
        $booking->loadMissing(['user', 'classTutorial.pengajar', 'classTutorial.modul', 'classTutorial.titikVideo']);
        $email = optional($booking->user)->email;

        $kunci = "terima-kasih:{$booking->id}";

        // Dicatat SETELAH berhasil terkirim, supaya kalau server email gagal, job antrean mencoba lagi.
        if (! $email || ! $booking->classTutorial || DB::table('kom_notifikasi_terkirim')->where('kunci', $kunci)->exists()) {
            return;
        }

        $isi = $this->dalamBahasa($booking->user->language_code, fn () => [
            'subjek' => __('Terima kasih! Anda sudah terdaftar di kelas tutorial 🎉'),
            'judul' => __('Persembahan Diterima, Sampai Jumpa di Kelas!'),
            'ikon' => '🎉',
            'aksen' => 'hijau',
            'sapaan' => __('Shalom, :nama!', ['nama' => $booking->user->name]),
            'paragraf' => [
                __('Terima kasih atas persembahan Anda sebesar :jumlah. Persembahan sudah kami terima dengan baik dan Anda sudah resmi terdaftar di kelas tutorial ini.', ['jumlah' => $donasi->formatted_amount]),
                $booking->zoom_join_url
                    ? __('Gunakan tombol di bawah untuk masuk ke kelas Zoom saat jadwalnya tiba. Kami akan mengingatkan Anda lagi sebelum kelas dimulai.')
                    : __('Link Zoom kelas bisa Anda lihat kapan saja di menu "Kelas Tutorial Saya". Kami akan mengingatkan Anda lagi sebelum kelas dimulai.'),
            ],
            'detail' => $this->detailKelas($booking->classTutorial),
            'tombol' => $booking->zoom_join_url
                ? ['label' => __('Masuk Kelas Zoom'), 'url' => $booking->zoom_join_url]
                : ['label' => __('Buka Kelas Tutorial Saya'), 'url' => $this->rute('kom.peserta.kelas_tutorial')],
            'catatan' => __('Simpan email ini sebagai bukti persembahan Anda.'),
        ]);

        Mail::to($email)->send(new KomNotifikasiMail($isi));
        $this->klaim($kunci);
    }

    /** 4. Kabar ke admin cabang: peserta sudah lunas dan tergabung di kelasnya. */
    public function pesertaBergabung(string $bookingId): void
    {
        if (! $this->aktif()) {
            return;
        }

        $booking = KmdUserClassTutorial::with(['user', 'classTutorial.user', 'classTutorial.pengajar', 'classTutorial.modul', 'classTutorial.titikVideo'])->find($bookingId);
        $kelas = optional($booking)->classTutorial;

        if (! $booking || ! $kelas || ! $booking->user) {
            return;
        }

        foreach ($this->unik($this->emailAdminCabang($kelas)) as $email) {
            if (! $this->klaim("peserta-bergabung:{$booking->id}:{$email}")) {
                continue;
            }

            $this->antre($email, $this->dalamBahasa('id', fn () => [
                'subjek' => __(':nama bergabung di kelas :topik', ['nama' => $booking->user->name, 'topik' => $kelas->topic]),
                'judul' => __('Ada Peserta Baru di Kelas Anda'),
                'ikon' => '🙌',
                'aksen' => 'ungu',
                'sapaan' => __('Shalom, Bapak/Ibu Admin Cabang!'),
                'paragraf' => [
                    $booking->is_rebook
                        ? __(':nama memilih kelas ini sebagai jadwal pengganti dan sudah tergabung di kelas tutorial Anda.', ['nama' => $booking->user->name])
                        : __(':nama sudah menyelesaikan persembahan dan kini tergabung di kelas tutorial Anda.', ['nama' => $booking->user->name]),
                    __('Terima kasih sudah menyambut setiap peserta. Semoga kelas ini menjadi berkat bagi semua yang hadir.'),
                ],
                'detail' => [
                    __('Peserta') => $booking->user->name,
                    __('Email peserta') => $booking->user->email ?: '-',
                ] + $this->detailKelas($kelas) + [
                    __('Sisa kuota') => __(':n dari :kuota', ['n' => $kelas->sisa_quota, 'kuota' => $kelas->quota]),
                ],
                'tombol' => ['label' => __('Lihat Peserta Kelas'), 'url' => $this->rute('kom.kelas_tutor_cabang.index')],
            ]));
        }
    }

    /** 5. Pengingat kelas H-3, H-2, hari H, dan 1 jam sebelum mulai, ke peserta & pengajar. Mengembalikan jumlah email. */
    public function pengingatKelas(): int
    {
        $sekarang = Carbon::now(self::ZONA);
        $terkirim = 0;

        KmdClassTutorial::with(['pengajar', 'modul', 'titikVideo', 'rayon'])
            ->where('status', 'active')
            ->whereBetween('start_time', [$sekarang->format('Y-m-d H:i:s'), $sekarang->copy()->addDays(3)->addMinutes(5)->format('Y-m-d H:i:s')])
            ->get()
            ->each(function (KmdClassTutorial $kelas) use ($sekarang, &$terkirim) {
                $mulai = Carbon::parse($kelas->start_time, self::ZONA);
                $tahap = $this->tahapPengingat($mulai, $sekarang);

                if (! $tahap) {
                    return;
                }

                // Peserta yang sudah lunas & masih terdaftar
                $peserta = KmdUserClassTutorial::with('user')
                    ->where('class_tutorial_id', $kelas->id)
                    ->whereIn('status', ['booked', 'aktif'])
                    ->lunas()
                    ->get();

                foreach ($peserta as $booking) {
                    $email = optional($booking->user)->email;
                    if ($email && $this->klaim("pengingat-kelas:{$tahap}:{$kelas->id}:".strtolower($email))) {
                        $this->antre($email, $this->isiPengingat($kelas, $mulai, $tahap, $booking->user, false, $booking->zoom_join_url));
                        $terkirim++;
                    }
                }

                $pengajar = $kelas->pengajar;
                if ($pengajar && $pengajar->email && $peserta->isNotEmpty()
                    && $this->klaim("pengingat-kelas:{$tahap}:{$kelas->id}:".strtolower($pengajar->email))) {
                    $this->antre($pengajar->email, $this->isiPengingat($kelas, $mulai, $tahap, $pengajar, true, $kelas->join_url, $peserta->count()));
                    $terkirim++;
                }
            });

        return $terkirim;
    }

    /** Tahap pengingat yang sedang berlaku: h3 | h2 | hari | 1jam | null. */
    private function tahapPengingat(Carbon $mulai, Carbon $sekarang): ?string
    {
        if ($sekarang->gte($mulai)) {
            return null;
        }

        if ($sekarang->gte($mulai->copy()->subHour())) {
            return '1jam';
        }

        if ($sekarang->isSameDay($mulai)) {
            return $sekarang->hour >= (int) config('kom.notifikasi.jam_pengingat_hari_h', 6) ? 'hari' : null;
        }

        if ($sekarang->gte($mulai->copy()->subDays(2)) && $sekarang->lt($mulai->copy()->subDay())) {
            return 'h2';
        }

        if ($sekarang->gte($mulai->copy()->subDays(3)) && $sekarang->lt($mulai->copy()->subDays(2))) {
            return 'h3';
        }

        return null;
    }

    private function isiPengingat(KmdClassTutorial $kelas, Carbon $mulai, string $tahap, $orang, bool $pengajar, ?string $linkZoom, int $jumlahPeserta = 0): array
    {
        return $this->dalamBahasa($orang->language_code, function () use ($kelas, $mulai, $tahap, $orang, $pengajar, $linkZoom, $jumlahPeserta) {
            $kapan = [
                'h3' => __('3 hari lagi'),
                'h2' => __('2 hari lagi'),
                'hari' => __('hari ini, pukul :jam WIB', ['jam' => $mulai->format('H.i')]),
                '1jam' => __('1 jam lagi'),
            ][$tahap];

            $paragraf = $pengajar
                ? [
                    __('Kelas tutorial yang Anda layani akan dimulai :kapan. Saat ini sudah ada :n peserta yang terdaftar dan menantikan kehadiran Anda.', ['kapan' => $kapan, 'n' => $jumlahPeserta]),
                    $tahap === '1jam'
                        ? __('Mohon bersiap dan masuk ke Zoom beberapa menit lebih awal ya. Terima kasih untuk pelayanan Anda!')
                        : __('Terima kasih sudah bersedia melayani. Semoga persiapannya lancar dan menjadi berkat.'),
                ]
                : [
                    __('Kelas tutorial Anda akan dimulai :kapan. Kami sudah tidak sabar bertemu dan belajar bersama Anda!', ['kapan' => $kapan]),
                    $tahap === '1jam'
                        ? __('Siapkan tempat yang nyaman dan koneksi internet yang stabil, lalu masuk ke Zoom beberapa menit lebih awal ya.')
                        : __('Tandai kalender Anda supaya tidak terlewat. Sampai jumpa di kelas!'),
                ];

            return [
                'subjek' => $tahap === '1jam'
                    ? __('⏰ 1 jam lagi: :topik segera dimulai', ['topik' => $kelas->topic])
                    : __('Pengingat kelas tutorial: :topik (:kapan)', ['topik' => $kelas->topic, 'kapan' => $kapan]),
                'judul' => $tahap === '1jam' ? __('Kelas Dimulai 1 Jam Lagi') : __('Pengingat Kelas Tutorial'),
                'ikon' => $tahap === '1jam' ? '⏰' : '📅',
                'aksen' => $tahap === '1jam' ? 'oranye' : 'biru',
                'sapaan' => __('Shalom, :nama!', ['nama' => $orang->name]),
                'paragraf' => $paragraf,
                'detail' => $this->detailKelas($kelas),
                'tombol' => $linkZoom
                    ? ['label' => __('Masuk Kelas Zoom'), 'url' => $linkZoom]
                    : ['label' => __('Buka Kelas Tutorial Saya'), 'url' => $this->rute('kom.peserta.kelas_tutorial')],
            ];
        });
    }

    /** Ringkasan kelas untuk tabel detail di email. */
    private function detailKelas(KmdClassTutorial $kelas): array
    {
        $mulai = $kelas->start_time ? Carbon::parse($kelas->start_time, self::ZONA) : null;

        return array_filter([
            __('Kelas') => $kelas->topic,
            __('Modul') => optional($kelas->modul)->name,
            __('Titik tutorial') => $kelas->label_titik,
            __('Jadwal') => $mulai ? $mulai->translatedFormat('l, d F Y').' · '.$mulai->format('H.i').' WIB' : null,
            __('Durasi') => $kelas->duration ? __(':n menit', ['n' => $kelas->duration]) : null,
            __('Pengajar') => optional($kelas->pengajar)->name,
            __('Cabang') => optional($kelas->rayon)->name,
        ], fn ($nilai) => filled($nilai));
    }

    /** Email admin cabang pemilik jadwal + email cabang yang diisi di jadwal. */
    private function emailAdminCabang(KmdClassTutorial $kelas): array
    {
        return [optional($kelas->user)->email, $kelas->email_cabang];
    }

    private function unik(array $email): array
    {
        return array_values(array_unique(array_filter(array_map(
            fn ($e) => filter_var(trim((string) $e), FILTER_VALIDATE_EMAIL) ? strtolower(trim($e)) : null,
            $email
        ))));
    }

    /** Tandai notifikasi sebagai terkirim; false = sudah pernah dikirim. */
    private function klaim(string $kunci): bool
    {
        $kunci = mb_substr($kunci, 0, 191);

        if ($this->simulasi) {
            return ! DB::table('kom_notifikasi_terkirim')->where('kunci', $kunci)->exists();
        }

        return DB::table('kom_notifikasi_terkirim')->insertOrIgnore(['kunci' => $kunci, 'created_at' => now()]) > 0;
    }

    private function antre(string $email, array $isi): void
    {
        if ($this->simulasi) {
            $this->hasilSimulasi[] = [$email, $isi['subjek'] ?? ''];

            return;
        }

        try {
            Mail::to($email)->queue(new KomNotifikasiMail($isi));
        } catch (Throwable $e) {
            Log::error('[Notifikasi] Gagal memasukkan email ke antrean.', ['email' => $email, 'subjek' => $isi['subjek'] ?? null, 'pesan' => $e->getMessage()]);
        }
    }

    private function dalamBahasa(?string $bahasa, callable $susun): array
    {
        return $this->withLocale($bahasa ?: 'id', $susun);
    }

    private function rute(string $nama): ?string
    {
        return \Illuminate\Support\Facades\Route::has($nama) ? route($nama) : config('app.url');
    }
}
