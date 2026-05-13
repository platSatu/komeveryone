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
                <div class="card-header bg-gradient-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-hand-holding-heart me-2"></i>&nbsp;&nbsp;
                            <h4 class="mb-0 pb-2">Donasi Per Kelas Tutorial</h4>
                        </div>
                       
                    </div>
                </div>
            </div>

            {{-- Overall Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Donasi
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalDonasi">
                                        {{ $overallStats['total_donasi'] ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-hand-holding-heart fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Jumlah
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalAmount">
                                        Rp {{ number_format($overallStats['total_amount'] ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                                        Jumlah Bersih
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Rp {{ number_format($overallStats['net_amount'] ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-info">
                                        Fee: Rp {{ number_format($overallStats['total_fee'] ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-coins fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Rata-rata Donasi
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Rp {{ number_format($overallStats['avg_amount'] ?? 0, 0, ',', '.') }}
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

            {{-- Filter Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>&nbsp;&nbsp;Filter Donasi
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('kom.donasi.index') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="class_tutorial_id" class="form-label fw-semibold">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>&nbsp;&nbsp;Kelas Tutorial
                                </label>
                                <select name="class_tutorial_id" id="class_tutorial_id" class="form-select form-control">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelasTutorialList as $kelas)
                                        <option value="{{ $kelas->id }}" {{ request('class_tutorial_id') == $kelas->id ? 'selected' : '' }}>
                                            {{ $kelas->kelas->name ?? '-' }} - {{ $kelas->modul->name ?? '-' }} 
                                            ({{ \Carbon\Carbon::parse($kelas->start_time)->format('d/m/Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="amount_min" class="form-label fw-semibold">
                                    <i class="fas fa-money-bill me-1"></i>&nbsp;&nbsp;Jumlah Min
                                </label>
                                <input type="number" name="amount_min" id="amount_min" class="form-control" 
                                       placeholder="0" value="{{ request('amount_min') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="amount_max" class="form-label fw-semibold">
                                    <i class="fas fa-money-bill me-1"></i>&nbsp;&nbsp;Jumlah Max
                                </label>
                                <input type="number" name="amount_max" id="amount_max" class="form-control" 
                                       placeholder="0" value="{{ request('amount_max') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-alt me-1"></i>&nbsp;&nbsp;Dari Tanggal
                                </label>
                                <input type="date" name="date_from" id="date_from" class="form-control" 
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-alt me-1"></i>&nbsp;&nbsp;Sampai Tanggal
                                </label>
                                <input type="date" name="date_to" id="date_to" class="form-control" 
                                       value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <div class="d-grid w-100 gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    @if(request()->hasAny(['class_tutorial_id', 'amount_min', 'amount_max', 'date_from', 'date_to']))
                                        <a href="{{ route('kom.donasi.index') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Payment Type Breakdown --}}
            {{-- @if($overallStats['payment_type_breakdown']->isNotEmpty()) --}}
            @if(!empty($overallStats['payment_type_breakdown']) && count($overallStats['payment_type_breakdown']) > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Breakdown Metode Pembayaran
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($overallStats['payment_type_breakdown'] as $paymentType => $data)
                                <div class="col-md-3 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-body text-center">
                                            @php
                                                $icon = match($paymentType) {
                                                    'bank_transfer' => 'fas fa-university',
                                                    'credit_card' => 'fas fa-credit-card',
                                                    'gopay' => 'fas fa-mobile-alt',
                                                    'shopeepay' => 'fas fa-mobile-alt',
                                                    'qris' => 'fas fa-qrcode',
                                                    'indomaret' => 'fas fa-store',
                                                    'alfamart' => 'fas fa-store',
                                                    default => 'fas fa-money-bill'
                                                };
                                            @endphp
                                            <i class="{{ $icon }} fa-2x text-primary mb-2"></i>
                                            <h6 class="font-weight-bold">{{ ucfirst(str_replace('_', ' ', $paymentType)) }}</h6>
                                            <div class="text-primary">{{ $data->total }} transaksi</div>
                                            <div class="text-success">Rp {{ number_format($data->total_amount, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Donasi Data Grouped by Kelas Tutorial --}}
            @if($donasiData->isNotEmpty())
                @foreach($donasiData as $kelasId => $data)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-gradient-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-lg bg-white rounded-circle d-flex align-items-center justify-content-center me-3 mr-3">
                                        <i class="fas fa-chalkboard-teacher text-primary fa-lg"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ $data['class_tutorial']->kelas->name ?? 'Kelas Tidak Diketahui' }}</h5>
                                        <small class="text-white-75">
                                            {{ $data['class_tutorial']->modul->name ?? '-' }} | 
                                            {{ $data['class_tutorial']->pengajar->name ?? '-' }}
                                        </small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="h4 mb-0">Rp {{ number_format($data['total_amount'], 0, ',', '.') }}</div>
                                    <small class="text-white-75">{{ $data['total_donasi'] }} donasi</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Summary Stats --}}
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h6 mb-1 text-success">Rp {{ number_format($data['total_amount'], 0, ',', '.') }}</div>
                                        <small class="text-muted">Total Donasi</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h6 mb-1 text-danger">Rp {{ number_format($data['total_fee'], 0, ',', '.') }}</div>
                                        <small class="text-muted">Total Fee</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h6 mb-1 text-primary">Rp {{ number_format($data['net_amount'], 0, ',', '.') }}</div>
                                        <small class="text-muted">Jumlah Bersih</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h6 mb-1 text-info">Rp {{ number_format($data['avg_amount'], 0, ',', '.') }}</div>
                                        <small class="text-muted">Rata-rata</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Donasi List --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="font-weight-bold mb-0">
                                            <i class="fas fa-list me-2"></i>&nbsp;&nbsp;Detail Donasi ({{ $data['total_donasi'] }})
                                        </h6>
                                        <button class="btn btn-outline-primary btn-sm" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#donasiList{{ $kelasId }}" 
                                                aria-expanded="false">
                                            <i class="fas fa-eye me-1"></i>&nbsp;&nbsp;Lihat Semua
                                        </button>
                                    </div>
                                    
                                    <div class="collapse" id="donasiList{{ $kelasId }}">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th width="5%">No</th>
                                                        <th width="20%">Donatur</th>
                                                        <th width="15%">Jumlah</th>
                                                        <th width="10%">Fee</th>
                                                        <th width="15%">Bersih</th>
                                                        <th width="15%">Metode</th>
                                                        <th width="15%">Tanggal</th>
                                                        <th width="5%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($data['donasi_list'] ?? [] as $index => $donasi)
                                                        <tr>
                                                            <td class="text-center">{{ $index + 1 }}</td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center me-2">
                                                                        <i class="fas fa-user text-white"></i>&nbsp;&nbsp;
                                                                    </div>
                                                                    <div>
                                                                        <strong>{{ $donasi->user->name ?? 'Anonim' }}</strong>
                                                                        <br><small class="text-muted">{{ $donasi->user->email ?? '-' }}</small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-success fs-6">
                                                                    {{ $donasi->formatted_amount }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-danger">
                                                                    {{ $donasi->formatted_fee }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-primary">
                                                                    {{ $donasi->formatted_net_amount }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $paymentBadge = match($donasi->payment_type) {
                                                                        'bank_transfer' => 'bg-info',
                                                                        'credit_card' => 'bg-warning',
                                                                        'gopay' => 'bg-success',
                                                                        'shopeepay' => 'bg-danger',
                                                                        'qris' => 'bg-dark',
                                                                        default => 'bg-secondary'
                                                                    };
                                                                @endphp
                                                                <span class="badge {{ $paymentBadge }}">
                                                                    {{ $donasi->payment_type_label }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <small>
                                                                    {{ $donasi->transaction_time->format('d/m/Y') }}
                                                                    <br>{{ $donasi->transaction_time->format('H:i') }}
                                                                </small>
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-info btn-sm" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#detailModal{{ $donasi->id }}"
                                                                        title="Detail">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                            </td>
                                                        </tr>

                                                        {{-- Modal Detail Donasi --}}
                                                        <div class="modal fade" id="detailModal{{ $donasi->id }}" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-success text-white">
                                                                        <h5 class="modal-title">
                                                                            <i class="fas fa-hand-holding-heart me-2"></i>Detail Donasi
                                                                        </h5>
                                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row g-4">
                                                                            <div class="col-md-6">
                                                                                <div class="card border-success">
                                                                                    <div class="card-header bg-success text-white">
                                                                                        <h6 class="mb-0">
                                                                                            <i class="fas fa-user me-2"></i>Informasi Donatur
                                                                                        </h6>
                                                                                    </div>
                                                                                    <div class="card-body">
                                                                                        <table class="table table-borderless table-sm">
                                                                                            <tr>
                                                                                                <td width="40%"><strong>Nama:</strong></td>
                                                                                                <td>{{ $donasi->user->name ?? 'Anonim' }}</td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td><strong>Email:</strong></td>
                                                                                                <td>{{ $donasi->user->email ?? '-' }}</td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td><strong>Order ID:</strong></td>
                                                                                                <td><code>{{ $donasi->order_id }}</code></td>
                                                                                            </tr>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="card border-primary">
                                                                                    <div class="card-header bg-primary text-white">
                                                                                        <h6 class="mb-0">
                                                                                            <i class="fas fa-chalkboard-teacher me-2"></i>Informasi Kelas
                                                                                        </h6>
                                                                                    </div>
                                                                                    <div class="card-body">
                                                                                        <table class="table table-borderless table-sm">
                                                                                            <tr>
                                                                                                <td width="40%"><strong>Kelas:</strong></td>
                                                                                                <td>{{ $donasi->kelas->name ?? $data['class_tutorial']->kelas->name ?? '-' }}</td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td><strong>Modul:</strong></td>
                                                                                                <td>{{ $donasi->modul->name ?? $data['class_tutorial']->modul->name ?? '-' }}</td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td><strong>Pengajar:</strong></td>
                                                                                                <td>{{ $data['class_tutorial']->pengajar->name ?? '-' }}</td>
                                                                                            </tr>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="card border-warning">
                                                                                    <div class="card-header bg-warning text-dark">
                                                                                        <h6 class="mb-0">
                                                                                            <i class="fas fa-money-bill me-2"></i>Detail Pembayaran
                                                                                        </h6>
                                                                                    </div>
                                                                                    <div class="card-body">
                                                                                        <div class="row">
                                                                                            <div class="col-md-6">
                                                                                                <table class="table table-borderless table-sm">
                                                                                                    <tr>
                                                                                                        <td width="40%"><strong>Jumlah Donasi:</strong></td>
                                                                                                        <td>
                                                                                                            <span class="badge bg-success fs-6">
                                                                                                                {{ $donasi->formatted_amount }}
                                                                                                            </span>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td><strong>Fee Gateway:</strong></td>
                                                                                                        <td>
                                                                                                            <span class="badge bg-danger">
                                                                                                                {{ $donasi->formatted_fee }}
                                                                                                            </span>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td><strong>Jumlah Bersih:</strong></td>
                                                                                                        <td>
                                                                                                            <span class="badge bg-primary fs-6">
                                                                                                                {{ $donasi->formatted_net_amount }}
                                                                                                            </span>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </div>
                                                                                            <div class="col-md-6">
                                                                                                <table class="table table-borderless table-sm">
                                                                                                    <tr>
                                                                                                        <td width="40%"><strong>Metode:</strong></td>
                                                                                                        <td>
                                                                                                            <span class="badge {{ $paymentBadge }}">
                                                                                                                {{ $donasi->payment_type_label }}
                                                                                                            </span>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td><strong>Status:</strong></td>
                                                                                                        <td>
                                                                                                            <span class="badge {{ $donasi->status_badge_class }}">
                                                                                                                {{ ucfirst($donasi->status) }}
                                                                                                            </span>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td><strong>Waktu:</strong></td>
                                                                                                        <td>{{ $donasi->transaction_time->format('d F Y H:i') }}</td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                            <i class="fas fa-times me-1"></i>Tutup
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-hand-holding-heart fa-3x mb-3"></i>
                                <h5>Tidak Ada Donasi</h5>
                                @if(request()->hasAny(['class_tutorial_id', 'amount_min', 'amount_max', 'date_from', 'date_to']))
                                    <p>Tidak ada donasi yang sesuai dengan filter yang dipilih.</p>
                                    <a href="{{ route('kom.donasi.index') }}" class="btn btn-primary">
                                        <i class="fas fa-refresh me-1"></i>Reset Filter
                                    </a>
                                @else
                                    <p>Belum ada donasi .</p>
                                   
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

                     {{-- Monthly Chart --}}
            @if($overallStats['monthly_stats']->isNotEmpty())
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>&nbsp;&nbsp;Trend Donasi 6 Bulan Terakhir
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Quick Actions --}}
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                        Export & Reports
                                    </div>
                                    <div class="text-white-50 small">
                                        Download laporan donasi
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-download fa-2x text-white-50"></i>&nbsp;&nbsp;
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <button onclick="exportDonasi()" class="btn btn-light btn-sm btn-block">
                                        <i class="fas fa-file-excel me-1"></i>&nbsp;&nbsp;Export Excel
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button onclick="printDonasi()" class="btn btn-light btn-sm btn-block">
                                        <i class="fas fa-print me-1"></i>&nbsp;&nbsp;Print Report
                                    </button>
                                </div>
                            </div>
                        </div>
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
    
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    
    .bg-gradient-success {
        background: linear-gradient(180deg, #1cc88a 10%, #13855c 100%);
    }
    
    .bg-gradient-primary {
        background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
    }
    
    .text-xs {
        font-size: 0.7rem;
    }
    
    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 12px;
    }
    
    .avatar-lg {
        width: 48px;
        height: 48px;
        font-size: 16px;
    }
    
    .card {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
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
    
    .fs-6 {
        font-size: 1rem !important;
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
    
    .text-white-75 {
        color: rgba(255, 255, 255, 0.75) !important;
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
    
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
    
    .table-borderless td {
        border: none;
        padding: 0.5rem 0.75rem;
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
        
        .card-body {
            padding: 1rem;
        }
        
        .avatar-lg {
            width: 40px;
            height: 40px;
            font-size: 14px;
        }
        
        .chart-container {
            height: 250px;
        }
        
        .table-responsive {
            font-size: 0.875rem;
        }
    }
</style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Monthly Trend Chart
    document.addEventListener('DOMContentLoaded', function() {
        const monthlyChart = document.getElementById('monthlyChart');
        if (monthlyChart) {
            const monthlyData = @json($overallStats['monthly_stats']);
            
            const labels = monthlyData.map(item => {
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
                                  'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                return monthNames[item.month - 1] + ' ' + item.year;
            }).reverse();
            
            const donasiData = monthlyData.map(item => item.total_donasi).reverse();
            const amountData = monthlyData.map(item => item.total_amount).reverse();
            
            new Chart(monthlyChart.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Donasi',
                        data: donasiData,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        yAxisID: 'y'
                    }, {
                        label: 'Total Amount (Rp)',
                        data: amountData,
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.1)',
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Bulan'
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Jumlah Donasi'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Total Amount (Rp)'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.datasetIndex === 1) {
                                        return context.dataset.label + ': Rp ' + 
                                               new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                    }
                                    return context.dataset.label + ': ' + context.parsed.y;
                                }
                            }
                        }
                    }
                }
            });
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
        const totalDonasi = document.getElementById('totalDonasi');
        
        if (totalDonasi) {
            const target = parseInt(totalDonasi.textContent);
            totalDonasi.textContent = '0';
            animateCounter(totalDonasi, target);
        }
    });

    // Auto submit form when select inputs change
    document.getElementById('class_tutorial_id').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    // Auto submit form when date inputs change (both dates must be filled)
    document.getElementById('date_from').addEventListener('change', function() {
        if (this.value && document.getElementById('date_to').value) {
            document.getElementById('filterForm').submit();
        }
    });
    
    document.getElementById('date_to').addEventListener('change', function() {
        if (this.value && document.getElementById('date_from').value) {
            document.getElementById('filterForm').submit();
        }
    });

    // Loading state untuk button submit
    document.getElementById('filterForm').addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        submitBtn.disabled = true;
    });

    // Enhanced collapse functionality
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.querySelector('i');
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            if (isExpanded) {
                icon.className = 'fas fa-eye me-1';
                this.innerHTML = '<i class="fas fa-eye me-1"></i>&nbsp;&nbsp;Lihat Semua';
            } else {
                icon.className = 'fas fa-eye-slash me-1';
                this.innerHTML = '<i class="fas fa-eye-slash me-1"></i>Sembunyikan';
            }
        });
    });

    // Export functionality
    function exportDonasi() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Mengekspor Data Donasi...',
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
                    text: 'Data donasi berhasil diekspor ke Excel',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Data donasi berhasil diekspor!');
            }
        }, 2000);
    }

    // Print functionality
    function printDonasi() {
        const printContent = document.querySelector('.container-fluid').innerHTML;
        const originalContent = document.body.innerHTML;
        
        document.body.innerHTML = `
            <div style="padding: 20px; font-family: Arial, sans-serif;">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1>Laporan Donasi Per Kelas Tutorial</h1>
                    <p>Dicetak pada: ${new Date().toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    })}</p>
                </div>
                ${printContent}
            </div>
        `;
        
               // Apply print styles
        const style = document.createElement('style');
        style.textContent = `
            @media print {
                body { font-size: 12px; }
                .btn, .modal, .collapse { display: none !important; }
                .card { break-inside: avoid; margin-bottom: 20px; }
                .badge { background: none !important; color: #000 !important; border: 1px solid #000; }
                .table { width: 100%; border-collapse: collapse; }
                .table th, .table td { border: 1px solid #000; padding: 5px; }
                .chart-container { display: none; }
                .bg-gradient-success, .bg-gradient-primary { background: #f8f9fa !important; color: #000 !important; }
            }
        `;
        document.head.appendChild(style);
        
        window.print();
        
        // Restore original content
        document.body.innerHTML = originalContent;
        location.reload();
    }

    // Initialize tooltips
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

    // Format currency input
    document.getElementById('amount_min').addEventListener('input', function() {
        formatCurrencyInput(this);
    });
    
    document.getElementById('amount_max').addEventListener('input', function() {
        formatCurrencyInput(this);
    });

    function formatCurrencyInput(input) {
        let value = input.value.replace(/[^\d]/g, '');
        if (value) {
            input.value = parseInt(value).toLocaleString('id-ID');
        }
    }

    // Search functionality (optional enhancement)
    function searchDonasi() {
        const searchTerm = document.getElementById('searchInput');
        if (searchTerm) {
            const filter = searchTerm.value.toUpperCase();
            const tables = document.querySelectorAll('.table tbody');
            
            tables.forEach(tbody => {
                const rows = tbody.getElementsByTagName('tr');
                for (let i = 0; i < rows.length; i++) {
                    const text = rows[i].textContent || rows[i].innerText;
                    if (text.toUpperCase().indexOf(filter) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            });
        }
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + F untuk focus ke filter
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            document.getElementById('class_tutorial_id').focus();
        }
        
        // Escape untuk reset filter
        if (e.key === 'Escape') {
            const resetBtn = document.querySelector('a[href*="donasi"]:not([href*="?"])');
            if (resetBtn) {
                window.location.href = resetBtn.href;
            }
        }
        
        // Ctrl + P untuk print
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            printDonasi();
        }
        
        // Ctrl + E untuk export
        if (e.ctrlKey && e.key === 'e') {
            e.preventDefault();
            exportDonasi();
        }
    });

    // Auto-refresh data setiap 10 menit (optional)
    setInterval(function() {
        // Uncomment jika ingin auto refresh
        // location.reload();
    }, 600000); // 10 menit

    // Page visibility API untuk pause/resume auto-refresh
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            console.log('Page hidden - pausing auto-refresh');
        } else {
            console.log('Page visible - resuming auto-refresh');
        }
    });

    // Enhanced error handling
    window.addEventListener('error', function(e) {
        console.error('JavaScript Error:', e.error);
    });

    // Calculate totals helper
    function calculateTotals() {
        let totalAmount = 0;
        let totalFee = 0;
        
        document.querySelectorAll('.table tbody tr').forEach(row => {
            const amountText = row.querySelector('.badge.bg-success')?.textContent || '0';
            const feeText = row.querySelector('.badge.bg-danger')?.textContent || '0';
            
            const amount = parseInt(amountText.replace(/[^\d]/g, '')) || 0;
            const fee = parseInt(feeText.replace(/[^\d]/g, '')) || 0;
            
            totalAmount += amount;
            totalFee += fee;
        });
        
        return {
            totalAmount: totalAmount,
            totalFee: totalFee,
            netAmount: totalAmount - totalFee
        };
    }

    // Dynamic badge color based on amount
    function getBadgeClass(amount) {
        if (amount >= 1000000) return 'bg-success';
        if (amount >= 500000) return 'bg-primary';
        if (amount >= 100000) return 'bg-info';
        if (amount >= 50000) return 'bg-warning';
        return 'bg-secondary';
    }

    // Initialize all interactive elements
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Donasi page loaded successfully');
        
        // Initialize any additional components
        if (typeof $ !== 'undefined') {
            // jQuery-based initializations if needed
            $('.table').each(function() {
                // Add any table enhancements
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

    // Add refresh button functionality (if exists)
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshData);
    }

    // Modal enhancement
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('show.bs.modal', function() {
            // Add fade-in animation
            this.style.display = 'block';
            this.style.opacity = '0';
            setTimeout(() => {
                this.style.opacity = '1';
            }, 10);
        });
    });

    // Table row click enhancement
    document.querySelectorAll('.table tbody tr').forEach(row => {
        row.addEventListener('click', function(e) {
            if (!e.target.closest('.btn')) {
                // Highlight selected row
                document.querySelectorAll('.table tbody tr').forEach(r => r.classList.remove('table-active'));
                this.classList.add('table-active');
            }
        });
    });

    // Amount formatting helper
    function formatRupiah(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    // Date formatting helper
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    // Payment type icon helper
    function getPaymentIcon(paymentType) {
        const icons = {
            'bank_transfer': 'fas fa-university',
            'credit_card': 'fas fa-credit-card',
            'gopay': 'fas fa-mobile-alt',
            'shopeepay': 'fas fa-mobile-alt',
            'qris': 'fas fa-qrcode',
            'indomaret': 'fas fa-store',
            'alfamart': 'fas fa-store'
        };
        return icons[paymentType] || 'fas fa-money-bill';
    }
</script>
@endsection



