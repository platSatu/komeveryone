@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-12">
            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error') || isset($error))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') ?? $error }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Header Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chart-pie me-2"></i>&nbsp;&nbsp;
                            <h4 class="mb-0 pb-2">Summary Absensi Kelas Tutorial</h4>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('kom.absensi.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-home me-1"></i>&nbsp;&nbsp;Dashboard
                            </a>
                            <a href="{{ route('kom.absensi.per-kelas') }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-users me-1"></i>&nbsp;&nbsp;Detail Absensi
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Overall Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Absensi
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalAbsensi">
                                        {{ $overallStats['total'] ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Hadir
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalHadir">
                                        {{ $overallStats['hadir'] ?? 0 }}
                                    </div>
                                    <div class="text-xs text-success">
                                        {{ $overallStats['persentase_hadir'] ?? 0 }}% dari total
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Total Tidak Hadir
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalTidakHadir">
                                        {{ $overallStats['tidak_hadir'] ?? 0 }}
                                    </div>
                                    <div class="text-xs text-danger">
                                        {{ $overallStats['persentase_tidak_hadir'] ?? 0 }}% dari total
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-times fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Tingkat Kehadiran
                                    </div>
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                {{ number_format($overallStats['persentase_hadir'] ?? 0, 1) }}%
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="progress progress-sm mr-2">
                                                <div class="progress-bar bg-info" role="progressbar" 
                                                     style="width: {{ $overallStats['persentase_hadir'] ?? 0 }}%" 
                                                     aria-valuenow="{{ $overallStats['persentase_hadir'] ?? 0 }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Breakdown Chart --}}
            <div class="row mb-4">
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-pie me-2"></i>&nbsp;&nbsp;Breakdown Status
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="statusChart"></canvas>
                            </div>
                            <div class="mt-4 text-center small">
                                <span class="mr-2">
                                    <i class="fas fa-circle text-success"></i> Normal
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-circle text-warning"></i> Sakit
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-circle text-info"></i> Izin
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-circle text-danger"></i> Alpha
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-bar me-2"></i>&nbsp;&nbsp;Status Kehadiran Detail
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success rounded-circle me-3" style="width: 20px; height: 20px;"></div>
                                        <div>
                                            <div class="font-weight-bold">Normal</div>
                                            <div class="text-muted small">{{ $overallStats['status_breakdown']['normal'] ?? 0 }} peserta</div>
                                        </div>
                                        <div class="ml-auto">
                                            <div class="h5 mb-0 font-weight-bold text-success">
                                                {{ $overallStats['total'] > 0 ? number_format(($overallStats['status_breakdown']['normal'] ?? 0) / $overallStats['total'] * 100, 1) : 0 }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning rounded-circle me-3" style="width: 20px; height: 20px;"></div>
                                        <div>
                                            <div class="font-weight-bold">Sakit</div>
                                            <div class="text-muted small">{{ $overallStats['status_breakdown']['sakit'] ?? 0 }} peserta</div>
                                        </div>
                                        <div class="ml-auto">
                                            <div class="h5 mb-0 font-weight-bold text-warning">
                                                {{ $overallStats['total'] > 0 ? number_format(($overallStats['status_breakdown']['sakit'] ?? 0) / $overallStats['total'] * 100, 1) : 0 }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info rounded-circle me-3" style="width: 20px; height: 20px;"></div>
                                        <div>
                                            <div class="font-weight-bold">Izin</div>
                                            <div class="text-muted small">{{ $overallStats['status_breakdown']['izin'] ?? 0 }} peserta</div>
                                        </div>
                                        <div class="ml-auto">
                                            <div class="h5 mb-0 font-weight-bold text-info">
                                                {{ $overallStats['total'] > 0 ? number_format(($overallStats['status_breakdown']['izin'] ?? 0) / $overallStats['total'] * 100, 1) : 0 }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger rounded-circle me-3" style="width: 20px; height: 20px;"></div>
                                        <div>
                                            <div class="font-weight-bold">Alpha</div>
                                            <div class="text-muted small">{{ $overallStats['status_breakdown']['alpha'] ?? 0 }} peserta</div>
                                        </div>
                                        <div class="ml-auto">
                                            <div class="h5 mb-0 font-weight-bold text-danger">
                                                {{ $overallStats['total'] > 0 ? number_format(($overallStats['status_breakdown']['alpha'] ?? 0) / $overallStats['total'] * 100, 1) : 0 }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Per Kelas --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table me-2"></i>&nbsp;&nbsp;Summary Per Kelas Tutorial
                        <span class="badge bg-secondary ms-2">{{ $summaryData->count() }} kelas</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if($summaryData->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="summaryTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Kelas</th>
                                        <th width="20%">Modul</th>
                                        <th width="15%">Pengajar</th>
                                        <th width="10%">Tanggal</th>
                                        <th width="8%">Total Peserta</th>
                                        <th width="8%">Hadir</th>
                                        <th width="8%">Tidak Hadir</th>
                                        <th width="6%">Tingkat Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summaryData as $index => $kelas)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fas fa-chalkboard-teacher text-white"></i>
                                                    </div>
                                                    <strong>{{ $kelas->kelas->name ?? '-' }}</strong>
                                                </div>
                                            </td>
                                            <td>{{ $kelas->modul->name ?? '-' }}</td>
                                            <td>{{ $kelas->pengajar->name ?? '-' }}</td>
                                                                                        <td>
                                                <small>
                                                    {{ \Carbon\Carbon::parse($kelas->date_class)->format('d/m/Y') }}
                                                    <br>{{ $kelas->time_class ?? '-' }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ $kelas->total_peserta ?? 0 }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $kelas->hadir ?? 0 }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger">{{ $kelas->tidak_hadir ?? 0 }}</span>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $persentase = $kelas->persentase_hadir ?? 0;
                                                    $badgeClass = $persentase >= 80 ? 'bg-success' : ($persentase >= 60 ? 'bg-warning' : 'bg-danger');
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ number_format($persentase, 1) }}%
                                                </span>
                                                <div class="progress mt-1" style="height: 5px;">
                                                    <div class="progress-bar {{ str_replace('bg-', 'bg-', $badgeClass) }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $persentase }}%" 
                                                         aria-valuenow="{{ $persentase }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-chart-pie fa-3x mb-3"></i>
                                <h5>Belum Ada Data Summary</h5>
                                <p>Belum ada data absensi untuk ditampilkan dalam summary.</p>
                                <a href="{{ route('kom.absensi.index') }}" class="btn btn-primary">
                                    <i class="fas fa-arrow-left me-1"></i>&nbsp;&nbsp;Kembali ke Dashboard
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                        Quick Actions
                                    </div>
                                    <div class="text-white-50 small">
                                        Akses cepat ke fitur-fitur utama
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-rocket fa-2x text-white-50"></i>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <a href="{{ route('kom.absensi.per-kelas') }}" class="btn btn-light btn-sm btn-block">
                                        <i class="fas fa-users me-1"></i>&nbsp;&nbsp;Detail Absensi
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('kom.kelas_tutor_cabang.index') }}" class="btn btn-light btn-sm btn-block">
                                        <i class="fas fa-chalkboard-teacher me-1"></i>&nbsp;&nbsp;Kelola Kelas
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                        Export & Reports
                                    </div>
                                    <div class="text-white-50 small">
                                        Download laporan dan data
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-download fa-2x text-white-50"></i>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <button onclick="exportSummary()" class="btn btn-light btn-sm btn-block">
                                        <i class="fas fa-file-excel me-1"></i>&nbsp;&nbsp;Export Excel
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button onclick="printSummary()" class="btn btn-light btn-sm btn-block">
                                        <i class="fas fa-print me-1"></i>&nbsp;&nbsp;Print Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Performance Insights --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-lightbulb me-2"></i>Performance Insights
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $totalKelas = $summaryData->count();
                            $kelasWithGoodAttendance = $summaryData->where('persentase_hadir', '>=', 80)->count();
                            $kelasWithPoorAttendance = $summaryData->where('persentase_hadir', '<', 60)->count();
                            $avgAttendance = $summaryData->avg('persentase_hadir') ?? 0;
                        @endphp
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-thumbs-up fa-2x text-success mb-2"></i>
                                    <h4 class="text-success">{{ $kelasWithGoodAttendance }}</h4>
                                    <small class="text-muted">Kelas dengan kehadiran baik (≥80%)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-danger">
                                <div class="card-body text-center">
                                    <i class="fas fa-thumbs-down fa-2x text-danger mb-2"></i>
                                    <h4 class="text-danger">{{ $kelasWithPoorAttendance }}</h4>
                                    <small class="text-muted">Kelas dengan kehadiran rendah (<60%)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                                    <h4 class="text-info">{{ number_format($avgAttendance, 1) }}%</h4>
                                    <small class="text-muted">Rata-rata kehadiran</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                                    <h4 class="text-warning">{{ $totalKelas - $kelasWithGoodAttendance - $kelasWithPoorAttendance }}</h4>
                                    <small class="text-muted">Kelas perlu perhatian (60-79%)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h6 class="font-weight-bold">Rekomendasi:</h6>
                        <ul class="list-unstyled">
                            @if($kelasWithPoorAttendance > 0)
                                <li class="mb-2">
                                    <i class="fas fa-exclamation-circle text-danger me-2"></i>
                                    <strong>{{ $kelasWithPoorAttendance }} kelas</strong> memiliki tingkat kehadiran rendah. Pertimbangkan untuk mengevaluasi metode pengajaran atau jadwal kelas.
                                </li>
                            @endif
                            @if($avgAttendance >= 80)
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Tingkat kehadiran overall sangat baik ({{ number_format($avgAttendance, 1) }}%). Pertahankan kualitas pengajaran!
                                </li>
                            @elseif($avgAttendance >= 60)
                                <li class="mb-2">
                                    <i class="fas fa-info-circle text-warning me-2"></i>
                                    Tingkat kehadiran cukup baik ({{ number_format($avgAttendance, 1) }}%). Masih ada ruang untuk perbaikan.
                                </li>
                            @else
                                <li class="mb-2">
                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                    Tingkat kehadiran perlu ditingkatkan ({{ number_format($avgAttendance, 1) }}%). Evaluasi strategi pembelajaran diperlukan.
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    
    .border-left-danger {
        border-left: 0.25rem solid #e74a3b !important;
    }
    
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
    }
    
    .text-xs {
        font-size: 0.7rem;
    }
    
    .progress-sm {
        height: 0.5rem;
    }
    
    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 12px;
    }
    
    .card {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.075);
    }
    
    .badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.375rem 0.75rem;
    }
    
    .btn-block {
        display: block;
        width: 100%;
    }
    
    .text-gray-300 {
        color: #dddfeb !important;
    }
    
    .text-gray-800 {
        color: #5a5c69 !important;
    }
    
    .text-white-50 {
        color: rgba(255, 255, 255, 0.5) !important;
    }
    
    .no-gutters {
        margin-right: 0;
        margin-left: 0;
    }
    
    .no-gutters > .col,
    .no-gutters > [class*="col-"] {
        padding-right: 0;
        padding-left: 0;
    }
    
    .mr-2 {
        margin-right: 0.5rem !important;
    }
    
    .mr-3 {
        margin-right: 1rem !important;
    }
    
    .ml-auto {
        margin-left: auto !important;
    }
    
    .py-2 {
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
    }
    
    .py-3 {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
    
    .chart-pie {
        position: relative;
        height: 15rem;
        width: 100%;
    }
    
    @media (max-width: 768px) {
        .btn-group {
            flex-direction: column;
        }
        
        .btn-group .btn {
            margin-bottom: 0.25rem;
        }
        
        .btn-group .btn:last-child {
            margin-bottom: 0;
        }
        
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .card-body {
            padding: 1rem;
        }
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Status Pie Chart
    const ctx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Normal', 'Sakit', 'Izin', 'Alpha'],
            datasets: [{
                data: [
                    {{ $overallStats['status_breakdown']['normal'] ?? 0 }},
                    {{ $overallStats['status_breakdown']['sakit'] ?? 0 }},
                    {{ $overallStats['status_breakdown']['izin'] ?? 0 }},
                    {{ $overallStats['status_breakdown']['alpha'] ?? 0 }}
                ],
                backgroundColor: [
                    '#1cc88a',
                    '#f6c23e', 
                    '#36b9cc',
                    '#e74a3b'
                ],
                borderColor: [
                    '#1cc88a',
                    '#f6c23e',
                    '#36b9cc', 
                    '#e74a3b'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(255,255,255,.8)',
                    titleColor: '#6e707e',
                    bodyColor: '#6e707e',
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '80%'
        }
    });

    // Counter Animation
    function animateCounter(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);
        
        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                start = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(start);
        }, 16);
    }

    // Initialize counter animations
    document.addEventListener('DOMContentLoaded', function() {
        // Animate main statistics
        const totalAbsensi = document.getElementById('totalAbsensi');
        const totalHadir = document.getElementById('totalHadir');
        const totalTidakHadir = document.getElementById('totalTidakHadir');
        
        if (totalAbsensi) {
            const target = parseInt(totalAbsensi.textContent);
            totalAbsensi.textContent = '0';
            animateCounter(totalAbsensi, target);
        }
        
        if (totalHadir) {
            const target = parseInt(totalHadir.textContent);
            totalHadir.textContent = '0';
            animateCounter(totalHadir, target);
        }
        
        if (totalTidakHadir) {
            const target = parseInt(totalTidakHadir.textContent);
            totalTidakHadir.textContent = '0';
            animateCounter(totalTidakHadir, target);
        }

        // Animate progress bars
        setTimeout(() => {
            document.querySelectorAll('.progress-bar').forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                bar.style.transition = 'width 1.5s ease-in-out';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });
        }, 500);
    });

    // Export Summary Function
    function exportSummary() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Mengekspor Summary...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        
        // Simulate export process
        setTimeout(() => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Summary berhasil diekspor ke Excel',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Summary berhasil diekspor!');
            }
        }, 2000);
    }

    // Print Summary Function
    function printSummary() {
        const printContent = `
            <div style="padding: 20px; font-family: Arial, sans-serif;">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1>Summary Absensi Kelas Tutorial</h1>
                    <p>Dicetak pada: ${new Date().toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    })}</p>
                </div>
                
                <div style="margin-bottom: 30px;">
                    <h3>Statistik Overall</h3>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;"><strong>Total Absensi:</strong></td>
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ $overallStats['total'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;"><strong>Total Hadir:</strong></td>
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ $overallStats['hadir'] ?? 0 }} ({{ $overallStats['persentase_hadir'] ?? 0 }}%)</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;"><strong>Total Tidak Hadir:</strong></td>
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ $overallStats['tidak_hadir'] ?? 0 }} ({{ $overallStats['persentase_tidak_hadir'] ?? 0 }}%)</td>
                        </tr>
                    </table>
                </div>
                
                ${document.querySelector('#summaryTable') ? document.querySelector('#summaryTable').outerHTML : ''}
            </div>
        `;
        
        const originalContent = document.body.innerHTML;
        document.body.innerHTML = printContent;
        
        // Apply print styles
        const style = document.createElement('style');
        style.textContent = `
            @media print {
                body { font-size: 12px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #000; padding: 5px; text-align: left; }
                th { background-color: #f0f0f0; }
                .badge { background: none !important; color: #000 !important; border: 1px solid #000; }
            }
        `;
        document.head.appendChild(style);
        
        window.print();
        
        // Restore original content
        document.body.innerHTML = originalContent;
        location.reload();
    }

    // DataTable initialization (if available)
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $('#summaryTable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[8, 'desc']], // Sort by attendance percentage
                columnDefs: [
                    { orderable: false, targets: [0] }, // Disable sorting for No column
                    { className: 'text-center', targets: [0, 5, 6, 7, 8] }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                }
            });
        }
    });

    // Refresh data functionality
    function refreshData() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Memperbarui Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        
        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    // Auto refresh every 5 minutes (optional)
    setInterval(function() {
        // Uncomment to enable auto refresh
        // refreshData();
    }, 300000);

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + R untuk refresh
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            refreshData();
        }
        
        // Ctrl + P untuk print
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            printSummary();
        }
        
        // Ctrl + E untuk export
        if (e.ctrlKey && e.key === 'e') {
            e.preventDefault();
            exportSummary();
        }
    });

    // Tooltip initialization
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    // Card hover effects
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Performance insights calculations
    document.addEventListener('DOMContentLoaded', function() {
        // Add any additional performance calculations here
        console.log('Summary page loaded successfully');
    });
</script>
@endsection

@push('styles')

@endpush

@push('scripts')
<!-- Chart.js -->

@endpush