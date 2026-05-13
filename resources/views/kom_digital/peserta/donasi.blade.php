@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Riwayat Donasi Saya</h3>
            </div>

            <div class="card-body">
                @if($donasi->isEmpty())
                    <div class="alert alert-info mb-0">
                        Belum ada data donasi.
                    </div>
                @else
                <div class="table-responsive b-0" data-pattern="priority-columns">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Modul</th>
                                <th>Rayon</th>
                                <th>Jadwal Kelas Tutorial</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($donasi as $index => $item)
                                <tr>
                                    <td>{{ $index + $donasi->firstItem() }}</td>
                                    <td>{{ $item->komModul->title ?? '-' }}</td>
                                    <td>{{ $item->rayon->name ?? '-' }}</td>
                                    <td>{{ $item->komSchedule->datetime ?? '-' }}</td>
                                    <td>Rp{{ number_format($item->donate, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->payment_status === 'paid' ? 'success' : 'warning' }}">
                                            {{ ucfirst(strtolower($item->payment_status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                     <div class="pagination justify-content-center mt-4">
                        {{ $donasi->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
