@extends('layouts.be')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Log Aktivitas</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead><tr><th>#</th><th>User</th><th>Aksi</th><th>Waktu</th></tr></thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $log->user->username }}</td>
                    <td>{{ $log->deskripsi }}</td>
                    <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                </tr>
                @endforeach
                @if($logs->isEmpty())<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>@endif
            </tbody>
        </table>
    </div>
</div>
@endsection
