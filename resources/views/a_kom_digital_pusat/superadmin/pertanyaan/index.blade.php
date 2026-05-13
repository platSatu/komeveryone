@extends('layouts.KomDigital')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">

                {{-- Search Form --}}
                <form method="GET" action="{{ route('kom.super_pertanyaan_user.index') }}" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama user atau pertanyaan..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </form>

                {{-- Tabel Pertanyaan --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-truncate">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama User</th>
                                <th>Pertanyaan</th>
                                <th>Modul</th>
                                <th>Kelas</th>
                                <th>Kelas Tutorial</th>
                                <th>Pengajar</th>
                                <th>Rayon</th>
                                <th>Tgl Submit</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pertanyaans as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($pertanyaans->currentPage() - 1) * $pertanyaans->perPage() }}</td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td style="white-space: normal; word-wrap: break-word;">{{ $item->pertanyaan }}</td>
                                <td>{{ $item->modul->name ?? '-' }}</td>
                                <td>{{ $item->class->name ?? '-' }}</td>
                                <td>{{ $item->kelasTutorial->topic ?? '-' }}</td>
                                <td>{{ $item->pengajar->name ?? '-' }}</td>
                                <td>{{ $item->rayon->name ?? '-' }}</td>
                                <td>{{ $item->tgl_submit ? $item->tgl_submit->format('d M Y H:i') : '-' }}</td>
                                <td>
                                    <form action="{{ route('kom.super_pertanyaan_user.destroy', Crypt::encrypt($item->id)) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pertanyaan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">Tidak ada data ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $pertanyaans->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
