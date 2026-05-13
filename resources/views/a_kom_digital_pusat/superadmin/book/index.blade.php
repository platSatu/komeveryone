@extends('layouts.KomDigital')

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <form method="GET" action="{{ route('kom.super_books.index') }}" class="d-flex">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control me-2"
                    placeholder="Cari berdasarkan kode buku..."
                />
                <button type="submit" class="btn btn-primary">Cari</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- ... isi card seperti sebelumnya ... -->
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pemilik Buku</th>
                                <th>Email</th>
                                <th>Kelas</th>
                                <th>Sertifikat</th>
                                <th>Kode Buku</th>
                                <th>Tanggal Aktivasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $index => $book)
                                <tr>
                                    <td>{{ $data->firstItem() + $index }}</td>
                                    <td>{{ optional($book->user)->name ?? 'N/A' }}</td>
                                    <td>{{ optional($book->user)->email ?? 'N/A' }}</td>
                                    {{-- <td>{{ $book->class_id ?? '-' }}</td> --}}
                                    <td>{{ $book->class->name ?? '-' }}</td>
                                    <td>{{ $book->certificate_id ?? '-' }}</td>
                                    <td>{{ $book->no_books }}</td>
                                    <td>{{ \Carbon\Carbon::parse($book->date_publish)->format('d-m-Y') ?? '-' }}</td>
                                    <td>
                                        @if($book->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('kom.super_books.edit', $book->id) }}" class="btn btn-warning btn-sm">Edit</a>

                                        <form action="{{ route('kom.super_books.destroy', $book->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus buku ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Data tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-center">
                        {{ $data->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection