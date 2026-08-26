@extends('layouts.KomDigital')
@section('content')

<div class="container-fluid">
    {{-- Judul Halaman --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Rekening</h1>
        <a href="{{ route('kom.super_daftar-rekening.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm text-white"></i> Tambah Rekening
        </a>
    </div>

    {{-- Filter Form --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('kom.super_daftar-rekening.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="user_id">User</label>
                            <select name="user_id" id="user_id" class="form-control">
                                <option value="">-- Semua User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ isset($filters['user_id']) && $filters['user_id'] == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cabang_id">Cabang</label>
                            <select name="cabang_id" id="cabang_id" class="form-control">
                                <option value="">-- Semua Cabang --</option>
                                @foreach($cabangs as $cabang)
                                    <option value="{{ $cabang->id }}" {{ isset($filters['cabang_id']) && $filters['cabang_id'] == $cabang->id ? 'selected' : '' }}>
                                        {{ $cabang->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nama_rek">Nama Rekening</label>
                            <input type="text" name="nama_rek" id="nama_rek" class="form-control" 
                                   placeholder="Cari nama rekening..." 
                                   value="{{ $filters['nama_rek'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
                @if(request()->hasAny(['user_id', 'cabang_id', 'nama_rek']))
                    <div class="mt-2">
                        <a href="{{ route('kom.super_daftar-rekening.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-times"></i> Reset Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Rekening</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama Rekening</th>
                            <th>Nomor Rekening</th>
                            <th>Nama Bank</th>
                            <th>Cabang</th>
                            <th>User</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $key => $rekening)
                            <tr>
                                <td class="text-center">{{ $data->firstItem() + $key }}</td>
                                <td>{{ $rekening->nama_rek ?? '-' }}</td>
                                <td>{{ $rekening->no_rek ?? '-' }}</td>
                                <td>{{ $rekening->nama_bank ?? '-' }}</td>
                                <td>{{ $rekening->cabang_bank ?? '-' }}</td>
                                <td>{{ $rekening->user->name ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('kom.super_daftar-rekening.show', $rekening->id) }}" 
                                           class="btn btn-info btn-sm" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('kom.super_daftar-rekening.edit', $rekening->id) }}" 
                                           class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('kom.super_daftar-rekening.destroy', $rekening->id) }}" 
                                              class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="alert alert-secondary mb-0">
                                        <i class="fas fa-info-circle"></i> Data tidak ditemukan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($data->hasPages())
                <div class="mt-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center flex-wrap">
                            <li class="page-item {{ $data->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $data->previousPageUrl() }}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            
                            @php
                                $currentPage = $data->currentPage();
                                $lastPage = $data->lastPage();
                                $start = max(1, $currentPage - 2);
                                $end = min($lastPage, $currentPage + 2);
                            @endphp
                            
                            @if($start > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $data->url(1) }}">1</a>
                                </li>
                                @if($start > 2)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endif
                            
                            @for($i = $start; $i <= $end; $i++)
                                <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $data->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor
                            
                            @if($end < $lastPage)
                                @if($end < $lastPage - 1)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link" href="{{ $data->url($lastPage) }}">{{ $lastPage }}</a>
                                </li>
                            @endif
                            
                            <li class="page-item {{ !$data->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $data->nextPageUrl() }}" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
                
                <div class="text-center mt-2">
                    <small class="text-muted">
                        Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} 
                        dari {{ $data->total() }} data
                    </small>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection