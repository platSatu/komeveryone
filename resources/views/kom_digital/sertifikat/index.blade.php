@extends('layouts.be')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Sertifikat</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead><tr><th>#</th><th>Siswa</th><th>Level</th><th>No Sertifikat</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($sertifikats as $s)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $s->user->username }}</td>
                    <td>{{ $s->level }}</td>
                    <td>{{ $s->nomor }}</td>
                    <td>{{ $s->tanggal_terbit }}</td>
                    <td><a href="{{ asset('storage/sertifikat/'.$s->file) }}" target="_blank" class="btn btn-info btn-sm">Lihat</a></td>
                </tr>
                @endforeach
                @if($sertifikats->isEmpty())<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>@endif
            </tbody>
        </table>
    </div>
</div>
@endsection
