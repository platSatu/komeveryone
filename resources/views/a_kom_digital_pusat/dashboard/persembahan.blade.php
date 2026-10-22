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
{{-- kom_v02: tampilan persembahan modern & minimalis --}}
<div class="kom-pay-wrap">
  <div class="kom-pay-card">

    {{-- Langkah rangkaian kelas tutorial --}}
    @include('a_kom_digital_pusat.partials._langkah_tutorial', ['aktif' => 3])

    <div class="kom-pay-head">
      <div class="kom-pay-icon"><i class="fas fa-hand-holding-heart"></i></div>
      <h3>{{ __('Persembahan') }}</h3>
      <p>{{ __('Langkah terakhir sebelum Anda mendapatkan link kelas tutorial.') }}</p>
    </div>

    {{-- kom_v02: pesan & batas waktu memberikan persembahan --}}
    @if (session('error'))
      <div class="alert alert-danger text-left">{{ session('error') }}</div>
    @endif
    @if (!empty($batasWaktu))
      <div class="kom-checkout-timer" id="checkoutTimer"
           data-batas="{{ $batasWaktu->toIso8601String() }}"
           data-total="{{ (int) $menitBatas * 60 }}">
        <div class="kom-timer-row">
          <span><i class="far fa-clock mr-1"></i>{{ __('Selesaikan persembahan dalam') }}</span>
          <strong id="sisaWaktu">--:--</strong>
        </div>
        <div class="kom-timer-bar"><div id="sisaBar"></div></div>
        <div class="kom-timer-note">
          {{ __('Jika melewati :menit menit, pendaftaran kelas tutorial dibatalkan dan Anda bisa memilih jadwal lagi.', ['menit' => $menitBatas]) }}
        </div>
      </div>
    @endif

    <div class="kom-pay-summary">
      <div class="kom-pay-line"><span>{{ __('Jumlah') }}</span><strong id="amountText">Rp -</strong></div>
      <div class="kom-pay-line"><span>{{ __('Biaya Admin') }}</span><strong id="feeText">Rp -</strong></div>
      <div class="kom-pay-line total"><span>{{ __('Total') }}</span><strong id="totalText">Rp -</strong></div>
    </div>

    <form id="donationForm" method="POST">
      @csrf
      <button type="submit" class="kom-pay-btn">
        <i class="fas fa-lock mr-2"></i>{{ __('Berikan Persembahan') }}
      </button>
    </form>
    <div class="kom-pay-secure"><i class="fas fa-shield-alt mr-1"></i>{{ __('Pembayaran aman diproses oleh :nama.', ['nama' => $namaGateway ?? 'Midtrans']) }}</div>

    {{-- kom_v02: tidak jadi → batalkan & pilih jadwal lain --}}
    <form id="batalForm" method="POST" action="{{ route('kom.persembahan.batal') }}" class="text-center">
      @csrf
      <button type="submit" class="kom-pay-cancel">
        <i class="fas fa-undo mr-1"></i>{{ __('Batalkan & pilih jadwal lain') }}
      </button>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- kom_v02: script pembayaran mengikuti gateway aktif (menu Admin Kom › Payment Gateway) --}}
