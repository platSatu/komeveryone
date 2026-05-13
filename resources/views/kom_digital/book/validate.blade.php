@extends('layouts.kom')

@section('content')
<style>
    #kode_feedback.text-danger { color: #dc3545; font-weight: bold; }
    #kode_feedback.text-success { color: #28a745; font-weight: bold; }
</style>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <strong>Aktivasi Kode Buku KOM</strong>
            </div>

            <div class="card-body">
                <form id="activationForm" autocomplete="off">
                    @csrf
                    <div class="form-group">
                        <label for="kode_buku">Kode Buku</label>
                        <input type="text" name="kode_buku" id="kode_buku" class="form-control"
                            placeholder="Masukkan kode unik dari buku KOM" required>
                        <small id="kode_feedback" class="form-text"></small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputKode = document.getElementById('kode_buku');
    const feedback = document.getElementById('kode_feedback');

    let timeout = null;

    inputKode.addEventListener('input', function () {
        const kode = inputKode.value.trim();
        clearTimeout(timeout);

        if (kode.length < 3) {
            feedback.innerText = 'Masukkan minimal 3 karakter kode.';
            feedback.classList.remove('text-success');
            feedback.classList.add('text-danger');
            return;
        }

        timeout = setTimeout(() => {
            fetch("{{ route('kom.kode_buku.validate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ kode_buku: kode })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' || data.status === 'valid') {
                    feedback.innerText = data.message;
                    feedback.classList.remove('text-danger');
                    feedback.classList.add('text-success');

                    // Reload halaman otomatis setelah 1.2 detik
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    feedback.innerText = data.message || 'Kode buku tidak valid.';
                    feedback.classList.remove('text-success');
                    feedback.classList.add('text-danger');
                }
            })
            .catch(() => {
                feedback.innerText = 'Terjadi kesalahan, silakan coba lagi.';
                feedback.classList.remove('text-success');
                feedback.classList.add('text-danger');
            });
        }, 500);
    });
});
</script>
@endsection
