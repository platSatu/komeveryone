@extends('layouts.KomDigital')

@section('content')
<div class="container py-4">
    <h1>Buat Zoom Meeting Baru</h1>

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kom.zoom.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email Host Zoom</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <label for="topic" class="form-label">Judul Meeting</label>
            <input type="text" class="form-control" id="topic" name="topic" value="{{ old('topic') }}" required>
        </div>

        <div class="mb-3">
            <label for="start_time" class="form-label">Waktu Mulai</label>
            <input type="datetime-local" class="form-control" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
        </div>

        <div class="mb-3">
            <label for="duration" class="form-label">Durasi (menit)</label>
            <input type="number" class="form-control" id="duration" name="duration" value="{{ old('duration', 60) }}" min="1">
        </div>

        <button type="submit" class="btn btn-success">Buat Meeting</button>
        <a href="{{ route('kom.zoom.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection