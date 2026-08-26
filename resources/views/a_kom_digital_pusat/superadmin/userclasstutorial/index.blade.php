@extends('layouts.KomDigital')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header">
                <form method="GET" action="{{ route('kom.super_userclasstutorial.index') }}" class="form-inline">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama user atau tutorial...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
                 <div class="table-responsive b-0" data-pattern="priority-columns">
                    <table id="tech-companies-1" class="table table-striped text-truncate">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Nama User</th>
                                <th>Topic Kelas Tutorial</th>
                                <th>Category Diklat</th>
                                <th>Tanggal Kelas</th>
                                <th>Modul</th>
                                <th>Kelas</th>
                                <th>Rayon</th>
                                <th>Pengajar</th>
                                <th>Status</th>
                                <th>Status Progress</th>
                                <th>Step</th>
                                <th>Tanggal Booking</th>
                                <th>Zoom</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $index => $item)
                                <tr>
                                    <td>{{ $data->firstItem() + $index }}</td>
                                    <td>{{ $item->user->name ?? '-' }}</td>
                                    <td>{{ $item->classTutorial->topic ?? '-' }}</td>
                                    <td>{{ $item->classTutorial->category_diklat ?? '-' }}</td>
                                    <td>{{ $item->classTutorial->start_time ?? '-' }}</td>
                                    <td>{{ $item->modul->name ?? '-' }}</td>
                                    <td>{{ $item->kelas->name ?? '-' }}</td>
                                    <td>{{ $item->rayon->name ?? '-' }}</td>
                                    <td>{{ $item->pengajar->name ?? '-' }}</td>
                                    <td>{{ $item->status ? ucfirst($item->status) : '-' }}</td>
                                    <td>{{ $item->status_progress ? ucfirst($item->status_progress) : '-' }}</td>
                                    <td>{{ $item->step ?? '-' }}</td>
                                    <td>{{ $item->date_booking ? \Carbon\Carbon::parse($item->date_booking)->format('d-m-Y') : '-' }}</td>
                                    <td>
                                        @if ($item->zoom_join_url)
                                            <a href="{{ $item->zoom_join_url }}" target="_blank">
                                                <i class="fas fa-video" style="color:#2D8CFF;"></i>&nbsp;Join Zoom
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('kom.super_userclasstutorial.edit', $item->id) }}" 
                                           class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                        <form action="{{ route('kom.super_userclasstutorial.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="text-center">Tidak ada data ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center mt-3">
                        {{ $data->appends(['search' => request('search')])->links('pagination::bootstrap-4') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
