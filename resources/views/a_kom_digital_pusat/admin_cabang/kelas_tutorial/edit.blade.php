@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Edit Jadwal Kelas Tutorial</h4>
                    <a href="{{ route('kom.kelas_tutor_cabang.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>

                <div class="card-body">
                    {{-- kom_v02: tampilkan pesan error validasi --}}
                    @if ($errors->any())
                        <div class="alert alert-danger mb-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                    @endif
                    <form action="{{ route('kom.kelas_tutor_cabang.update', Crypt::encryptString($data->id)) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Pengajar</label>
                            <select name="pengajar_id" class="form-control" required>
                                <option value="">-- Pilih Pengajar --</option>
                                @foreach ($pengajars as $pengajar)
                                    <option value="{{ $pengajar->id }}"
                                        {{ old('pengajar_id', $data->pengajar_id) == $pengajar->id ? 'selected' : '' }}>
                                        {{ $pengajar->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Kelas</label>
                            <select name="class_id" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $class)
                                    <option value="{{ $class->id }}"
                                        {{ old('class_id', $data->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->nama_kelas ?? $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Modul</label>
                            <select name="modul_id" id="modul_id" class="form-control" required>
                                <option value="">-- Pilih Modul --</option>
                                @foreach ($moduls as $modul)
                                    <option value="{{ $modul->id }}"
                                        {{ old('modul_id', $data->modul_id) == $modul->id ? 'selected' : '' }}>
                                        {{ $modul->nama_modul ?? $modul->name }}@if($modul->kelas) — {{ $modul->kelas->name }}@endif ({{ strtoupper($modul->language_code ?? 'id') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @include('a_kom_digital_pusat.admin_cabang.kelas_tutorial._titik_tutorial', [
                            'modulIdTerpilih' => $data->modul_id,
                            'videoIdTerpilih' => $data->video_id ?? '',
                        ])

                        <div class="form-group">
                            <label for="start_time" class="form-label">Waktu Mulai</label>
                            <input type="datetime-local" class="form-control" id="start_time" name="start_time"
                                value="{{ old('start_time', optional($data->start_time ? \Carbon\Carbon::parse($data->start_time) : null)->format('Y-m-d\TH:i')) }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Topic</label>
                            <input type="text" name="topic" class="form-control" value="{{ old('topic', $data->topic) }}">
                        </div>

                        <div class="form-group">
                            <label for="duration" class="form-label">Durasi (menit)</label>
                            <input type="number" class="form-control" id="duration" name="duration"
                                value="{{ old('duration', $data->duration ?? 60) }}" min="1">
                        </div>

                        <div class="form-group">
                            <label>Kuota</label>
                            <input type="number" name="quota" class="form-control" required
                                value="{{ old('quota', $data->quota) }}">
                        </div>

                        <div class="form-group">
                            <label>Status Kelas</label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status', $data->status) == 'active' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="inactive" {{ old('status', $data->status) == 'inactive' ? 'selected' : '' }}>
                                    Nonaktif
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $data->description) }}</textarea>
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" name="notifikasi" class="form-check-input" value="1"
                                {{ old('notifikasi', $data->notifikasi) ? 'checked' : '' }}>
                            <label class="form-check-label">Aktifkan Notifikasi</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Perbarui</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
