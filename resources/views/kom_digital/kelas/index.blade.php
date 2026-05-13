@extends('layouts.be')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Kelas Tutorial</h3>
        <a href="{{ route('kom.kelas.create') }}" class="btn btn-primary btn-sm float-end">+ Tambah Kelas</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead><tr><th>#</th><th>Cabang</th><th>Hari</th><th>Jam</th><th>Kuota</th><th>Zoom Link</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($kelas as $k)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $k->cabang->nama ?? '-' }}</td>
                    <td>{{ $k->hari }}</td>
                    <td>{{ $k->jam }}</td>
                    <td>{{ $k->kuota }}</td>
                    <td><a href="{{ $k->zoom_link }}" target="_blank">Lihat</a></td>
                    <td>
                        <a href="{{ route('kom.kelas.edit', $k->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('kom.kelas.destroy', $k->id) }}" method="POST" class="d-inline">@csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if($kelas->isEmpty())<tr><td colspan="7" class="text-center">Tidak ada data</td></tr>@endif
            </tbody>
        </table>
    </div>
</div>
@endsection
