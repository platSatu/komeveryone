@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Pertanyaan</h3>
                </div>

                <div class="card-body">
                    @if ($pertanyaans->isEmpty())
                        <div class="alert alert-info">
                            Belum ada pertanyaan yang diajukan.
                        </div>
                    @else
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Schedule</th>
                                    <th>Kelas</th>
                                    <th>Modul</th>
                                    <th>Rayon</th>
                                    <th>Pertanyaan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pertanyaans as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->user->username ?? '-' }}</td>
                                        <td>{{ $item->schedule->datetime ?? '-' }}</td>
                                        <td>{{ $item->kelas->name ?? '-' }}</td>
                                        <td>{{ $item->modul->title ?? '-' }}</td>
                                        <td>{{ $item->rayon->name ?? '-' }}</td>
                                        <td>{{ $item->pertanyaan }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tgl_pertanyaan)->format('d-m-Y') }}</td>
                                        <td>
                                            @if ($item->status === 'selesai')
                                                <span class="badge bg-success">Selesai</span>
                                            @else
                                                <span class="badge bg-warning">Menunggu</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="pagination justify-content-center mt-4">
                            {{ $pertanyaans->links('pagination::bootstrap-4') }}
                        </div>
                       
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
