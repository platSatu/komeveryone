@extends('layouts.KomDigital')

@section('page_title', $language->exists ? __('Ubah Bahasa') : __('Tambah Bahasa'))

{{-- kom_v02: Superadmin — form tambah / ubah bahasa --}}
@section('content')
<div class="kom-page container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="kom-card">
                <h4 class="kom-card-title">{{ $language->exists ? __('Ubah Bahasa') : __('Tambah Bahasa') }}</h4>

                @if ($errors->any())
                    <div class="kom-note kom-note-danger mb-3">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                @endif

                <form method="POST" action="{{ $language->exists ? route('kom.super_bahasa.update', $language) : route('kom.super_bahasa.store') }}">
                    @csrf
                    @if ($language->exists) @method('PUT') @endif

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">{{ __('Kode Bahasa') }}</label>
                            <input type="text" name="code" class="form-control" maxlength="10"
                                   value="{{ old('code', $language->code) }}" placeholder="zh" {{ $language->exists ? 'disabled' : 'required' }}>
                            <small class="kom-text">{{ $language->exists ? __('Kode tidak bisa diubah.') : __('Huruf kecil, mis. en, zh, ko.') }}</small>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">{{ __('Nama Bahasa') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $language->name) }}" placeholder="Mandarin" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">{{ __('Nama Asli') }}</label>
                            <input type="text" name="native_name" class="form-control" value="{{ old('native_name', $language->native_name) }}" placeholder="中文">
                            <small class="kom-text">{{ __('Tampil di pilihan bahasa peserta.') }}</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">{{ __('Prefix Nomor Sertifikat') }}</label>
                            <input type="text" name="certificate_prefix" class="form-control" value="{{ old('certificate_prefix', $language->certificate_prefix) }}" placeholder="DSCP0104-ZH">
                            <small class="kom-text">{{ __('Opsional. Dipakai saat sertifikat per bahasa diterapkan.') }}</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">{{ __('Urutan Tampil') }}</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $language->sort_order) }}" min="0">
                        </div>
                    </div>

                    @unless ($language->exists)
                        <div class="kom-note mb-3">
                            <i class="fas fa-info-circle mr-1"></i>{{ __('Bahasa baru disimpan dalam keadaan NONAKTIF. Aktifkan setelah konten selesai disiapkan.') }}
                        </div>
                    @endunless

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('kom.super_bahasa.index') }}" class="btn btn-light kom-btn">{{ __('Kembali') }}</a>
                        <button class="btn btn-primary kom-btn">{{ __('Simpan') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('a_kom_digital_pusat.katalog._style')
@endsection
