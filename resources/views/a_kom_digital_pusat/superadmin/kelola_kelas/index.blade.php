@extends('layouts.KomDigital')

@section('page_title', __('Kelola Kelas'))

{{-- kom_v02: Superadmin — Kelola Kelas (KOM 100 / 200 / 300 / 400) --}}
@section('content')
<div class="kom-page container-fluid px-0">
    @include('a_kom_digital_pusat.katalog._flash')

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap:10px">
        <p class="kom-text mb-0">{{ __('Atur kelas yang tampil di katalog peserta: urutan, kelas prasyarat, gambar, serta nama & deskripsi per bahasa.') }}</p>
        <a href="{{ route('kom.super_kelola_kelas.create') }}" class="btn btn-primary kom-btn"><i class="fas fa-plus mr-1"></i>{{ __('Tambah Kelas') }}</a>
    </div>

    <div class="row g-3">
        @forelse ($classes as $kelas)
            @php $modul = ($modulPerBahasa[$kelas->id] ?? collect())->pluck('jumlah', 'language_code'); @endphp
            <div class="col-12 col-md-6">
                <div class="kom-class-card {{ $kelas->status !== 'active' ? 'is-muted' : '' }}">
                    <div class="kom-class-cover" @if ($kelas->cover_image) style="background-image:url('{{ asset($kelas->cover_image) }}')" @endif>
                        @unless ($kelas->cover_image) {{ $kelas->name }} @endunless
                    </div>
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h5>{{ $kelas->name }}</h5>
                        <span class="kom-badge {{ $kelas->status === 'active' ? 'kom-badge-selesai' : 'kom-badge-segera' }}">
                            {{ $kelas->status === 'active' ? __('Aktif') : __('Nonaktif') }}
                        </span>
                    </div>
                    <div class="kom-class-meta">
                        <div><i class="fas fa-link mr-1"></i>{{ __('Kode URL:') }} <code>{{ $kelas->code ?? '-' }}</code> · {{ __('Urutan:') }} {{ $kelas->sort_order }}</div>
                        <div><i class="fas fa-lock mr-1"></i>{{ __('Prasyarat:') }} {{ $kelas->prerequisite?->name ?? __('Tidak ada') }}</div>
                        <div><i class="fas fa-users mr-1"></i>{{ __(':n peserta terdaftar', ['n' => $peserta[$kelas->id] ?? 0]) }}</div>
                    </div>
                    <div class="mb-2">
                        @foreach ($languages as $lang)
                            @php $ada = $kelas->translations->firstWhere('language_code', $lang->code); @endphp
                            <span class="kom-pill mr-1 mb-1" style="{{ $ada ? '' : 'background:#f1f2f4;color:#9aa3b2' }}"
                                  title="{{ $ada ? $ada->name : __('Nama belum diisi') }}">
                                {{ strtoupper($lang->code) }} · {{ __(':n modul', ['n' => $modul[$lang->code] ?? 0]) }}
                            </span>
                        @endforeach
                    </div>
                    <div class="kom-class-action">
                        <a href="{{ route('kom.super_kelola_kelas.edit', $kelas) }}" class="btn btn-outline-primary kom-btn">
                            <i class="fas fa-edit mr-1"></i>{{ __('Ubah Kelas') }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="kom-note">{{ __('Belum ada kelas.') }}</div></div>
        @endforelse
    </div>
</div>
@include('a_kom_digital_pusat.katalog._style')
@endsection
