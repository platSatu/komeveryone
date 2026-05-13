@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Zoom Meeting Saya</h3>
                </div>

                <div class="card-body">
                    @if ($zoom->isEmpty())
                        <div class="alert alert-info mb-0">
                            Belum ada jadwal Zoom yang tersedia.
                        </div>
                    @else
                    <div class="table-responsive b-0" data-pattern="priority-columns">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Modul</th>
                                    <th>Kelas</th>
                                    <th>Rayon</th>
                                    <th>Jadwal</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($zoom as $index => $item)
                                    <tr>
                                        <td>{{ $index + $zoom->firstItem() }}</td>
                                        <td>{{ $item->zoom->modul->title ?? '-' }}</td>
                                        <td>{{ $item->zoom->kelas->name ?? '-' }}</td>
                                        <td>{{ $item->zoom->rayon->name ?? '-' }}</td>
                                        <td>{{ $item->zoom->schedule->datetime ?? '-' }}</td>
                                        <td>
                                            @if ($item->zoom_link)
                                                <a href="{{ $item->zoom_link }}" target="_blank" rel="noopener noreferrer">
                                                    Klik untuk Gabung Zoom
                                                </a>
                                            @else
                                                <span class="text-muted">Tidak tersedia</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $item->zoom->status === 'aktif' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($item->zoom->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        {{-- Tampilkan pagination --}}
                        <div class="pagination justify-content-center mt-4">
                        {{ $zoom->links('pagination::bootstrap-4') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
