@component('mail::message')
# Halo, {{ $user->name }}

Kelas tutorial **{{ $kelas->topic }}** akan dimulai pada **{{ \Carbon\Carbon::parse($kelas->start_time)->format('d M Y H:i') }}**.

Jangan lupa hadir tepat waktu ya!

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
