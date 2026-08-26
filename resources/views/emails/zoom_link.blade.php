<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Zoom Link</title>
</head>

<body>
    <p>Halo,</p>
    <p>Terima kasih atas donasi Anda. Berikut ini adalah link Zoom kelas tutorial Anda:</p>
    <p>Rayon ID: {{ $rayonsId ?? '-' }}</p>
    <p>Schedule ID: {{ $komSchedulesId ?? '-' }}</p>
    <p>
        <strong>
            <a href="{{ $link }}">{{ $link }}</a>
        </strong>
    </p>

    <p>Silakan klik link di atas saat waktu kelas dimulai.</p>
    <br>
    <p>Salam,</p>
    <p><strong>Tim KOM Digital</strong></p>
</body>

</html>
