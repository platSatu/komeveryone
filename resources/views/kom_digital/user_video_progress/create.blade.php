@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tambah Progress Video User</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.user_video_progress.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>

            <form action="{{ route('kom.user_video_progress.store') }}" method="POST">
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
                        <label for="kom_user_id">Pilih User</label>
                        <select name="kom_user_id" class="form-control" required>
                            <option value="">-- Pilih User --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('kom_user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kom_video_id">Pilih Video</label>
                        <select name="kom_video_id" class="form-control" required>
                            <option value="">-- Pilih Video --</option>
                            @foreach ($videos as $video)
                                <option value="{{ $video->id }}" {{ old('kom_video_id') == $video->id ? 'selected' : '' }}>{{ $video->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" name="is_locked" value="1" class="form-check-input" id="is_locked"
                            {{ old('is_locked', true) ? 'checked' : '' }}>
                        <label for="is_locked" class="form-check-label">Terkunci?</label>
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
