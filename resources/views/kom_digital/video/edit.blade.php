@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Video</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.video.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>

            <form action="{{ route('kom.video.update', $video->id) }}" method="POST">
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

                    <div class="form-group">
                        <label>Judul Video</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $video->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control">{{ old('description', $video->description) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>URL Video</label>
                        <input type="text" name="url_video" class="form-control" value="{{ old('url_video', $video->url_video) }}">
                    </div>

                    <div class="form-group">
                        <label>Durasi</label>
                        <input type="text" name="durasi_video" class="form-control" value="{{ old('durasi_video', $video->durasi_video) }}">
                    </div>

                    <div class="form-group">
                        <label>Urutan Video</label>
                        <input type="number" name="urutan_video" class="form-control" value="{{ old('urutan_video', $video->urutan_video) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Pilih Modul</label>
                        <select name="kom_modul_id" class="form-control" required>
                            <option value="">-- Pilih Modul --</option>
                            @foreach ($moduls as $modul)
                                <option value="{{ $modul->id }}" {{ old('kom_modul_id', $video->kom_modul_id) == $modul->id ? 'selected' : '' }}>
                                    {{ $modul->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" name="status" value="1" class="form-check-input" id="status_video" {{ old('status', $video->status) ? 'checked' : '' }}>
                        <label for="status_video" class="form-check-label">Aktifkan Video</label>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <button class="btn btn-primary" type="submit">Update Video</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
