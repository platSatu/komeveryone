@extends('layouts.be')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Booking Kelas</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead><tr><th>#</th><th>Siswa</th><th>Kelas</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($bookings as $b)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $b->user->username }}</td>
                    <td>{{ $b->kelas->hari }} - {{ $b->kelas->jam }}</td>
                    <td>{{ $b->status }}</td>
                    <td>
                        <a href="{{ route('kom.booking.edit', $b->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('kom.booking.destroy', $b->id) }}" method="POST" class="d-inline">@csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if($bookings->isEmpty())<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>@endif
            </tbody>
        </table>
    </div>
</div>
@endsection
