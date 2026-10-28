@extends('layouts.KomDigital')

@section('page_title', __('Statistik Pengunjung'))

{{--
    Statistik Pengunjung (data dari App\Http\Middleware\TrackVisitor -> visitor_histories).
    kom_v02: tampilan kartu, mengikuti gaya halaman kom_v02 lain (katalog._style).
--}}
@section('content')
@php
    $host = request()->getSchemeAndHttpHost();
    $path = fn ($url) => \Illuminate\Support\Str::after($url, $host) ?: '/';
    $angka = fn ($n) => number_format($n, 0, ',', '.');
    $topPage = $topPages->first();
    $maxPage = max(1, (int) optional($topPage)->total);
    $periods = ['week' => __('Minggu Ini'), 'month' => __('Bulan Ini'), 'year' => __('Tahun Ini')];
@endphp

<div class="kom-page container-fluid px-0">

    {{-- Filter periode --}}
    <div class="kom-card kom-stat-filter mb-3">
        <div class="kom-stat-tabs">
            @foreach ($periods as $kode => $nama)
                <a href="{{ route('kom.super_visitor.index', ['period' => $kode]) }}" class="{{ $period === $kode ? 'is-active' : '' }}">{{ $nama }}</a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('kom.super_visitor.index') }}" class="kom-stat-range">
            <input type="hidden" name="period" value="custom">
            <input type="date" name="start" value="{{ $customStart }}" class="form-control form-control-sm" required>
            <span class="text-muted">{{ __('s/d') }}</span>
            <input type="date" name="end" value="{{ $customEnd }}" class="form-control form-control-sm" required>
            <button type="submit" class="btn btn-sm {{ $period === 'custom' ? 'btn-primary' : 'btn-outline-primary' }} kom-stat-apply">{{ __('Terapkan') }}</button>
        </form>

        <div class="kom-stat-rangelabel"><i class="far fa-calendar-alt mr-1"></i>{{ $rangeLabel }}</div>
    </div>

    {{-- Ringkasan --}}
    <div class="row">
        <div class="col-6 col-xl-3 mb-3">
            <div class="kom-card kom-stat-card">
                <span class="kom-stat-icon"><i class="fas fa-eye"></i></span>
                <div class="kom-stat-body">
                    <small>{{ __('Total Kunjungan') }}</small>
                    <strong>{{ $angka($totalVisits) }}</strong>
                    <span>{{ __('halaman dibuka') }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 mb-3">
            <div class="kom-card kom-stat-card">
                <span class="kom-stat-icon is-green"><i class="fas fa-chart-line"></i></span>
                <div class="kom-stat-body">
                    <small>{{ __('Rata-rata per :satuan', ['satuan' => $bucketLabel]) }}</small>
                    <strong>{{ $angka($average) }}</strong>
                    <span>{{ __('kunjungan') }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 mb-3">
            <div class="kom-card kom-stat-card">
                <span class="kom-stat-icon is-orange"><i class="fas fa-fire"></i></span>
                <div class="kom-stat-body">
                    <small>{{ __('Paling Ramai') }}</small>
                    <strong>{{ $peakLabel ?? '-' }}</strong>
                    <span>{{ $peakLabel ? __(':n kunjungan', ['n' => $angka($peakValue)]) : __('belum ada data') }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 mb-3">
            <div class="kom-card kom-stat-card">
                <span class="kom-stat-icon is-purple"><i class="fas fa-star"></i></span>
                <div class="kom-stat-body">
                    <small>{{ __('Halaman Terpopuler') }}</small>
                    <strong class="kom-stat-path" title="{{ $topPage ? $path($topPage->page_url) : '' }}">{{ $topPage ? $path($topPage->page_url) : '-' }}</strong>
                    <span>{{ $topPage ? __(':n kunjungan', ['n' => $angka($topPage->total)]) : __('belum ada data') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Grafik --}}
        <div class="col-lg-8 mb-3">
            <div class="kom-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="kom-card-title mb-0">{{ __('Grafik Kunjungan') }}</h5>
                    <span class="kom-pill">{{ __('per :satuan', ['satuan' => $bucketLabel]) }}</span>
                </div>
                <div class="kom-stat-chart">
                    <canvas id="visitorChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Halaman terpopuler --}}
        <div class="col-lg-4 mb-3">
            <div class="kom-card h-100">
                <h5 class="kom-card-title">{{ __('Halaman Paling Banyak Dikunjungi') }}</h5>

                @forelse ($topPages as $i => $row)
                    <div class="kom-stat-page">
                        <div class="d-flex align-items-center">
                            <span class="kom-stat-rank {{ $i < 3 ? 'is-top' : '' }}">{{ $i + 1 }}</span>
                            <span class="kom-stat-path flex-grow-1" title="{{ $path($row->page_url) }}">{{ $path($row->page_url) }}</span>
                            <strong class="ml-2">{{ $angka($row->total) }}</strong>
                        </div>
                        <div class="kom-progress kom-stat-bar"><div style="width: {{ round($row->total / $maxPage * 100) }}%"></div></div>
                    </div>
                @empty
                    <div class="kom-stat-empty">
                        <i class="far fa-chart-bar"></i>
                        <p>{{ __('Belum ada data kunjungan pada rentang ini.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@include('a_kom_digital_pusat.katalog._style')
<style>
    .kom-stat-filter { display:flex; flex-wrap:wrap; align-items:center; gap:12px 20px; padding:16px 20px; }
    .kom-stat-tabs { display:inline-flex; background:#f1f4f9; border-radius:12px; padding:4px; }
    .kom-stat-tabs a { padding:8px 16px; border-radius:9px; color:#5b6576; font-weight:600; font-size:.9rem; text-decoration:none; transition:.15s; white-space:nowrap; }
    .kom-stat-tabs a:hover { color:#2a5298; }
    .kom-stat-tabs a.is-active { background:#fff; color:#2a5298; box-shadow:0 2px 8px rgba(30,60,114,.12); }
    .kom-stat-range { display:flex; flex-wrap:wrap; align-items:center; gap:8px; }
    .kom-stat-range .form-control { width:150px; border-radius:10px; border-color:#e1e7f0; }
    .kom-stat-apply { border-radius:10px; font-weight:600; }
    .kom-stat-rangelabel { margin-left:auto; color:#5b6576; font-size:.88rem; background:#f6f8fc; border-radius:999px; padding:6px 14px; }

    .kom-stat-card { display:flex; align-items:center; gap:14px; height:100%; padding:20px; }
    .kom-stat-icon { flex:0 0 48px; width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:20px; background:#eef3fc; color:#2a5298; }
    .kom-stat-icon.is-green { background:#e6f6ec; color:#1e7e34; }
    .kom-stat-icon.is-orange { background:#fff3e0; color:#d9822b; }
    .kom-stat-icon.is-purple { background:#f0ecff; color:#6a5cff; }
    .kom-stat-body { min-width:0; display:flex; flex-direction:column; }
    .kom-stat-body small { color:#6b7585; font-size:.78rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; }
    .kom-stat-body strong { color:#1f2a44; font-size:1.55rem; font-weight:700; line-height:1.25; }
    .kom-stat-body span { color:#7a8699; font-size:.82rem; }
    .kom-stat-body strong.kom-stat-path { font-size:1rem; margin:4px 0 2px; }
    .kom-stat-path { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; min-width:0; color:#1f2a44; }

    /* tinggi tetap: Chart.js butuh wadah dengan tinggi pasti supaya tidak membesar terus */
    .kom-stat-chart { position:relative; height:400px; }

    .kom-stat-page { padding:10px 0; border-bottom:1px solid #eef1f6; }
    .kom-stat-page:last-child { border-bottom:0; }
    .kom-stat-rank { flex:0 0 26px; width:26px; height:26px; border-radius:8px; margin-right:10px; display:inline-flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:700; background:#f1f4f9; color:#6b7585; }
    .kom-stat-rank.is-top { background:#2a5298; color:#fff; }
    .kom-stat-bar { height:6px; margin:8px 0 0 36px; }
    .kom-stat-empty { text-align:center; color:#7a8699; padding:40px 10px; }
    .kom-stat-empty i { font-size:34px; color:#c3cbd8; margin-bottom:10px; }

    @media (max-width: 767.98px) {
        .kom-stat-rangelabel { margin-left:0; }
        .kom-stat-tabs { width:100%; overflow-x:auto; }
        .kom-stat-range .form-control { width:auto; flex:1 1 130px; }
        .kom-stat-card { flex-direction:column; align-items:flex-start; gap:10px; padding:16px; }
        .kom-stat-icon { flex:0 0 40px; width:40px; height:40px; font-size:17px; border-radius:12px; }
        .kom-stat-body { width:100%; }
        .kom-stat-body small { font-size:.7rem; letter-spacing:.2px; }
        .kom-stat-body strong { font-size:1.25rem; }
        .kom-stat-chart { height:240px; }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function () {
        var canvas = document.getElementById('visitorChart');
        if (!canvas || typeof Chart === 'undefined') return;
        var ctx = canvas.getContext('2d');
        var gradient = ctx.createLinearGradient(0, 0, 0, 320);
        gradient.addColorStop(0, 'rgba(42, 82, 152, 0.28)');
        gradient.addColorStop(1, 'rgba(42, 82, 152, 0.02)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: @json(__('Kunjungan')),
                    data: @json($chartValues),
                    borderColor: '#2a5298',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2a5298',
                    fill: true,
                    tension: 0.35,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#1f2a44', padding: 10, cornerRadius: 10, displayColors: false }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#7a8699', maxRotation: 0, autoSkip: true } },
                    y: { beginAtZero: true, grid: { color: '#eef1f6' }, border: { display: false }, ticks: { color: '#7a8699', precision: 0 } }
                }
            }
        });
    })();
</script>
@endsection
