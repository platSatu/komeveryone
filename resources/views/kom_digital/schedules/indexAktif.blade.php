@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Booking Class</h3>
                </div>
                <div class="card-body">
                    @if ($schedules->isEmpty())
                        <div class="alert alert-info">
                            Tidak ada booking Kelas yang aktif.
                        </div>
                    @else
                        <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                   
                                    <th>Rayon</th>
                                    <th>Kelas</th>
                                    <th>Modul</th>
                                    <th>Hari & Tanggal</th>
                                    <th>Description</th>
                                    <th>Quota</th>
                                    <th>Status</th>
                                    <th>Aksi</th> {{-- Tambahkan kolom aksi --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($schedules as $index => $schedule)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        
                                        <td>{{ $schedule->rayon->name ?? '-' }}</td>
                                        <td>{{ $schedule->komClass->name ?? '-' }}</td>
                                        <td>{{ $schedule->komModul->title ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->datetime)->translatedFormat('l d F Y') }}</td>
                                        <td>{{ $schedule->description }}</td>
                                        <td>{{ $schedule->quota }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ ucfirst($schedule->status) }}</span>
                                        </td>
                                        <td>
                                            {{-- Tombol Detail --}}
                                            <a href="{{ route('kom.jadwal.detail', $schedule->id) }}"
                                                class="btn btn-info btn-sm mb-1">
                                                Detail Peserta
                                            </a>
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
