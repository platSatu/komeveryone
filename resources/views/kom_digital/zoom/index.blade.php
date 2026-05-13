@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <strong>Daftar Link Zoom</strong><br>
                    <a href="{{ route('kom.zoom.create') }}" class="btn btn-primary btn-sm float-end">Tambah</a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th> <!-- Nomor urut -->
                                    <th>User</th>
                                    <th>Modul</th>
                                    <th>Kelas</th>
                                    <th>Jadwal</th>
                                    <th>Rayon</th>
                                    {{-- <th>Status</th>
                                    <th>Aksi</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $zoom)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td> <!-- Nomor otomatis -->
                                        <td>{{ $zoom->user->username ?? '-' }}</td>
                                        <td>{{ $zoom->modul->title ?? '-' }}</td>
                                        <td>{{ $zoom->kelas->name ?? '-' }}</td>
                                        <td>
                                            {{ $zoom->schedule->datetime ?? '-' }}
                                        </td>
                                        <td>{{ $zoom->rayon->name ?? '-' }}</td>
                                        {{-- <td>
                                            <form action="{{ route('kom.zoom.toggleStatus', $zoom->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $zoom->status === 'aktif' ? 'btn-success' : 'btn-secondary' }}">
                                                    {{ ucfirst($zoom->status) }}
                                                </button>
                                            </form>
                                            </td>

                                            <td>
                                                <a href="{{ route('kom.zoom.edit', $zoom->id) }}"
                                                    class="btn btn-sm btn-warning">Edit</a>
                                                <form action="{{ route('kom.zoom.destroy', $zoom->id) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection
