@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Modul KOM</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.modul.create') }}" class="btn btn-primary btn-sm">+ Tambah Modul</a>
                </div>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Judul Modul</th>
                            <th>Deskripsi</th>
                            <th>Kelas</th>
                            <th>Urutan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($moduls as $modul)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $modul->title }}</td>
                            <td>{{ Str::limit($modul->description, 50) }}</td>
                            <td>{{ $modul->komClass->name ?? '-' }}</td>
                            <td>{{ $modul->order }}</td>
                            <td>
                                <span class="badge {{ $modul->status ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $modul->status ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('kom.modul.edit', $modul->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('kom.modul.destroy', $modul->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus modul ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data modul.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{-- Pagination kalau diperlukan --}}
                    {{-- {{ $moduls->links() }} --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
