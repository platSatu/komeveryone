@extends('layouts.KomDigital')

@section('page_title', __('Pilih Kelas'))

{{-- kom_v02 — Halaman "Masukkan Kode Buku" untuk membuka sebuah kelas --}}
@section('content')
<div class="kom-page container-fluid px-2 px-md-3">
    <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-7 col-xl-6">

            @include('a_kom_digital_pusat.katalog._flash')

            <div class="kom-hero mb-4">
                <div class="kom-hero-icon"><i class="fas fa-book-open"></i></div>
                <div>
                    <h3 class="mb-1">{{ __('Buka Kelas :kelas', ['kelas' => $kartu['nama']]) }}</h3>
                    <p class="mb-0">{{ __('Masukkan kode yang tertera di buku Anda untuk mulai belajar.') }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius:16px">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('kom.katalog.kode_buku.store', $kelas->code) }}" id="formKodeBuku">
                        @csrf
                        <label for="no_books" class="font-weight-bold mb-2">{{ __('Kode Buku') }}</label>
                        <input type="text" name="no_books" id="no_books" value="{{ old('no_books') }}"
                               class="form-control kom-input-big @error('no_books') is-invalid @enderror"
                               placeholder="{{ __('Contoh: abc123') }}" maxlength="50" autocomplete="off" autofocus required>
                        @error('no_books')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle mr-1"></i>{{ __('Satu kode buku hanya bisa dipakai untuk satu akun.') }}
                        </small>

                        @if ($bahasa)
                            <div class="kom-note mt-4">
                                <i class="fas fa-language mr-2"></i>
                                {{ __('Kelas ini akan Anda ikuti dalam :bahasa. Bahasa akan terkunci sampai kelas selesai.', ['bahasa' => $bahasa->native_name ?: $bahasa->name]) }}
                                <a href="{{ route('kom.bahasa.index') }}">{{ __('Ganti bahasa') }}</a>
                            </div>
                        @endif

                        <div class="d-flex flex-wrap gap-2 justify-content-between mt-4">
                            <a href="{{ route('kom.katalog.index') }}" class="btn btn-light kom-btn">
                                <i class="fas fa-arrow-left mr-1"></i>{{ __('Kembali') }}
                            </a>
                            <button type="submit" class="btn btn-primary kom-btn" id="btnAktifkan">
                                <span class="label-normal">{{ __('Aktifkan & Mulai Belajar') }}</span>
                                <span class="label-loading d-none"><i class="fas fa-spinner fa-spin mr-1"></i>{{ __('Sedang memeriksa...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center text-muted small mt-3">
                {{ __('Belum punya buku? Dapatkan di Toko Areopagus:') }}
                <a href="https://www.tokopedia.com/areopagus/" target="_blank" rel="noopener">Tokopedia</a> &middot;
                <a href="https://shopee.co.id/areopagus" target="_blank" rel="noopener">Shopee</a>
            </div>
        </div>
    </div>
</div>

@include('a_kom_digital_pusat.katalog._style')

<script>
    document.getElementById('formKodeBuku').addEventListener('submit', function () {
        const btn = document.getElementById('btnAktifkan');
        btn.disabled = true;
        btn.querySelector('.label-normal').classList.add('d-none');
        btn.querySelector('.label-loading').classList.remove('d-none');
    });
</script>
@endsection
