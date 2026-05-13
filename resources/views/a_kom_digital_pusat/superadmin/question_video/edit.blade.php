@extends('layouts.KomDigital')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Edit Relasi Video & Pertanyaan</h4>
                <div class="card-tools">
                    <a href="{{ route('kom.super_pertanyaan-video.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>

            <form action="{{ route('kom.super_pertanyaan-video.update', $data->id) }}" method="POST">

                @csrf
                @method('PUT')
                <div class="card-body">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="border p-3 mb-3">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Pilih Video</label>
                                <select name="videos_id" class="form-control" required>
                                    <option value="">-- Pilih Video --</option>
                                    @foreach ($videos as $video)
                                        <option value="{{ $video->id }}" {{ $data->videos_id == $video->id ? 'selected' : '' }}>
                                            {{ $video->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Pilih Pertanyaan</label>
                                <select name="questions_id" class="form-control" required>
                                    <option value="">-- Pilih Pertanyaan --</option>
                                    @foreach ($questions as $pertanyaan)
                                        <option value="{{ $pertanyaan->id }}" {{ $data->questions_id == $pertanyaan->id ? 'selected' : '' }}>
                                            {{ $pertanyaan->questions }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="active" {{ $data->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ $data->status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer text-right">
                    <button class="btn btn-primary" type="submit">Update Relasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
