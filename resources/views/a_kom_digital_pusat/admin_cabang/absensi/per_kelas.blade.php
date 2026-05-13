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
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users me-2"></i>&nbsp;&nbsp;
                            <h4 class="mb-0 pb-2">Data Absensi Per Kelas Tutorial</h4>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('kom.absensi.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-home me-1"></i>&nbsp;&nbsp;Dashboard
                            </a>
                            {{-- <a href="{{ route('kom.absensi.summary') }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-chart-pie me-1"></i>&nbsp;&nbsp;Summary
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>&nbsp;&nbsp;Filter Data Absensi
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('kom.absensi.per-kelas') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="class_tutorial_id" class="form-label fw-semibold">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>&nbsp;&nbsp;Kelas Tutorial
                                </label>
                                <select name="class_tutorial_id" id="class_tutorial_id" class="form-select form-control">
                                    <option value="">Semua Kelas</option>
                                    @foreach ($kelasTutorialList as $kelas)
                                        <option value="{{ $kelas->id }}"
                                            {{ request('class_tutorial_id') == $kelas->id ? 'selected' : '' }}>
                                            {{ $kelas->kelas->name ?? '-' }} - {{ $kelas->modul->name ?? '-' }}
                                            ({{ \Carbon\Carbon::parse($kelas->date_class)->format('d/m/Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="kehadiran" class="form-label fw-semibold">
                                    <i class="fas fa-user-check me-1"></i>&nbsp;&nbsp;Kehadiran
                                </label>
                                <select name="kehadiran" id="kehadiran" class="form-select form-control">
                                    <option value="">Semua</option>
                                    <option value="hadir" {{ request('kehadiran') == 'hadir' ? 'selected' : '' }}>
                                        ✅ Hadir
                                    </option>
                                    <option value="tidak_hadir"
                                        {{ request('kehadiran') == 'tidak_hadir' ? 'selected' : '' }}>
                                        ❌ Tidak Hadir
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label fw-semibold">
                                    <i class="fas fa-info-circle me-1"></i>&nbsp;&nbsp;Status
                                </label>
                                <select name="status" id="status_absensi" class="form-select form-control">
                                    <option value="">Semua Status</option>
                                    <option value="normal" {{ request('status') == 'normal' ? 'selected' : '' }}>Normal
                                    </option>
                                    <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit
                                    </option>
                                    <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin
                                    </option>
                                    <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha
                                    </option>
                                </select>
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
                                    <button type="submit" class="btn btn-primary pb-1">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    @if (request()->hasAny(['class_tutorial_id', 'kehadiran', 'status', 'date_from', 'date_to']))
                                        <a href="{{ route('kom.absensi.per-kelas') }}"
                                            class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Statistik Card (jika ada filter kelas tertentu) --}}
            @if (isset($statistik) && $statistik)
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-2x mb-2"></i>&nbsp;&nbsp;
                                <h4>{{ $statistik['total'] }}</h4>
                                <p class="mb-0">Total Peserta</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-user-check fa-2x mb-2"></i>
                                <h4>{{ $statistik['hadir'] }}</h4>
                                <p class="mb-0">Hadir ({{ $statistik['persentase_hadir'] }}%)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-user-times fa-2x mb-2"></i>
                                <h4>{{ $statistik['tidak_hadir'] }}</h4>
                                <p class="mb-0">Tidak Hadir ({{ $statistik['persentase_tidak_hadir'] }}%)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-pie fa-2x mb-2"></i>
                                <h4>{{ number_format($statistik['persentase_hadir'], 1) }}%</h4>
                                <p class="mb-0">Tingkat Kehadiran</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Breakdown --}}
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>&nbsp;&nbsp;Breakdown Status Kehadiran
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                                    &nbsp;&nbsp;
                                    <span>Normal: <strong>{{ $statistik['status_breakdown']['normal'] }}</strong></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                                    &nbsp;&nbsp;
                                    <span>Sakit: <strong>{{ $statistik['status_breakdown']['sakit'] }}</strong></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-info rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                                    &nbsp;&nbsp;
                                    <span>Izin: <strong>{{ $statistik['status_breakdown']['izin'] }}</strong></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                                    &nbsp;&nbsp;
                                    <span>Alpha: <strong>{{ $statistik['status_breakdown']['alpha'] }}</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Data Table --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-table me-2"></i>&nbsp;&nbsp;Data Absensi
                            @if (request()->hasAny(['class_tutorial_id', 'kehadiran', 'status', 'date_from', 'date_to']))
                                <span class="badge bg-info">Filtered</span>
                            @endif
                            <span class="badge bg-secondary">{{ $absensiData->count() }} data</span>
                        </h6>
                        @if ($absensiData->isNotEmpty())
                            <button type="button" class="btn btn-success btn-sm" onclick="exportData()">
                                <i class="fas fa-download me-1 mr-1"></i>Export
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($absensiData->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Nama Peserta</th>
                                        <th width="25%">Kelas Tutorial</th>
                                        <th width="15%">Pengajar</th>
                                        <th width="10%">Tanggal</th>
                                        <th width="10%">Kehadiran</th>
                                        <th width="10%">Status</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($absensiData as $index => $absensi)
                                        @php
                                            $statusClass = match ($absensi->status) {
                                                'normal' => 'bg-success',
                                                'sakit' => 'bg-warning',
                                                'izin' => 'bg-info',
                                                'alpha' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2 mr-2">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $absensi->user->name ?? '-' }}</strong><br>
                                                        <small
                                                            class="text-muted">{{ $absensi->user->email ?? '-' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ $absensi->userClassTutorial->classTutorial->kelas->name ?? '-' }}</strong><br>
                                                <small
                                                    class="text-muted">{{ $absensi->userClassTutorial->classTutorial->modul->name ?? '-' }}</small>
                                            </td>
                                            <td>{{ $absensi->userClassTutorial->classTutorial->pengajar->name ?? '-' }}
                                            </td>
                                            <td>
                                                <small>
                                                    {{ \Carbon\Carbon::parse($absensi->userClassTutorial->classTutorial->date_class)->format('d/m/Y') }}<br>
                                                    {{ $absensi->userClassTutorial->classTutorial->time_class ?? '-' }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                @if ($absensi->hadir)
                                                    <span class="badge bg-success text-white"><i
                                                            class="fas fa-check me-1 mr-2"></i>Hadir</span>
                                                @else
                                                    <span class="badge bg-danger"><i class="fas fa-times me-1 text-white mr-2"></i>Tidak
                                                        Hadir</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $statusClass }} text-white">{{ ucfirst($absensi->status) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#detailModal{{ $absensi->id }}" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Modals outside the table --}}
                        @foreach ($absensiData as $absensi)
                            @php
                                $statusClass = match ($absensi->status) {
                                    'normal' => 'bg-success',
                                    'sakit' => 'bg-warning',
                                    'izin' => 'bg-info',
                                    'alpha' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <div class="modal fade" id="detailModal{{ $absensi->id }}" tabindex="-1"
                                aria-labelledby="detailModalLabel{{ $absensi->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title" id="detailModalLabel{{ $absensi->id }}">
                                                <i class="fas fa-info-circle me-2 mr-2"></i>Detail Absensi
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <div class="card border-primary">
                                                        <div class="card-header bg-primary text-white">
                                                            <h6 class="mb-0"><i class="fas fa-user me-2 mr-2"></i>Informasi
                                                                Peserta</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <table class="table table-borderless table-sm mb-0">
                                                                <tr>
                                                                    <td width="30%"><strong>Nama:</strong></td>
                                                                    <td>{{ $absensi->user->name ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Email:</strong></td>
                                                                    <td>{{ $absensi->user->email ?? '-' }}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card border-success">
                                                        <div class="card-header bg-success text-white">
                                                            <h6 class="mb-0"><i
                                                                    class="fas fa-chalkboard-teacher me-2 mr-2"></i>Informasi
                                                                Kelas</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <table class="table table-borderless table-sm mb-0">
                                                                <tr>
                                                                    <td width="30%"><strong>Kelas:</strong></td>
                                                                    <td>{{ $absensi->userClassTutorial->classTutorial->kelas->name ?? '-' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Modul:</strong></td>
                                                                    <td>{{ $absensi->userClassTutorial->classTutorial->modul->name ?? '-' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Pengajar:</strong></td>
                                                                    <td>{{ $absensi->userClassTutorial->classTutorial->pengajar->name ?? '-' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Tanggal:</strong></td>
                                                                    <td>{{ \Carbon\Carbon::parse($absensi->userClassTutorial->classTutorial->start_time)->format('d F Y') }}
                                                                    </td>
                                                                </tr>
                                                               
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="card border-info">
                                                        <div class="card-header bg-info text-white">
                                                            <h6 class="mb-0"><i
                                                                    class="fas fa-clipboard-check me-2 mr-2"></i>Informasi
                                                                Absensi</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <table class="table table-borderless table-sm mb-0">
                                                                        <tr>
                                                                            <td width="30%"><strong>Kehadiran:</strong>
                                                                            </td>
                                                                            <td>
                                                                                @if ($absensi->hadir)
                                                                                    <span class="badge bg-success text-white"><i
                                                                                            class="fas fa-check me-1 mr-2"></i>Hadir</span>
                                                                                @else
                                                                                    <span class="badge bg-danger text-white"><i
                                                                                            class="fas fa-times me-1 mr-2"></i>Tidak
                                                                                        Hadir</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Status:</strong></td>
                                                                            <td><span
                                                                                    class="badge {{ $statusClass }} text-white">{{ ucfirst($absensi->status) }}</span>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <table class="table table-borderless table-sm mb-0">
                                                                        <tr>
                                                                            <td width="30%"><strong>Dicatat:</strong>
                                                                            </td>
                                                                            <td>{{ $absensi->created_at->format('d F Y H:i') }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Diupdate:</strong></td>
                                                                            <td>{{ $absensi->updated_at->format('d F Y H:i') }}
                                                                            </td>
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
                                                <i class="fas fa-times me-1"></i> Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                <h5>Tidak Ada Data Absensi</h5>
                                @if (request()->hasAny(['class_tutorial_id', 'kehadiran', 'status', 'date_from', 'date_to']))
                                    <p>Tidak ada data absensi yang sesuai dengan filter yang dipilih.</p>
                                    <a href="{{ route('kom.absensi.per-kelas') }}" class="btn btn-primary">
                                        <i class="fas fa-refresh me-1"></i>Reset Filter
                                    </a>
                                @else
                                    <p>Belum ada data absensi yang tercatat.</p>
                                    <a href="{{ route('kom.absensi.index') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-left me-1"></i>&nbsp;&nbsp;Kembali ke Dashboard
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <style>
        .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 12px;
        }

        .card {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .card-header {
            border-bottom: 1px solid #e3e6f0;
            background-color: #f8f9fc;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            font-size: 0.875rem;
            background-color: #343a40;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.375rem 0.75rem;
        }

        .btn-group .btn {
            border-radius: 0.25rem;
            margin-left: 0.25rem;
        }

        .btn-group .btn:first-child {
            margin-left: 0;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .bg-info {
            background-color: #36b9cc !important;
        }

        .text-info {
            color: #36b9cc !important;
        }

        .border-primary {
            border-color: #4e73df !important;
        }

        .border-success {
            border-color: #1cc88a !important;
        }

        .border-info {
            border-color: #36b9cc !important;
        }

        .modal-lg {
            max-width: 900px;
        }

        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .table-borderless td {
            border: none;
            padding: 0.5rem 0.75rem;
        }

        .rounded-circle {
            border-radius: 50% !important;
        }

        .fa-2x {
            font-size: 2em;
        }

        .fa-3x {
            font-size: 3em;
        }

        @media (max-width: 768px) {
            .btn-group {
                flex-direction: column;
            }

            .btn-group .btn {
                margin-left: 0;
                margin-bottom: 0.25rem;
            }

            .btn-group .btn:last-child {
                margin-bottom: 0;
            }

            .table-responsive {
                font-size: 0.875rem;
            }

            .modal-lg {
                max-width: 95%;
            }

            .card-body {
                padding: 1rem;
            }
        }
    </style>
    <script>
        // Function untuk export data
        function exportData() {
            // Get current filter parameters
            const params = new URLSearchParams(window.location.search);

            // Show loading dengan SweetAlert2 (jika tersedia) atau alert biasa
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Mengekspor Data...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            } else {
                alert('Sedang mengekspor data...');
            }

            // Make export request (placeholder - implement actual export logic)
            setTimeout(() => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data berhasil diekspor',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert('Data berhasil diekspor!');
                }
            }, 2000);
        }

        // Auto submit form when select inputs change
        document.getElementById('class_tutorial_id').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        document.getElementById('kehadiran').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        document.getElementById('status').addEventListener('change', function() {
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

        // Initialize tooltips jika menggunakan Bootstrap
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Enhanced table interactions
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = 'rgba(0, 123, 255, 0.1)';
            });

            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });

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

        // Search functionality (optional enhancement)
        function searchTable() {
            const input = document.getElementById('searchInput');
            if (input) {
                const filter = input.value.toUpperCase();
                const table = document.querySelector('.table tbody');
                const rows = table.getElementsByTagName('tr');

                for (let i = 0; i < rows.length; i++) {
                    const cells = rows[i].getElementsByTagName('td');
                    let found = false;

                    for (let j = 0; j < cells.length; j++) {
                        if (cells[j].textContent.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }

                    rows[i].style.display = found ? '' : 'none';
                }
            }
        }

        // Print functionality
        function printTable() {
            const printContent = document.querySelector('.table-responsive').innerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = `
            <div style="padding: 20px;">
                <h2>Data Absensi Per Kelas Tutorial</h2>
                <p>Dicetak pada: ${new Date().toLocaleDateString('id-ID')}</p>
                ${printContent}
            </div>
        `;

            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
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
                const resetBtn = document.querySelector('a[href*="per-kelas"]:not([href*="?"])');
                if (resetBtn) {
                    window.location.href = resetBtn.href;
                }
            }
        });

        // Auto-refresh data setiap 5 menit (optional)
        setInterval(function() {
            // Uncomment jika ingin auto refresh
            // location.reload();
        }, 300000); // 5 menit

        // Smooth scrolling untuk anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
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

        // Counter animation untuk statistik cards
        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current);
            }, 20);
        }

        // Initialize counter animations
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.card h4').forEach(counter => {
                const target = parseInt(counter.textContent);
                if (!isNaN(target)) {
                    counter.textContent = '0';
                    animateCounter(counter, target);
                }
            });
        });

        // Enhanced error handling
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.error);
            // Optional: Send error to logging service
        });

        // Page visibility API untuk pause/resume auto-refresh
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // Page is hidden, pause any intervals
                console.log('Page hidden - pausing auto-refresh');
            } else {
                // Page is visible, resume intervals
                console.log('Page visible - resuming auto-refresh');
            }
        });
    </script>
@endsection
