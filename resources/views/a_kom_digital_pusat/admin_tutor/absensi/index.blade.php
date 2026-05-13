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

            {{-- Statistik Cards --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h4>{{ $overallStats['total'] ?? 0 }}</h4>
                            <p class="mb-0">Total Absensi</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-user-check fa-2x mb-2"></i>
                            <h4>{{ $overallStats['hadir'] ?? 0 }}</h4>
                            <p class="mb-0">Hadir ({{ $overallStats['persentase_hadir'] ?? 0 }}%)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-user-times fa-2x mb-2"></i>
                            <h4>{{ $overallStats['tidak_hadir'] ?? 0 }}</h4>
                            <p class="mb-0">Tidak Hadir ({{ $overallStats['persentase_tidak_hadir'] ?? 0 }}%)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i>
                            <h4>{{ $kelasTutorialList->count() }}</h4>
                            <p class="mb-0">Total Kelas</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Header Card --}}
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chalkboard-teacher me-2"></i>&nbsp;&nbsp;
                            <h5 class="mb-0 pb-3">Daftar Kelas Tutorial Saya</h5>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('kom.absensi.per-kelas') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-users me-1"></i>&nbsp;&nbsp;Lihat Absensi
                            </a>
                            {{-- <a href="{{ route('kom.absensi.summary') }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-chart-pie me-1">&nbsp;&nbsp;</i>Summary
                            </a> --}}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($kelasTutorialList->isNotEmpty())
                        <div class="row">
                            @foreach($kelasTutorialList as $kelas)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border-left-primary">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    {{-- <i class="fas fa-book text-white"></i> --}}
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 font-weight-bold">{{ $kelas->kelas->name ?? '-' }}</h6>
                                                    <small class="text-muted">{{ $kelas->modul->name ?? '-' }}</small>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <div class="row text-center">
                                                    <div class="col-4">
                                                        <div class="border-right">
                                                            <h6 class="text-primary mb-0">{{ $kelas->quota ?? 0 }}</h6>
                                                            <small class="text-muted">Kuota</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="border-right">
                                                            <h6 class="text-success mb-0">{{ $kelas->jml_ikut ?? 0 }}</h6>
                                                            <small class="text-muted">Peserta</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <h6 class="text-info mb-0">{{ $kelas->sisa_quota ?? 0 }}</h6>
                                                        <small class="text-muted">Sisa</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-user me-1 mr-1"></i>{{ $kelas->pengajar->name ?? '-' }}
                                                    </small>
                                                    @php
                                                        $statusClass = match(strtolower($kelas->status)) {
                                                            'aktif' => 'bg-success',
                                                            'completed' => 'bg-primary',
                                                            'pending' => 'bg-warning',
                                                            'reschedule' => 'bg-info',
                                                            'failed' => 'bg-danger',
                                                            default => 'bg-secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $statusClass }}">{{ ucfirst($kelas->status) }}</span>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1 mr-1"></i>{{ \Carbon\Carbon::parse($kelas->start_time)->format('d M Y') }}
                                                    
                                                </small>
                                            </div>

                                            <div class="d-grid">
                                                <a href="{{ route('kom.absensi.per-kelas', ['class_tutorial_id' => $kelas->id]) }}" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye me-1 mr-3"></i>Lihat Absensi
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                                <h5>Belum Ada Kelas Tutorial</h5>
                                <p>Anda belum membuat kelas tutorial apapun.</p>
                                <a href="{{ route('kom.kelas_tutor_cabang.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1 mr-3"></i>Buat Kelas Tutorial
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .avatar-lg {
        width: 48px;
        height: 48px;
    }
    
    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }
    
    .border-right {
        border-right: 1px solid #e3e6f0;
    }
    
    .card:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .bg-primary {
        background-color: #4e73df !important;
    }
    
    .bg-success {
        background-color: #1cc88a !important;
    }
    
    .bg-danger {
        background-color: #e74a3b !important;
    }
    
    .bg-info {
        background-color: #36b9cc !important;
    }
    
    .text-primary {
        color: #4e73df !important;
    }
    
    .text-success {
        color: #1cc88a !important;
    }
    
    .text-info {
        color: #36b9cc !important;
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .font-weight-bold {
        font-weight: 700 !important;
    }
    
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    
    .btn-group .btn {
        border-radius: 0.35rem;
        margin-left: 0.25rem;
    }
    
    .btn-group .btn:first-child {
        margin-left: 0;
    }
    
    .badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.375rem 0.75rem;
    }
    
    .alert {
        border: none;
        border-radius: 0.35rem;
    }
    
    .alert-success {
        background-color: #d1edff;
        color: #0c5460;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        background-color: rgba(0, 0, 0, 0.03);
    }
    
    .card-header.bg-primary {
        background-color: #4e73df !important;
        border-bottom: 1px solid #4e73df;
    }
    
    .text-muted {
        color: #6c757d !important;
    }
    
    .mb-0 {
        margin-bottom: 0 !important;
    }
    
    .mb-1 {
        margin-bottom: 0.25rem !important;
    }
    
    .mb-2 {
        margin-bottom: 0.5rem !important;
    }
    
    .mb-3 {
        margin-bottom: 1rem !important;
    }
    
    .mb-4 {
        margin-bottom: 1.5rem !important;
    }
    
    .me-1 {
        margin-right: 0.25rem !important;
    }
    
    .me-2 {
        margin-right: 0.5rem !important;
    }
    
    .me-3 {
        margin-right: 1rem !important;
    }
    
    .ms-2 {
        margin-left: 0.5rem !important;
    }
    
    .py-5 {
        padding-top: 3rem !important;
        padding-bottom: 3rem !important;
    }
    
    .fa-2x {
        font-size: 2em;
    }
    
    .fa-3x {
        font-size: 3em;
    }
    
    .d-grid {
        display: grid !important;
    }
    
    .h-100 {
        height: 100% !important;
    }
    
    .text-center {
        text-align: center !important;
    }
    
    .text-white {
        color: #fff !important;
    }
    
    .rounded-circle {
        border-radius: 50% !important;
    }
    
    .d-flex {
        display: flex !important;
    }
    
    .align-items-center {
        align-items: center !important;
    }
    
    .justify-content-between {
        justify-content: space-between !important;
    }
    
    .justify-content-center {
        justify-content: center !important;
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
        
        .card-body {
            padding: 1rem;
        }
        
        .avatar-lg {
            width: 40px;
            height: 40px;
        }
        
        .fa-2x {
            font-size: 1.5em;
        }
        
        .fa-3x {
            font-size: 2em;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto refresh statistik setiap 30 detik
    setInterval(function() {
        // Optional: Auto refresh statistik cards
        // location.reload();
    }, 30000);
    
    // Smooth scroll untuk navigasi
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    
    // Tooltip initialization jika menggunakan Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Card hover effect enhancement
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Loading state untuk button
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function() {
            if (this.getAttribute('href') && !this.getAttribute('href').startsWith('#')) {
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...';
                this.disabled = true;
            }
        });
    });
</script>
@endpush