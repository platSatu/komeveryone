<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email</title>
</head>
<body>
    <h2>Halo, {{ $user->username }}</h2>
    <p>Terima kasih telah mendaftar. Klik tombol di bawah ini untuk memverifikasi email Anda:</p>
    
    <p>
        <a href="{{ route('kom.users.verify', ['token' => $user->email_verification_token]) }}" 
           style="background-color: #3490dc; color: white; padding: 10px 20px; text-decoration: none;">
            Verifikasi Email
        </a>
    </p>

    <p>Jika Anda tidak merasa mendaftar, abaikan email ini.</p>
</body>
</html>
