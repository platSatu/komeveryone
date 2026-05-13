@extends('layouts.KomDigital')

@section('content')
<div class="row justify-content-center mb-4">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-3">Filter Statistik Progress User</h4>
                <form method="GET" action="{{ url()->current() }}" class="row g-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label for="filter_type" class="form-label">Filter Berdasarkan</label>
                        <select name="filter_type" id="filter_type" class="form-select form-control" onchange="toggleFilterInputs()">
                            <option value="month" {{ (isset($filterType) && $filterType == 'month') ? 'selected' : '' }}>Bulan</option>
                            <option value="year" {{ (isset($filterType) && $filterType == 'year') ? 'selected' : '' }}>Tahun</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-3" id="monthInput" style="{{ (isset($filterType) && $filterType == 'month') ? '' : 'display:none;' }}">
                        <label for="month" class="form-label">Bulan</label>
                        <select name="month" id="month" class="form-select form-control">
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}" {{ (isset($month) && $month == $m) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="year" class="form-label">Tahun</label>
                        <select name="year" id="year" class="form-select form-control">
                            @foreach(range(date('Y'), date('Y') - 5) as $y)
                                <option value="{{ $y }}" {{ (isset($year) && $year == $y) ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3>Statistik Progress User (Data Dinamis)</h3>
                <div style="position: relative; height: 400px; width: 100%;">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleFilterInputs() {
        const filterType = document.getElementById('filter_type').value;
        const monthInput = document.getElementById('monthInput');
        if (filterType === 'month') {
            monthInput.style.display = '';
        } else {
            monthInput.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleFilterInputs(); // pastikan input bulan sesuai filter saat load halaman

        const ctx = document.getElementById('progressChart').getContext('2d');
        const chartData = @json($chartData);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Progress Video dan Modul'
                    }
                }
            }
        });
    });
</script>
@endsection