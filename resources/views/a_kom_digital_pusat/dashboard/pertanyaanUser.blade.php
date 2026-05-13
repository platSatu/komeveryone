@extends('layouts.KomDigital')

@section('content')
<div class="container py-4" style="max-width: 700px;">
    <h3 class="mb-4 text-center fw-bold">Form Pertanyaan</h3>

    {{-- Alert Messages --}}
    <div id="alertContainer">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
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
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <label for="pertanyaan1" class="form-label fw-semibold">Pertanyaan 1 <span class="text-danger">*</span></label>
                <textarea id="pertanyaan1" name="pertanyaan[]" class="form-control form-control-lg" rows="4" placeholder="Tulis pertanyaan Anda di sini..." required></textarea>
                <div class="invalid-feedback">
                    Pertanyaan pertama wajib diisi.
                </div>
            </div>
        </div>

        {{-- Pertanyaan 2 --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <label for="pertanyaan2" class="form-label fw-semibold">Pertanyaan 2 <span class="text-danger">*</span></label>
                <textarea id="pertanyaan2" name="pertanyaan[]" class="form-control form-control-lg" rows="4" placeholder="Tulis pertanyaan tambahan di sini..." required></textarea>
                <div class="invalid-feedback">
                    Pertanyaan kedua wajib diisi.
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold" id="submitBtn">
            <i class="fas fa-paper-plane me-2"></i> Kirim Pertanyaan
        </button>
    </form>
</div>

{{-- Font Awesome CDN (jika belum ada di layout) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            title: 'Yakin mau kirim pertanyaan ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, kirim',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Mengirim...`;

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
                        Swal.fire('Gagal', `Terjadi kesalahan server (kode: ${res.status}). Coba refresh halaman.`, 'error');
                        resetButton();
                        return;
                    }

                    if (!contentType.includes('application/json')) {
                        console.warn('⚠️ Response bukan JSON. Mungkin redirect atau session expired.');
                        // Perbaikan: Cek apakah response mengindikasikan login redirect (umum di Laravel)
                        const isLoginRedirect = responseText.toLowerCase().includes('login') || res.status === 302;
                        const errorMessage = isLoginRedirect 
                            ? 'Session Anda telah kedaluwarsa. Silakan login ulang.' 
                            : 'Respons dari server tidak sesuai format. Coba refresh halaman.';
                        Swal.fire('Error', errorMessage, 'error');
                        resetButton();
                        return;
                    }

                    try {
                        const data = JSON.parse(responseText);
                        console.log('✅ Parsed JSON:', data);

                        if (data.success) {
                            Swal.fire({
                                title: 'Pertanyaan berhasil dikirim',
                                text: 'Sekarang Saatnya memberi...',
                                icon: 'success',
                                confirmButtonText: 'Lanjut ke persembahan',
                                allowOutsideClick: false,
                            }).then(() => {
                                window.location.href = "{{ route('kom.form.persembahan') }}";
                            });
                        } else {
                            Swal.fire('Gagal', data.message || 'Terjadi kesalahan saat mengirim.', 'error');
                            resetButton();
                        }
                    } catch (err) {
                        console.error('❌ Gagal parse JSON:', err);
                        Swal.fire('Error', 'Respons server rusak atau tidak bisa dibaca. Coba login ulang atau hubungi admin.', 'error');
                        resetButton();
                    }
                })
                .catch(err => {
                    console.error('🚨 Network/Server Error:', err);
                    Swal.fire('Gagal', 'Terjadi kesalahan jaringan. Periksa koneksi internet Anda.', 'error');
                    resetButton();
                });
            }
        });

        function resetButton() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<i class="fas fa-paper-plane me-2"></i> Kirim Pertanyaan`;
        }
    });
})();
</script>



@endsection