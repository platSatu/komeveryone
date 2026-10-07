{{-- kom_v02 — pesan sukses / gagal --}}
@if (session('success'))
    <div class="kom-note kom-note-success mb-3"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="kom-note kom-note-danger mb-3"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>
@endif
