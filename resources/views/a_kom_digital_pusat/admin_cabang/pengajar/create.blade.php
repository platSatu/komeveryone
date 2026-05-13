@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Oops! Ada beberapa kesalahan:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li> {{-- Ini akan menampilkan pesan error per aturan validasi --}}
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif


            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Tambah User</h4>
                    <a href="" class="btn btn-primary">Kembali</a>
                </div>

                <div class="card-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
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
                        <div class="form-group">
                            <label for="cabang_id">Rayon</label>
                            <select name="cabang_id" class="form-control" required>
                                @foreach ($rayons as $rayon)
                                    <option value="{{ $rayon->id }}">{{ $rayon->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" class="form-control" required>
                                <option value="siswa">Siswa</option>
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
@endsection
