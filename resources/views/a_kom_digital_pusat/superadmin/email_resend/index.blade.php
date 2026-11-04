@extends('layouts.KomDigital')

@section('page_title', __('Kirim Ulang Email'))

{{-- 3 Nov 2026: Admin KOM — email gagal / masih di antrean + kirim ulang (EmailResendController) --}}
@section('content')
@include('a_kom_digital_pusat.katalog._style')
@use('App\Services\Superadmin\EmailResendService', 'ER')
@php
    $badge = [ER::TYPE_VERIFICATION => 'kom-er-tag biru', ER::TYPE_RESET => 'kom-er-tag ungu', ER::TYPE_OTHER => 'kom-er-tag abu'];
@endphp
<div class="kom-page container-fluid px-0">
    @include('a_kom_digital_pusat.katalog._flash')

    @if ($errors->any())
        <div class="kom-note kom-note-danger mb-3">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    <p class="kom-text mb-3">
        Link <b>verifikasi</b> berlaku 10 menit dan link <b>reset password</b> 60 menit, jadi kirim ulang untuk dua jenis ini
        selalu membuat <b>link baru</b> (link lama otomatis tidak berlaku). Email lain (undangan kelas, link Zoom, notifikasi)
        dikirim ulang apa adanya. Semua email dikirim lewat antrean, biasanya sampai dalam 1&ndash;2 menit.
    </p>

    {{-- Ringkasan --}}
    <div class="kom-er-stats mb-3">
        <div class="kom-er-stat {{ $counts['failed'] ? 'merah' : '' }}"><small>Email gagal</small><strong>{{ number_format($counts['failed']) }}</strong></div>
        <div class="kom-er-stat"><small>Masih di antrean</small><strong>{{ number_format($counts['pending']) }}</strong></div>
    </div>

    {{-- Kirim manual --}}
    <div class="kom-card mb-3">
        <h5 class="kom-card-title mb-3">Kirim ke email tertentu</h5>
        <form method="POST" action="{{ route('kom.super_email_resend.send_manual') }}" class="kom-er-form" data-kirim>
            @csrf
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="email@contoh.com" required>
            <select name="type" class="form-control">
                <option value="{{ ER::TYPE_VERIFICATION }}" @selected(old('type') === ER::TYPE_VERIFICATION)>Link verifikasi baru</option>
                <option value="{{ ER::TYPE_RESET }}" @selected(old('type') === ER::TYPE_RESET)>Link reset password baru</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Kirim</button>
        </form>
        <small class="text-muted d-block mt-2">Email harus sudah terdaftar sebagai user. Verifikasi tidak dikirim kalau email user sudah terverifikasi.</small>
    </div>

    {{-- Email gagal --}}
    <div class="kom-card mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap:10px">
            <h5 class="kom-card-title m-0">Email gagal terkirim</h5>
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
        {{ $failed->appends(request()->except('gagal'))->links('pagination::bootstrap-4') }}
    </div>

    {{-- Antrean --}}
    <div class="kom-card mb-3">
        <h5 class="kom-card-title mb-1">Masih di antrean</h5>
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
        {{ $pending->appends(request()->except('menunggu'))->links('pagination::bootstrap-4') }}
    </div>
</div>

<style>
    .kom-er-stats { display:flex; flex-wrap:wrap; gap:12px; }
    .kom-er-stat { background:#fff; border:1px solid #e6ebf2; border-radius:14px; padding:12px 18px; min-width:160px; }
    .kom-er-stat small { display:block; color:#5b6576; }
    .kom-er-stat strong { font-size:1.5rem; color:#1e3c72; }
    .kom-er-stat.merah strong { color:#dc3545; }
    .kom-er-form { display:flex; flex-wrap:wrap; gap:8px; }
    .kom-er-form input { flex:1 1 240px; }
    .kom-er-form select { flex:0 1 230px; }
    .kom-er-table td, .kom-er-table th { vertical-align:middle; }
    .kom-er-tag { display:inline-block; font-size:.78rem; font-weight:600; border-radius:999px; padding:3px 10px; white-space:nowrap; }
    .kom-er-tag.biru { background:#e8effb; color:#2a5298; }
    .kom-er-tag.ungu { background:#f1eafb; color:#6f42c1; }
    .kom-er-tag.abu  { background:#eef0f3; color:#5b6576; }
</style>
<script>
    // Konfirmasi (kalau ada) + cegah klik dobel.
    document.querySelectorAll('form[data-kirim]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (form.dataset.konfirmasi && !confirm(form.dataset.konfirmasi)) { e.preventDefault(); return; }
            form.querySelectorAll('button[type=submit]').forEach(function (b) { b.disabled = true; });
        });
    });
</script>
@endsection
