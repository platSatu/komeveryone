@extends('layouts.KomDigital')

@section('page_title', __('Ubah Modul'))

{{-- kom_v02: halaman edit modul (sebelumnya file view ini belum ada, tombol Edit error) --}}
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ __('Ubah Modul') }}</h4>
                    <a href="{{ route('kom.super_kom-modul.index') }}" class="btn btn-secondary btn-sm">{{ __('Kembali') }}</a>
                </div>

                <form action="{{ route('kom.super_kom-modul.update', $data->id) }}" method="POST">
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

                        @if ($data->source_modul_id)
                            @php $asal = \App\Models\Kmd\KmdModuls::find($data->source_modul_id); @endphp
                            @if ($asal)
                                <div class="alert alert-info">
                                    <i class="fas fa-language mr-1"></i>
                                    {{ __('Disalin dari modul (:bahasa):', ['bahasa' => strtoupper($asal->language_code)]) }}
                                    <strong>{{ $asal->name }}</strong>
                                    @if ($asal->description)
                                        <div class="small mt-1">{{ $asal->description }}</div>
                                    @endif
                                </div>
                            @endif
                        @endif

                        <div class="form-group">
                            <label>{{ __('Judul Modul') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $data->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Deskripsi') }}</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $data->description) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Urutan Modul') }}</label>
                            <input type="number" name="order" class="form-control" value="{{ old('order', $data->order) }}" required>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Kelas') }}</label>
                            <select name="kom_class_id" class="form-control" required>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('kom_class_id', $data->kom_class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Status') }}</label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status', $data->status) == 'active' ? 'selected' : '' }}>{{ __('Aktif') }}</option>
                                <option value="inactive" {{ old('status', $data->status) == 'inactive' ? 'selected' : '' }}>{{ __('Nonaktif') }}</option>
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
