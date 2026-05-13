@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kelas Tutorial Saya</h3>
                </div>

                <div class="card-body">
                    @if ($kelas->isEmpty())
                        {{-- Kondisi 1: Tidak ada data --}}
                        <div class="alert alert-info mb-0">
                            Belum ada kelas yang diikuti. 
                        </div>
                    @else
                        @php
                            $kelas = collect($kelas); // pastikan Collection
                            $allPending = $kelas->isNotEmpty() &&
                                          $kelas->every(fn($item) => strtolower($item->status) === 'pending');
                        @endphp

                        @if ($allPending)
                            {{-- Kondisi 2: Semua kelas pending --}}
                            <div class="alert alert-warning">
                                <h5>Kelas tutorial Anda telah dibatalkan. Silakan pilih kelas tutorial Anda
                                    <a href="{{ route('kom.kelas_tutor.index') }}">di sini</a>.<br>
                                    Donasi Anda akan diteruskan ke kelas tutorial yang Anda pilih selanjutnya.
                                </h5>
                            </div>
                        @else
                            {{-- Kondisi 3: Ada kelas aktif --}}
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Modul</th>
                                        <th>Kelas</th>
                                        <th>Rayon</th>
                                        <th>Jadwal</th>
                                        <th>Tanggal Booking</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kelas as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->modul->title ?? '-' }}</td>
                                            <td>{{ $item->kelas->name ?? '-' }}</td>
                                            <td>{{ $item->rayon->name ?? '-' }}</td>
                                            <td>{{ $item->schedule->datetime ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tgl_booking)->format('d-m-Y') }}</td>
                                            <td>
                                                @if (strtolower($item->status) === 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                    <p style="margin-top:5px; color:red;">
                                                        Kelas dibatalkan, silakan pilih ulang
                                                        <a href="{{ route('kom.kelas_tutor.index') }}">di sini</a>.
                                                    </p>
                                                @else
                                                    <span class="badge badge-success">Aktif</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
