@extends('layouts.KomDigital')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                {{-- Header Section --}}
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">📊 {{ __('Progress Pembelajaran Saya') }}</h4>
                        <p class="text-muted mb-0">{{ __('Pantau kemajuan belajar Anda dalam berbagai kategori') }}</p>
                    </div>
                    <button class="btn btn-outline-primary btn-sm mt-2 mt-md-0" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-1"></i> {{ __('Refresh') }}
                    </button>
                </div>

                {{-- Error Alert --}}
                @if (session('error'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Stats Cards --}}
                <div class="row mb-4 g-3">
                    @php
                        $completedCount =
                            $progressModul->where('status', 'completed')->count() +
                            $videoProgress->where('is_watched', true)->count();
                    @endphp
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                            <div class="card-body p-3 d-flex align-items-center">
                                <i class="fas fa-book fa-2x opacity-75 flex-shrink-0 me-4"></i>
                                <div>
                                    <div class="fw-bold fs-4">{{ $progressModul->count() }}</div>
                                    <div class="small opacity-75">{{ __('Total Modul') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm bg-gradient-success text-white">
                            <div class="card-body p-3 d-flex align-items-center">
                                <i class="fas fa-play-circle fa-2x opacity-75 flex-shrink-0 me-4"></i>
                                <div>
                                    <div class="fw-bold fs-4">{{ $videoProgress->count() }}</div>
                                    <div class="small opacity-75">{{ __('Total Video') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm bg-gradient-info text-white">
                            <div class="card-body p-3 d-flex align-items-center">
                                <i class="fas fa-chalkboard-teacher fa-2x opacity-75 flex-shrink-0 me-4"></i>
                                <div>
                                    <div class="fw-bold fs-4">{{ $tutorialStatus->count() }}</div>
                                    <div class="small opacity-75">{{ __('Kelas Tutorial') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                            <div class="card-body p-3 d-flex align-items-center">
                                <i class="fas fa-trophy fa-2x opacity-75 flex-shrink-0 me-4"></i>
                                <div>
                                    <div class="fw-bold fs-4">{{ $completedCount }}</div>
                                    <div class="small opacity-75">{{ __('Selesai') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main Content Card --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2 flex-wrap">
                            <i class="fas fa-chart-line text-primary"></i> {{ __('Detail Progress Pembelajaran') }}
                        </h5>
                    </div>

                    <div class="card-body">
                        {{-- Nav Tabs --}}
                        <ul class="nav nav-tabs mb-4" id="progressTabs" role="tablist"
                            style="border-bottom: 1px solid #dee2e6;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="modul-tab" data-bs-toggle="tab" data-bs-target="#modul"
                                    type="button" role="tab" aria-controls="modul" aria-selected="true">
                                    <i class="fas fa-book me-2"></i> {{ __('Progress Modul') }}
                                    <span class="badge bg-light text-dark ms-2">{{ $progressModul->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="video-tab" data-bs-toggle="tab" data-bs-target="#video"
                                    type="button" role="tab" aria-controls="video" aria-selected="false">
                                    <i class="fas fa-play-circle me-2"></i> {{ __('Progress Video') }}
                                    <span class="badge bg-light text-dark ms-2">{{ $videoProgress->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tutorial-tab" data-bs-toggle="tab" data-bs-target="#tutorial"
                                    type="button" role="tab" aria-controls="tutorial" aria-selected="false">
                                    <i class="fas fa-chalkboard-teacher me-2"></i> {{ __('Kelas Tutorial') }}
                                    <span class="badge bg-light text-dark ms-2">{{ $tutorialStatus->count() }}</span>
                                </button>
                            </li>
                        </ul>

                        {{-- Tab Content --}}
                        <div class="tab-content">
                            {{-- Progress Modul Tab --}}
                            <div class="tab-pane fade show active" id="modul" role="tabpanel"
                                aria-labelledby="modul-tab">
                                @if ($progressModul->isEmpty())
                                    <div class="text-center py-5">
                                        <i class="fas fa-book fa-4x text-muted mb-4"></i>
                                        <h4 class="text-muted mb-3">{{ __('Belum Ada Progress Modul') }}</h4>
                                        <p class="text-muted">{{ __('Anda belum memulai pembelajaran modul apapun.') }}</p>
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('Judul Modul') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($progressModul as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $item->modul->name ?? '-' }}</td>
                                                        <td>
                                                            @if (strtolower($item->status ?? '') == 'completed')
                                                                <span class="badge bg-success text-white">{{ __('Selesai') }}</span>
                                                            @else
                                                                <span
                                                                    class="badge bg-secondary text-white">{{ ucfirst($item->status ?? '-') }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>

                            {{-- Progress Video Tab --}}
                            <div class="tab-pane fade" id="video" role="tabpanel" aria-labelledby="video-tab">
                                @if ($videoProgress->isEmpty())
                                    <div class="text-center py-5">
                                        <i class="fas fa-play-circle fa-4x text-muted mb-4"></i>
                                        <h4 class="text-muted mb-3">{{ __('Belum Ada Progress Video') }}</h4>
                                        <p class="text-muted">{{ __('Anda belum menonton video pembelajaran apapun.') }}</p>
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('Judul Video') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    <th>{{ __('Ditonton') }}</th>
                                                    <th>{{ __('Tanggal') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($videoProgress as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $item->video->name ?? (__('Video') . ' ID: ' . $item->video_id) }}</td>
                                                        <td>
                                                            @if (strtolower($item->status ?? '') == 'completed')
                                                                <span class="badge bg-success text-white">{{ __('Selesai') }}</span>
                                                            @elseif (strtolower($item->status ?? '') == 'in_progress')
                                                                <span class="badge bg-warning text-dark">{{ __('Sedang Berjalan') }}</span>
                                                            @else
                                                                <span
                                                                    class="badge bg-secondary text-white">{{ ucfirst($item->status ?? '-') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($item->is_watched)
                                                                <span class="badge bg-success text-white">
                                                                    <i class="fas fa-check me-1"></i> {{ __('Sudah') }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary text-white">
                                                                    <i class="fas fa-times me-1"></i> {{ __('Belum') }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ $item->watched_at ? $item->watched_at->translatedFormat('d M Y') : '-' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>

                            {{-- Tutorial Status Tab --}}
                            <div class="tab-pane fade" id="tutorial" role="tabpanel" aria-labelledby="tutorial-tab">
                                @if ($tutorialStatus->isEmpty())
                                    <div class="text-center py-5">
                                        <i class="fas fa-chalkboard-teacher fa-4x text-muted mb-4"></i>
                                        <h4 class="text-muted mb-3">{{ __('Belum Ada Kelas Tutorial') }}</h4>
                                        <p class="text-muted">{{ __('Anda belum mengikuti kelas tutorial apapun.') }}</p>
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('Judul Modul') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    {{-- <th>{{ __('Selesai') }}</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tutorialStatus as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $item->modul->name ?? (__('Modul') . ' ID: ' . $item->module_id) }}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $statusClass = match (strtolower($item->status ?? '')) {
                                                                    'selesai' => 'bg-success',
                                                                    'in_progress' => 'bg-warning text-dark',
                                                                    'enrolled' => 'bg-primary',
                                                                    'cancelled' => 'bg-danger',
                                                                    default => 'bg-secondary',
                                                                };
                                                                $statusText = match (strtolower($item->status ?? '')) {
                                                                    'selesai' => __('Selesai'),
                                                                    'in_progress' => __('Sedang Berjalan'),
                                                                    'enrolled' => __('Terdaftar'),
                                                                    'cancelled' => __('Dibatalkan'),
                                                                    'not_enrolled' => __('Belum Terdaftar'),
                                                                    default => ucfirst($item->status ?? '-'),
                                                                };
                                                            @endphp
                                                            <span
                                                                class="badge {{ $statusClass }} text-white">{{ $statusText }}</span>
                                                        </td>
                                                        {{-- <td>
                                                            {{ $item->completed_at ? $item->completed_at->format('d M Y') : '-' }}
                                                        </td> --}}
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
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

        .card {
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .badge {
            border-radius: 6px;
            font-weight: 500;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        /* Remove border-radius on tabs */
        .nav-tabs .nav-link {
            border-radius: 0 !important;
            border: 1px solid transparent;
            border-bottom: none;
            transition: all 0.2s ease;
        }

        .nav-tabs .nav-link:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .nav-tabs .nav-link.active {
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .badge {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }

            .nav-tabs .nav-link {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
            }
        }

        .card-body.d-flex.align-items-center i {
            margin-right: 1.5rem !important;
        }
    </style>

    <script>
        // Animate stats numbers
        document.addEventListener('DOMContentLoaded', function() {
            const statNumbers = document.querySelectorAll('.fs-4');
            statNumbers.forEach(number => {
                const finalValue = parseInt(number.textContent);
                if (!isNaN(finalValue)) {
                    animateNumber(number, 0, finalValue, 1000);
                }
            });
        });

        function animateNumber(element, start, end, duration) {
            const startTime = performance.now();

            function updateNumber(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const current = Math.floor(start + (end - start) * progress);
                element.textContent = current;
                if (progress < 1) {
                    requestAnimationFrame(updateNumber);
                }
            }
            requestAnimationFrame(updateNumber);
        }

        // Card entrance animation
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

        // Tab content fade-in animation
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabButtons.forEach(button => {
                button.addEventListener('shown.bs.tab', function(e) {
                    const targetPane = document.querySelector(e.target.getAttribute(
                        'data-bs-target'));
                    targetPane.style.opacity = '0';
                    targetPane.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        targetPane.style.transition = 'all 0.3s ease';
                        targetPane.style.opacity = '1';
                        targetPane.style.transform = 'translateY(0)';
                    }, 50);
                });
            });
        });

        // Table row hover effect
        document.addEventListener('DOMContentLoaded', function() {
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(13, 110, 253, 0.05)';
                    this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                    this.style.boxShadow = '';
                });
            });
        });
    </script>
@endsection
