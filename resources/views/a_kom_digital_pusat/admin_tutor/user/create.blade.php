@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Oops! Ada beberapa kesalahan:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Tambah User</h4>
                    <a href="{{ route('kom.admin_cabang_users.index') }}" class="btn btn-primary">Kembali</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('kom.admin_cabang_users.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Username</label>
                            <input type="text" class="form-control" id="username" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" required>
                        </div>
                        {{-- <div class="form-group">
                            <label for="cabang_id">Rayon</label>
                            <select name="cabang_id" class="form-control" required>
                                @foreach ($rayons as $rayon)
                                    <option value="{{ $rayon->id }}">{{ $rayon->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" class="form-control" required>
                                <option value="peserta">Peserta</option>
                                <option value="pengajar">Pengajar</option>
                                <option value="admin_cabang">Admin Cabang</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tampilkan pesan sukses jika ada
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sukses',
            text: @json(session('success')),
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    // Tampilkan pesan error jika ada
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: @json(session('error')),
            confirmButtonText: 'OK'
        });
    @endif

    // Tampilkan pesan error validasi jika ada
    @if ($errors->any())
        let errorMessages = '';
        @foreach ($errors->all() as $error)
            errorMessages += `- {{ $error }}<br>`;
        @endforeach

        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            html: errorMessages,
            confirmButtonText: 'OK'
        });
    @endif
});
</script>
@endsection