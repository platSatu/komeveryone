@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Jadwal Kelas</h3>
                    <div class="card-tools">
                        <a href="{{ route('kom.schedules.create') }}" class="btn btn-primary btn-sm">+ Tambah Jadwal Kelas
                            Tutorial</a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <div class="table-responsive b-0" data-pattern="priority-columns">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>

                                <th>Pengajar</th>
                                <th>Rayon</th>
                                <th>Kelas</th>
                                <th>Modul</th>
                                <th>Waktu</th>
                                <th>Kuota</th>
                                <th>Daftar</th>
                                <th>Sisa</th>
                                <th>Status Kelas</th>
                                <th>Aksi</th>
                                {{-- <th>Kelas Selesai</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schedules as $schedule)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $schedule->pengajar->username ?? '-' }}</td>
                                    <td>{{ $schedule->rayon->name ?? '-' }}</td>
                                    <td>{{ $schedule->komClass->name ?? '-' }}</td>
                                    <td>{{ $schedule->komModul->title ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($schedule->datetime)->format('d M Y H:i') }}</td>
                                    <td class="text-center font-weight-bold">{{ $schedule->quota }}</td>
                                    <td class="text-center font-weight-bold">{{ $schedule->kelas_tutorial_count }}</td>
                                    <td class="text-center font-weight-bold">
                                        {{ $schedule->quota - $schedule->kelas_tutorial_count }}</td>
                                    <td>
                                        <form action="{{ route('kom.jadwal_rayon.toggleStatus', $schedule->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm {{ $schedule->status === 'aktif' ? 'btn-success' : 'btn-danger' }}">
                                                {{ ucfirst($schedule->status) }}
                                            </button>
                                        </form>
                                    </td>

                                    <td>
                                        {{-- <a href="{{ route('kom.schedules.edit', $schedule->id) }}"
                                            class="btn btn-warning btn-sm">Edit</a> --}}
                                        {{-- Tambahkan tombol hapus jika diperlukan --}}

                                        <a href="{{ route('kom.schedules.peserta', $schedule->id) }}"
                                            class="btn btn-sm btn-info">
                                            Lihat Peserta
                                        </a>
                                    </td>
                                    {{-- <style>
                                        .bootstrap-toggle .toggle-on,
                                        .bootstrap-toggle .toggle-off {
                                            color: white !important;
                                        }
                                    </style>
                                    <td>
                                        <form action="{{ route('kom.kelas-tutorial.finish', $schedule->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            <input type="checkbox" class="toggle-status" data-id="{{ $schedule->id }}"
                                                {{ $schedule->status_kelas === 'finish' ? 'checked' : '' }}
                                                data-toggle="toggle" data-on="Finish" data-off="Ongoing"
                                                data-onstyle="success" data-offstyle="warning" data-size="sm" />

                                        </form>
                                    </td> --}}



                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center">Belum ada data jadwal</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                    {{-- Pagination jika diperlukan --}}
                    {{-- {{ $schedules->links() }} --}}
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $('.toggle-status').change(function() {
                var id = $(this).data('id');

                $.ajax({
                    url: '/kelas-tutorial/' + id + '/toggle-status',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('Status diubah ke:', response.new_status);
                        // Optional: tampilkan notifikasi swal/toast, dll
                    },
                    error: function() {
                        alert('Gagal mengubah status.');
                    }
                });
            });
        });
    </script>
@endsection
