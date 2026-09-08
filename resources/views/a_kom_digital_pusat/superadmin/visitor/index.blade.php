@extends('layouts.KomDigital')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Statistik Pengunjung</h4>
                <p class="text-muted mb-0">Rentang: {{ $rangeLabel }}</p>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="btn-group" role="group">
                <a href="{{ route('kom.super_visitor.index', ['period' => 'week']) }}"
                   class="btn btn-{{ $period === 'week' ? 'primary' : 'outline-primary' }}">Minggu Ini</a>
                <a href="{{ route('kom.super_visitor.index', ['period' => 'month']) }}"
                   class="btn btn-{{ $period === 'month' ? 'primary' : 'outline-primary' }}">Bulan Ini</a>
                <a href="{{ route('kom.super_visitor.index', ['period' => 'year']) }}"
                   class="btn btn-{{ $period === 'year' ? 'primary' : 'outline-primary' }}">Tahun Ini</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Kunjungan</h6>
                    <h2 class="mb-0">{{ number_format($totalVisits, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Pengunjung Unik (IP)</h6>
                    <h2 class="mb-0">{{ number_format($uniqueVisitors, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Grafik Kunjungan</h5>
                </div>
                <div class="card-body">
                    <canvas id="visitorChart" height="110"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Halaman Paling Banyak Dikunjungi</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Halaman</th>
                                    <th class="text-end">Kunjungan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPages as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="text-break" style="max-width: 260px;">
                                            {{ \Illuminate\Support\Str::after($row->page_url, request()->getSchemeAndHttpHost()) }}
                                        </td>
                                        <td class="text-end">{{ number_format($row->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">Belum ada data kunjungan pada rentang ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    (function () {
        var ctx = document.getElementById('visitorChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Kunjungan',
                    data: @json($chartValues),
                    borderColor: '#3b7ddd',
                    backgroundColor: 'rgba(59, 125, 221, 0.15)',
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    })();
</script>
@endsection
