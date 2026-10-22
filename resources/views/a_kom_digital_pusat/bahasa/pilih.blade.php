@extends('layouts.KomDigital')

@section('page_title', __('Bahasa Belajar'))

{{-- kom_v02 — Halaman "Pilih Bahasa Belajar" --}}
@section('content')
<div class="kom-page kom-bahasa container-fluid px-2 px-md-3">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9 col-xl-8">

            @include('a_kom_digital_pusat.katalog._flash')

            <div class="kom-hero mb-4">
                <div class="kom-hero-icon"><i class="fas fa-language"></i></div>
                <div>
                    @if ($untukKelas)
                        <h3 class="mb-1">{{ __('Pilih bahasa untuk :kelas', ['kelas' => $untukKelas->translatedName()]) }}</h3>
                        <p class="mb-0">{{ __('Selamat, kelas sebelumnya sudah selesai! Anda boleh memakai bahasa yang sama atau memilih bahasa lain untuk kelas ini.') }}</p>
                    @elseif ($isFirstTime)
                        <h3 class="mb-1">{{ __('Selamat datang di KOM EveryONE!') }}</h3>
                        <p class="mb-0">{{ __('Sebelum mulai, pilih bahasa yang ingin Anda gunakan untuk belajar.') }}</p>
                    @else
                        <h3 class="mb-1">{{ __('Bahasa Belajar') }}</h3>
                        <p class="mb-0">{{ __('Bahasa ini dipakai untuk video, pertanyaan, dan sertifikat Anda.') }}</p>
                    @endif
                </div>
            </div>

            {{-- kom_v02: isi halaman dibungkus kartu supaya terpisah dari latar belakang --}}
            <div class="kom-bahasa-panel">
            <div class="kom-bahasa-panel-head">
                <h5 class="mb-0">{{ __('Pilih Bahasa') }}</h5>
                <span class="kom-bahasa-jumlah">{{ __(':jumlah bahasa tersedia', ['jumlah' => count($languages)]) }}</span>
            </div>

            @if ($isLocked)
                <div class="kom-note kom-note-warning mb-4">
                    <i class="fas fa-lock mr-2"></i>
                    {{ __('Bahasa belajar Anda sedang terkunci karena kelas :kelas masih berjalan. Anda bisa memilih bahasa lain setelah kelas tersebut selesai.', ['kelas' => $kelasBerjalan?->kelas?->translatedName() ?? '-']) }}
                </div>
            @else
                <div class="kom-note mb-4">
                    <i class="fas fa-info-circle mr-2"></i>
                    {{ __('Setelah Anda memulai sebuah kelas, bahasa akan terkunci sampai kelas tersebut selesai. Pilih dengan tenang, ya.') }}
                </div>
            @endif

            <form method="POST" action="{{ route('kom.bahasa.store') }}" id="formBahasa">
                @csrf
                @if ($untukKelas)
                    <input type="hidden" name="kelas" value="{{ $untukKelas->code }}">
                @endif
                <div class="row g-3">
                    @forelse ($languages as $lang)
                        @php
                            $dipilih = $current === $lang->code;
                            $nonaktif = $isLocked && ! $dipilih;
                        @endphp
                        <div class="col-12 col-sm-6">
                            <label class="kom-lang-card {{ $dipilih ? 'is-selected' : '' }} {{ $nonaktif ? 'is-disabled' : '' }}">
                                <input type="radio" name="language_code" value="{{ $lang->code }}"
                                       class="d-none" {{ $dipilih ? 'checked' : '' }} {{ $nonaktif ? 'disabled' : '' }}>
                                <span class="kom-lang-code">{{ strtoupper($lang->code) }}</span>
                                <span class="kom-lang-text">
                                    <strong>{{ $lang->native_name ?: $lang->name }}</strong>
                                    <small>{{ $lang->name }}</small>
                                </span>
                                <span class="kom-lang-check"><i class="fas fa-check-circle"></i></span>
                            </label>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="kom-note">{{ __('Belum ada bahasa yang tersedia. Silakan hubungi admin.') }}</div>
                        </div>
                    @endforelse
                </div>

                @error('language_code')
                    <div class="text-danger mt-3"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                @enderror

                <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
                    @unless ($isFirstTime)
                        <a href="{{ route('kom.katalog.index') }}" class="btn btn-light kom-btn">{{ __('Kembali') }}</a>
                    @endunless
                    @unless ($isLocked)
                        <button type="submit" class="btn btn-primary kom-btn" id="btnSimpanBahasa" {{ $current ? '' : 'disabled' }}>
                            {{ ($isFirstTime || $untukKelas) ? __('Lanjutkan') : __('Simpan Bahasa') }}
                            <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    @endunless
                </div>
            </form>
            </div>{{-- /kom-bahasa-panel --}}
        </div>
    </div>
</div>

<style>
    .kom-bahasa-panel {
        background:#fff; border:1px solid #e6edf7; border-radius:20px; padding:24px;
        box-shadow: 0 14px 34px -24px rgba(30, 60, 120, .45);
    }
    .kom-bahasa-panel-head {
        display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
        padding-bottom:16px; margin-bottom:20px; border-bottom:1px solid #eef2f8;
    }
    .kom-bahasa-panel-head h5 { font-weight:700; color:#1c2b43; }
    .kom-bahasa-jumlah { font-size:.78rem; font-weight:600; color:#5d6a7e; background:#f2f6fc; padding:4px 10px; border-radius:99px; }
    .kom-bahasa-panel .kom-lang-card { background:#f7f9fc; }
    .kom-bahasa-panel .kom-lang-card.is-selected { background:#f2f6ff; }
    .kom-bahasa-panel .btn-light.kom-btn { background:#fff; border:1px solid #dfe6f0; }
    .kom-bahasa-panel .btn-light.kom-btn:hover { background:#f7f9fc; }
    @media (max-width: 576px) { .kom-bahasa-panel { padding:18px; border-radius:16px; } }
</style>

@include('a_kom_digital_pusat.katalog._style')

<script>
    document.querySelectorAll('.kom-lang-card input[type=radio]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.kom-lang-card').forEach(c => c.classList.remove('is-selected'));
            this.closest('.kom-lang-card').classList.add('is-selected');
            const btn = document.getElementById('btnSimpanBahasa');
            if (btn) btn.disabled = false;
        });
    });
</script>
@endsection
