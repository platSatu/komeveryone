@extends('layouts.kom')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Pengajar</h5><br>
                    <a href="{{ route('kom.peserta_by_rayon.create') }}" class="btn btn-success">Tambah Pengajar</a>

                </div>

                <div class="card-body">
                    @if($pengajars->isEmpty())
                        <div class="alert alert-info mb-0">
                            Tidak ada pengajar di rayon ini.
                        </div>
                    @else
                        <div class="table-responsive b-0" data-pattern="priority-columns">
                            <table class="table table-bordered table-hover table-striped mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Pengajar</th>
                                        <th>Email</th>
                                        <th>Rayon</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pengajars as $index => $pengajar)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $pengajar->username ?? $pengajar->name }}</td>
                                            <td>{{ $pengajar->email }}</td>
                                            <td>{{ $pengajar->rayon->name ?? '-' }}</td>
                                            <td>
                                                <form action="{{ route('kom.user.toggleStatus', $pengajar->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    @if ($pengajar->status === 'aktif')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-warning">Nonaktifkan</button>
                                                    @else
                                                        <button type="submit"
                                                            class="btn btn-sm btn-success">Aktifkan</button>
                                                    @endif
                                                </form>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
