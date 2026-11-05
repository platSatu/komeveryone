@extends('layouts.KomDigital')

@section('page_title', __('Kirim Ulang Email'))

{{-- 3 Nov 2026: Admin KOM — email gagal / masih di antrean + kirim ulang (EmailResendController) --}}
@section('content')
@include('a_kom_digital_pusat.katalog._style')
@use('App\Services\Superadmin\EmailResendService', 'ER')
@php
    $badge = [ER::TYPE_VERIFICATION => 'kom-er-tag biru', ER::TYPE_RESET => 'kom-er-tag ungu', ER::TYPE_OTHER => 'kom-er-tag abu'];
    // 4 Nov 2026: tampilan 2 tab (Email gagal / Masih di antrean). Tab ikut di URL (?tab=) supaya
    // tetap di tab yang sama setelah kirim ulang, hapus, atau pindah halaman.
    // 5 Nov 2026: tab ke-3 "Riwayat Terkirim" (semua email yang dicatat di kom_email_log)
    $tab = in_array(request('tab'), ['gagal', 'antrean', 'terkirim'], true) ? request('tab')
        : (request()->has('riwayat') ? 'terkirim' : (request()->has('menunggu') ? 'antrean' : 'gagal'));
    $statusBadge = [\App\Models\Kmd\KomEmailLog::TERKIRIM => 'kom-er-tag hijau', \App\Models\Kmd\KomEmailLog::GAGAL => 'kom-er-tag merah', \App\Models\Kmd\KomEmailLog::ANTRE => 'kom-er-tag abu'];
