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
                    <h4 class="card-title">Edit User</h4>
                    <a href="{{ route('kom.super_user.index') }}" class="btn btn-primary">Kembali</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('kom.super_user.update', Crypt::encrypt($data->id)) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Metode PUT untuk update --}}

                        <div class="form-group">
                            <label for="name">Username</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name', $data->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $data->email) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation">
                        </div>

                        <div class="form-group">
                            <label for="cabang_id">Rayon</label>
                            <select name="cabang_id" class="form-control">
                                <option value="">-- Pilih Rayon --</option>
                                @foreach ($rayons as $rayon)
                                    <option value="{{ $rayon->id }}" {{ $rayon->id == $data->cabang_id ? 'selected' : '' }}>
                                        {{ $rayon->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" class="form-control">
                                <option value="peserta" {{ $data->role == 'peserta' ? 'selected' : '' }}>Siswa</option>
                                <option value="pengajar" {{ $data->role == 'pengajar' ? 'selected' : '' }}>Pengajar</option>
                                <option value="admin_cabang" {{ $data->role == 'admin_cabang' ? 'selected' : '' }}>Admin
                                    Cabang</option>
                            </select>
                        </div>

                        <!-- Tambahan kolom baru: Asal Gereja -->
                        <div class="form-group">
                            <label for="asal_gereja">Asal Gereja</label>
                            <input type="text" class="form-control" id="asal_gereja" name="asal_gereja"
                                value="{{ old('asal_gereja', $data->asal_gereja) }}" placeholder="Opsional">
                        </div>

                        <!-- Tambahan kolom baru: Jenis -->
                        <div class="form-group">
                            <label for="jenis">Jenis</label>
                            <input type="text" class="form-control" id="jenis" name="jenis"
                                value="{{ old('jenis', $data->jenis) }}" placeholder="Opsional">
                        </div>

                        <!-- Tambahan kolom baru: No SIM -->
                        <div class="form-group">
                            <label for="no_sim">No SIM</label>
                            <input type="text" class="form-control" id="no_sim" name="no_sim"
                                value="{{ old('no_sim', $data->no_sim) }}" placeholder="Opsional">
                        </div>

                        <button type="submit" class="btn btn-success btn-sm">Update</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection