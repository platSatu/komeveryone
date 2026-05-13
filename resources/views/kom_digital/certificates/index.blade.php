@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Sertifikat KOM</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.certificates.create') }}" class="btn btn-primary btn-sm">+ Tambah Sertifikat</a>
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
                            <th>No Sertifikat</th>
                            <th>Tanggal Sertifikat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($certificates as $certificate)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $certificate->no_sertifikat }}</td>
                            <td>{{ $certificate->tgl_sertifikat }}</td>
                            <td>{{ $certificate->status ? 'Aktif' : 'Tidak Aktif' }}</td>
                            <td>
                                <a href="{{ route('kom.certificates.edit', $certificate->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('kom.certificates.destroy', $certificate->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($certificates->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data</td>
                        </tr>
                        @endif
                    </tbody>
                </table>

                <!-- Pagination -->
                {{ $certificates->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
