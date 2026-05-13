@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        {{-- Flash Message untuk Sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- Error Validation Message --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tambah User</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.users.index') }}" class="btn btn-primary">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('kom.users.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <div class="form-group">
                        <label for="rayon_id">Rayon</label>
                        <select name="rayon_id" class="form-control" required>
                            @foreach($rayons as $rayon)
                            <option value="{{ $rayon->id }}" {{ old('rayon_id') == $rayon->id ? 'selected' : '' }}>{{ $rayon->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select name="role" class="form-control" required>
                            <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                            <option value="pengajar" {{ old('role') == 'pengajar' ? 'selected' : '' }}>Pengajar</option>
                            <option value="admin_cabang" {{ old('role') == 'admin_cabang' ? 'selected' : '' }}>Admin Cabang</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
