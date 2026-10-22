@extends('layouts.KomDigital')

@section('content')
{{-- kom_v02: tampilan modern & minimalis, seragam dengan halaman persembahan --}}
@include('a_kom_digital_pusat.partials._kom_ui')
<div class="kom-q-wrap">
  <div class="kom-ui-card">
    @include('a_kom_digital_pusat.partials._langkah_tutorial', ['aktif' => 2])

    <div class="kom-ui-head">
      <div class="kom-ui-icon"><i class="fas fa-comments"></i></div>
      <h3>{{ __('Form Pertanyaan') }}</h3>
      <p>{{ __('Tulis 2 pertanyaan yang ingin Anda tanyakan kepada pengajar di kelas tutorial.') }}</p>
    </div>

    {{-- Alert Messages --}}
    <div id="alertContainer">
        @if(session('success'))
            <div class="alert kom-ui-alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert kom-ui-alert alert-danger">{{ session('error') }}</div>
        @endif
    </div>

    <form id="formPertanyaan" action="{{ route('kom.kelas_tutorial.pertanyaan-kom-100.store') }}" method="POST" novalidate>
        @csrf
        <input type="hidden" name="class_id" value="{{ $class_id }}">
        <input type="hidden" name="modul_id" value="{{ $modul_id }}">
        <input type="hidden" name="rayon_id" value="{{ $rayon_id }}">
        <input type="hidden" name="kelas_tutorial_id" value="{{ $class_tutorial_id }}">
        <input type="hidden" name="pengajar_id" value="{{ $pengajar_id }}">
        <input type="hidden" name="category_diklat" value="{{ $category_diklat ?? '' }}">

        {{-- Pertanyaan 1 --}}
        <div class="kom-q-item">
            <label for="pertanyaan1" class="kom-q-label">
                <span class="kom-q-no">1</span>{{ __('Pertanyaan 1') }} <span class="text-danger">*</span>
            </label>
            <textarea id="pertanyaan1" name="pertanyaan[]" class="form-control kom-ui-input" rows="4" maxlength="1000" placeholder="{{ __('Tulis pertanyaan Anda di sini...') }}" required></textarea>
            <div class="invalid-feedback">
                {{ __('Pertanyaan pertama wajib diisi.') }}
            </div>
            <div class="kom-q-count"><span data-hitung="pertanyaan1">0</span>/1000</div>
        </div>

        {{-- Pertanyaan 2 --}}
        <div class="kom-q-item">
            <label for="pertanyaan2" class="kom-q-label">
                <span class="kom-q-no">2</span>{{ __('Pertanyaan 2') }} <span class="text-danger">*</span>
            </label>
            <textarea id="pertanyaan2" name="pertanyaan[]" class="form-control kom-ui-input" rows="4" maxlength="1000" placeholder="{{ __('Tulis pertanyaan tambahan di sini...') }}" required></textarea>
            <div class="invalid-feedback">
                {{ __('Pertanyaan kedua wajib diisi.') }}
            </div>
            <div class="kom-q-count"><span data-hitung="pertanyaan2">0</span>/1000</div>
        </div>

        <button type="submit" class="kom-btn-primary w-100" id="submitBtn">
            <i class="fas fa-paper-plane me-2"></i> {{ __('Kirim Pertanyaan') }}
        </button>
    </form>

    {{-- kom_v02: tidak jadi → batalkan & pilih jadwal lain --}}
    <form method="POST" action="{{ route('kom.persembahan.batal') }}" class="text-center mt-3"
        onsubmit='return confirm(@json(__("Batalkan pendaftaran kelas tutorial ini dan pilih jadwal lain?")))'>
        @csrf
        <button type="submit" class="kom-link-cancel">
            <i class="fas fa-undo mr-1"></i>{{ __('Batalkan & pilih jadwal lain') }}
        </button>
    </form>
  </div>
