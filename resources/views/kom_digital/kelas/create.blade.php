@extends('layouts.be')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Buat Kelas Tutorial</h3>
                <div class="card-tools">
                    <a href="{{ route('kom_classes.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('kom_classes.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="judul_kelas">Judul Kelas</label>
                        <input type="text" name="judul_kelas" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="hari">Hari</label>
                        <input type="text" name="hari" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="jam">Jam</label>
                        <input type="time" name="jam" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="kuota">Kuota</label>
                        <input type="number" name="kuota" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="zoom_link">Link Zoom</label>
                        <input type="url" name="zoom_link" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
