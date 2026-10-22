@extends('layouts.KomDigital')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                {{-- Header Section --}}
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">📚 {{ __('e-Book Saya') }}</h5>
                        <p class="text-muted mb-0">{{ __('Koleksi buku digital yang telah Anda miliki') }}</p>
                    </div>
                    <button class="btn btn-outline-primary btn-sm mt-2 mt-md-0" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-1"></i> {{ __('Refresh') }}
                    </button>
                </div>

                {{-- Stats Cards --}}
                <div class="row mb-4 g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                            <div class="card-body p-3 d-flex align-items-center">
                                <i class="fas fa-book fa-2x opacity-75 flex-shrink-0 me-4"></i>
                                <div>
                                    <div class="fw-bold fs-4">{{ $data->total() }}</div>
                                    <div class="small opacity-75">{{ __('Total e-Book') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm bg-gradient-success text-white">
                            <div class="card-body p-3 d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x opacity-75 flex-shrink-0 me-4"></i>
                                <div>
                                    <div class="fw-bold fs-4">{{ $data->where('status', 'active')->count() }}</div>
                                    <div class="small opacity-75">{{ __('Aktif') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm bg-gradient-info text-white">
                            <div class="card-body p-3 d-flex align-items-center">
                                <i class="fas fa-calendar fa-2x opacity-75 flex-shrink-0 me-4"></i>
                                <div>
                                    <div class="fw-bold fs-4">
                                        {{ $data->where('date_publish', '>=', now()->startOfMonth())->count() }}</div>
                                    <div class="small opacity-75">{{ __('Bulan Ini') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                            <div class="card-body p-3 d-flex align-items-center">
                                <i class="fas fa-list fa-2x opacity-75 flex-shrink-0 me-4"></i>
                                <div>
                                    <div class="fw-bold fs-4">{{ $data->groupBy('class.name')->count() }}</div>
                                    <div class="small opacity-75">{{ __('Kelas') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main Table Card --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2 flex-wrap">
                            <i class="fas fa-list text-primary"></i> {{ __('Daftar e-Book') }}
                        </h5>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 fw-bold text-center" style="min-width: 40px;">#</th>
                                        <th class="border-0 fw-bold text-nowrap">
                                            <i class="fas fa-barcode me-1 text-primary"></i> {{ __('Kode Buku') }}
                                        </th>
                                        <th class="border-0 fw-bold text-nowrap">
                                            <i class="fas fa-graduation-cap me-1 text-success"></i> {{ __('Kelas') }}
                                        </th>
                                        <th class="border-0 fw-bold text-nowrap">
                                            <i class="fas fa-calendar-alt me-1 text-info"></i> {{ __('Tanggal Aktivasi') }}
                                        </th>
                                        <th class="border-0 fw-bold text-nowrap">
                                            <i class="fas fa-toggle-on me-1 text-warning"></i> {{ __('Status') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr class="align-middle">
                                            <td class="fw-bold text-muted text-center">
                                                {{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar-sm bg-primary-subtle rounded me-2 d-flex align-items-center justify-content-center mr-3">
                                                        <i class="fas fa-book text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $item->no_books ?? '-' }}</div>
                                                        <small class="text-muted">ID: {{ $item->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-3 py-2">
                                                    {{ $item->class->name ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($item->date_publish)
                                                    @php
                                                        $date = \Carbon\Carbon::parse($item->date_publish);
                                                    @endphp
                                                    <div class="fw-bold">{{ $date->translatedFormat('d M Y') }}</div>
                                                    <small class="text-muted">{{ $date->diffForHumans() }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            <td>
                                                @php
                                                    $statusClass = match (strtolower($item->status ?? '')) {
                                                        'active' => 'bg-success',
                                                        'inactive' => 'bg-danger',
                                                        'pending' => 'bg-warning text-dark',
                                                        default => 'bg-secondary',
                                                    };
                                                    $statusIcon = match (strtolower($item->status ?? '')) {
                                                        'active' => 'fa-check-circle',
                                                        'inactive' => 'fa-times-circle',
                                                        'pending' => 'fa-clock',
                                                        default => 'fa-question-circle',
                                                    };
                                                @endphp
                                                <span
                                                    class="badge {{ $statusClass }} px-3 py-2 d-inline-flex align-items-center gap-1">
                                                    <i class="fas {{ $statusIcon }}"></i>
                                                    {{ ['active' => __('Aktif'), 'inactive' => __('Tidak Aktif'), 'pending' => __('Menunggu')][$item->status] ?? ucfirst($item->status ?? '-') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">{{ __('Belum Ada e-Book') }}</h5>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    @if ($data->hasPages())
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="text-muted small mb-2 mb-md-0">
                                    {{ __('Menampilkan :awal - :akhir dari :total hasil', ['awal' => $data->firstItem() ?? 0, 'akhir' => $data->lastItem() ?? 0, 'total' => $data->total()]) }}
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

            .avatar-sm {
                width: 35px;
                height: 35px;
                font-size: 12px;
            }

            .badge {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
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
    </script>
@endsection
