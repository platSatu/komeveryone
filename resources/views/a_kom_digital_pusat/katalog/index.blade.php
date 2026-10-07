@extends('layouts.KomDigital')

@section('page_title', __('Pilih Kelas'))

{{-- kom_v02 — Halaman "Pilih Kelas" (katalog KOM 100 / 200 / 300 / 400) --}}
@section('content')
<div class="kom-page container-fluid px-2 px-md-3">

    @include('a_kom_digital_pusat.katalog._flash')

    <div class="kom-hero mb-4">
        <div class="kom-hero-icon"><i class="fas fa-graduation-cap"></i></div>
        <div class="flex-grow-1">
            <h3 class="mb-1">{{ __('Pilih Kelas') }}</h3>
            <p class="mb-0">{{ __('Mulai perjalanan belajar Anda. Selesaikan setiap kelas untuk membuka kelas berikutnya.') }}</p>
        </div>
        @if ($bahasa)
            <a href="{{ route('kom.bahasa.index') }}" class="btn btn-light btn-sm kom-btn d-none d-md-inline-block"
               title="{{ __('Bahasa Belajar') }}">
                <i class="fas fa-language mr-1"></i>{{ $bahasa->native_name ?: $bahasa->name }}
                @if ($bahasaTerkunci) <i class="fas fa-lock ml-1"></i> @endif
            </a>
        @endif
    </div>

    <div class="row g-3">
        @foreach ($kartu as $item)
            @php
                $kelas = $item['kelas'];
                $status = $item['status'];
                $muted = in_array($status, ['segera', 'terkunci'], true);
                $label = [
                    'berjalan' => __('Sedang Dipelajari'),
                    'selesai'  => __('Selesai'),
                    'terkunci' => __('Terkunci'),
                    'segera'   => __('Segera Hadir'),
                    'tersedia' => __('Tersedia'),
                ][$status];
            @endphp
            <div class="col-12 col-md-6">
                <div class="kom-class-card {{ $muted ? 'is-muted' : '' }}">
                    <div class="kom-class-cover"
                         @if ($kelas->cover_image) style="background-image:url('{{ asset($kelas->cover_image) }}')" @endif>
                        @unless ($kelas->cover_image) {{ $item['nama'] }} @endunless
                    </div>

                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h5>{{ $item['nama'] }}</h5>
                        <span class="kom-badge kom-badge-{{ $status }}">{{ $label }}</span>
                    </div>

                    <div class="kom-class-meta">
                        @if ($item['jumlah_modul'] > 0)
                            <i class="fas fa-layer-group mr-1"></i>{{ __(':jumlah modul', ['jumlah' => $item['jumlah_modul']]) }}
                        @endif
                        @if ($item['user_class'] && $item['user_class']->language_code)
                            &middot; {{ strtoupper($item['user_class']->language_code) }}
                        @endif
                    </div>

                    @if ($item['deskripsi'])
                        <p class="text-muted small mb-2">{{ $item['deskripsi'] }}</p>
                    @endif

                    @if ($item['keterangan'])
                        <p class="small mb-2 {{ $status === 'terkunci' ? 'text-warning' : 'text-muted' }}">
                            <i class="fas {{ $status === 'terkunci' ? 'fa-lock' : 'fa-clock' }} mr-1"></i>{{ $item['keterangan'] }}
                        </p>
                    @endif

                    <div class="kom-class-action">
                        @switch($status)
                            @case('berjalan')
                                <a href="{{ route('kom.katalog.masuk', $kelas->code) }}" class="btn btn-primary kom-btn">
                                    <i class="fas fa-play mr-1"></i>{{ __('Lanjutkan Belajar') }}
                                </a>
                                @break
                            @case('selesai')
                                <a href="{{ route('kom.katalog.masuk', $kelas->code) }}" class="btn btn-outline-success kom-btn">
                                    <i class="fas fa-redo mr-1"></i>{{ __('Buka Materi Lagi') }}
                                </a>
                                @break
                            @case('tersedia')
                                <a href="{{ route('kom.katalog.kode_buku', $kelas->code) }}" class="btn btn-primary kom-btn">
                                    <i class="fas fa-book-open mr-1"></i>{{ __('Mulai Kelas') }}
                                </a>
                                @break
                            @default
                                <button type="button" class="btn btn-light kom-btn" disabled>
                                    {{ $status === 'terkunci' ? __('Belum Bisa Dibuka') : __('Segera Hadir') }}
                                </button>
                        @endswitch
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@include('a_kom_digital_pusat.katalog._style')
@endsection
