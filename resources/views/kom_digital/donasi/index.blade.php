@extends('layouts.kom')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Halaman Donasi</h2>

    {{-- Pesan sukses atau error --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Tombol donasi --}}
    <button id="checkout-button" class="btn btn-success btn-lg">
        💳 Donasi Sekarang
    </button>

    {{-- Script Doku Checkout --}}
    <script src="https://sandbox.doku.com/jokul-checkout-js/v1/jokul-checkout-1.0.0.js"></script>
    <script>
        document.getElementById('checkout-button').addEventListener('click', function () {
            fetch("{{ route('kom.donasi.snap.token') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (data.paymentUrl) {
                    Doku.pay(data.paymentUrl);
                } else if (data.error) {
                    alert('Gagal mendapatkan URL pembayaran: ' + data.error);
                    console.error('DOKU Response Error:', data.doku_response || data);
                } else {
                    alert('Gagal mendapatkan URL pembayaran tanpa pesan error.');
                    console.error('Unknown response:', data);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Terjadi kesalahan saat menghubungi server.');
            });
        });
    </script>
</div>
@endsection
