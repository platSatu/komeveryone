@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tambah Progress User</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.user_modul_progress.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>

            <form action="{{ route('kom.user_modul_progress.store') }}" method="POST">
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
                        <label for="kom_user_id">User</label>
                        <select name="kom_user_id" class="form-control" required>
                            <option value="">-- Pilih User --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kom_modul_id">Modul</label>
                        <select name="kom_modul_id" class="form-control" required>
                            <option value="">-- Pilih Modul --</option>
                            @foreach ($moduls as $modul)
                                <option value="{{ $modul->id }}">{{ $modul->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="last_video_id">Video Terakhir Ditonton</label>
                        <select name="last_video_id" class="form-control">
                            <option value="">-- Pilih Video --</option>
                            @foreach ($videos as $video)
                                <option value="{{ $video->id }}">{{ $video->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="video_terakhir">Urutan Video Terakhir</label>
                        <input type="number" name="video_terakhir" class="form-control" value="{{ old('video_terakhir', 0) }}" min="0" required>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
