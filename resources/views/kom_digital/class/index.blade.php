@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Kelas KOM</h3>
                    <div class="card-tools">
                        <a href="{{ route('kom.kom_class.create') }}" class="btn btn-primary btn-sm">+ Tambah Kelas</a>
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
                                <th>Nama Kelas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($classes as $class)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $class->name }}</td>
                                    <td>{{ $class->status ? 'Aktif' : 'Tidak Aktif' }}</td>
                                    <td>
                                        <a href="{{ route('kom.kom_class.edit', $class->id) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('kom.kom_class.destroy', $class->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($classes->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>


                    <!-- Pagination -->
                    {{ $classes->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
