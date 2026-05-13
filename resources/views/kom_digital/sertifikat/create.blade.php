@extends('layouts.be')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Buat Sertifikat KOM</h3>
                <div class="card-tools">
                    <a href="{{ route('kom_certificates.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('kom_certificates.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="user_id">Siswa</label>
                        <select name="user_id" class="form-control" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kode_sertifikat">Kode Sertifikat</label>
                        <input type="text" name="kode_sertifikat" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_kelulusan">Tanggal Kelulusan</label>
                        <input type="date" name="tanggal_kelulusan" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Terbitkan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
