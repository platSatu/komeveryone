@extends('layouts.be')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pembayaran Siswa</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead><tr><th>#</th><th>Siswa</th><th>Total</th><th>Status</th><th>Metode</th><th>Tanggal</th></tr></thead>
            <tbody>
                @foreach($payments as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p->user->username }}</td>
                    <td>{{ number_format($p->total) }}</td>
                    <td>{{ $p->status }}</td>
                    <td>{{ $p->metode }}</td>
                    <td>{{ $p->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
                @if($payments->isEmpty())<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>@endif
            </tbody>
        </table>
    </div>
</div>
@endsection
