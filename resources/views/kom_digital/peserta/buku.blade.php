@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">e-Book</h3>
            </div>

            <div class="card-body">
                @if($buku->isEmpty())
                    <div class="alert alert-info mb-0">
                        Belum ada data buku.
                    </div>
                @else
                 <div class="table-responsive b-0" data-pattern="priority-columns">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode Buku</th>
                                <th>Kelas</th>
                                <th>Tanggal Aktifasi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($buku as $index => $item)
                                <tr>
                                    <td>{{ $index + $buku->firstItem() }}</td>
                                    <td>{{ $item->kode_buku }}</td>
                                    <td>{{ $item->komClass->name ?? '-' }}</td>
                                    <td>{{ $item->tgl_aktifasi ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->status === 'aktif' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
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
