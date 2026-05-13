@extends('layouts.kom')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <h4>Total Donasi Terverifikasi: 
                <strong>Rp {{ number_format($totalDonasi, 0, ',', '.') }}</strong>
            </h4>

            @forelse ($groupedDonasi as $group)
                <div class="card mt-4">
                    <div class="card-header">
                        <strong>Jadwal:</strong> 
                        {{ $group['schedule']->komModul->title ?? '-' }} -
                        {{ $group['schedule']->komClass->name ?? '-' }} |
                        {{ \Carbon\Carbon::parse($group['schedule']->datetime)->format('d M Y H:i') }}<br>
                        <small>
                            <strong>Total Donasi Paid (Jadwal Ini):</strong> 
                             Rp {{ number_format($totalDonasi, 0, ',', '.') }}
                        </small>
                    </div>
                    <div class="card-body">
                        @if ($group['donasi']->isEmpty())
                            <p>Tidak ada donasi untuk jadwal ini.</p>
                        @else
                        <div class="table-responsive b-0" data-pattern="priority-columns">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group['donasi'] as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->komUser->username ?? '-' }}</td>
                                            <td>{{ $item->virtual_account_email ?? '-' }}</td>
                                            <td>Rp {{ number_format($item->donate , 0, ',', '.') }}</td>
                                            <td>
                                                <span class="badge badge-{{ $item->payment_status === 'paid' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($item->payment_status) }}
                                                </span>
                                            </td>
                                            <td> {{ $item -> created_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="alert alert-info mt-4">
                    Belum ada data donasi yang tersedia untuk rayon Anda.
                </div>
            @endforelse
        </div>
    </div>

@endsection
