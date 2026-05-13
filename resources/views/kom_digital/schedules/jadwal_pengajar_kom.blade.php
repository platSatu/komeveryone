@extends('layouts.kom')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Daftar Jadwal Mengajar</h4>
        </div>

        <div class="card-body">
        <div class="table-responsive b-0" data-pattern="priority-columns">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal Jadwal Kelas</th>
                        <th>Deskripsi</th>
                        <th>Link Zoom</th>
                        <th>Jumlah Peserta yang Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwal as $schedule)
                        @if ($schedule->status === 'aktif')
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($schedule->datetime)->format('d M Y H:i') }}</td>
                                <td>
                                    {{ $schedule->description }}
                                   
                                </td>
                                <td> 
                                    @if ($schedule->zoom)
                                        <a href="{{ $schedule->zoom->link }}" target="_blank" class="badge bg-success text-white">
                                            Klik disini untuk Link Zoom
                                        </a>
                                    @endif
                                </td>
                                <td><strong>Jumlah Peserta:</strong> {{ $schedule->kelas_tutorial_count }}</td>
                                <td>
                                    <a href="{{ route('kom.jadwal.pengajar.detail', $schedule->id) }}"
                                        class="btn btn-sm btn-primary">Lihat Detail</a>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="text-center"><em>Tidak ada jadwal aktif yang tersedia.</em></td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
        </div>
    </div>
@endsection
