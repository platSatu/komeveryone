@extends('layouts.kom')

@section('content')

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Daftar Pengguna Rayon: {{ $rayonName }}</h5>
        </div>

        <div class="card-body">
            <form method="GET" class="form-inline mb-4">
                <div class="form-group mr-3">
                    <select name="role" class="form-control">
                        <option value="">-- Semua Role --</option>
                        <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="pengajar" {{ request('role') == 'pengajar' ? 'selected' : '' }}>Pengajar</option>
                    </select>
                </div>

                <div class="form-group mr-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama/email..."
                        value="{{ request('search') }}">
                </div>

                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="" class="btn btn-sm btn-secondary ml-2">Reset</a>
            </form>

            @if($siswa->isEmpty())
                <div class="alert alert-info">Tidak ada data yang sesuai.</div>
            @else
                <div class="table-responsive b-0" data-pattern="priority-columns">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswa as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->username ?? '-' }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ ucfirst($user->role) }}</td>
                                      <td>
                                                <form action="{{ route('kom.user.toggleStatus', $user->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    @if ($user->status === 'aktif')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-warning">Nonaktifkan</button>
                                                    @else
                                                        <button type="submit"
                                                            class="btn btn-sm btn-success">Aktifkan</button>
                                                    @endif
                                                </form>

                                            </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

@endsection
