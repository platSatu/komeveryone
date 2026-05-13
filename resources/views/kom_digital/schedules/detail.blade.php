@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Detail Jadwal tanggal {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</h3>
                <a href="" class="btn btn-secondary btn-sm">Kembali</a>
            </div>
            <div class="card-body">

                @if($allSchedulesInDate->isEmpty())
                    <div class="alert alert-info">
                        Tidak ada jadwal aktif pada tanggal ini.
                    </div>
                @else
                    @foreach($allSchedulesInDate as $schedule)
                        <h5>{{ $schedule->description }} ({{ \Carbon\Carbon::parse($schedule->datetime)->format('H:i') }})</h5>
                        <p>Kuota: {{ $schedule->quota }}</p>

                        {{-- Kalau kamu punya relasi user booking, tampilkan disini --}}
                        @if($schedule->users && $schedule->users->isNotEmpty())
                            <table class="table table-bordered table-hover mb-4">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Rayon</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedule->users as $index => $user)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->username ?? '-' }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->rayon->name ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-info mb-4">Belum ada peserta yang mendaftar untuk jadwal ini.</div>
                        @endif
                    @endforeach
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
