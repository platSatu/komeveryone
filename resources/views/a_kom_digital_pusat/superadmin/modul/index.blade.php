@extends('layouts.KomDigital')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Modul KOM</h4>
                    <div class="card-tools">
                        <a href="{{ route('kom.super_kom-modul.create') }}" class="btn btn-primary btn-sm">+ Tambah Modul</a>
                    </div>
                </div>

                <div class="card-body">


                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul Modul</th>
                                <th>Deskripsi</th>
                                <th>Kelas</th>
                                <th>Urutan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $index => $modul)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $modul->name }}</td>
                                    <td>{{ \Str::limit($modul->description, 50) }}</td>
                                    <td>{{ $modul->kelas->name ?? '-' }}</td>
                                    <td>{{ $modul->order }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $modul->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                            {{ ucfirst($modul->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('kom.super_kom-modul.edit', $modul->id) }}"
                                            class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('kom.super_kom-modul.destroy', $modul->id) }}" method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Yakin ingin menghapus modul ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data modul tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>


                    <div class="d-flex justify-content-center">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
