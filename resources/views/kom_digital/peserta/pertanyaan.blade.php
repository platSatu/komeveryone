@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pertanyaan Saya</h3>
                
            </div>

            <div class="card-body">
                @if($pertanyaan->isEmpty())
                    <div class="alert alert-info mb-0">
                        Belum ada pertanyaan yang diajukan.
                    </div>
                @else
                 <div class="table-responsive b-0" data-pattern="priority-columns">
                            <table id="tech-companies-1" class="table table-striped text-truncate">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Modul</th>
                                <th>Kelas</th>
                                <th>Rayon</th>
                                <th>Jadwal</th>
                                <th>Pertanyaan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pertanyaan as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->modul->title ?? '-' }}</td>
                                    <td>{{ $item->kelas->name ?? '-' }}</td>
                                    <td>{{ $item->rayon->name ?? '-' }}</td>
                                    <td>{{ $item->schedule->datetime ?? '-' }}</td>
                                    <td>{{ $item->pertanyaan }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tgl_pertanyaan)->format('d-m-Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->status === 'dijawab' ? 'success' : 'warning' }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                     <div class="pagination justify-content-center mt-4">
                        {{ $pertanyaan->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
