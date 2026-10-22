@extends('layouts.KomDigital')

@section('page_title', __('Review Pengajar'))

{{-- kom_v02: Admin KOM — detail review satu pengajar, dikelompokkan per kelas tutorial --}}
@section('content')
@include('a_kom_digital_pusat.superadmin.review_pengajar._style')
@php
    $label = [
        'sangat_baik' => [__('Sangat Baik'), 'hijau'],
        'baik' => [__('Baik'), 'biru'],
        'cukup' => [__('Cukup'), 'kuning'],
        'kurang' => [__('Kurang'), 'merah'],
        'sangat_kurang' => [__('Sangat Kurang'), 'merah'],
    ];
@endphp
<div class="kom-rv container-fluid px-0">

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap:10px">
        <div>
            <h4 class="mb-0 font-weight-bold">{{ $pengajarUser->name }}</h4>
            <small class="text-muted">{{ $pengajarUser->email }}</small>
        </div>
        <a href="{{ route('kom.super_review_pengajar.index', array_filter(\Illuminate\Support\Arr::only($filters, ['cabang_id', 'modul_id', 'date_from', 'date_to', 'jenis_tanggal']))) }}" class="kom-rv-btn abu">
            <i class="fas fa-arrow-left mr-1"></i>{{ __('Kembali') }}
        </a>
    </div>

    <div class="kom-rv-stats">
        <div class="kom-rv-stat utama">
            <small>{{ __('Rata-rata Nilai') }}</small>
            <strong>{{ $rataSemua > 0 ? number_format($rataSemua, 2) : '-' }} <span style="font-size:.9rem">/ 5</span></strong>
        </div>
        <div class="kom-rv-stat"><small>{{ __('Total Review') }}</small><strong>{{ $totalReview }}</strong></div>
        <div class="kom-rv-stat"><small>{{ __('Kelas Tutorial') }}</small><strong>{{ $totalKelas }}</strong></div>
    </div>

    {{-- kom_v02: filter tanggal & pencarian kelas tutorial --}}
    <div class="kom-rv-card">
        <form method="GET" action="{{ route('kom.super_review_pengajar.show', ['pengajarId' => $pengajarUser->id]) }}" class="kom-rv-filter">
            <div style="flex-basis:260px">
                <label>{{ __('Cari') }}</label>
                <input type="search" name="cari" value="{{ $filters['cari'] }}" class="form-control"
                       placeholder="{{ __('Topik kelas, modul, cabang, atau nama peserta') }}">
            </div>
            <div>
                <label>{{ __('Tanggal Berdasarkan') }}</label>
                <select name="jenis_tanggal" class="form-control">
                    <option value="kelas" @selected($filters['jenis_tanggal'] === 'kelas')>{{ __('Jadwal kelas tutorial') }}</option>
                    <option value="review" @selected($filters['jenis_tanggal'] === 'review')>{{ __('Tanggal review') }}</option>
                </select>
            </div>
            <div>
                <label>{{ __('Dari Tanggal') }}</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="form-control">
            </div>
            <div>
                <label>{{ __('Sampai Tanggal') }}</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="form-control">
            </div>
            <div>
                <label>{{ __('Cabang / Rayon') }}</label>
                <select name="cabang_id" class="form-control">
                    <option value="">{{ __('Semua Cabang') }}</option>
                    @foreach ($cabangList as $c)
                        <option value="{{ $c->id }}" @selected($filters['cabang_id'] == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>{{ __('Modul') }}</label>
                <select name="modul_id" class="form-control">
                    <option value="">{{ __('Semua Modul') }}</option>
                    @foreach ($modulList as $m)
                        <option value="{{ $m->id }}" @selected($filters['modul_id'] == $m->id)>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="tombol">
                <button type="submit" class="kom-rv-btn"><i class="fas fa-search mr-1"></i>{{ __('Cari') }}</button>
                <a href="{{ route('kom.super_review_pengajar.show', ['pengajarId' => $pengajarUser->id]) }}" class="kom-rv-btn abu">{{ __('Reset') }}</a>
            </div>
        </form>
        @if ($filters['cari'] || $filters['date_from'] || $filters['date_to'] || $filters['cabang_id'] || $filters['modul_id'])
            <div class="small text-muted mt-2">
                <i class="fas fa-info-circle mr-1"></i>{{ __('Ditemukan :kelas kelas tutorial dengan :review review.', ['kelas' => $totalKelas, 'review' => $totalReview]) }}
            </div>
        @endif
    </div>

    <div class="kom-rv-card">
        <h5>{{ __('Rata-rata per Penilaian') }}</h5>
        <div class="row">
            @foreach ($kriteria as $kolom => $nama)
                @php $v = $rataKriteria[$kolom] ?? 0; @endphp
                <div class="col-md-6">
                    <div class="kom-rv-bar">
                        <span class="nama">{{ __($nama) }}</span>
                        <span class="jalur"><div style="width: {{ min(100, $v / 5 * 100) }}%"></div></span>
                        <span class="angka">{{ $v > 0 ? number_format($v, 1) : '-' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @forelse ($perKelas as $grup)
        @php $ct = $grup['kelas_tutorial']; @endphp
        <div class="kom-rv-card">
            <div class="d-flex justify-content-between align-items-start flex-wrap mb-3" style="gap:10px">
                <div>
                    <h5 class="mb-1">{{ $ct->topic ?? __('Kelas Tutorial') }}</h5>
                    <div class="small text-muted">
                        <i class="fas fa-book mr-1"></i>{{ optional(optional($ct)->modul)->name ?? '-' }}
                        {{-- label titik hanya ada di versi kom_v02 (aman jika belum ada) --}}
                        @if ($ct && method_exists($ct, 'getLabelTitikAttribute')) · <i class="fas fa-flag-checkered mx-1"></i>{{ $ct->label_titik }} @endif
                        · <i class="fas fa-map-marker-alt mx-1"></i>{{ optional(optional($ct)->rayon)->name ?? '-' }}
                        @if ($ct && $ct->start_time)
                            · <i class="far fa-calendar-alt mx-1"></i>{{ \Carbon\Carbon::parse($ct->start_time)->translatedFormat('d M Y H:i') }}
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    @include('a_kom_digital_pusat.superadmin.review_pengajar._bintang', ['nilai' => $grup['rata_rata']])
                    <div><span class="kom-rv-chip">{{ __(':n review', ['n' => $grup['reviews']->count()]) }}</span></div>
                </div>
            </div>

            <div class="kom-rv-wrap">
                <table class="table kom-rv-table">
                    <thead>
                        <tr>
                            <th>{{ __('Nama Peserta') }}</th>
                            @foreach ($kriteria as $nama)
                                <th class="text-center">{{ __($nama) }}</th>
                            @endforeach
                            <th>{{ __('Tanggal Review') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grup['reviews'] as $review)
                            <tr>
                                <td class="text-nowrap">{{ $review->user->name ?? '-' }}</td>
                                @foreach ($kriteria as $kolom => $nama)
                                    @php $isi = $review->$kolom; $l = $label[$isi] ?? null; @endphp
                                    <td class="text-center text-nowrap">
                                        @if ($l)
                                            <span class="kom-rv-chip {{ $l[1] }}">{{ $l[0] }}</span>
                                        @else
                                            {{ $isi ? ucfirst(str_replace('_', ' ', $isi)) : '-' }}
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-nowrap">{{ \Carbon\Carbon::parse($review->created_at)->translatedFormat('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="kom-rv-card"><div class="kom-rv-kosong"><i class="fas fa-comment-dots"></i>{{ __('Belum ada review yang tersedia.') }}</div></div>
    @endforelse

    @if ($halamanKelas->hasPages())
        <div class="d-flex justify-content-center">{{ $halamanKelas->links() }}</div>
    @endif
</div>
@endsection
