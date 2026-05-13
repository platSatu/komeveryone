@extends('layouts.KomDigital')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h3 class="mb-0">Profil Saya</h3>
                </div>
                <div class="card-body p-4">

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="d-flex flex-column flex-md-row align-items-center gap-4 mb-4">
                        <div class="profile-image-wrapper position-relative mr-3">
                            @if ($user->image)
                                <img src="{{ asset('kom/profile/' . $user->image) }}" alt="Foto Profil" class="rounded-circle border border-3 border-primary" style="width:120px; height:120px; object-fit:cover;">
                            @else
                                <img src="{{ asset('be/assets/images/users/avatar-1.jpg') }}" alt="Foto Default" class="rounded-circle border border-3 border-secondary" style="width:120px; height:120px; object-fit:cover;">
                            @endif
                        </div>
                        <div class="profile-info flex-grow-1">
                            <h4 class="fw-bold mb-1">{{ $user->username }}</h4>
                            <p class="mb-1 text-muted"><i class="fas fa-envelope me-2 mr-1"></i>{{ $user->email }}</p>
                            <p class="mb-1"><i class="fas fa-user-tag me-2 mr-1"></i>{{ ucfirst($user->role) }}</p>
                            <p class="mb-1">
                                <i class="fas fa-toggle-on me-2"></i>
                                {!! $user->status ? '<span class="text-success fw-semibold">Aktif</span>' : '<span class="text-danger fw-semibold">Tidak Aktif</span>' !!}
                            </p>
                            <p class="mb-1"><i class="fas fa-calendar-alt me-2 mr-1"></i>Daftar: {{ \Carbon\Carbon::parse($user->tanggal_daftar)->format('d M Y') }}</p>
                            <p class="mb-1"><i class="fas fa-birthday-cake me-2 mr-1"></i>Tahun Lahir: {{ $user->thn_lahir ?? '-' }}</p>
                            <p class="mb-0"><i class="fas fa-map-marker-alt me-2 mr-1"></i>Rayon: {{ $user->rayon->name ?? '-' }}</p>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3">Update Password & Foto Profil</h5>
                    <form action="{{ route('kom.profile_kom.update') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru (opsional)</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Kosongkan jika tidak diubah" autocomplete="new-password">
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label">Foto Profil (opsional)</label>
                            <div class="mb-2">
                                @if ($user->image)
                                    <img src="{{ asset('storage/foto_user/' . $user->image) }}" alt="Foto Profil" class="rounded-circle border border-2 border-primary" style="width:80px; height:80px; object-fit:cover;">
                                @else
                                    <img src="{{ asset('be/assets/images/users/avatar-1.jpg') }}" alt="Foto Default" class="rounded-circle border border-2 border-secondary" style="width:80px; height:80px; object-fit:cover;">
                                @endif
                            </div>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-semibold">Simpan Perubahan</button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- Optional: client-side validation --}}
<script>
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>

<style>
    .profile-image-wrapper img {
        box-shadow: 0 4px 8px rgb(0 0 0 / 0.1);
        transition: transform 0.3s ease;
    }
    .profile-image-wrapper img:hover {
        transform: scale(1.05);
    }
</style>
@endsection