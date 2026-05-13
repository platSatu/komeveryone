@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Jawaban User atas Pertanyaan</h3>
                    <div class="card-tools">
                        <a href="{{ route('kom.user_jawaban_pertanyaan.create') }}" class="btn btn-primary btn-sm">+ Tambah
                            Jawaban</a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive b-0" data-pattern="priority-columns">
                        <table id="tech-companies-1" class="table table-striped text-truncate">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Pertanyaan</th>
                                    <th>Jawaban</th>
                                    <th>Benar?</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jawabans as $data)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->user->username ?? '-' }}</td>
                                        <td>{{ $data->pertanyaan->pertanyaan ?? '-' }}</td>
                                        <td>{{ $data->jawaban->jawaban ?? '-' }}</td>
                                        <td>{{ $data->jawaban_benar ? '✔️' : '❌' }}</td>
                                        <td>
                                            <a href="{{ route('kom.user_jawaban_pertanyaan.edit', $data->id) }}"
                                                class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('kom.user_jawaban_pertanyaan.destroy', $data->id) }}"
                                                method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                @if ($jawabans->isEmpty())
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada data</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                     <div class="pagination justify-content-center mt-4">
                        {{ $jawabans->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
