@extends('layouts.KomDigital')
@section('content')
    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="mb-4">Edit Jadwal Kelas Tutorial</h3>

                    {{-- Tampilkan error validasi --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('kom.super_kelas_tutorial.update', Crypt::encrypt($item->id)) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- User --}}
                        <div class="mb-3">
                            <label for="user_id" class="form-label">User  </label>
                            <select name="user_id" id="user_id" class="form-select form-control" required>
                                <option value="">-- Pilih User --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ (old('user_id', $item->user_id) == $user->id) ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Rayon --}}
                        <div class="mb-3">
                            <label for="rayon_id" class="form-label">Rayon</label>
                            <select name="rayon_id" id="rayon_id" class="form-select form-control" required>
                                <option value="">-- Pilih Rayon --</option>
                                @foreach ($rayons as $rayon)
                                    <option value="{{ $rayon->id }}"
                                        {{ (old('rayon_id', $item->rayon_id) == $rayon->id) ? 'selected' : '' }}>
                                        {{ $rayon->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pengajar --}}
                        <div class="mb-3">
                            <label for="pengajar_id" class="form-label">Pengajar</label>
                            <select name="pengajar_id" id="pengajar_id" class="form-select form-control" required>
                                <option value="">-- Pilih Pengajar --</option>
                                @foreach ($pengajars as $pengajar)
                                    <option value="{{ $pengajar->id }}"
                                        {{ (old('pengajar_id', $item->pengajar_id) == $pengajar->id) ? 'selected' : '' }}>
                                        {{ $pengajar->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kelas --}}
                        <div class="mb-3">
                            <label for="class_id" class="form-label">Kelas</label>
                            <select name="class_id" id="class_id" class="form-select form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($classes as $k)
                                    <option value="{{ $k->id }}"
                                        {{ (old('class_id', $item->class_id) == $k->id) ? 'selected' : '' }}>
                                        {{ $k->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Modul --}}
                        <div class="mb-3">
                            <label for="modul_id" class="form-label">Modul</label>
                            <select name="modul_id" id="modul_id" class="form-select form-control" required>
                                <option value="">-- Pilih Modul --</option>
                                @foreach ($moduls as $modul)
                                    <option value="{{ $modul->id }}"
                                        {{ (old('modul_id', $item->modul_id) == $modul->id) ? 'selected' : '' }}>
                                        {{ $modul->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Quota --}}
                        <div class="mb-3">
                            <label for="quota" class="form-label">Kuota</label>
                            <input type="number" name="quota" id="quota" class="form-control"
                                value="{{ old('quota', $item->quota) }}" min="1" required>
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="active" {{ (old('status', $item->status) == 'active') ? 'selected' : '' }}>
                                    Aktif
                                </option>
                                <option value="inactive"
                                    {{ (old('status', $item->status) == 'inactive') ? 'selected' : '' }}>
                                    Nonaktif
                                </option>
                            </select>
                        </div>

                        {{-- Notifikasi --}}
                        <div class="mb-3 form-check">
                            <input type="hidden" name="notifikasi" value="0">
                            <input type="checkbox" name="notifikasi" id="notifikasi" class="form-check-input" value="1"
                                {{ old('notifikasi', $item->notifikasi) ? 'checked' : '' }}>
                            <label for="notifikasi" class="form-check-label">Aktifkan Notifikasi</label>
                        </div>

                        {{-- Topic --}}
                        <div class="mb-3">
                            <label for="topic" class="form-label">Topic</label>
                            <input type="text" name="topic" id="topic" class="form-control"
                                value="{{ old('topic', $item->topic) }}">
                        </div>

                        {{-- Start Time --}}
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="datetime-local" name="start_time" id="start_time" class="form-control"
                                value="{{ old('start_time', \Carbon\Carbon::parse($item->start_time)->format('Y-m-d\TH:i')) }}"
                                required>
                        </div>

                        {{-- Host Email --}}
                        <div class="mb-3">
                            <label for="host_email" class="form-label">Host Email</label>
                            <input type="email" name="host_email" id="host_email" class="form-control"
                                value="{{ old('host_email', $item->host_email) }}">
                        </div>

                        {{-- No Whatsapp --}}
                        <div class="mb-3">
                            <label for="no_whatsapp" class="form-label">No Whatsapp</label>
                            <input type="text" name="no_whatsapp" id="no_whatsapp" class="form-control"
                                value="{{ old('no_whatsapp', $item->no_whatsapp) }}">
                        </div>

                        {{-- Email Cabang --}}
                        <div class="mb-3">
                            <label for="email_cabang" class="form-label">Email Cabang</label>
                            <input type="email" name="email_cabang" id="email_cabang" class="form-control"
                                value="{{ old('email_cabang', $item->email_cabang) }}">
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('kom.super_kelas_tutorial.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection