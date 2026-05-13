@extends('layouts.KomDigital')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-4">Persembahan</h3>
                <p>Jumlah: <strong>Rp 40.000</strong></p>
                <p>Fee Admin: <strong>Rp 5.000</strong></p>
                <p><strong>Total: Rp 45.000</strong></p>

                <form id="donationForm" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg">Persembahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
document.getElementById('donationForm').addEventListener('submit', function(e) {
    e.preventDefault();

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Yakin akan memberikan persembahan Rp 45.000?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Donasi',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
           fetch("{{ route('kom.persembahan.token') }}", {  // Gunakan route Laravel
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.snap_token) {
                    snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil',
                                text: 'Terima kasih atas persembahan Anda!',
                            }).then(() => {
                                window.location.href = "{{ route('kom.dashboard_kom') }}";
                            });
                        },
                        onPending: function(result) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Pembayaran Pending',
                                text: 'Pembayaran Anda sedang diproses.',
                            }).then(() => {
                                window.location.href = "{{ route('kom.kelas_tutorial.index') }}";
                            });
                        },
                        onError: function(result) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran Gagal',
                                text: 'Terjadi kesalahan saat proses pembayaran.',
                            });
                        },
                        onClose: function() {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Pembayaran Dibatalkan',
                                text: 'Anda menutup popup pembayaran.',
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.error || 'Gagal mendapatkan token pembayaran.',
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan: ' + error.message,
                });
            });
        }
    });
});
</script>
@endsection
