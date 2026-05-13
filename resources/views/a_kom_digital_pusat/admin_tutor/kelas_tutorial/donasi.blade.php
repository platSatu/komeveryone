@extends('layouts.KomDigital')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Donasi Kelas Tutorial - {{ $kelasTutorial->kelas->name ?? 'Kelas' }} / {{ $kelasTutorial->modul->name ?? 'Modul' }}</h5>
    </div>
    <div class="card-body">
        @if($donasi->isEmpty())
            <p class="text-muted">Belum ada donasi yang masuk.</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Peserta</th>
                        <th>Jumlah Donasi</th>
                        <th>Status Transaksi</th>
                        <th>Status Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donasi as $index => $d)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $d->user->name ?? '-' }}</td>
                            <td>Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($d->transaction_status ?? '-') }}</td>
                            <td>
                                <span class="badge {{ $d->status_badge_class ?? '' }} text-white">
                                    {{ ucfirst($d->status ?? '-') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2"><strong>Total Donasi (Paid)</strong></td>
                        <td colspan="3"><strong>Rp {{ number_format($totalDonasiPaid, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