@endphp
<div class="kom-page container-fluid px-0">
    @include('a_kom_digital_pusat.katalog._flash')

    @if ($errors->any())
        <div class="kom-note kom-note-danger mb-3">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    <p class="kom-text mb-3">Kirim ulang verifikasi / reset password selalu membuat <b>link baru</b>. Email dikirim lewat antrean, biasanya sampai dalam 1&ndash;2 menit.</p>

    {{-- Kirim manual (dilipat supaya halaman tidak panjang) --}}
    <details class="kom-er-manual mb-3" @if ($errors->any() || old('email')) open @endif>
        <summary><i class="bi bi-envelope-plus"></i> Kirim ke email tertentu</summary>
        <form method="POST" action="{{ route('kom.super_email_resend.send_manual') }}" class="kom-er-form mt-3" data-kirim>
            @csrf
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="email@contoh.com" required>
            <select name="type" class="form-control">
                <option value="{{ ER::TYPE_VERIFICATION }}" @selected(old('type') === ER::TYPE_VERIFICATION)>Link verifikasi baru</option>
                <option value="{{ ER::TYPE_RESET }}" @selected(old('type') === ER::TYPE_RESET)>Link reset password baru</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Kirim</button>
        </form>
        <small class="text-muted d-block mt-2">Email harus sudah terdaftar sebagai user. Verifikasi tidak dikirim kalau email user sudah terverifikasi.</small>
    </details>

    {{-- Tab --}}
    <div class="kom-er-tabs" role="tablist">
        <button type="button" role="tab" class="kom-er-tabbtn {{ $tab === 'gagal' ? 'aktif' : '' }}" data-tab="gagal" aria-selected="{{ $tab === 'gagal' ? 'true' : 'false' }}">
            <i class="bi bi-x-octagon"></i> Email gagal terkirim
            <span class="kom-er-count {{ $counts['failed'] ? 'merah' : '' }}">{{ number_format($counts['failed']) }}</span>
        </button>
        <button type="button" role="tab" class="kom-er-tabbtn {{ $tab === 'antrean' ? 'aktif' : '' }}" data-tab="antrean" aria-selected="{{ $tab === 'antrean' ? 'true' : 'false' }}">
            <i class="bi bi-hourglass-split"></i> Masih di antrean
            <span class="kom-er-count">{{ number_format($counts['pending']) }}</span>
        </button>
        <button type="button" role="tab" class="kom-er-tabbtn {{ $tab === 'terkirim' ? 'aktif' : '' }}" data-tab="terkirim" aria-selected="{{ $tab === 'terkirim' ? 'true' : 'false' }}">
            <i class="bi bi-send-check"></i> Riwayat terkirim
            <span class="kom-er-count" title="Terkirim hari ini">{{ number_format($counts['sent_today']) }} hari ini</span>
        </button>
    </div>

    {{-- Tab 1: Email gagal --}}
    <div class="kom-card kom-er-pane mb-3" data-pane="gagal" role="tabpanel" @if ($tab !== 'gagal') hidden @endif>
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap:10px">
            <small class="text-muted">Verifikasi &amp; reset password dikirim ulang dengan <b>link baru</b>; email lain dikirim apa adanya.</small>
            @if ($counts['failed'])
                <div class="d-flex flex-wrap" style="gap:8px">
                    @foreach ([ER::TYPE_VERIFICATION => 'verifikasi', ER::TYPE_RESET => 'reset password'] as $type => $label)
                        <form method="POST" action="{{ route('kom.super_email_resend.resend_all_failed') }}" class="m-0" data-kirim
                            data-konfirmasi="Kirim ulang SEMUA email {{ $label }} yang gagal dengan link baru? Satu user hanya dikirimi satu email.">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-repeat"></i> Kirim ulang semua {{ $label }}</button>
                        </form>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-sm kom-er-table">
                <thead>
                    <tr><th>Jenis</th><th>Penerima</th><th>Gagal pada</th><th>Alasan</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($failed as $item)
                        <tr>
                            <td><span class="{{ $badge[$item->type] }}">{{ $item->label }}</span></td>
                            <td class="text-break">{{ $item->recipients }}</td>
                            <td class="text-nowrap">{{ $item->at?->format('d M Y H:i') ?? '-' }}</td>
                            <td><small class="text-muted text-break">{{ $item->reason }}</small></td>
                            <td class="text-right text-nowrap">
                                <form method="POST" action="{{ route('kom.super_email_resend.resend_failed', $item->key) }}" class="d-inline m-0" data-kirim>
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary" title="{{ $item->type === ER::TYPE_OTHER ? 'Kirim ulang apa adanya' : 'Kirim dengan link baru' }}">
                                        <i class="bi bi-send"></i> Kirim Ulang
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('kom.super_email_resend.destroy_failed', $item->key) }}" class="d-inline m-0" data-kirim
                                    data-konfirmasi="Hapus data email gagal ini dari daftar? Email tidak akan dikirim.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus dari daftar"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada email gagal.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $failed->appends(['tab' => 'gagal'])->links('pagination::bootstrap-4') }}
    </div>

    {{-- Tab 2: Antrean --}}
    <div class="kom-card kom-er-pane mb-3" data-pane="antrean" role="tabpanel" @if ($tab !== 'antrean') hidden @endif>
        <small class="text-muted d-block mb-3">Diproses otomatis tiap menit. Kalau link verifikasi / reset password di sini sudah lewat batas waktunya, klik <b>Kirim Link Baru</b>.</small>

        <div class="table-responsive">
            <table class="table table-sm kom-er-table">
                <thead>
                    <tr><th>Jenis</th><th>Penerima</th><th>Masuk antrean</th><th>Percobaan</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($pending as $item)
                        <tr>
                            <td><span class="{{ $badge[$item->type] }}">{{ $item->label }}</span></td>
                            <td class="text-break">{{ $item->recipients }}</td>
                            <td class="text-nowrap">{{ $item->at?->format('d M Y H:i') ?? '-' }}</td>
                            <td>{{ $item->attempts }}x</td>
                            <td class="text-right text-nowrap">
                                @if ($item->processing)
                                    <small class="text-muted">Sedang dikirim&hellip;</small>
                                @elseif ($item->type === ER::TYPE_OTHER)
                                    <small class="text-muted">Terkirim otomatis</small>
                                @else
                                    <form method="POST" action="{{ route('kom.super_email_resend.resend_pending', $item->key) }}" class="d-inline m-0" data-kirim
                                        data-konfirmasi="Ganti email ini dengan link baru?">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-repeat"></i> Kirim Link Baru</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Antrean kosong.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $pending->appends(['tab' => 'antrean'])->links('pagination::bootstrap-4') }}
    </div>

    {{-- Tab 3: Riwayat terkirim (5 Nov 2026) --}}
    <div class="kom-card kom-er-pane mb-3" data-pane="terkirim" role="tabpanel" @if ($tab !== 'terkirim') hidden @endif>
        @unless ($historyReady)
            <div class="kom-note kom-note-danger mb-3">Tabel riwayat belum dibuat. Jalankan <code>php artisan migrate</code> di server.</div>
        @endunless

        <form method="GET" class="kom-er-filter mb-2">
            <input type="hidden" name="tab" value="terkirim">
            <input type="text" name="cari" value="{{ request('cari') }}" class="form-control" maxlength="100" placeholder="Cari email peserta atau judul email">
            <select name="status" class="form-control">
                <option value="">Semua status</option>
                @foreach (\App\Models\Kmd\KomEmailLog::STATUS_LABEL as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="dari" value="{{ request('dari') }}" class="form-control" title="Dari tanggal">
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control" title="Sampai tanggal">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
            @if (request()->hasAny(['cari', 'status', 'dari', 'sampai']))
                <a href="{{ route('kom.super_email_resend.index', ['tab' => 'terkirim']) }}" class="btn btn-outline-secondary" title="Hapus filter"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
        <small class="text-muted d-block mb-3">
            Semua email aplikasi tercatat otomatis sejak fitur ini aktif. <b>Terkirim</b> = sudah diterima server email
            (belum tentu dibuka peserta / tidak masuk spam). Isi email tidak disimpan, hanya judulnya.
        </small>

        <div class="table-responsive">
            <table class="table table-sm kom-er-table">
                <thead>
                    <tr><th>Waktu</th><th>Jenis</th><th>Penerima</th><th>Judul</th><th>Status</th><th>Terkirim pada</th></tr>
                </thead>
                <tbody>
                    @forelse ($history as $log)
                        <tr>
                            <td class="text-nowrap">{{ $log->created_at?->format('d M Y H:i') }}</td>
                            <td><span class="kom-er-tag abu">{{ ER::labelFor($log->jenis) }}</span></td>
                            <td class="text-break">{{ $log->penerima }}</td>
                            <td><small class="text-break">{{ \Illuminate\Support\Str::limit($log->judul, 70) ?: '-' }}</small></td>
                            <td>
                                <span class="{{ $statusBadge[$log->status] ?? 'kom-er-tag abu' }}">{{ \App\Models\Kmd\KomEmailLog::STATUS_LABEL[$log->status] ?? $log->status }}</span>
                                @if ($log->error)
                                    <small class="d-block text-danger text-break" title="{{ $log->error }}">{{ \Illuminate\Support\Str::limit($log->error, 60) }}</small>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ $log->terkirim_at?->format('d M Y H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat email{{ request()->hasAny(['cari', 'status', 'dari', 'sampai']) ? ' yang cocok dengan filter' : '' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $history->appends(['tab' => 'terkirim'])->links('pagination::bootstrap-4') }}
    </div>
</div>

<style>
    .kom-er-manual { background:#fff; border:1px solid #e6ebf2; border-radius:14px; padding:12px 18px; }
    .kom-er-manual summary { cursor:pointer; font-weight:600; color:#2a5298; }
    .kom-er-tabs { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px; }
    .kom-er-tabbtn { display:inline-flex; align-items:center; gap:8px; border:1px solid #e3e8f0; background:#fff; color:#5b6576;
        border-radius:12px; padding:10px 16px; font-weight:600; cursor:pointer; }
    .kom-er-tabbtn:hover { border-color:#9db8f2; }
    .kom-er-tabbtn.aktif { background:#2a5298; border-color:#2a5298; color:#fff; }
    .kom-er-count { background:#eef0f3; color:#5b6576; border-radius:999px; padding:1px 9px; font-size:.8rem; }
    .kom-er-count.merah { background:#fdeeee; color:#dc3545; }
    .kom-er-tabbtn.aktif .kom-er-count { background:rgba(255,255,255,.2); color:#fff; }
    .kom-er-form { display:flex; flex-wrap:wrap; gap:8px; }
    .kom-er-form input { flex:1 1 240px; }
    .kom-er-form select { flex:0 1 230px; }
    .kom-er-table td, .kom-er-table th { vertical-align:middle; }
    .kom-er-tag { display:inline-block; font-size:.78rem; font-weight:600; border-radius:999px; padding:3px 10px; white-space:nowrap; }
    .kom-er-tag.biru { background:#e8effb; color:#2a5298; }
    .kom-er-tag.ungu { background:#f1eafb; color:#6f42c1; }
    .kom-er-tag.abu  { background:#eef0f3; color:#5b6576; }
    .kom-er-tag.hijau { background:#e7f6ec; color:#198754; }
    .kom-er-tag.merah { background:#fdeeee; color:#dc3545; }
    .kom-er-filter { display:flex; flex-wrap:wrap; gap:8px; }
    .kom-er-filter input[type=text] { flex:1 1 240px; }
    .kom-er-filter select { flex:0 1 170px; }
    .kom-er-filter input[type=date] { flex:0 1 160px; }
</style>
<script>
    // Pindah tab tanpa memuat ulang halaman; tab disimpan di URL supaya tetap sama setelah aksi.
    document.querySelectorAll('.kom-er-tabbtn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tab = btn.dataset.tab;
            document.querySelectorAll('.kom-er-tabbtn').forEach(function (b) {
                b.classList.toggle('aktif', b === btn);
                b.setAttribute('aria-selected', b === btn ? 'true' : 'false');
            });
            document.querySelectorAll('.kom-er-pane').forEach(function (p) { p.hidden = p.dataset.pane !== tab; });
            try {
                var url = new URL(window.location.href);
                url.searchParams.set('tab', tab);
                window.history.replaceState(null, '', url.toString());
            } catch (e) {}
        });
    });

    // Konfirmasi (kalau ada) + cegah klik dobel.
    document.querySelectorAll('form[data-kirim]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (form.dataset.konfirmasi && !confirm(form.dataset.konfirmasi)) { e.preventDefault(); return; }
            form.querySelectorAll('button[type=submit]').forEach(function (b) { b.disabled = true; });
        });
    });
</script>
@endsection
