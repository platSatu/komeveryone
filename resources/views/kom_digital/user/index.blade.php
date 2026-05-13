@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Pengguna</h3>

                <!-- Tombol tambah pengguna -->
                <a href="{{ route('kom.users.create') }}" class="btn btn-success">Tambah User</a>
            </div>

            <div class="card-body">
                <!-- Form Filter dan Pencarian -->
                <form method="GET" action="{{ route('kom.users.index') }}" class="form-inline mb-3">
                    <div class="form-group mr-2">
                        <input type="text" name="search" class="form-control" placeholder="Cari pengguna..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="form-group mr-2">
                        <select name="role" class="form-control">
                            <option value="">-- Semua Role --</option>
                            <option value="siswa" {{ request('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                            <option value="pengajar" {{ request('role') === 'pengajar' ? 'selected' : '' }}>Pengajar</option>
                            <option value="admin_cabang" {{ request('role') === 'admin_cabang' ? 'selected' : '' }}>Admin Cabang</option>
                            <option value="admin_kom" {{ request('role') === 'admin_kom' ? 'selected' : '' }}>Admin Kom</option>
                        </select>
                    </div>

                    <button class="btn btn-primary">Filter</button>
                </form>

                <!-- Tabel Daftar Pengguna -->
                <div class="table-responsive b-0" data-pattern="priority-columns">
                            <table id="tech-companies-1" class="table table-striped text-truncate">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Tahun Lahir</th>
                                <th>Asal Gereja</th>
                                <th>Ikut Kelas di</th>
                                <th>Role</th>
                                <th>Tanggal Daftar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->thn_lahir }}</td>
                                    <td>{{ $user->asal_gereja }}</td>
                                    <td>{{ $user->rayon->name ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($user->role) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y') }}</td>
                                    <td>
                                        <form action="{{ route('kom.users.updateStatus', $user->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $user->status ? 0 : 1 }}">
                                            <button type="submit"
                                                class="btn btn-sm {{ $user->status ? 'btn-success' : 'btn-secondary' }}">
                                                {{ $user->status ? 'Aktif' : 'Nonaktif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="{{ route('kom.users.edit', $user->id) }}"
                                            class="btn btn-warning btn-sm">Edit</a>

                                        <form action="{{ route('kom.users.destroy', $user->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada pengguna ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                 <div class="pagination justify-content-center mt-4">
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>
                <!-- Pagination -->
               
            </div>
        </div>
    </div>
</div>
@endsection
