@extends('layouts.KomDigital')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Relasi Video & Pertanyaan</h4>
                    <div class="card-tools">
                        <a href="{{ route('kom.super_pertanyaan-video.create') }}" class="btn btn-primary btn-sm">+ Tambah Relasi</a>
                    </div>
                </div>

                <div class="card-body">

                    {{-- Form Pencarian --}}
                    <form method="GET" action="{{ route('kom.super_pertanyaan-video.index') }}" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari Video atau Pertanyaan..."
                                value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">Cari</button>
                            @if(request('search'))
                                <a href="{{ route('kom.super_pertanyaan-video.index') }}" class="btn btn-outline-danger ms-2">Reset</a>
                            @endif
                        </div>
                    </form>

                    <div class="table-responsive b-0" data-pattern="priority-columns">
                        <table id="tech-companies-1" class="table table-striped text-truncate">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Video</th>
                                    <th>Pertanyaan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td> {{-- untuk pagination --}}
                                        <td>{{ $item->video ? $item->video->name ?? 'Judul Video Tidak Ada' : 'Video Tidak Ditemukan' }}</td>
                                        <td>{{ $item->question ? $item->question->questions ?? 'Pertanyaan Tidak Ada' : 'Pertanyaan Tidak Ditemukan' }}</td>
                                        <td>
                                            <a href="{{ route('kom.super_pertanyaan-video.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('kom.super_pertanyaan-video.destroy', $item->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button onclick="return confirm('Yakin ingin hapus?')" class="btn btn-sm btn-danger">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Data tidak ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $data->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
