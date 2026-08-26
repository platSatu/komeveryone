@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-12">
         @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Tambah Jadwal Kelas Tutorial</h4>
                    <a href="{{ route('kom.kelas_tutor_cabang.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('kom.kelas_tutor_cabang.store') }}" method="POST">
                        @csrf

                        {{-- <div class="form-group">
                            <label for="email" class="form-label">Email Host Zoom</label>
                            <input type="email" class="form-control" id="host_email" name="host_email" value="{{ old('host_email') }}" required>
                        </div> --}}

                       

                        <div class="form-group">
                            <label>Kelas</label>
                            <select name="class_id" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $class)
                                    <option value="{{ $class->id }}"
                                        {{ old('class_id', $class->class_id) == $class->id ? 'selected' : '' }}>
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
            <option value="{{ $modul->id }}" {{ old('modul_id', $selectedModulId ?? '') == $modul->id ? 'selected' : '' }}>
                {{ $modul->nama_modul ?? $modul->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Pengajar</label>
    <select name="pengajar_id" id="pengajar_id" class="form-control" required>
        <option value="">-- Pilih Pengajar --</option>
        @foreach ($pengajars as $pengajar)
            <option value="{{ $pengajar->id }}" {{ old('pengajar_id') == $pengajar->id ? 'selected' : '' }}>
                {{ $pengajar->name }}
            </option>
        @endforeach
    </select>
</div>

                        <div class="form-group">
                            <label for="start_time" class="form-label">Waktu Mulai</label>
                            <input type="datetime-local" class="form-control" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                        </div>

                      

                        {{-- <div class="form-group">
                            <label>Jam Kelas</label>
                            <input type="time" name="time_class" class="form-control" required
                                value="{{ old('start_time') }}">
                        </div> --}}

                        <div class="form-group">
                            <label>Kuota</label>
                            <input type="number" name="quota" class="form-control" required value="{{ old('quota') }}">
                        </div>



                        <div class="form-group">
                            <label>Status Kelas</label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Nonaktif
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Topic</label>
                            <input type="text" name="topic" class="form-control" value="{{ old('topic') }}">
                        </div>

                         <div class="mb-3">
                            <label for="duration" class="form-label">Durasi (menit)</label>
                            <input type="number" class="form-control" id="duration" name="duration" value="{{ old('duration', 60) }}" min="1">
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" name="notifikasi" class="form-check-input" value="1"
                                {{ old('notifikasi') ? 'checked' : '' }}>
                            <label class="form-check-label">Aktifkan Notifikasi <small class="text-success">(Optional)</small></label>
                        </div>

                        <div class="form-group">
                            <label>No Whatsapp <small class="text-success">(Optional)</small></label>
                            <input type="numeric" name="no_whatsapp" class="form-control" value="{{ old('no_whatsapp') }}">
                        </div>

                        <div class="form-group">
                            <label>Email <small class="text-success">(Optional)</small></label>
                            <input type="email" name="email_cabang" class="form-control" value="{{ old('email_cabang') }}">
                        </div>

                        <button type="submit" class="btn btn-success">Simpan</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
<!-- CSS Select2 -->
{{-- jQuery dan AJAX --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#modul_id').on('change', function() {
            let modulId = $(this).val();

            $.ajax({
                url: '{{ route("kom.kelas_tutor_cabang.api.pengajars.byModul") }}',
                data: { modul_id: modulId },
                dataType: 'json',
                success: function(data) {
                    let pengajarSelect = $('#pengajar_id');
                    pengajarSelect.empty(); 

                    pengajarSelect.append('<option value="">-- Pilih Pengajar --</option>');

                    if(data.length > 0) {
                        data.forEach(function(pengajar) {
                            pengajarSelect.append('<option value="'+pengajar.id+'">'+pengajar.name+'</option>');
                        });
                    } else {
                        pengajarSelect.append('<option value="" disabled>Tidak ada pengajar tersedia</option>');
                    }
                },
                error: function() {
                    alert('Gagal mengambil data pengajar.');
                }
            });
        });
    });
</script>
@endsection
