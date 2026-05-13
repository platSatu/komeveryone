@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Progress Video User</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.user_video_progress.create') }}" class="btn btn-primary btn-sm">+ Tambah Progress</a>
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
                            <th>User</th>
                            <th>Video</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($progress as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->user->username ?? 'N/A' }}</td>
                            <td>{{ $item->video->name ?? 'N/A' }}</td>
                            <td>{{ $item->is_locked ? 'Terkunci' : 'Terbuka' }}</td>
                            <td>
                                <a href="{{ route('kom.user_video_progress.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('kom.user_video_progress.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($progress->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
