@extends('layouts.KomDigital')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Pertanyaan</h4>
                    <div class="card-tools">
                        <a href="{{ route('kom.super_pertanyaan.import_form') }}" class="btn btn-primary btn-sm">+ Tambah Import</a>
                        <a href="{{ route('kom.super_pertanyaan.create') }}" class="btn btn-primary btn-sm">+ Tambah Pertanyaan</a>
                    </div>
                </div>

                <div class="card-body">

                    {{-- Form Pencarian --}}
                    <form action="{{ route('kom.super_pertanyaan.index') }}" method="GET" class="mb-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-10">
                                <input 
                                    type="text" 
                                    name="search" 
                                    class="form-control form-control-lg" 
                                    placeholder="Cari pertanyaan..." 
                                    value="{{ request('search') }}" 
                                    autocomplete="off"
                                >
                            </div>
                            <div class="col-12 col-md-2 d-grid">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Cari</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive b-0" data-pattern="priority-columns">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th>Pertanyaan</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $index => $question)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $question->questions }}</td>
                                    <td>
                                        <span class="badge badge-{{ $question->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($question->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('kom.super_pertanyaan.edit', $question->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('kom.super_pertanyaan.destroy', $question->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pertanyaan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data pertanyaan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>

                <div class="pagination justify-content-center mt-4">
                    {{ $data->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>
    </div>
@endsection