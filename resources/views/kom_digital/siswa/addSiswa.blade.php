@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-12">

            {{-- Flash Message: Sukses --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-check-circle"></i> Sukses!</strong> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- Flash Message: Error Umum --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle"></i> Gagal!</strong> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- Validasi Error --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-times-circle"></i> Ada kesalahan:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- Form Tambah User --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah User</h3>
                    <div class="card-tools">
                        <a href="{{ route('kom.peserta_by_rayon.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('kom.siswa.store') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="{{ old('username') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="pengajar" {{ old('role') == 'pengajar' ? 'selected' : '' }}>Pengajar</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">Simpan</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
