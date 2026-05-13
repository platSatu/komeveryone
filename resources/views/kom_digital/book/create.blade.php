@extends('layouts.kom')

@section('content')
    <div class="row">

        <div class="col-12">
            <!-- Menampilkan Pesan Success/Error -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Buku KOM</h3>
                    <div class="card-tools">
                        <a href="{{ route('kom.kode_buku.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('kom.kode_buku.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="kom_class_id">Pilih Kelas</label>
                            <select name="kom_class_id" class="form-control" required>
                                @foreach ($komClasses as $komClass)
                                    <option value="{{ $komClass->id }}">{{ $komClass->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="kode_buku">Kode Buku</label>
                            <input type="text" name="kode_buku" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="0">Belum Aktif</option>
                                <option value="1">Aktif</option>

                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
