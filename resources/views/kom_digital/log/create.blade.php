@extends('layouts.be')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tambah Log Aktivitas</h3>
                <div class="card-tools">
                    <a href="{{ route('kom_activity_logs.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('kom_activity_logs.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="user_id">User</label>
                        <select name="user_id" class="form-control" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="aktivitas">Aktivitas</label>
                        <textarea name="aktivitas" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Log</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
