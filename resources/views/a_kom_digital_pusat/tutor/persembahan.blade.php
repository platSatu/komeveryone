@extends('layouts.KomDigital')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                {{-- Header Section --}}
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">💰 Persembahan Saya</h5>
                        <p class="text-muted mb-0">History persembahan yang telah Anda berikan</p>
                    </div>
                    <button class="btn btn-outline-primary btn-sm mt-2 mt-md-0" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>

                {{-- Stats Cards --}}
                <div class="row mb-4 g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                            <div class="card-body p-3 d-flex align-items-center">
                                <i class="fas fa-hand-holding-heart fa-2x opacity-75 flex-shrink-0 me-4"></i>
                                <div>
                                    <div class="fw-bold fs-4">{{ $data->total() }}</div>
                                    <div class="small opacity-75">Total Persembahan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm bg-gradient-success text-white">
                            <div class="card-body p-3 d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x opacity-75 flex-shrink-0 me-4"></i>
                                <div>
                                    <div class="fw-bold fs-4">
                                        {{ $data->whereIn('status', ['paid', 'settlement','pending', 'expired', 'denied'])->count() }}</div>
                                    <div class="small opacity-75">Persembahan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm bg-gradient-info text-white">
                            <div class="card-body p-3 d-flex align-items-center">
                                <i class="fas fa-coins fa-2x opacity-75 flex-shrink-0 me-4"></i>
                                <div>
                                    <div class="fw-bold fs-6">Rp
                                        {{ number_format($data->whereIn('status', ['paid', 'settlement','pending'])->sum('amount'), 0, ',', '.') }}
                                    </div>
                                    <div class="small opacity-75">Total Nominal</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                            <div class="card-body p-3 d-flex align-items-center">
                                <i class="fas fa-calendar fa-2x opacity-75 flex-shrink-0 me-4"></i>
                                <div>
                                    <div class="fw-bold fs-4">
                                        {{ $data->where('transaction_time', '>=', now()->startOfMonth())->count() }}</div>
                                    <div class="small opacity-75">Bulan Ini</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main Table Card --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2 flex-wrap">
                            <i class="fas fa-list text-primary"></i> Riwayat Persembahan
                        </h5>
                    </div>

                    <div class="card-body p-0">
                        @if ($data->isEmpty())
                            <div class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-hand-holding-heart fa-4x text-muted mb-4"></i>
                                    <h4 class="text-muted mb-3">Belum Ada Persembahan</h4>
                                </div>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 fw-bold text-center" style="min-width: 40px;">#</th>
                                            <th class="border-0 fw-bold text-nowrap">
                                                <i class="fas fa-book me-1 text-primary"></i> Modul
                                            </th>
                                            {{-- <th class="border-0 fw-bold text-nowrap">
                                            <i class="fas fa-map-marker-alt me-1 text-success"></i> Rayon
                                        </th> --}}
                                            <th class="border-0 fw-bold text-nowrap">
                                                <i class="fas fa-clock me-1 text-info"></i> Jadwal Kelas Tutorial
                                            </th>
                                            <th class="border-0 fw-bold text-nowrap">
                                                <i class="fas fa-money-bill-wave me-1 text-warning"></i> Nominal
                                            </th>
                                            <th class="border-0 fw-bold text-nowrap">
                                                <i class="fas fa-toggle-on me-1 text-secondary"></i> Status
                                            </th>
                                            <th class="border-0 fw-bold text-nowrap">
                                                <i class="fas fa-calendar-alt me-1 text-dark"></i> Tanggal
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $item)
                                            <tr class="align-middle">
                                                <td class="fw-bold text-muted text-center">
                                                    {{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <div class="fw-bold">{{ $item->modul->name ?? '-' }}</div>
                                                        <small class="text-muted">Modul Pembelajaran</small>
                                                    </div>
                                                </td>
                                                {{-- <td>
                                                <div class="d-flex flex-column">
                                                    <div class="fw-bold">{{ $item->cabang->name ?? '-' }}</div>
                                                    <small class="text-muted">Lokasi</small>
                                                </div>
                                            </td> --}}
                                                <td>
                                                    @if ($item->userClassTutorial?->classTutorial?->start_time)
                                                        <div class="fw-bold text-warning">
                                                            {{ \Carbon\Carbon::parse($item->userClassTutorial->classTutorial->start_time)->translatedFormat('d F Y') }}
                                                        </div>
                                                        <small class="text-muted">Waktu Kelas</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-success">
                                                        Rp {{ number_format($item->amount, 0, ',', '.') }}
                                                    </div>
                                                    <small class="text-muted">Nominal</small>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusClass = match (strtolower($item->status ?? '')) {
                                                            'paid', 'settlement' => 'bg-success',
                                                            'pending' => 'bg-warning text-dark',
                                                            'failed', 'cancel', 'expire' => 'bg-danger',
                                                            default => 'bg-secondary',
                                                        };
                                                        $statusIcon = match (strtolower($item->status ?? '')) {
                                                            'paid', 'settlement' => 'fa-check-circle',
                                                            'pending' => 'fa-clock',
                                                            'failed', 'cancel', 'expire' => 'fa-times-circle',
                                                            default => 'fa-question-circle',
                                                        };
                                                        $statusText = match (strtolower($item->status ?? '')) {
                                                            'paid' => 'Paid',
                                                            'settlement' => 'Selesai',
                                                            'pending' => 'Pending',
                                                            'failed' => 'Gagal',
                                                            'cancel' => 'Dibatalkan',
                                                            'expired' => 'Kedaluwarsa',
                                                            'denied' => 'ditolak',
                                                            default => ucfirst($item->status ?? 'Unknown'),
                                                        };
                                                    @endphp
                                                    <span
                                                        class="badge {{ $statusClass }} px-3 py-2 text-white d-inline-flex align-items-center gap-1 mr-1">
                                                        <i class="fas {{ $statusIcon }} mr-1"></i> {{ $statusText }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($item->transaction_time)
                                                        <div class="fw-bold">{{ $item->transaction_time->format('d M Y') }}
                                                        </div>
                                                        <small
                                                            class="text-muted">{{ $item->transaction_time->format('H:i') }}</small>
                                                        <div class="small text-muted">
                                                            {{ $item->transaction_time->diffForHumans() }}</div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- Pagination --}}
                    @if ($data->hasPages())
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="text-muted small mb-2 mb-md-0">
                                    Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} dari
                                    {{ $data->total() }} hasil
                                </div>
                                <nav>
                                    {{ $data->links('pagination::bootstrap-4') }}
                                </nav>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .avatar-sm {
            width: 40px;
            height: 40px;
            font-size: 14px;
        }

        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-warning-subtle {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
            transition: background-color 0.3s ease;
        }

        .empty-state {
            padding: 2rem;
        }

        .card {
            border-radius: 12px;
        }

        .badge {
            border-radius: 6px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .badge {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }

            .d-flex.align-items-center {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .d-flex.align-items-center .avatar-sm {
                margin-bottom: 0.5rem;
                margin-right: 0 !important;
            }

            .fs-6 {
                font-size: 0.875rem !important;
            }
        }

        .pagination .page-link {
            border-radius: 6px;
            margin: 0 2px;
            border: none;
            color: #6c757d;
        }

        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }

        .pagination .page-link:hover {
            background-color: #e9ecef;
            color: #0d6efd;
        }

        .card-body.d-flex.align-items-center i {
            margin-right: 1.5rem !important;
        }
    </style>

    <script>
        // Simple fade-in animation
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Enhanced table interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add row hover effects
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(13, 110, 253, 0.05)';
                    this.style.transform = 'scale(1.01)';
                    this.style.transition = 'all 0.2s ease';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
@endsection
