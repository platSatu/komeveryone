@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-md-12">
        {{-- Card filter & info --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <form action="{{ route('kom.schedules.index') }}" method="GET" class="form-inline mb-2 mb-md-0">
                    <label for="rayon_id" class="mr-2">Filter Rayon:</label>
                    <select name="rayon_id" id="rayon_id" class="form-control mr-2">
                        <option value="">-- Semua Rayon --</option>
                        @foreach($rayons as $rayon)
                            <option value="{{ $rayon->id }}" {{ request('rayon_id') == $rayon->id ? 'selected' : '' }}>
                                {{ $rayon->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
                </form>

                <div>
                    <strong>Total Kelas Dibuka:</strong> {{ $jumlahKelasDibuka }}
                </div>
            </div>
        </div>

        {{-- Card tabel jadwal --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Jadwal Kelas</h5>
                <a href="{{ route('kom.schedules.create') }}" class="btn btn-primary btn-sm">+ Tambah Jadwal</a>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Pengajar</th>
                                <th>Rayon</th>
                                <th>Kelas</th>
                                <th>Modul</th>
                                <th>Waktu</th>
                                <th>Kuota</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schedules as $schedule)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $schedule->komUser->username ?? '-' }}</td>
                                    <td>{{ $schedule->pengajar->username ?? '-' }}</td>
                                    <td>{{ $schedule->rayon->name ?? '-' }}</td>
                                    <td>{{ $schedule->komClass->name ?? '-' }}</td>
                                    <td>{{ $schedule->komModul->title ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($schedule->datetime)->format('d M Y H:i') }}</td>
                                    <td>{{ $schedule->quota }}</td>
                                    <td>
                                        <form action="{{ route('kom.jadwal_rayon.toggleStatus', $schedule->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm {{ $schedule->status === 'aktif' ? 'btn-success' : 'btn-secondary' }}">
                                                {{ ucfirst($schedule->status) }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="{{ route('kom.schedules.edit', $schedule->id) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">Belum ada data jadwal</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Uncomment if you enable pagination --}}
                {{-- {{ $schedules->links() }} --}}
            </div>
        </div>
    </div>
</div>
@endsection
