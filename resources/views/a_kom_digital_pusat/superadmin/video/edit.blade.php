@extends('layouts.KomDigital')

@section('page_title', __('Ubah Video'))

{{-- kom_v02: halaman edit video (sebelumnya file view ini belum ada, tombol Edit error) --}}
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ __('Ubah Video') }}</h4>
                    <a href="{{ route('kom.super_videos.index') }}" class="btn btn-secondary btn-sm">{{ __('Kembali') }}</a>
                </div>

                <form action="{{ route('kom.super_videos.update', \Illuminate\Support\Facades\Crypt::encrypt($video->id)) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if ($video->source_video_id)
                            @php $asal = \App\Models\Kmd\KmdVideo::find($video->source_video_id); @endphp
                            @if ($asal)
                                <div class="alert alert-info">
                                    <i class="fas fa-language mr-1"></i>
                                    {{ __('Disalin dari video:') }} <strong>{{ $asal->name }}</strong>
                                    @if ($asal->video && $asal->video === $video->video)
                                        <div class="small mt-1 text-danger">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ __('File video masih sama dengan bahasa asal. Unggah file video versi bahasa ini.') }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endif

                        <div class="form-group">
                            <label>{{ __('Judul Video') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $video->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Deskripsi') }}</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $video->description) }}</textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>{{ __('Durasi (jam:menit:detik)') }}</label>
                                <input type="text" name="durations" class="form-control" value="{{ old('durations', $video->durations) }}" placeholder="00:18:46">
                            </div>
                            <div class="form-group col-md-6">
                                <label>{{ __('Urutan Video') }}</label>
                                <input type="number" name="sort_video" class="form-control" value="{{ old('sort_video', $video->sort_video) }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Modul') }}</label>
                            <select name="modul_id" class="form-control" required>
                                @foreach ($moduls as $modul)
                                    <option value="{{ $modul->id }}" {{ old('modul_id', $video->modul_id) == $modul->id ? 'selected' : '' }}>
                                        {{ $modul->name }} @if($modul->kelas) ({{ $modul->kelas->name }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Link Video (opsional)') }}</label>
                            <input type="text" name="url_video" class="form-control" value="{{ old('url_video', $video->url_video) }}">
                        </div>

                        <div class="form-group">
                            <label>{{ __('Ganti File Video') }}</label>
                            @if ($video->video)
                                <div class="small text-muted mb-1">{{ __('File saat ini:') }} <code>{{ $video->video }}</code></div>
                            @endif
                            <input type="file" name="video" class="form-control" accept="video/*">
                            <small class="text-muted">{{ __('Kosongkan jika tidak ingin mengganti file.') }}</small>
                        </div>

                        @include('a_kom_digital_pusat.superadmin.video._titik_tutorial', ['video' => $video])

                        <div class="form-group">
                            <label>{{ __('Status') }}</label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status', $video->status) == 'active' ? 'selected' : '' }}>{{ __('Aktif') }}</option>
                                <option value="inactive" {{ old('status', $video->status) == 'inactive' ? 'selected' : '' }}>{{ __('Nonaktif') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-primary" type="submit">{{ __('Simpan Perubahan') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
