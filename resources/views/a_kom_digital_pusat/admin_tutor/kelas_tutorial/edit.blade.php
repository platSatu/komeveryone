@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Edit Jadwal Kelas Tutorial</h4>
                    <a href="{{ route('kom.kelas_tutor_diklat.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('kom.kelas_tutor_diklat.update', Crypt::encryptString($data->id)) }}" method="POST">
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
                            <label>Kategori Diklat</label>
                            <select name="category_diklat" class="form-control" required>
                                <option value="">-- Pilih Kategori Diklat --</option>
                                <option value="Doktrinal" {{ old('category_diklat', $data->category_diklat) == 'Doktrinal' ? 'selected' : '' }}>Doktrinal</option>
                                <option value="Praktikal" {{ old('category_diklat', $data->category_diklat) == 'Praktikal' ? 'selected' : '' }}>Praktikal</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Waktu Mulai</label>
                            <input type="datetime-local" name="start_time" class="form-control" required
                                value="{{ old('start_time', $data->start_time ? date('Y-m-d\TH:i', strtotime($data->start_time)) : '') }}">
                        </div>

                        <div class="form-group">
                            <label>Kuota</label>
                            <input type="number" name="quota" class="form-control" required
                                value="{{ old('quota', $data->quota) }}">
                        </div>

                        <div class="form-group">
                            <label>Status Kelas</label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status', $data->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $data->status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Topic</label>
                            <input type="text" name="topic" class="form-control"
                                value="{{ old('topic', $data->topic) }}">
                        </div>

                        <div class="mb-3">
                            <label for="duration" class="form-label">Durasi (menit)</label>
                            <input type="number" class="form-control" id="duration" name="duration"
                                value="{{ old('duration', $data->duration ?? 60) }}" min="1">
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" name="notifikasi" class="form-check-input" value="1"
                                {{ old('notifikasi', $data->notifikasi) ? 'checked' : '' }}>
                            <label class="form-check-label">Aktifkan Notifikasi</label>
                        </div>

                        <div class="form-group">
                            <label>No Whatsapp <small class="text-success">(Optional)</small></label>
                            <input type="numeric" name="no_whatsapp" class="form-control"
                                value="{{ old('no_whatsapp', $data->no_whatsapp) }}">
                        </div>

                        <div class="form-group">
                            <label>Email <small class="text-success">(Optional)</small></label>
                            <input type="email" name="email_cabang" class="form-control"
                                value="{{ old('email_cabang', $data->email_cabang) }}">
                        </div>

                        <button type="submit" class="btn btn-primary">Perbarui</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success') || session('error') || $errors->any())
        <script>
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: "{{ session('error') }}",
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: `<ul style="text-align:left;">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>`,
                    showConfirmButton: true
                });
            @endif
        </script>
    @endif
@endsection
