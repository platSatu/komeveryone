@extends('layouts.kom')

@section('content')
    <div class="container mt-4">
        <h2>Memproses Pembayaran</h2>
        <p>Mohon tunggu, Anda akan diarahkan ke halaman pembayaran DOKU...</p>

        <script src="https://sandbox.doku.com/jokul-checkout-js/v1/jokul-checkout-1.0.0.js"></script>

        <script>
            (function() {
                const paymentUrl = @json($paymentUrl);
                console.log("Payment URL:", paymentUrl);

                if (paymentUrl) {
                    // Load DOKU checkout page
                    loadJokulCheckout(paymentUrl);
                } else {
                    alert("Payment URL tidak tersedia.");
                    // Jika URL tidak ada, arahkan user kembali ke halaman sebelumnya atau home
                    window.location.href = "{{ route('home') }}"; // Ganti sesuai route yang kamu inginkan
                }
            })();
        </script>
    </div>
@endsection
