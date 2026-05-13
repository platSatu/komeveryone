@extends('layouts.KomDigital')

@section('content')

    <div class="row">
        <div class="col-12">
            {{-- Header Section --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div>
                    <h5 class="fw-bold text-dark mb-1">🎓 Kelas Tutorial Saya</h5>
                    <p class="text-muted mb-0">Daftar kelas tutorial yang telah Anda ikuti</p>
                </div>
                <button class="btn btn-outline-primary btn-sm mt-2 mt-md-0" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </button>
            </div>

            {{-- Stats Cards --}}
            <div class="row mb-4 g-3">
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <i class="fas fa-chalkboard-teacher fa-2x opacity-75 flex-shrink-0"></i>
                            <div>
                                <div class="fw-bold fs-4">{{ $data->count() }}</div>
                                <div class="small opacity-75">Total Kelas</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm bg-gradient-success text-white">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <i class="fas fa-check-circle fa-2x opacity-75 flex-shrink-0"></i>
                            <div>
                                <div class="fw-bold fs-4">{{ $data->where('status', 'active')->count() }}</div>
                                <div class="small opacity-75">Aktif</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm bg-gradient-info text-white">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <i class="fas fa-book fa-2x opacity-75 flex-shrink-0"></i>
                            <div>
                                <div class="fw-bold fs-4">{{ $data->groupBy('modul.name')->count() }}</div>
                                <div class="small opacity-75">Modul</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <i class="fas fa-map-marker-alt fa-2x opacity-75 flex-shrink-0"></i>
                            <div>
                                <div class="fw-bold fs-4">{{ $data->groupBy('rayon.name')->count() }}</div>
                                <div class="small opacity-75">Rayon</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Table Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-list text-primary"></i> Kelas Tutorial
                    </h5>
                </div>

                <div class="card-body p-0">
                    @if ($data->isEmpty())
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-chalkboard-teacher fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted mb-3">Belum Ada Kelas Tutorial</h4>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 fw-bold text-center">#</th>
                                        <th class="border-0 fw-bold text-nowrap">
                                            <i class="fas fa-book me-1 text-primary"></i> Modul
                                        </th>
                                        <th class="border-0 fw-bold text-center text-nowrap">
                                            <i class="fas fa-graduation-cap me-1 text-success"></i> Kelas
                                        </th>
                                        <th class="border-0 fw-bold text-center text-nowrap">
                                            <i class="fas fa-map-marker-alt me-1 text-info"></i> Rayon
                                        </th>
                                        <th class="border-0 fw-bold text-center text-nowrap">
                                            <i class="fas fa-clock me-1 text-warning"></i> Hari & Jam
                                        </th>
                                        <th class="border-0 fw-bold text-center text-nowrap">
                                            <i class="fas fa-video me-1 text-primary"></i> Join Zoom
                                        </th>
                                        <th class="border-0 fw-bold text-center text-nowrap">
                                            <i class="fas fa-calendar-alt me-1 text-secondary"></i> Tanggal Booking
                                        </th>
                                        <th class="border-0 fw-bold text-center text-nowrap">
                                            <i class="fas fa-star text-warning"></i> Review Pengajar
                                        </th>
                                        <th class="border-0 fw-bold text-center text-nowrap">
                                            <i class="fas fa-toggle-on me-1 text-danger"></i> Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $index => $kelas)
                                        <tr>
                                            <td class="fw-bold text-muted text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="fw-bold">{{ $kelas->modul->name ?? '-' }}</div>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $kelas->kelas->name ?? '-' }}</td>
                                            <td class="text-center">{{ $kelas->rayon->name ?? '-' }}</td>
                                            <td class="text-center">
                                                @if ($kelas->classTutorial)
                                                    <span class="badge bg-light text-dark border px-3 py-2 text-nowrap">
                                                        {{ \Carbon\Carbon::parse($kelas->classTutorial->start_time)->translatedFormat('l, d F Y H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            {{-- <td class="text-center">
                                                @if ($kelas->classTutorial)
                                                    <span class="badge bg-light text-dark border px-3 py-2 text-nowrap">
                                                        <a href="{{ $kelas->classTutorial->join_url }}" target="_blank"
                                                            class="">
                                                            Join Zoom Meeting
                                                        </a>
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td> --}}

                                            <td class="text-center">
                                                @if ($kelas->zoom_join_url)
                                                    <span class="badge bg-light text-dark border px-3 py-2 text-nowrap font-weight-bold">
                                                        <i class="fas fa-video me-1 text-primary mr-1"></i>
                                                        <a href="{{ $kelas->zoom_join_url }}" target="_blank"
                                                            rel="noopener noreferrer">
                                                            {{ $kelas->name ?? ($kelas->topic ?? 'Join Zoom') }}
                                                        </a>
                                                    </span>
                                                @else
                                                    {{ $kelas->name ?? ($kelas->topic ?? '-') }}
                                                @endif
                                            </td>


                                            <td class="text-center">
                                                @if ($kelas->date_booking)
                                                    <div class="fw-bold">
                                                        {{ \Carbon\Carbon::parse($kelas->date_booking)->format('d M Y') }}
                                                    </div>
                                                    <small
                                                        class="text-muted">{{ \Carbon\Carbon::parse($kelas->date_booking)->diffForHumans() }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('kom.peserta.formReviewPeserta', ['id' => Crypt::encryptString($kelas->id)]) }}"
                                                    class="badge bg-light text-dark border px-3 py-2 d-inline-flex align-items-center gap-1">
                                                    <i class="fas fa-star text-warning mr-1"></i> Isi Review
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                @if ($kelas->status === 'booked')
                                                    <span
                                                        class="badge bg-success px-3 py-2 text-white d-inline-flex align-items-center gap-1">
                                                        <i class="fas fa-check-circle mr-1"></i> Aktif
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge bg-secondary px-3 py-2 d-inline-flex align-items-center gap-1">
                                                        <i class="fas fa-times-circle mr-1"></i> Tidak Aktif
                                                    </span>
                                                @endif
                                            </td>
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

            /* Jangan ubah flex direction di tabel agar tetap rapi */
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success') || session('error') || $errors->any())
        <script>
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: "{{ session('error') }}",
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: `<ul style="text-align:left;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>`,
                    showConfirmButton: true
                });
            @endif
        </script>
    @endif
@endsection
