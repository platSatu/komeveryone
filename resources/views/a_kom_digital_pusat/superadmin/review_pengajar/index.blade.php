@extends('layouts.KomDigital')

@section('page_title', __('Review Pengajar'))

{{-- kom_v02: Admin KOM — review semua pengajar dari semua cabang --}}
@section('content')
@include('a_kom_digital_pusat.superadmin.review_pengajar._style')
<div class="kom-rv container-fluid px-0">

    <p class="text-muted mb-3">{{ __('Semua review pengajar dari peserta di seluruh cabang.') }}</p>

    {{-- Ringkasan --}}
    <div class="kom-rv-stats">
        <div class="kom-rv-stat utama">
            <small>{{ __('Rata-rata Nilai') }}</small>
            <strong>{{ $rataSemua > 0 ? number_format($rataSemua, 2) : '-' }} <span style="font-size:.9rem">/ 5</span></strong>
        </div>
        <div class="kom-rv-stat"><small>{{ __('Total Review') }}</small><strong>{{ number_format($ringkasan->total_review ?? 0) }}</strong></div>
        <div class="kom-rv-stat"><small>{{ __('Pengajar Direview') }}</small><strong>{{ number_format($ringkasan->total_pengajar ?? 0) }}</strong></div>
        <div class="kom-rv-stat"><small>{{ __('Kelas Tutorial') }}</small><strong>{{ number_format($ringkasan->total_kelas ?? 0) }}</strong></div>
    </div>

    {{-- Filter --}}
    <div class="kom-rv-card">
        <form method="GET" action="{{ route('kom.super_review_pengajar.index') }}" class="kom-rv-filter">
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
                <label>{{ __('Pengajar') }}</label>
                <select name="pengajar_id" class="form-control">
                    <option value="">{{ __('Semua Pengajar') }}</option>
                    @foreach ($pengajarList as $p)
                        <option value="{{ $p->id }}" @selected($filters['pengajar_id'] == $p->id)>{{ $p->name }}</option>
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
            <div>
                <label>{{ __('Dari Tanggal') }}</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="form-control">
            </div>
            <div>
                <label>{{ __('Sampai Tanggal') }}</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="form-control">
            </div>
            <div>
                <label>{{ __('Urutkan') }}</label>
                <select name="urut" class="form-control">
                    <option value="terbaru" @selected($filters['urut'] === 'terbaru')>{{ __('Review terbaru') }}</option>
                    <option value="tertinggi" @selected($filters['urut'] === 'tertinggi')>{{ __('Nilai tertinggi') }}</option>
                    <option value="terendah" @selected($filters['urut'] === 'terendah')>{{ __('Nilai terendah') }}</option>
                    <option value="terbanyak" @selected($filters['urut'] === 'terbanyak')>{{ __('Review terbanyak') }}</option>
                </select>
            </div>
            <div class="tombol">
                <button type="submit" class="kom-rv-btn"><i class="fas fa-filter mr-1"></i>{{ __('Filter') }}</button>
                <a href="{{ route('kom.super_review_pengajar.index') }}" class="kom-rv-btn abu">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>

    <div class="row">
        {{-- Rata-rata per kriteria --}}
        <div class="col-lg-5">
            <div class="kom-rv-card">
                <h5>{{ __('Rata-rata per Penilaian') }}</h5>
                @foreach ($kriteria as $kolom => $label)
                    @php $v = $rataKriteria[$kolom] ?? 0; @endphp
                    <div class="kom-rv-bar">
                        <span class="nama">{{ __($label) }}</span>
                        <span class="jalur"><div style="width: {{ min(100, $v / 5 * 100) }}%"></div></span>
                        <span class="angka">{{ $v > 0 ? number_format($v, 1) : '-' }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Per pengajar --}}
        <div class="col-lg-7">
            <div class="kom-rv-card">
                <h5>{{ __('Daftar Pengajar') }}</h5>
                @if ($perPengajar->isEmpty())
                    <div class="kom-rv-kosong"><i class="fas fa-comment-dots"></i>{{ __('Belum ada review yang tersedia.') }}</div>
                @else
                    <div class="kom-rv-wrap">
                        <table class="table kom-rv-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Pengajar') }}</th>
                                    <th class="text-center">{{ __('Review') }}</th>
                                    <th class="text-center">{{ __('Kelas') }}</th>
                                    <th>{{ __('Rata-rata') }}</th>
                                    <th>{{ __('Review Terakhir') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($perPengajar as $row)
                                    @php $p = $pengajar[$row->pengajar_id] ?? null; @endphp
                                    <tr>
                                        <td class="text-muted">{{ $perPengajar->firstItem() + $loop->index }}</td>
                                        <td>
                                            <div class="font-weight-bold">{{ $p->name ?? __('(pengajar tidak ditemukan)') }}</div>
                                            <small class="text-muted">{{ $p->email ?? '' }}</small>
                                            @if ($row->total_cabang > 1)
                                                <div><span class="kom-rv-chip biru">{{ __(':n cabang', ['n' => $row->total_cabang]) }}</span></div>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $row->total_review }}</td>
                                        <td class="text-center">{{ $row->total_kelas }}</td>
                                        <td>@include('a_kom_digital_pusat.superadmin.review_pengajar._bintang', ['nilai' => $row->rata_rata])</td>
                                        <td class="text-nowrap">{{ $row->review_terakhir ? \Carbon\Carbon::parse($row->review_terakhir)->translatedFormat('d M Y') : '-' }}</td>
                                        <td class="text-right">
                                            @if ($row->pengajar_id)
                                                <a href="{{ route('kom.super_review_pengajar.show', array_merge(['pengajarId' => $row->pengajar_id], array_filter(\Illuminate\Support\Arr::only($filters, ['cabang_id', 'modul_id', 'date_from', 'date_to', 'jenis_tanggal'])))) }}"
                                                   class="kom-rv-btn" style="padding:6px 12px;font-size:.8rem">
                                                    <i class="fas fa-eye mr-1"></i>{{ __('Detail') }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-center">{{ $perPengajar->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
