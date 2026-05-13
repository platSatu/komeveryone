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
                    <h4 class="card-title">Edit User</h4>
                    <a href="{{ route('kom.admin_cabang_users.index') }}" class="btn btn-primary">Kembali</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('kom.admin_cabang_users.update', Crypt::encryptString($user->id)) }}" method="POST">

                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Username</label>
                            <input type="text" class="form-control" id="username" name="name" 
                                   value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password Baru (kosongkan jika tidak ingin ganti)</label>
                            <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation" 
                                   name="password_confirmation" autocomplete="new-password">
                        </div>

                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" class="form-control" required>
                                <option value="peserta" {{ old('role', $user->role) == 'peserta' ? 'selected' : '' }}>Peserta</option>
                                <option value="pengajar" {{ old('role', $user->role) == 'pengajar' ? 'selected' : '' }}>Pengajar</option>
                                <option value="admin_cabang" {{ old('role', $user->role) == 'admin_cabang' ? 'selected' : '' }}>Admin Cabang</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success btn-sm">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
