@extends('layouts.KomDigital')

@section('page_title', $kelas->exists ? __('Ubah Kelas') : __('Tambah Kelas'))

{{-- kom_v02: Superadmin — form tambah / ubah kelas --}}
@section('content')
@php $default = \App\Models\Kmd\Language::defaultCode(); @endphp
<div class="kom-page container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <div class="kom-card">
                <h4 class="kom-card-title">{{ $kelas->exists ? __('Ubah Kelas :nama', ['nama' => $kelas->name]) : __('Tambah Kelas') }}</h4>

                @if ($errors->any())
                    <div class="kom-note kom-note-danger mb-3">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                @endif

                <form method="POST" enctype="multipart/form-data"
                      action="{{ $kelas->exists ? route('kom.super_kelola_kelas.update', $kelas) : route('kom.super_kelola_kelas.store') }}">
                    @csrf
                    @if ($kelas->exists) @method('PUT') @endif

                    <h6 class="kom-section-title">{{ __('Nama & Deskripsi per Bahasa') }}</h6>
                    @foreach ($languages as $lang)
                        @php $t = $kelas->exists ? $kelas->translations->firstWhere('language_code', $lang->code) : null; @endphp
                        <div class="border rounded p-3 mb-3" style="border-radius:12px!important">
                            <div class="d-flex align-items-center mb-2" style="gap:10px">
                                <span class="kom-lang-mini">{{ strtoupper($lang->code) }}</span>
                                <strong>{{ $lang->native_name ?: $lang->name }}</strong>
                                @if ($lang->code === $default) <span class="kom-pill">{{ __('Wajib') }}</span> @endif
                            </div>
                            <div class="form-group">
                                <input type="text" name="translations[{{ $lang->code }}][name]" class="form-control"
                                       value="{{ old("translations.{$lang->code}.name", $t?->name ?? ($lang->code === $default ? $kelas->name : '')) }}"
                                       placeholder="{{ __('Nama kelas, mis. KOM 200') }}" {{ $lang->code === $default ? 'required' : '' }}>
                            </div>
                            <textarea name="translations[{{ $lang->code }}][description]" class="form-control" rows="2"
                                      placeholder="{{ __('Deskripsi singkat yang tampil di kartu katalog (opsional)') }}">{{ old("translations.{$lang->code}.description", $t?->description) }}</textarea>
                        </div>
                    @endforeach

                    <h6 class="kom-section-title mt-4">{{ __('Pengaturan Kelas') }}</h6>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">{{ __('Kode URL') }}</label>
                            <input type="text" name="code" class="form-control" value="{{ old('code', $kelas->code) }}" placeholder="kom-200">
                            <small class="kom-text">{{ __('Kosongkan untuk dibuat otomatis dari nama.') }}</small>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">{{ __('Urutan di Katalog') }}</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $kelas->sort_order) }}" min="0" placeholder="200">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">{{ __('Status') }}</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ old('status', $kelas->status) === 'active' ? 'selected' : '' }}>{{ __('Aktif') }}</option>
                                <option value="inactive" {{ old('status', $kelas->status) === 'inactive' ? 'selected' : '' }}>{{ __('Nonaktif (Segera Hadir)') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('Kelas Prasyarat') }}</label>
                        <select name="prerequisite_class_id" class="form-control">
                            <option value="">{{ __('Tidak ada — bisa langsung diikuti') }}</option>
                            @foreach ($prasyarat as $p)
                                <option value="{{ $p->id }}" {{ old('prerequisite_class_id', $kelas->prerequisite_class_id) === $p->id ? 'selected' : '' }}>
                                    {{ __('Harus lulus :kelas dulu', ['kelas' => $p->name]) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('Gambar Kartu Kelas') }}</label>
                        @if ($kelas->cover_image)
                            <div class="mb-2">
                                <img src="{{ asset($kelas->cover_image) }}" alt="" style="height:90px;border-radius:10px">
                                <label class="ml-2 small"><input type="checkbox" name="hapus_cover" value="1"> {{ __('Hapus gambar') }}</label>
                            </div>
                        @endif
                        <input type="file" name="cover_image" class="form-control" accept="image/*">
                        <small class="kom-text">{{ __('Opsional. Tanpa gambar, kartu memakai warna biru dengan nama kelas.') }}</small>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('kom.super_kelola_kelas.index') }}" class="btn btn-light kom-btn">{{ __('Kembali') }}</a>
                        <button class="btn btn-primary kom-btn">{{ __('Simpan Kelas') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('a_kom_digital_pusat.katalog._style')
@endsection
