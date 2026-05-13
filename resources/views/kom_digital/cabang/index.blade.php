@extends('layouts.be')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Cabang</h3>
        <a href="{{ route('kom.cabang.create') }}" class="btn btn-primary btn-sm float-end">+ Tambah Cabang</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead><tr><th>#</th><th>Nama</th><th>Alamat</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($cabangs as $c)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $c->nama }}</td>
                    <td>{{ $c->alamat }}</td>
                    <td>
                        <a href="{{ route('kom.cabang.edit', $c->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('kom.cabang.destroy', $c->id) }}" method="POST" class="d-inline">@csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if($cabangs->isEmpty())<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>@endif
            </tbody>
        </table>
    </div>
</div>
@endsection
