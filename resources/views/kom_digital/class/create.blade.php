@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tambah Kelas KOM</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.kom_class.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Ups!</strong> Ada masalah pada input Anda.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('kom.kom_class.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama Kelas</label>
                        <input type="text" name="name" class="form-control" placeholder="Masukkan nama kelas" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status Kelas</label>
                        <select name="status" class="form-control" required>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
