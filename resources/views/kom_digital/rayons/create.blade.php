@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Rayon</h3>
                    <div class="card-tools">
                        <a href="{{ route('kom.rayons.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('kom.rayons.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Nama Rayon</label>
                            <input type="text" name="name" class="form-control" required
                                placeholder="Masukkan nama rayon">
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" class="form-control">
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
