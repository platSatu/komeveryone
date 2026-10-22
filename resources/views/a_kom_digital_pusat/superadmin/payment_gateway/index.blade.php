@extends('layouts.KomDigital')

@section('page_title', __('Payment Gateway'))

{{-- kom_v02: Admin KOM — pilih payment gateway persembahan (Midtrans / BB Bizbos) --}}
@section('content')
<div class="kom-page container-fluid px-0">
    @include('a_kom_digital_pusat.katalog._flash')

    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <p class="kom-text mb-3">{{ __('Pilih payment gateway untuk persembahan kelas tutorial dan diklat tutor. Persembahan yang sudah dibuat tetap diproses dengan gateway semula.') }}</p>

            @if ($errors->any())
                <div class="kom-note kom-note-danger mb-3">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            @endif

            <form method="POST" action="{{ route('kom.super_payment_gateway.update') }}">
                @csrf
                @method('PUT')

                <div class="kom-card mb-3">
                    <h5 class="kom-card-title">{{ __('Gateway yang Dipakai') }}</h5>
                    <div class="row">
                        @foreach ($daftar as $kode => $nama)
                            @php $dipilih = old('gateway', $pilihan) === $kode; @endphp
                            <div class="col-12 col-md-6 mb-2">
                                <label class="kom-pg-opsi {{ $dipilih ? 'is-selected' : '' }}">
                                    <input type="radio" name="gateway" value="{{ $kode }}" {{ $dipilih ? 'checked' : '' }}>
                                    <span class="kom-pg-ikon"><i class="fas {{ $kode === 'bizbos' ? 'fa-store' : 'fa-credit-card' }}"></i></span>
                                    <span class="flex-grow-1">
                                        <strong>{{ $nama }}</strong>
                                        <small class="d-block text-muted">
                                            @if ($kode === 'midtrans')
                                                {{ $midtransLengkap ? __('Kunci di .env sudah lengkap') : __('Kunci di .env belum lengkap') }}
                                                · {{ $midtransProduction ? 'Production' : 'Sandbox' }}
                                            @else
                                                {{ $bizbosLengkap ? __('Kredensial sudah lengkap') : __('Kredensial belum lengkap') }}
                                            @endif
                                        </small>
                                    </span>
                                    @if ($aktif === $kode)
                                        <span class="kom-pill">{{ __('Aktif') }}</span>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="kom-card mb-3" id="kartuBizbos">
                    <h5 class="kom-card-title">{{ __('Kredensial BB (Bizbos)') }}</h5>
                    <p class="small text-muted">{{ __('Didapat dari Bizbos › Invoice › API & Integrasi › Buat API Key. Secret Key dan Webhook Secret hanya ditampilkan sekali oleh Bizbos.') }}</p>

                    <div class="form-group">
                        <label>{{ __('Base URL Bizbos') }}</label>
                        <input type="url" name="bizbos_base_url" class="form-control" placeholder="https://app.bizbos.id"
                               value="{{ old('bizbos_base_url', $bizbosBaseUrl) }}">
                        <small class="form-text text-muted">{{ __('Alamat utama Bizbos tanpa /api/v1, mis. https://staging.bizbos.id. Kalau yang disalin "Base URL API" (…/api/v1), akhiran itu dibuang otomatis.') }}</small>
                    </div>
                    <div class="form-group">
                        <label>{{ __('API Key') }} <code>pk_...</code></label>
                        <input type="text" name="bizbos_api_key" class="form-control" autocomplete="off"
                               value="{{ old('bizbos_api_key', $bizbosApiKey) }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('Secret Key') }} <code>sk_...</code></label>
                        <input type="password" name="bizbos_secret" class="form-control" autocomplete="new-password"
                               placeholder="{{ $adaSecret ? __('Sudah tersimpan — kosongkan jika tidak diganti') : '' }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('Webhook Secret') }} <code>whsec_...</code></label>
                        <input type="password" name="bizbos_webhook_secret" class="form-control" autocomplete="new-password"
                               placeholder="{{ $adaWebhookSecret ? __('Sudah tersimpan — kosongkan jika tidak diganti') : '' }}">
                    </div>

                    <div class="kom-note mb-0">
                        <div class="font-weight-bold mb-1"><i class="fas fa-info-circle mr-1"></i>{{ __('Isi juga di Bizbos (API & Integrasi):') }}</div>
                        <div>{{ __('URL Webhook') }}: <code id="webhookUrl">{{ $webhookUrl }}</code>
                            <button type="button" class="btn btn-link btn-sm p-0 ml-1" data-salin="#webhookUrl">{{ __('Salin') }}</button>
                        </div>
                        <div>{{ __('Website yang boleh membuka popup') }}: <code id="originPopup">{{ $originPopup }}</code>
                            <button type="button" class="btn btn-link btn-sm p-0 ml-1" data-salin="#originPopup">{{ __('Salin') }}</button>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-end" style="gap:10px">
                    <button type="submit" class="btn btn-primary kom-btn"><i class="fas fa-save mr-1"></i>{{ __('Simpan Setting') }}</button>
                </div>
            </form>

            @if ($bizbosLengkap)
                <form method="POST" action="{{ route('kom.super_payment_gateway.tes_bizbos') }}" class="mt-2 text-right">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary kom-btn"><i class="fas fa-plug mr-1"></i>{{ __('Tes Koneksi Bizbos') }}</button>
                </form>
            @endif
        </div>
    </div>
</div>

@include('a_kom_digital_pusat.katalog._style')
<style>
    .kom-pg-opsi { display:flex; align-items:center; gap:12px; width:100%; margin:0; cursor:pointer; padding:14px 16px;
        border:2px solid #e3e8f0; border-radius:14px; background:#fff; transition:.15s; }
    .kom-pg-opsi:hover { border-color:#9db8f2; }
    .kom-pg-opsi input { display:none; }
    .kom-pg-opsi.is-selected { border-color:#2a5298; background:#f4f7fd; }
    .kom-pg-ikon { flex:0 0 42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center;
        background:#e8eefb; color:#2a5298; font-size:1.1rem; }
    .kom-pg-opsi.is-selected .kom-pg-ikon { background:#2a5298; color:#fff; }
</style>
<script>
    document.querySelectorAll('.kom-pg-opsi input').forEach(function (r) {
        r.addEventListener('change', function () {
            document.querySelectorAll('.kom-pg-opsi').forEach(function (el) { el.classList.remove('is-selected'); });
            r.closest('.kom-pg-opsi').classList.add('is-selected');
        });
    });
    document.querySelectorAll('[data-salin]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var teks = document.querySelector(btn.dataset.salin).textContent.trim();
            if (navigator.clipboard) navigator.clipboard.writeText(teks);
            btn.textContent = @json(__('Tersalin'));
        });
    });
</script>
@endsection
