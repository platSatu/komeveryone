@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Relasi Video & Pertanyaan</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.video_pertanyaan.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>

            <form action="{{ route('kom.video_pertanyaan.update', $videoPertanyaan->id) }}" method="POST">
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
                        <label for="kom_video_id">Pilih Video</label>
                        <select name="kom_video_id" class="form-control" required>
                            @foreach ($videos as $video)
                                <option value="{{ $video->id }}" {{ $videoPertanyaan->kom_video_id == $video->id ? 'selected' : '' }}>
                                    {{ $video->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pertanyaan_id">Pilih Pertanyaan</label>
                        <select name="pertanyaan_id" class="form-control" required>
                            @foreach ($pertanyaans as $pertanyaan)
                                <option value="{{ $pertanyaan->id }}" {{ $videoPertanyaan->pertanyaan_id == $pertanyaan->id ? 'selected' : '' }}>
                                    {{ $pertanyaan->pertanyaan }}
                                </option>
                            @endforeach
                        </select>
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
