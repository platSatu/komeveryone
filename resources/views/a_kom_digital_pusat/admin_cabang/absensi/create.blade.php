@extends('layouts.KomDigital')

@section('content')
<div class="container mt-4">
    <h3>Input Absensi Kelas Tutorial</h3>

    <form action="{{ route('admin-cabang.absensi.store') }}" method="POST">
        @csrf

        <!-- Pilih Kelas Tutorial -->
        <div class="form-group mb-3">
            <label for="userclasstutorial_id">Kelas Tutorial</label>
            <select name="userclasstutorial_id" id="userclasstutorial_id" class="form-control" required>
                <option value="">-- Pilih Kelas Tutorial --</option>
                @foreach($kelasTutorial as $kelas)
                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas ?? 'Kelas ' . $kelas->id }}</option>
                @endforeach
            </select>
        </div>

        <hr>

        <!-- Daftar User -->
        <h5>Daftar Peserta</h5>

        @foreach($users as $user)
        <div class="card mb-2">
            <div class="card-body">
                <p><strong>{{ $user->name }}</strong> ({{ $user->email }})</p>
                <input type="hidden" name="users[{{ $loop->index }}][user_id]" value="{{ $user->id }}">
                
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="users[{{ $loop->index }}][status_hadir]" id="hadir_{{ $user->id }}" value="hadir" checked>
                    <label class="form-check-label" for="hadir_{{ $user->id }}">Hadir</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="users[{{ $loop->index }}][status_hadir]" id="tidak_hadir_{{ $user->id }}" value="tidak_hadir">
                    <label class="form-check-label" for="tidak_hadir_{{ $user->id }}">Tidak Hadir</label>
                </div>
            </div>
        </div>
        @endforeach

        <button type="submit" class="btn btn-primary mt-3">Simpan Absensi</button>
    </form>
</div>
@endsection
