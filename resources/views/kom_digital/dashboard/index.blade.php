@extends('layouts.KomDigital')

@section('page_title', __('Beranda'))

{{-- kom_v02: Beranda — sapaan, lanjutkan belajar, akses cepat, dan cara belajar --}}
@section('content')
@php
    $jam = (int) now()->timezone('Asia/Jakarta')->format('H');
    $sapaan = $jam < 11 ? __('Selamat pagi') : ($jam < 15 ? __('Selamat siang') : ($jam < 19 ? __('Selamat sore') : __('Selamat malam')));
    $namaDepan = \Illuminate\Support\Str::of(auth()->user()->name ?? '')->explode(' ')->first();
@endphp

<div class="kom-page container-fluid px-2 px-md-3">

    @include('a_kom_digital_pusat.katalog._flash')

    {{-- Sapaan --}}
    <div class="kom-hero mb-4">
        <div class="kom-hero-icon"><i class="fas fa-sun"></i></div>
        <div class="flex-grow-1">
            <h3 class="mb-1">{{ $sapaan }}, {{ $namaDepan }}!</h3>
            <p class="mb-0">{{ __('Senang melihat Anda kembali. Yuk, lanjutkan belajar hari ini.') }}</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- Lanjutkan Belajar / Mulai --}}
        <div class="col-12 col-lg-8">
            @if ($ringkasan && $ringkasan['selesai_kelas'])
                <div class="kom-card kom-card-success h-100">
                    <div class="kom-card-label"><i class="fas fa-award mr-1"></i>{{ $ringkasan['nama_kelas'] }}</div>
                    <h4 class="kom-card-title">{{ __('Selamat! Anda telah menyelesaikan :kelas.', ['kelas' => $ringkasan['nama_kelas']]) }}</h4>
                    <p class="kom-text mb-4">{{ __('Terima kasih sudah setia belajar. Sertifikat Anda sudah bisa dilihat, dan kelas berikutnya menanti.') }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('kom.peserta.sertifikat') }}" class="btn btn-success kom-btn"><i class="fas fa-certificate mr-1"></i>{{ __('Lihat Sertifikat') }}</a>
                        <a href="{{ route('kom.katalog.index') }}" class="btn btn-outline-primary kom-btn">{{ __('Pilih Kelas Berikutnya') }}</a>
                    </div>
                </div>
            @elseif ($ringkasan)
                <div class="kom-card h-100">
                    <div class="kom-card-label"><i class="fas fa-play-circle mr-1"></i>{{ __('Lanjutkan Belajar') }}</div>
                    <h4 class="kom-card-title">{{ $ringkasan['nama_kelas'] }}</h4>

                    <div class="d-flex justify-content-between kom-text small mb-1">
                        <span>{{ __(':selesai dari :total video selesai', ['selesai' => $ringkasan['video_selesai'], 'total' => $ringkasan['total_video']]) }}</span>
                        <strong>{{ $ringkasan['persen'] }}%</strong>
                    </div>
                    <div class="kom-progress mb-3"><div style="width: {{ $ringkasan['persen'] }}%"></div></div>

                    @if ($ringkasan['video_berikut'])
                        <div class="kom-next mb-4">
                            <i class="fas fa-forward"></i>
                            <div>
                                <small>{{ __('Berikutnya') }}</small>
                                <strong>{{ $ringkasan['video_berikut'] }}</strong>
                                <span>{{ $ringkasan['modul_berikut'] }}</span>
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('kom.katalog.masuk', $ringkasan['kode_kelas']) }}" class="btn btn-primary kom-btn kom-btn-lg">
                        <i class="fas fa-play mr-1"></i>{{ $ringkasan['video_selesai'] > 0 ? __('Lanjutkan Belajar') : __('Mulai Belajar') }}
                    </a>
                </div>
            @else
                <div class="kom-card h-100">
                    <div class="kom-card-label"><i class="fas fa-seedling mr-1"></i>{{ __('Langkah Pertama') }}</div>
                    <h4 class="kom-card-title">{{ __('Yuk, mulai kelas pertama Anda') }}</h4>
                    <p class="kom-text mb-4">{{ __('Pilih kelas, lalu masukkan kode yang tertera di buku Anda. Setelah itu video materi langsung terbuka.') }}</p>
                    <a href="{{ route('kom.katalog.index') }}" class="btn btn-primary kom-btn kom-btn-lg">
                        <i class="fas fa-graduation-cap mr-1"></i>{{ __('Pilih Kelas') }}
                    </a>
                    <p class="kom-text small mt-3 mb-0">
                        {{ __('Belum punya buku? Dapatkan di Toko Areopagus:') }}
                        <a href="https://www.tokopedia.com/areopagus/" target="_blank" rel="noopener">Tokopedia</a> &middot;
                        <a href="https://shopee.co.id/areopagus" target="_blank" rel="noopener">Shopee</a>
                    </p>
                </div>
            @endif
        </div>

        {{-- Menu --}}
        <div class="col-12 col-lg-4">
            <div class="kom-card h-100">
                <div class="kom-card-label"><i class="fas fa-th-large mr-1"></i>{{ __('Menu') }}</div>
                <div class="kom-quick">
                    <a href="{{ route('kom.peserta.kelas_tutorial') }}"><i class="fas fa-video"></i><span>{{ __('Kelas Tutorial Saya') }}</span><i class="fas fa-chevron-right"></i></a>
                    <a href="{{ route('kom.peserta.progress_kom') }}"><i class="fas fa-chart-line"></i><span>{{ __('Progress Saya') }}</span><i class="fas fa-chevron-right"></i></a>
                    <a href="{{ route('kom.peserta.sertifikat') }}"><i class="fas fa-certificate"></i><span>{{ __('Sertifikat Saya') }}</span><i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    {{-- Cara belajar --}}
    <h5 class="kom-section-title">{{ __('Cara Belajar di KOM EveryONE') }}</h5>
    <div class="row g-3">
        @foreach ([
            ['fas fa-book-open', __('Pilih kelas & masukkan kode buku'), __('Buka menu Pilih Kelas, pilih kelas yang ingin Anda ikuti, lalu masukkan kode yang tertera di buku Anda.')],
            ['fas fa-play-circle', __('Tonton video & jawab pertanyaan'), __('Tonton setiap video materi sampai selesai, lalu jawab pertanyaannya. Jika jawaban Anda benar, video berikutnya akan terbuka.')],
            ['fas fa-users', __('Ikuti Kelas Tutorial'), __('Pada titik tertentu di modul, Anda akan diajak mengikuti Kelas Tutorial bersama Tutor/Pengajar. Syaratnya: kirim 2 pertanyaan dan berikan persembahan.')],
            ['fas fa-award', __('Bergabung & dapatkan sertifikat'), __('Link Zoom dikirim ke email Anda. Sertifikat terbit setelah semua modul selesai dan kehadiran Anda di setiap Kelas Tutorial tercatat.')],
        ] as $i => [$ikon, $judul, $isi])
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="kom-step-card">
                    <div class="kom-step-icon"><i class="{{ $ikon }}"></i><span>{{ $i + 1 }}</span></div>
                    <h6>{{ $judul }}</h6>
                    <p>{{ $isi }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>

@include('a_kom_digital_pusat.katalog._style')
@endsection
