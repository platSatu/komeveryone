@extends('layouts.KomDigital')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{ route('kom.super_jawaban.index') }}" method="GET">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-6">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari jawaban..." value="{{ request('search') }}" autocomplete="off">
                            </div>
                            <div class="col-8 col-md-4">
                                <select name="questions_id" class="form-select form-select-lg form-control">
                                    <option value="">-- Pilih Pertanyaan --</option>
                                    @foreach ($questions as $question)
                                        <option value="{{ $question->id }}"
                                            {{ request('questions_id') == $question->id ? 'selected' : '' }}>
                                            {{ \Illuminate\Support\Str::limit($question->questions, 50) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4 col-md-2 d-grid">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Cari</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h4 class="card-title mb-0">Daftar Jawaban</h4>

                    <div class="card-tools">
                        <a href="{{ route('kom.super_jawaban.create') }}" class="btn btn-primary btn-sm">+ Tambah
                            Jawaban</a>
                    </div>
                </div>

                <div class="card-body">

                    <div class="table-responsive b-0" data-pattern="priority-columns">
                        <table id="tech-companies-1" class="table table-striped text-truncate">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Pertanyaan</th>
                                    <th>Jawaban</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $perPage = $data->perPage(); // jumlah item per halaman, misal 10
                                    $currentPage = $data->currentPage(); // halaman saat ini
                                    $no = ($currentPage - 1) * $perPage + 1;
                                @endphp
                                @foreach ($data->groupBy('questions_id') as $questionId => $answers)
                                    @php $rowCount = $answers->count(); @endphp
                                    @foreach ($answers as $index => $answer)
                                        <tr>
                                            <td>{{ $no }}</td>

                                            @if ($index == 0)
                                                <td rowspan="{{ $rowCount }}">
                                                    {{ $answer->question->questions ?? 'Pertanyaan tidak ditemukan' }}
                                                </td>
                                            @endif

                                            <td>{{ $answer->answer }}</td>
                                            <td>
                                                @if ($answer->is_true)
                                                    <span class="badge bg-success text-white">Benar</span>
                                                @else
                                                    <span class="badge bg-danger text-white">Salah</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('kom.super_jawaban.edit', $answer->id) }}"
                                                    class="btn btn-sm btn-warning">Edit</a>
                                                <form action="{{ route('kom.super_jawaban.destroy', $answer->id) }}"
                                                    method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button onclick="return confirm('Yakin ingin hapus jawaban?')"
                                                        class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @php $no++; @endphp
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination justify-content-center mt-4">
                        {{ $data->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
