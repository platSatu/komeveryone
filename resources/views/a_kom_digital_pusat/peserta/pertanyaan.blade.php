@extends('layouts.KomDigital')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                {{-- Header Section --}}
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">❓ Pertanyaan Saya</h5>
                        <p class="text-muted mb-0">Daftar pertanyaan yang telah Anda ajukan</p>
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
                                <i class="fas fa-question-circle fa-2x opacity-75 flex-shrink-0 me-4"></i>
                                <div>
                                    <div class="fw-bold fs-4">{{ $data->total() }}</div>
                                    <div class="small opacity-75">Total Pertanyaan</div>
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
                                    <div class="small opacity-75">Aktif</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm bg-gradient-info text-white">
                            <div class="card-body p-3 d-flex align-items-center">
                                <i class="fas fa-book fa-2x opacity-75 flex-shrink-0 me-4"></i>
                                <div>
                                    <div class="fw-bold fs-4">{{ $data->groupBy('modul.name')->count() }}</div>
                                    <div class="small opacity-75">Modul</div>
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
                                        {{ $data->where('tgl_submit', '>=', now()->startOfMonth())->count() }}</div>
                                    <div class="small opacity-75">Bulan Ini</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main Table Card --}}
                <div class="card border-0 shadow-sm">
                    {{-- <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                            <i class="fas fa-list text-primary"></i> Kelas Tutorial
                        </h5>
                    </div> --}}
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                            <i class="fas fa-list text-primary"></i> Daftar Pertanyaan
                        </h5>
                    </div>

                    <div class="card-body p-0">
                        @if ($data->isEmpty())
                            <div class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-question-circle fa-4x text-muted mb-4"></i>
                                    <h4 class="text-muted mb-3">Belum Ada Pertanyaan</h4>
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
                                            <th class="border-0 fw-bold text-nowrap">
                                                <i class="fas fa-graduation-cap me-1 text-success"></i> Kelas
                                            </th>
                                            <th class="border-0 fw-bold text-nowrap">
                                                <i class="fas fa-comment me-1 text-secondary"></i> Pertanyaan
                                            </th>
                                            <th class="border-0 fw-bold text-nowrap">
                                                <i class="fas fa-calendar-alt me-1 text-dark"></i> Tanggal
                                            </th>
                                            <th class="border-0 fw-bold text-nowrap">
                                                <i class="fas fa-toggle-on me-1 text-danger"></i> Status
                                            </th>
                                            <th class="border-0 fw-bold text-nowrap">
                                                <i class="fas fa-cog me-1 text-dark"></i> Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $index => $item)
                                            <tr>
                                                <td class="fw-bold text-muted text-center">{{ $data->firstItem() + $index }}
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <div class="fw-bold">{{ $item->modul->name ?? '-' }}</div>
                                                        <small class="text-muted">Modul Pembelajaran</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border px-3 py-2">
                                                        {{ $item->class->name ?? '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="question-content" style="max-width: 250px;">
                                                        <div class="fw-bold text-truncate" title="{{ $item->pertanyaan }}">
                                                            {{ $item->pertanyaan }}
                                                        </div>
                                                        <small
                                                            class="text-muted">{{ Str::limit($item->pertanyaan, 50) }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($item->tgl_submit)
                                                        <div class="fw-bold">
                                                            {{ \Carbon\Carbon::parse($item->tgl_submit)->format('d M Y') }}
                                                        </div>
                                                        <small
                                                            class="text-muted">{{ \Carbon\Carbon::parse($item->tgl_submit)->format('H:i') }}</small>
                                                        <div class="small text-muted">
                                                            {{ \Carbon\Carbon::parse($item->tgl_submit)->diffForHumans() }}
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($item->status === 'active')
                                                        <span
                                                            class="badge bg-success px-3 py-2 text-white d-inline-flex align-items-center gap-1">
                                                            <i class="fas fa-check-circle"></i> Aktif
                                                        </span>
                                                    @else
                                                        <span
                                                            class="badge bg-secondary px-3 py-2 d-inline-flex align-items-center gap-1">
                                                            <i class="fas fa-times-circle"></i> Tidak Aktif
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $canEdit = ($item->edit_count ?? 0) < 1;
                                                    @endphp
                                                    
                                                    @if($canEdit)
                                                        <a href="{{ route('kom.peserta.pertanyaan.edit', $item->id) }}" 
                                                        class="btn btn-outline-primary btn-sm"
                                                        title="Edit Pertanyaan (Sisa: 1x)">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    @else
                                                        <button class="btn btn-secondary btn-sm" 
                                                                disabled 
                                                                title="Tidak dapat diedit (maksimal 1x edit)">
                                                            <i class="fas fa-lock"></i> Diedit
                                                        </button>
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

        .bg-info-subtle {
            background-color: rgba(13, 202, 240, 0.1);
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

        .question-content {
            max-width: 250px;
        }

        .text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .badge {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }

            .question-content {
                max-width: 150px;
            }

            .d-flex.align-items-center {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .d-flex.align-items-center .avatar-sm {
                margin-bottom: 0.5rem;
                margin-right: 0 !important;
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
        // Simple fade-in animation for cards
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

        // Tooltip initialization for truncated questions
        document.addEventListener('DOMContentLoaded', function() {
            const truncatedElements = document.querySelectorAll('[title]');
            truncatedElements.forEach(element => {
                if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                    new bootstrap.Tooltip(element);
                }
            });
        });

        // Row hover effect
        document.addEventListener('DOMContentLoaded', function() {
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
