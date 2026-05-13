@extends('layouts.KomDigital')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Video KOM</h4>
                    <div class="card-tools">
                        <a href="{{ route('kom.super_videos.create') }}" class="btn btn-primary btn-sm">+ Tambah Video</a>
                    </div>
                </div>

                <div class="card-body">


                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul Video</th>
                                <th>Modul</th>
                                <th>Urutan</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $index => $video)
                                <tr>
                                    <td>{{ $index + $data->firstItem() }}</td>
                                    <td>{{ $video->name }}</td>
                                    <td>{{ $video->modul->name ?? '-' }}</td>
                                    <td>{{ $video->sort_video }}</td>
                                    <td>{{ $video->durations }}</td>
                                    <td>
                                        @if ($video->status == 'active')
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Contoh tombol aksi, sesuaikan route-nya -->
                                        <a href="{{ route('kom.super_videos.edit', $video->id) }}"
                                            class="btn btn-sm btn-warning">Edit</a>

                                        <form action="{{ route('kom.super_videos.destroy', Crypt::encrypt($video->id)) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin ingin hapus video ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Data video tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
