@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Jawaban</h3>
                    <div class="card-tools">
                        <a href="{{ route('kom.jawaban.create') }}" class="btn btn-primary btn-sm">+ Tambah Jawaban</a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
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
                                    $no = ($pertanyaanJawaban->currentPage() - 1) * $pertanyaanJawaban->perPage() + 1;
                                @endphp

                                @foreach ($pertanyaanJawaban as $group)
                                    @php
                                        $rowspan = $group['jawabans']->count();
                                    @endphp
                                    @foreach ($group['jawabans'] as $index => $jawaban)
                                        <tr>
                                            @if ($index === 0)
                                                <td rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                                                <td rowspan="{{ $rowspan }}">{{ $group['pertanyaan']->pertanyaan }}</td>
                                            @endif
                                            <td>{{ $jawaban->jawaban }}</td>
                                            <td>{{ $jawaban->is_benar ? 'Benar' : 'Salah' }}</td>
                                            <td>
                                                <a href="{{ route('kom.jawaban.edit', $jawaban->id) }}"
                                                    class="btn btn-warning btn-sm">Edit</a>
                                                <form action="{{ route('kom.jawaban.destroy', $jawaban->id) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    <div class="pagination justify-content-center mt-4">
                        {{ $pertanyaanJawaban->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection