<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Terima Kasih</title>
</head>
<body>
    <p>Shalom {{ $userName }},</p>

    <p>Terima kasih atas persembahan Anda sebesar <strong>{{ $donasi->formatted_amount }}</strong>.</p>
    <p>Metode pembayaran: {{ ucfirst($donasi->payment_type) }}</p>
    <p>Waktu transaksi: {{ \Carbon\Carbon::parse($donasi->transaction_time)->format('d M Y H:i') }}</p>

    <p>Persembahan Anda sudah kami terima dengan baik.</p>

    @if(!empty($joinUrl))
        <p>Untuk mengikuti kelas/tutorial, silakan klik link berikut:</p>
        <p><a href="{{ $joinUrl }}" target="_blank">{{ $joinUrl }}</a></p>
    @endif

    <p>Tuhan Yesus memberkati.</p>
    <p><strong>Tim KOM Digital</strong></p>
</body>
</html>
