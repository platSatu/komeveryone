@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-12">
            {{-- Notifikasi Success --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                   
                </div>
            @endif

            {{-- Notifikasi Error --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Daftar Kelas KOM</h4>
                    <a href="{{ route('kom.super_kelas-kom.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Kelas
                    </a>
                </div>
                <div class="card-body">
                    {{-- Filter Form --}}
                    <form method="GET" action="{{ route('kom.super_kelas-kom.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari nama kelas..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary">Filter</button>
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kelas</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classList as $index => $item)
                                    <tr>
                                        <td>{{ $classList->firstItem() + $index }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            <span class="badge {{ $item->status ? 'bg-success' : 'bg-danger' }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('kom.super_kelas-kom.show', $item->encrypted_id) }}" 
                                                   class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                                <a href="{{ route('kom.super_kelas-kom.edit', $item->encrypted_id) }}" 
                                                   class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('kom.super_kelas-kom.destroy', $item->encrypted_id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('Yakin ingin menghapus kelas {{ $item->name }}?')">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data kelas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center">
                        {{ $classList->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection