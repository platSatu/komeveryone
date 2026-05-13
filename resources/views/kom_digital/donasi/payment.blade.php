@extends('layouts.kom')
@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Konfirmasi Persembahan</h4>
            </div>
            <div class="card-body">

                <div class="mb-3 border-bottom pb-2">
                    <strong>Jumlah Donasi:</strong><br>
                    Rp 40.000
                </div>

                <div class="mb-3 border-bottom pb-2">
                    <strong>Fee Admin Payment Gateway & admin :</strong><br>
                    Rp 5.000
                </div>

                <div class="mb-4">
                    <strong>Total Pembayaran:</strong><br>
                    <span class="h5">Rp 45.000</span>
                </div>

                <button id="pay-button" class="btn btn-primary btn-lg w-100">Persembahan Sekarang</button>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://app.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    <script type="text/javascript">
        document.getElementById('pay-button').addEventListener('click', function() {
            console.log("Pay button clicked");
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    console.log('Success', result);
                    alert(
                        'Terima kasih atas donasi Anda. Tuhan Yesus memberkati!\nCek email Anda. Link Zoom telah dikirimkan.');
                    window.location.href = '{{ url('kom-digital/dashboard') }}';
                },
                onPending: function(result) {
                    console.log('Pending', result);
                },
                onError: function(result) {
                    console.log('Error', result);
                },
                onClose: function() {
                    alert('Anda belum menyelesaikan pembayaran.');
                }
            });
        });
    </script>
@endsection
