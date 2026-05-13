@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Edit Kelas Peserta</h5>
                    <a href="{{ route('kom.super_userclasstutorial.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('kom.super_userclasstutorial.update', $data->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="user_name" class="form-label">Nama Peserta</label>
                                <input type="text" id="user_name" class="form-control"
                                    value="{{ $data->user->name ?? '-' }}" disabled>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="email" class="form-label">Email Peserta</label>
                                <input type="text" id="email" class="form-control"
                                    value="{{ $data->user->email ?? '-' }}" disabled>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="kelas_tutorial_id" class="form-label">Kelas Tutorial Saat Ini</label>
                                <input type="text" id="kelas_tutorial_id" class="form-control"
                                    value="{{ $data->classTutorial->topic ?? 'Belum terdaftar' }}" disabled>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="kelas_tutorial_baru" class="form-label">Pindahkan ke Kelas Baru</label>
                                <select name="class_tutorial_id" id="kelas_tutorial_baru" class="form-control" required>
                                    <option value="">-- Pilih Kelas Baru --</option>
                                    @foreach ($kelasTutorials as $kelas)
                                        <option value="{{ $kelas->id }}" class="font-weight-bold"
                                            
                                            {{ $kelas->id == $data->kelas_tutorial_id ? 'selected' : '' }}>
                                            {{ $kelas->topic }} | {{ $kelas->start_time }} |
                                            (Kuota: {{ $kelas->quota }} | Ikut: {{ $kelas->jml_ikut }} | Sisa:
                                            {{ $kelas->sisa_quota }} |
                                            {{ $kelas->category_diklat }})
                                            
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('kom.super_userclasstutorial.index') }}" class="btn btn-secondary ms-2">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
