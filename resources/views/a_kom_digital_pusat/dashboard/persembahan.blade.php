{{-- @extends('layouts.KomDigital')

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
@endsection --}}
@extends('layouts.KomDigital')

@section('content')
<div class="row">
  <div class="col-md-8 offset-md-2">
    <div class="card">
      <div class="card-body text-center">
        <h3 class="mb-4">Persembahan</h3>

        <p>Jumlah: <strong id="amountText">Rp -</strong></p>
        <p>Fee Admin: <strong id="feeText">Rp -</strong></p>
        <p><strong>Total: <span id="totalText">Rp -</span></strong></p>

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
const formatRupiah = (num) =>
  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(num);

// 🔹 Fungsi untuk ambil data harga saat halaman dimuat
function loadDonationAmount() {
  fetch("{{ route('kom.persembahan.token') }}", {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({ preview: true }) // hanya preview harga
  })
    .then(res => res.json())
    .then(data => {
      if (data.amount) {
        document.getElementById('amountText').innerText = formatRupiah(data.amount);
        document.getElementById('feeText').innerText = formatRupiah(data.fee);
        document.getElementById('totalText').innerText = formatRupiah(data.total);
      }
    })
    .catch(err => console.error('Gagal memuat data harga:', err));
}

// Jalankan saat halaman dimuat
document.addEventListener('DOMContentLoaded', loadDonationAmount);

// 🔹 Event klik tombol donasi
document.getElementById('donationForm').addEventListener('submit', function (e) {
  e.preventDefault();

  fetch("{{ route('kom.persembahan.token') }}", {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({}) // request normal (bukan preview)
  })
    .then(res => res.json())
    .then(data => {
      if (!data.snap_token) {
        Swal.fire('Gagal', data.error || 'Tidak bisa mendapatkan token.', 'error');
        return;
      }

      Swal.fire({
        title: 'Konfirmasi Persembahan',
        html: `
          Jumlah: <b>${formatRupiah(data.amount)}</b><br>
          Fee: <b>${formatRupiah(data.fee)}</b><br>
          Total: <b>${formatRupiah(data.total)}</b>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Lanjutkan Pembayaran',
        cancelButtonText: 'Batal',
      }).then((result) => {
        if (result.isConfirmed) {
          snap.pay(data.snap_token, {
            onSuccess: () => Swal.fire('Berhasil', 'Terima kasih atas persembahan Anda!', 'success')
              .then(() => window.location.href = "{{ route('kom.dashboard_kom') }}"),
            onPending: () => Swal.fire('Info', 'Pembayaran sedang diproses.', 'info')
              .then(() => window.location.href = "{{ route('kom.kelas_tutorial.index') }}"),
            onError: () => Swal.fire('Error', 'Terjadi kesalahan saat pembayaran.', 'error'),
            onClose: () => Swal.fire('Dibatalkan', 'Anda menutup popup pembayaran.', 'warning'),
          });
        }
      });
    })
    .catch(err => Swal.fire('Error', 'Terjadi kesalahan: ' + err.message, 'error'));
});
</script>
@endsection

