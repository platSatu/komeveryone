@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit User</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.users.index') }}" class="btn btn-primary">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('kom.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>

                    <div class="form-group">
                        <label for="rayon_id">Rayon</label>
                        <select name="rayon_id" class="form-control" required>
                            @foreach($rayons as $rayon)
                                <option value="{{ $rayon->id }}" {{ $user->rayon_id == $rayon->id ? 'selected' : '' }}>
                                    {{ $rayon->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="role">Role</label>
                        <select name="role" class="form-control" required>
                            <option value="siswa" {{ $user->role === 'siswa' ? 'selected' : '' }}>Siswa</option>
                            <option value="pengajar" {{ $user->role === 'pengajar' ? 'selected' : '' }}>Pengajar</option>
                            <option value="admin_cabang" {{ $user->role === 'admin_cabang' ? 'selected' : '' }}>Admin Cabang</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-warning">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