</div>
<style>
  .kom-q-wrap { max-width: 640px; margin: 0 auto; padding: 24px 12px 48px; }
  .kom-q-item { margin-bottom: 20px; }
  .kom-q-label { display:flex; align-items:center; gap:10px; font-weight:600; color:#1c2b43; margin-bottom:8px; }
  .kom-q-no {
    display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px;
    background:#eaf2ff; color:#2f6fed; font-size:.8rem; font-weight:700;
  }
  .kom-q-item textarea.kom-ui-input { resize: vertical; min-height: 120px; line-height:1.5; }
  .kom-q-count { text-align:right; font-size:.72rem; color:#9aa5b5; margin-top:4px; }
</style>
<script>
  // kom_v02: penghitung karakter pertanyaan
  document.querySelectorAll('[data-hitung]').forEach(function (el) {
    const ta = document.getElementById(el.dataset.hitung);
    if (!ta) return;
    const upd = () => { el.textContent = ta.value.length; };
    ta.addEventListener('input', upd); upd();
  });
</script>

{{-- Font Awesome CDN (jika belum ada di layout) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- kom_v02: teks JavaScript mengikuti bahasa peserta --}}
@php
    $komTeksJs = [
        'Yakin mau kirim pertanyaan ini?' => __('Yakin mau kirim pertanyaan ini?'),
        'Ya, kirim' => __('Ya, kirim'),
        'Batal' => __('Batal'),
        'Mengirim...' => __('Mengirim...'),
        'Gagal' => __('Gagal'),
        'Terjadi kesalahan server (kode: :kode). Coba refresh halaman.' => __('Terjadi kesalahan server (kode: :kode). Coba refresh halaman.'),
        'Sesi Anda telah berakhir. Silakan login ulang.' => __('Sesi Anda telah berakhir. Silakan login ulang.'),
        'Respons dari server tidak sesuai format. Coba refresh halaman.' => __('Respons dari server tidak sesuai format. Coba refresh halaman.'),
        'Error' => __('Error'),
        'Pertanyaan berhasil dikirim' => __('Pertanyaan berhasil dikirim'),
        'Sekarang saatnya memberi persembahan.' => __('Sekarang saatnya memberi persembahan.'),
        'Lanjut ke persembahan' => __('Lanjut ke persembahan'),
        'Terjadi kesalahan saat mengirim.' => __('Terjadi kesalahan saat mengirim.'),
        'Respons server rusak atau tidak bisa dibaca. Coba login ulang atau hubungi admin.' => __('Respons server rusak atau tidak bisa dibaca. Coba login ulang atau hubungi admin.'),
        'Terjadi kesalahan jaringan. Periksa koneksi internet Anda.' => __('Terjadi kesalahan jaringan. Periksa koneksi internet Anda.'),
        'Kirim Pertanyaan' => __('Kirim Pertanyaan'),
    ];
@endphp
@include('a_kom_digital_pusat.partials._teks_js', ['teks' => $komTeksJs])
<script>
(() => {
    const form = document.getElementById('formPertanyaan');
    const submitBtn = document.getElementById('submitBtn');

    if (!form || !submitBtn) {
        console.warn('⚠️ Form atau tombol submit tidak ditemukan di halaman.');
        return;
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            console.warn('🔴 Form tidak valid. Harap periksa input.');
            return;
        }

        Swal.fire({
            title: komT('Yakin mau kirim pertanyaan ini?'),
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: komT('Ya, kirim'),
            cancelButtonText: komT('Batal'),
        }).then((result) => {
            if (result.isConfirmed) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> ${komT('Mengirim...')}`;

                const formData = new FormData(form);
                const requestHeaders = {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                };

                console.log('📤 Mengirim data ke:', form.action);
                console.log('📦 Form Data:', Object.fromEntries(formData.entries()));
                console.log('🧾 Headers:', requestHeaders);

                fetch(form.action, {
                    method: 'POST',
                    credentials: 'same-origin', // agar session Laravel tetap valid
                    headers: requestHeaders,
                    body: formData,
                })
                .then(async res => {
                    console.log('📬 Response Status:', res.status);
                    const contentType = res.headers.get('Content-Type') || '';
                    const responseText = await res.text();
                    console.log('📄 Raw Response Text (snippet):', responseText.slice(0, 300)); // hanya tampilkan sebagian untuk debugging

                    if (!res.ok) {
                        console.error(`❌ Server response error (${res.status})`);
                        // kom_v02: tampilkan pesan dari server jika ada (mis. waktu habis / sudah terkirim)
                        let pesanServer = null, tujuan = null;
                        try { const j = JSON.parse(responseText); pesanServer = j.message || null; tujuan = j.redirect || j.redirect_url || null; } catch (e) {}
                        Swal.fire(komT('Gagal'), pesanServer || komT('Terjadi kesalahan server (kode: :kode). Coba refresh halaman.', {kode: res.status}), 'error')
                            .then(() => { if (tujuan) window.location.href = tujuan; });
                        resetButton();
                        return;
                    }

                    if (!contentType.includes('application/json')) {
                        console.warn('⚠️ Response bukan JSON. Mungkin redirect atau session expired.');
                        // Perbaikan: Cek apakah response mengindikasikan login redirect (umum di Laravel)
                        const isLoginRedirect = responseText.toLowerCase().includes('login') || res.status === 302;
                        const errorMessage = isLoginRedirect 
                            ? komT('Sesi Anda telah berakhir. Silakan login ulang.')
                            : komT('Respons dari server tidak sesuai format. Coba refresh halaman.');
                        Swal.fire(komT('Error'), errorMessage, 'error');
                        resetButton();
                        return;
                    }

                    try {
                        const data = JSON.parse(responseText);
                        console.log('✅ Parsed JSON:', data);

                        if (data.success) {
                            Swal.fire({
                                title: komT('Pertanyaan berhasil dikirim'),
                                text: komT('Sekarang saatnya memberi persembahan.'),
                                icon: 'success',
                                confirmButtonText: komT('Lanjut ke persembahan'),
                                allowOutsideClick: false,
                            }).then(() => {
                                window.location.href = "{{ route('kom.form.persembahan') }}";
                            });
                        } else {
                            Swal.fire(komT('Gagal'), data.message || komT('Terjadi kesalahan saat mengirim.'), 'error');
                            resetButton();
                        }
                    } catch (err) {
                        console.error('❌ Gagal parse JSON:', err);
                        Swal.fire(komT('Error'), komT('Respons server rusak atau tidak bisa dibaca. Coba login ulang atau hubungi admin.'), 'error');
                        resetButton();
                    }
                })
                .catch(err => {
                    console.error('🚨 Network/Server Error:', err);
                    Swal.fire(komT('Gagal'), komT('Terjadi kesalahan jaringan. Periksa koneksi internet Anda.'), 'error');
                    resetButton();
                });
            }
        });

        function resetButton() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<i class="fas fa-paper-plane me-2"></i> ${komT('Kirim Pertanyaan')}`;
        }
    });
})();
</script>



@endsection