@if (($gateway ?? 'midtrans') === 'bizbos' && !empty($bizbosBaseUrl))
<script src="{{ $bizbosBaseUrl }}/js/bizbos-pay.js"></script>
@else
{{-- snap.js sandbox / production mengikuti MIDTRANS_IS_PRODUCTION --}}
<script src="{{ config('midtrans.is_production', true) ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif
{{-- kom_v02: teks JavaScript mengikuti bahasa peserta --}}
@php
    $komTeksJs = [
        'Memeriksa pembayaran...' => __('Memeriksa pembayaran...'),
        'Pembayaran belum kami terima. Jika Anda sudah membayar, tunggu sebentar lalu klik tombol cek lagi.' => __('Pembayaran belum kami terima. Jika Anda sudah membayar, tunggu sebentar lalu klik tombol cek lagi.'),
        'Cek lagi' => __('Cek lagi'),
        'Tutup' => __('Tutup'),
        'Belum dibayar' => __('Belum dibayar'),
        'Terjadi kesalahan jaringan. Silakan coba lagi.' => __('Terjadi kesalahan jaringan. Silakan coba lagi.'),
        'Gagal' => __('Gagal'),
        'Tidak bisa mendapatkan token pembayaran.' => __('Tidak bisa mendapatkan token pembayaran.'),
        'Konfirmasi Persembahan' => __('Konfirmasi Persembahan'),
        'Jumlah' => __('Jumlah'),
        'Biaya Admin' => __('Biaya Admin'),
        'Total' => __('Total'),
        'Lanjutkan Pembayaran' => __('Lanjutkan Pembayaran'),
        'Batal' => __('Batal'),
        'Berhasil' => __('Berhasil'),
        'Terima kasih atas persembahan Anda!' => __('Terima kasih atas persembahan Anda!'),
        'Info' => __('Info'),
        'Pembayaran sedang diproses.' => __('Pembayaran sedang diproses.'),
        'Error' => __('Error'),
        'Terjadi kesalahan saat pembayaran.' => __('Terjadi kesalahan saat pembayaran.'),
        'Dibatalkan' => __('Dibatalkan'),
        'Anda menutup jendela pembayaran.' => __('Anda menutup jendela pembayaran.'),
        'Terjadi kesalahan:' => __('Terjadi kesalahan:'),
        'Waktu habis' => __('Waktu habis'),
        'Waktu memberikan persembahan sudah habis. Silakan pilih jadwal kelas tutorial lagi.' => __('Waktu memberikan persembahan sudah habis. Silakan pilih jadwal kelas tutorial lagi.'),
        'Batalkan pendaftaran?' => __('Batalkan pendaftaran?'),
        'Pertanyaan dan pendaftaran kelas tutorial ini akan dihapus, lalu Anda bisa memilih jadwal lagi.' => __('Pertanyaan dan pendaftaran kelas tutorial ini akan dihapus, lalu Anda bisa memilih jadwal lagi.'),
        'Ya, batalkan' => __('Ya, batalkan'),
        'Kembali' => __('Kembali'),
    ];
@endphp
@include('a_kom_digital_pusat.partials._teks_js', ['teks' => $komTeksJs])
<script>
// kom_v02: hitung mundur batas waktu persembahan
(function () {
  const el = document.getElementById('checkoutTimer');
  if (!el) return;
  const batas = new Date(el.dataset.batas).getTime();
  const sisa = document.getElementById('sisaWaktu');
  const bar = document.getElementById('sisaBar');
  const total = parseInt(el.dataset.total || '0', 10);
  const tick = () => {
    const detik = Math.max(0, Math.floor((batas - Date.now()) / 1000));
    sisa.textContent = String(Math.floor(detik / 60)).padStart(2, '0') + ':' + String(detik % 60).padStart(2, '0');
    if (bar && total > 0) bar.style.width = Math.min(100, (detik / total) * 100) + '%';
    if (detik <= 60) el.classList.add('hampir-habis');
    if (detik === 0) {
      clearInterval(timer);
      Swal.fire({
        icon: 'info',
        title: komT('Waktu habis'),
        text: komT('Waktu memberikan persembahan sudah habis. Silakan pilih jadwal kelas tutorial lagi.'),
        allowOutsideClick: false,
      }).then(() => { window.location.href = "{{ route('kom.kelas_tutorial.index') }}"; });
    }
  };
  const timer = setInterval(tick, 1000);
  tick();
})();

