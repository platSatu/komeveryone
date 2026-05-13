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
                            <select name="modul_id" class="form-control" required>
                                <option value="">-- Pilih Modul --</option>
                                @foreach ($moduls as $modul)
                                    <option value="{{ $modul->id }}"
                                        {{ old('modul_id', $data->modul_id) == $modul->id ? 'selected' : '' }}>
                                        {{ $modul->nama_modul ?? $modul->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Kelas</label>
                            <input type="date" name="date_class" class="form-control" required
                                value="{{ old('date_class', $data->date_class) }}">
                        </div>

                        <div class="form-group">
                            <label>Jam Kelas</label>
                            <input type="time" name="time_class" class="form-control" required
                                value="{{ old('time_class', $data->time_class) }}">
                        </div>

                        <div class="form-group">
                            <label>Kuota</label>
                            <input type="number" name="quota" class="form-control" required
                                value="{{ old('quota', $data->quota) }}">
                        </div>

                        <div class="form-group">
                            <label>Status Kelas</label>
                            <select name="status" class="form-control" required>
                                <option value="aktif" {{ old('status', $data->status) == 'aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="nonaktif" {{ old('status', $data->status) == 'nonaktif' ? 'selected' : '' }}>
                                    Nonaktif
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Link Zoom</label>
                            <input type="url" name="link_zoom" class="form-control"
                                value="{{ old('link_zoom', $data->link_zoom) }}">
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
