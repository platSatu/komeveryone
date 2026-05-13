@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan!</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Tambah Jadwal Kelas</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('kom.schedules.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kom_user_id" value="{{ auth('kom')->id() }}">
                        <input type="hidden" name="rayon_id" value="{{ auth('kom')->user()->rayon_id }}">

                        <div class="form-group">
                            <label for="pengajar_id">Pengajar</label>
                            <select name="pengajar_id" id="pengajar_id" class="form-control" required>
                                <option value="">-- Pilih Pengajar --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('pengajar_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->username }} {{-- Bisa diganti ke nama jika ada --}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="kom_class_id">Kelas</label>
                            <select name="kom_class_id" id="kom_class_id" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('kom_class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="kom_modul_id">Modul</label>
                            <select name="kom_modul_id" id="kom_modul_id" class="form-control" required>
                                <option value="">-- Pilih Modul --</option>
                                @foreach ($moduls as $modul)
                                    <option value="{{ $modul->id }}" {{ old('kom_modul_id') == $modul->id ? 'selected' : '' }}>
                                        {{ $modul->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="datetime">Tanggal & Waktu</label>
                            <input type="datetime-local" name="datetime" id="datetime" class="form-control" value="{{ old('datetime') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="quota">Kuota</label>
                            <input type="number" name="quota" id="quota" class="form-control" value="{{ old('quota') }}" required min="0">
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status_schedule" class="form-control" required>
                                <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="{{ route('kom.schedules.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
