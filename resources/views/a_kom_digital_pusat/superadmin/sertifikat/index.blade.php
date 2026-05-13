@extends('layouts.KomDigital')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Sertifikat KOM</h3>
                    <div class="card-tools">
                        <a href="" class="btn btn-primary btn-sm">+ Tambah Sertifikat</a>
                    </div>
                </div>
                <div class="card-body">


                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>No Sertifikat</th>
                                <th>Nama</th>
                                <th>Tanggal Sertifikat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $item)
                                <tr>
                                    <td>{{ $data->firstItem() + $index }}</td>
                                    <td>{{ $item->no_certificate }}</td>
                                    <td>{{ $item->user->name ?? '-' }}</td> {{-- Menampilkan nama user --}}
                                    <td>{{ \Carbon\Carbon::parse($item->date_publish)->format('d-m-Y') }}</td>
                                    <td>
                                        @if ($item->status === 'active')
                                            <span class="badge bg-success text-white">{{ $item->status }}</span>
                                        @else
                                            <span class="badge bg-info">{{ $item->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('kom.super_e-sertifikat.show', $item->id) }}"
                                            class="btn btn-sm btn-info">Lihat</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data sertifikat.</td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>

                    <!-- Pagination -->
                    <div class="pagination justify-content-center mt-4">
                        {{ $data->withQueryString()->links('pagination::bootstrap-4') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