// kom_v02: konfirmasi batal
document.getElementById('batalForm')?.addEventListener('submit', function (e) {
  e.preventDefault();
  const form = this;
  Swal.fire({
    icon: 'question',
    title: komT('Batalkan pendaftaran?'),
    text: komT('Pertanyaan dan pendaftaran kelas tutorial ini akan dihapus, lalu Anda bisa memilih jadwal lagi.'),
    showCancelButton: true,
    confirmButtonText: komT('Ya, batalkan'),
    cancelButtonText: komT('Kembali'),
    confirmButtonColor: '#dc3545',
  }).then((r) => { if (r.isConfirmed) form.submit(); });
});

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
      // kom_v02: BB (Bizbos) — sudah lunas sebelumnya
      if (data.status === 'paid') {
        Swal.fire(komT('Berhasil'), data.message || komT('Terima kasih atas persembahan Anda!'), 'success')
          .then(() => window.location.href = data.redirect || "{{ route('kom.peserta.kelas_tutorial') }}");
        return;
      }

      if (!data.snap_token && !(data.gateway === 'bizbos' && data.checkout_url)) {
        Swal.fire(komT('Gagal'), data.error || data.message || komT('Tidak bisa mendapatkan token pembayaran.'), 'error')
          .then(() => { if (data.redirect) window.location.href = data.redirect; }); // kom_v02: waktu habis
        return;
      }

      Swal.fire({
        title: komT('Konfirmasi Persembahan'),
        html: `
          ${komT('Jumlah')}: <b>${formatRupiah(data.amount)}</b><br>
          ${komT('Biaya Admin')}: <b>${formatRupiah(data.fee)}</b><br>
          ${komT('Total')}: <b>${formatRupiah(data.total)}</b>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: komT('Lanjutkan Pembayaran'),
        cancelButtonText: komT('Batal'),
      }).then((result) => {
        if (result.isConfirmed && data.gateway === 'bizbos') {
          // kom_v02: popup BB (Bizbos). Status lunas tetap dicek ke server, bukan dari popup.
          if (window.BizbosPay) {
            window.BizbosPay.open(data.checkout_url, {
              onSuccess: () => cekPembayaranBizbos(true),
              onClose: () => cekPembayaranBizbos(false),
            });
          } else {
            window.location.href = data.checkout_url;
          }
          return;
        }
        if (result.isConfirmed) {
          snap.pay(data.snap_token, {
            onSuccess: () => Swal.fire(komT('Berhasil'), komT('Terima kasih atas persembahan Anda!'), 'success')
              .then(() => window.location.href = "{{ route('kom.dashboard_kom') }}"),
            onPending: () => Swal.fire(komT('Info'), komT('Pembayaran sedang diproses.'), 'info')
              .then(() => window.location.href = "{{ route('kom.kelas_tutorial.index') }}"),
            onError: () => Swal.fire(komT('Error'), komT('Terjadi kesalahan saat pembayaran.'), 'error'),
            onClose: () => Swal.fire(komT('Dibatalkan'), komT('Anda menutup jendela pembayaran.'), 'warning'),
          });
        }
      });
    })
    .catch(err => Swal.fire(komT('Error'), komT('Terjadi kesalahan:') + ' ' + err.message, 'error'));
});

// kom_v02: cek status pembayaran BB (Bizbos) ke server
function cekPembayaranBizbos(dariPopupBerhasil) {
  Swal.fire({ title: komT('Memeriksa pembayaran...'), allowOutsideClick: false, didOpen: () => Swal.showLoading() });

  fetch("{{ route('kom.persembahan.cek') }}", {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'paid') {
        Swal.fire(komT('Berhasil'), komT('Terima kasih atas persembahan Anda!'), 'success')
          .then(() => window.location.href = data.redirect || "{{ route('kom.peserta.kelas_tutorial') }}");
        return;
      }
      if (data.redirect) { window.location.href = data.redirect; return; }

      Swal.fire({
        icon: dariPopupBerhasil ? 'info' : 'warning',
        title: komT('Belum dibayar'),
        text: komT('Pembayaran belum kami terima. Jika Anda sudah membayar, tunggu sebentar lalu klik tombol cek lagi.'),
        showCancelButton: true,
        confirmButtonText: komT('Cek lagi'),
        cancelButtonText: komT('Tutup'),
      }).then(r => { if (r.isConfirmed) cekPembayaranBizbos(dariPopupBerhasil); });
    })
    .catch(() => Swal.fire(komT('Error'), komT('Terjadi kesalahan jaringan. Silakan coba lagi.'), 'error'));
}

// kembali dari halaman bayar Bizbos (mode redirect) → langsung cek status
if (new URLSearchParams(window.location.search).get('cek') === '1') {
  document.addEventListener('DOMContentLoaded', () => cekPembayaranBizbos(true));
}
</script>
<style>
  /* kom_v02: persembahan — modern & minimalis */
  .kom-pay-wrap { display:flex; justify-content:center; padding: 24px 12px 48px; }
  .kom-pay-card {
    width:100%; max-width: 520px; padding: 32px 32px 24px; border-radius: 24px;
    background: linear-gradient(180deg, #f2f7ff 0%, #ffffff 180px);
    border: 1px solid #e6edf7; box-shadow: 0 18px 40px -22px rgba(30, 60, 120, .35);
  }

  .kom-pay-head { text-align:center; margin-bottom: 22px; }
  .kom-pay-icon {
    width:60px; height:60px; margin:0 auto 12px; border-radius:18px; display:flex; align-items:center; justify-content:center;
    background: linear-gradient(135deg, #2f6fed, #6a5cff); color:#fff; font-size:1.5rem;
    box-shadow: 0 10px 22px -10px rgba(47,111,237,.7);
  }
  .kom-pay-head h3 { margin:0 0 4px; font-weight:700; color:#1c2b43; }
  .kom-pay-head p { margin:0; color:#7b879a; font-size:.9rem; }

  .kom-checkout-timer { padding:14px 16px; border-radius:16px; background:#fff7e6; color:#8a5a00; margin-bottom:20px; transition: background .3s, color .3s; }
  .kom-timer-row { display:flex; align-items:center; justify-content:space-between; gap:12px; font-size:.9rem; }
  .kom-timer-row strong { font-size:1.35rem; font-variant-numeric: tabular-nums; letter-spacing:1px; }
  .kom-timer-bar { height:5px; border-radius:99px; background:rgba(138,90,0,.12); margin:10px 0 8px; overflow:hidden; }
  .kom-timer-bar > div { height:100%; width:100%; border-radius:99px; background:#f0a500; transition: width 1s linear, background .3s; }
  .kom-timer-note { font-size:.75rem; opacity:.75; }
  .kom-checkout-timer.hampir-habis { background:#fdecec; color:#b42318; }
  .kom-checkout-timer.hampir-habis .kom-timer-bar > div { background:#e5484d; }

  .kom-pay-summary { background:#f7f9fc; border-radius:16px; padding:6px 18px; margin-bottom:20px; }
  .kom-pay-line { display:flex; justify-content:space-between; align-items:center; padding:12px 0; color:#5d6a7e; font-size:.95rem; }
  .kom-pay-line + .kom-pay-line { border-top:1px dashed #e1e7f0; }
  .kom-pay-line strong { color:#1c2b43; font-weight:600; }
  .kom-pay-line.total { border-top:1px solid #dfe6f0; }
  .kom-pay-line.total span { color:#1c2b43; font-weight:600; }
  .kom-pay-line.total strong { font-size:1.25rem; color:#2f6fed; font-weight:700; }

  .kom-pay-btn {
    width:100%; border:0; border-radius:14px; padding:14px 18px; font-size:1rem; font-weight:600; color:#fff;
    background: linear-gradient(135deg, #22b07d, #12996a); box-shadow: 0 12px 24px -12px rgba(18,153,106,.8);
    transition: transform .15s, box-shadow .15s, filter .15s;
  }
  .kom-pay-btn:hover { transform: translateY(-1px); filter: brightness(1.05); box-shadow: 0 16px 28px -12px rgba(18,153,106,.9); }
  .kom-pay-btn:active { transform: translateY(0); }
  .kom-pay-secure { text-align:center; font-size:.75rem; color:#9aa5b5; margin:10px 0 6px; }
  .kom-pay-cancel { background:none; border:0; color:#8a96a8; font-size:.85rem; padding:6px 10px; border-radius:10px; transition: color .15s, background .15s; }
  .kom-pay-cancel:hover { color:#b42318; background:#fdecec; }

  @media (max-width: 576px) {
    .kom-pay-card { padding:24px 18px 18px; border-radius:20px; }
    .kom-timer-row { font-size:.82rem; }
  }
</style>
@endsection

