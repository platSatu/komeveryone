@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Video KOM</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.video.create') }}" class="btn btn-primary btn-sm">+ Tambah Video</a>
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
                            <th>Judul Video</th>
                            <th>Modul</th>
                            <th>Urutan</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($videos as $video)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $video->name }}</td>
                            <td>{{ $video->komModul->title ?? '-' }}</td>
                            <td>{{ $video->urutan_video }}</td>
                            <td>{{ $video->durasi_video ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $video->status ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $video->status ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('kom.video.edit', $video->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('kom.video.destroy', $video->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus video ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada video.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
