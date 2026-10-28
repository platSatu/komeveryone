{{--
    kom_v02 — template email notifikasi KOM EveryONE (App\Mail\KomNotifikasiMail).
    Semua gaya ditulis inline supaya tampil rapi di Gmail, Outlook, dan aplikasi email di HP.
--}}
@php
    $warna = [
        'biru' => ['#1e3c72', '#2a5298', '#eef3fc'],
        'hijau' => ['#13795b', '#22a06b', '#e8f8f1'],
        'oranye' => ['#b45309', '#e8892b', '#fff4e5'],
        'ungu' => ['#4c3bcf', '#6a5cff', '#f0ecff'],
    ][$isi['aksen'] ?? 'biru'] ?? ['#1e3c72', '#2a5298', '#eef3fc'];
    [$gelap, $terang, $lembut] = $warna;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $isi['subjek'] }}</title>
</head>
<body style="margin:0; padding:0; background:#f2f5fa; font-family:-apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#1f2a44;">
    {{-- teks pratinjau di daftar kotak masuk --}}
    <div style="display:none; max-height:0; overflow:hidden; opacity:0;">{{ $isi['paragraf'][0] ?? '' }}</div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f2f5fa; padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;">

                    {{-- Kepala --}}
                    <tr>
                        <td style="background:{{ $gelap }}; background-image:linear-gradient(135deg, {{ $gelap }}, {{ $terang }}); border-radius:20px 20px 0 0; padding:28px 32px; text-align:center;">
                            <div style="font-size:13px; letter-spacing:2px; text-transform:uppercase; color:rgba(255,255,255,.75); font-weight:700;">KOM EveryONE</div>
                            <div style="font-size:40px; line-height:1; margin:14px 0 10px;">{{ $isi['ikon'] ?? '✉️' }}</div>
                            <div style="font-size:22px; line-height:1.35; font-weight:700; color:#ffffff;">{{ $isi['judul'] }}</div>
                        </td>
                    </tr>

                    {{-- Isi --}}
                    <tr>
                        <td style="background:#ffffff; padding:30px 32px 8px; border-left:1px solid #e6ebf2; border-right:1px solid #e6ebf2;">
                            <p style="margin:0 0 14px; font-size:16px; font-weight:600; color:#1f2a44;">{{ $isi['sapaan'] }}</p>

                            @foreach ($isi['paragraf'] ?? [] as $kalimat)
                                <p style="margin:0 0 14px; font-size:15px; line-height:1.65; color:#4a5568;">{{ $kalimat }}</p>
                            @endforeach

                            @if (! empty($isi['detail']))
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:18px 0 6px; background:{{ $lembut }}; border-radius:14px;">
                                    <tr>
                                        <td style="padding:6px 20px;">
                                            @foreach ($isi['detail'] as $label => $nilai)
                                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="{{ $loop->last ? '' : 'border-bottom:1px solid rgba(31,42,68,.08);' }}">
                                                    <tr>
                                                        <td style="padding:11px 0; font-size:13px; color:#6b7585; width:38%; vertical-align:top;">{{ $label }}</td>
                                                        <td style="padding:11px 0; font-size:14px; color:#1f2a44; font-weight:600; vertical-align:top;">{{ $nilai }}</td>
                                                    </tr>
                                                </table>
                                            @endforeach
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            @if (! empty($isi['tombol']['url']))
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:24px 0 10px;">
                                    <tr>
                                        <td align="center">
                                            <a href="{{ $isi['tombol']['url'] }}" target="_blank"
                                               style="display:inline-block; background:{{ $terang }}; background-image:linear-gradient(135deg, {{ $gelap }}, {{ $terang }}); color:#ffffff; text-decoration:none; font-size:15px; font-weight:700; padding:14px 30px; border-radius:12px;">
                                                {{ $isi['tombol']['label'] }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            @if (! empty($isi['catatan']))
                                <p style="margin:18px 0 6px; font-size:13px; line-height:1.6; color:#7a8699; background:#f7f9fc; border-radius:12px; padding:12px 16px;">{{ $isi['catatan'] }}</p>
                            @endif
                        </td>
                    </tr>

                    {{-- Penutup --}}
                    <tr>
                        <td style="background:#ffffff; padding:14px 32px 30px; border:1px solid #e6ebf2; border-top:0; border-radius:0 0 20px 20px;">
                            <p style="margin:0; font-size:15px; line-height:1.6; color:#4a5568;">{{ __('Tuhan Yesus memberkati,') }}<br>
                                <strong style="color:#1f2a44;">{{ __('Tim KOM EveryONE') }}</strong></p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 12px 0; text-align:center; font-size:12px; line-height:1.6; color:#9aa5b5;">
                            {{ __('Email ini dikirim otomatis oleh sistem KOM EveryONE, mohon tidak membalas email ini.') }}<br>
                            GBI Jl. Jend. Gatot Subroto · <a href="{{ config('app.url') }}" style="color:#9aa5b5;">{{ preg_replace('#^https?://#', '', config('app.url')) }}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
