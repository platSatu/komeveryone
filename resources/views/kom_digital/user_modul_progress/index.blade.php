@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Progress Modul Pengguna</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.user_modul_progress.create') }}" class="btn btn-primary btn-sm">+ Tambah Progress</a>
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
                            <th>Nama User</th>
                            <th>Modul</th>
                            <th>Video Terakhir</th>
                            <th>Urutan Video</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($progresses as $progress)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $progress->user->name ?? '-' }}</td>
                            <td>{{ $progress->modul->title ?? '-' }}</td>
                            <td>{{ $progress->lastVideo->name ?? '-' }}</td>
                            <td>{{ $progress->video_terakhir }}</td>
                            <td>
                                <a href="{{ route('kom.user_modul_progress.edit', $progress->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('kom.user_modul_progress.destroy', $progress->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($progresses->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
