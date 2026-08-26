<h2>Reset Password</h2>
<p>Halo,</p>
<p>Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.</p>

<p>
    <a href="{{ url('/app/reset-password/' . $token . '?email=' . urlencode($email)) }}"
        style="padding: 10px 20px; background-color: #dc3545; color: #fff; text-decoration: none; border-radius: 5px;">
        Reset Password
    </a>

</p>

<p>Link ini hanya berlaku dalam beberapa menit.</p>

<p>Jika Anda tidak meminta reset password, abaikan email ini.</p>

<p>Salam,<br />Tim KOM</p>
