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
                    <div class="d-flex align-items-center">
                        <i class="fas fa-donate fa-lg me-3"></i>
                        <h4 class="mb-0">Data Persembahan</h4>
                    </div>
                </div>
            </div>

            {{-- Info Cards --}}
            <div class="row mb-4 g-3">
                <div class="col-xl-4 col-md-6">
                    <div class="card border-start border-4 border-success shadow-sm h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3 text-success fs-1">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div>
                                <div class="text-uppercase fw-bold text-success small mb-1">Total Persembahan</div>
                                <div class="h4 fw-bold">
                                    Rp {{ number_format($totalPersembahan ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card border-start border-4 border-info shadow-sm h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3 text-info fs-1">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div>
                                <div class="text-uppercase fw-bold text-info small mb-1">Total Kelas</div>
                                <div class="h4 fw-bold">{{ $totalKelas ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card border-start border-4 border-warning shadow-sm h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3 text-warning fs-1">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <div class="text-uppercase fw-bold text-warning small mb-1">Total User</div>
                                <div class="h4 fw-bold">{{ $totalUser ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filter Persembahan
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ url()->current() }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="search" class="form-label fw-semibold">
                                <i class="fas fa-search me-1"></i> Cari User / Kelas Tutorial
                            </label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Cari user atau kelas tutorial" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label fw-semibold">
                                <i class="fas fa-calendar-alt me-1"></i> Dari Tanggal
                            </label>
                            <input type="date" name="date_from" id="date_from" class="form-control"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label fw-semibold">
                                <i class="fas fa-calendar-alt me-1"></i> Sampai Tanggal
                            </label>
                            <input type="date" name="date_to" id="date_to" class="form-control"
                                value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2 d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            @if (request()->hasAny(['search', 'date_from', 'date_to']))
                                <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- List grouped donasi --}}
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Data Persembahan per User Class Tutorial</h5>
                </div>
                <div class="card-body p-0">
                    @if ($groupedDonasi->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-donate fa-3x mb-3"></i>
                            <p>Belum ada donasi untuk cabang Anda.</p>
                        </div>
                    @else
                        <div class="accordion" id="donasiAccordion">
                            @foreach ($groupedDonasi as $uctId => $data)
                                <div class="accordion-item border-0">
                                    <h2 class="accordion-header" id="heading-{{ $uctId }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse-{{ $uctId }}" aria-expanded="false"
                                            aria-controls="collapse-{{ $uctId }}">
                                            <div class="d-flex justify-content-between w-100 align-items-center">
                                                <div>
                                                    <strong>UserClassTutorial ID: {{ $uctId }}</strong><br>
                                                    <small class="text-muted">
                                                        @if ($data['user_class_tutorial'])
                                                            User:
                                                            {{ $data['user_class_tutorial']->user->name ?? 'Anonim' }}<br>
                                                            Keterangan:
                                                            {{ $data['user_class_tutorial']->description ?? '-' }}
                                                        @else
                                                            Data UserClassTutorial tidak tersedia
                                                        @endif
                                                    </small>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-success rounded-pill px-3 py-2 fs-6">
                                                        Rp {{ number_format($data['total_amount'], 0, ',', '.') }}
                                                        ({{ $data['total_donasi'] }} donasi)
                                                    </span>
                                                    <button type="button" class="btn btn-sm btn-warning btn-tarik-dana"
                                                        data-uctid="{{ $uctId }}"
                                                        data-route="{{ route('tarik.dana', ['userclasstutorial' => $uctId]) }}">
                                                        <span class="btn-text">Tarik Dana</span>
                                                        <span class="spinner-border spinner-border-sm ms-2 d-none"
                                                            role="status" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $uctId }}" class="accordion-collapse collapse"
                                        aria-labelledby="heading-{{ $uctId }}" data-bs-parent="#donasiAccordion">
                                        <div class="accordion-body p-0">
                                            <table class="table table-striped table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Donatur</th>
                                                        <th>Jumlah</th>
                                                        <th>Tanggal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data['donasi_list'] as $index => $donasi)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $donasi->user->name ?? 'Anonim' }}</td>
                                                            <td>Rp {{ number_format($donasi->amount, 0, ',', '.') }}</td>
                                                            <td>{{ $donasi->transaction_time->format('d/m/Y H:i') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
