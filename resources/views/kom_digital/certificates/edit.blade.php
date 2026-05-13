@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Sertifikat KOM</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.kom_certificates.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
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

                <form action="{{ route('kom.kom_certificates.update', $certificate->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="no_sertifikat">No Sertifikat</label>
                        <input type="text" name="no_sertifikat" class="form-control" value="{{ $certificate->no_sertifikat }}" required>
                    </div>

                    <div class="form-group">
                        <label for="tgl_sertifikat">Tanggal Sertifikat</label>
                        <input type="date" name="tgl_sertifikat" class="form-control" value="{{ $certificate->tgl_sertifikat }}" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status Sertifikat</label>
                        <select name="status" class="form-control" required>
                            <option value="1" {{ $certificate->status == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ $certificate->status == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
