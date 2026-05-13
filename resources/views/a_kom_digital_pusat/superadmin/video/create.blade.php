@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Video</h3>
                    <div class="card-tools">
                        <a href="{{ route('kom.super_videos.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                    </div>
                </div>

                <form action="{{ route('kom.super_videos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
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
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Durasi</label>
                            <input type="text" name="durations" class="form-control" value="{{ old('durations') }}">
                        </div>


                        <div class="form-group">
                            <label>Urutan Video</label>
                            <input type="number" name="sort_video" class="form-control" value="{{ old('sort_video') }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Url Video</label>
                            <input type="text" name="url_video" class="form-control" value="{{ old('url_video') }}"
                                >
                        </div>

                        <div class="form-group">
                            <label>Pilih Modul</label>
                            <select name="modul_id" class="form-control" required>
                                <option value="">-- Pilih Modul --</option>
                                @foreach ($moduls as $modul)
                                    <option value="{{ $modul->id }}"
                                        {{ old('modul_id') == $modul->id ? 'selected' : '' }}>
                                        {{ $modul->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Video (Upload Video)</label>
                            <input type="file" name="video" class="form-control" accept="video/*">
                        </div>
                        <div class="form-group">
                            <label for="status">Status Modul</label>
                            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Nonaktif
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-primary" type="submit">Simpan Video</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
