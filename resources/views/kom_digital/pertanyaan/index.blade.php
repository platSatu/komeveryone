@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pertanyaan</h3>
                    <div class="card-tools">
                        <a href="{{ route('kom.pertanyaan.create') }}" class="btn btn-primary btn-sm">+ Tambah
                            Pertanyaan</a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <div class="table-responsive b-0" data-pattern="priority-columns">
                        <table id="tech-companies-1" class="table table-striped text-truncate">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Pertanyaan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $pertanyaan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $pertanyaan->user->username ?? '-' }}</td>
                                    <td>{{ $pertanyaan->pertanyaan }}</td>
                                    <td>
                                        <a href="{{ route('kom.pertanyaan.edit', $pertanyaan->id) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('kom.pertanyaan.destroy', $pertanyaan->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada pertanyaan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="pagination justify-content-center mt-4">
                    {{-- {{ $data->links('pagination::bootstrap-4') }} --}}
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection