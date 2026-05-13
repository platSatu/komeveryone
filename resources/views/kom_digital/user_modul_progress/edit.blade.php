@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Progress User</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.user_modul_progress.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>

            <form action="{{ route('kom.user_modul_progress.update', $progress->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">

                    <div class="form-group">
                        <label for="kom_user_id">User</label>
                        <select name="kom_user_id" class="form-control" required>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ $progress->kom_user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kom_modul_id">Modul</label>
                        <select name="kom_modul_id" class="form-control" required>
                            @foreach ($moduls as $modul)
                                <option value="{{ $modul->id }}" {{ $progress->kom_modul_id == $modul->id ? 'selected' : '' }}>
                                    {{ $modul->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="last_video_id">Video Terakhir Ditonton</label>
                        <select name="last_video_id" class="form-control">
                            <option value="">-- Tidak Ada --</option>
                            @foreach ($videos as $video)
                                <option value="{{ $video->id }}" {{ $progress->last_video_id == $video->id ? 'selected' : '' }}>
                                    {{ $video->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="video_terakhir">Urutan Video Terakhir</label>
                        <input type="number" name="video_terakhir" class="form-control" value="{{ $progress->video_terakhir }}" min="0" required>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <button class="btn btn-primary" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
