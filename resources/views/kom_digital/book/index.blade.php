@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Buku KOM</h3>
                    <div class="card-tools">
                        <a href="{{ route('kom.kode_buku.create') }}" class="btn btn-primary btn-sm">+ Tambah Buku</a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pemilik Buku</th>
                                <th>Kelas</th>
                                <th>Sertifikat</th>
                                <th>Kode Buku</th>
                                <th>Tanggal Aktivasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($books as $book)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $book->komUser->username ?? 'N/A' }}</td>
                                    <td>{{ $book->komClass->name ?? 'N/A' }}</td>
                                    <td>{{ $book->komCertificate->no_sertifikat ?? 'N/A' }}</td>
                                    <td>{{ $book->kode_buku }}</td>
                                    <td>{{ \Carbon\Carbon::parse($book->tgl_aktifasi)->format('d-m-Y') }}</td>
                                    <td>
                                        <!-- Jika status adalah 1, tampilkan 'Aktif', jika 0 tampilkan 'Belum Aktif' -->
                                        {{ $book->status }}
                                    </td>

                                    <td>
                                          <form action="{{ route('kom.kode_buku.update_status', $book->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status"
                                                value="{{ $book->status == 'aktif' ? 'belum_aktif' : 'aktif' }}">
                                            <!-- Kondisi untuk menampilkan tombol berdasarkan status -->
                                            @if ($book->status == 'belum_aktif')
                                                <!-- Jika statusnya belum aktif, tampilkan tombol untuk mengaktifkan -->
                                                <button type="submit" class="btn btn-success btn-sm">Aktifkan Buku</button>
                                            @elseif($book->status == 'aktif')
                                                <!-- Jika statusnya aktif, tampilkan tombol untuk menonaktifkan -->
                                                <button type="submit" class="btn btn-danger btn-sm">Nonaktifkan
                                                    Buku</button>
                                            @endif
                                        </form>
                                    </td>


                                    <td>
                                        <!-- Misalnya kita punya tombol untuk mengubah status -->
                                      

                                        <a href="{{ route('kom.kode_buku.edit', $book->id) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('kom.kode_buku.destroy', $book->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($books->isEmpty())
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada data</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-center">
                        {{-- {{ $books->links() }} --}}
                        {{ $books->withQueryString()->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
