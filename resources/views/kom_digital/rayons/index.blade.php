@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Rayon</h3>
                    <div class="card-tools">
                        <a href="{{ route('kom.rayon.create') }}" class="btn btn-primary btn-sm">+ Tambah Rayon</a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Rayon</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rayons as $rayon)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $rayon->name }}</td>
                                    <td>{{ $rayon->status ? 'Aktif' : 'Tidak Aktif' }}</td>
                                    <td>
                                        <a href="{{ route('kom.rayon.edit', $rayon->id) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('kom.rayon.destroy', $rayon->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $rayons->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
