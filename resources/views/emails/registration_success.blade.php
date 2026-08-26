<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Berhasil</title>
</head>
<body>
    <h1>Terima kasih, {{ $event->name }}!</h1>
    <p>Registrasi Anda telah berhasil. Berikut adalah barcode registrasi Anda:</p>
    <p>Nama: {{ $event->name }}</p>
    <p>Email: {{ $event->email }}</p>
    <p>Handphone: {{ $event->handphone }}</p>
    <p>Kode Registrasi: {{ $event->code_registrasi }}</p>

    <p>Berikut adalah barcode untuk registrasi Anda:</p>
    <img src="cid:barcode-{{ $event->code_registrasi }}.png" alt="Barcode" />
</body>
</html>
