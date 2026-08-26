@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-12">
            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2 mr-3"></i>{{ session('success') }}
                    
                </div>
            @endif

            @if (session('error') || isset($error))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2 mr-3"></i>{{ session('error') ?? $error }}
                    
                </div>
            @endif

            {{-- Header Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-wallet me-2"></i>&nbsp;&nbsp;
                            <h4 class="mb-0 pb-2">Tarik Saldo Persembahan</h4>
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
                                        Saldo Tersedia
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSaldoTersedia">
                                        Rp {{ number_format($overallStats['total_saldo_tersedia'] ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-wallet fa-2x text-gray-300"></i>
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
                                        Sudah Ditarik
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Rp {{ number_format($overallStats['total_sudah_ditarik'] ?? 0, 0, ',', '.') }}
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
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Pending
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Rp {{ number_format($overallStats['total_pending'] ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                        Jumlah Kelas
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $overallStats['jumlah_kelas'] ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Saldo Per Kelas Tutorial --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2"></i>&nbsp;&nbsp;Saldo Per Kelas Tutorial
                        <span class="badge bg-secondary ms-2">{{ $saldoPerKelas->count() }} kelas</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if($saldoPerKelas->isNotEmpty())
                        <div class="row">
                            @foreach($saldoPerKelas as $saldo)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border-left-primary">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center me-3 mr-3">
                                                    <i class="fas fa-chalkboard-teacher text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 font-weight-bold">{{ $saldo['class_tutorial']->kelas->name ?? '-' }}</h6>
                                                    <small class="text-muted">{{ $saldo['class_tutorial']->modul->name ?? '-' }}</small>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <div class="border-end">
                                                            <h6 class="text-success mb-0">Rp {{ number_format($saldo['saldo_tersedia'], 0, ',', '.') }}</h6>
                                                            <small class="text-muted">Tersedia</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <h6 class="text-info mb-0">{{ $saldo['jumlah_donatur'] }}</h6>
                                                        <small class="text-muted">Persembahan</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <small class="text-muted d-block">Total Donasi: Rp {{ number_format($saldo['total_donasi'], 0, ',', '.') }}</small>
                                                <small class="text-muted d-block">Fee: Rp {{ number_format($saldo['total_fee'], 0, ',', '.') }}</small>
                                                <small class="text-muted d-block">Sudah Ditarik: Rp {{ number_format($saldo['sudah_ditarik'], 0, ',', '.') }}</small>
                                                @if($saldo['pending'] > 0)
                                                    <small class="text-warning d-block">Pending: Rp {{ number_format($saldo['pending'], 0, ',', '.') }}</small>
                                                @endif
                                            </div>

                                            <div class="d-grid">
                                                @if($saldo['dapat_ditarik'] && $saldo['saldo_tersedia'] >= 1000)
                                                    <button class="btn btn-success btn-sm" 
                                                            onclick="showTarikSaldoModal('{{ $saldo['class_tutorial']->id }}', '{{ $saldo['class_tutorial']->kelas->name ?? '' }}', {{ $saldo['saldo_tersedia'] }})">
                                                        <i class="fas fa-money-bill-wave me-1 mr-2"></i>Tarik Saldo
                                                    </button>
                                                @else
                                                    <button class="btn btn-secondary btn-sm" disabled>
                                                        <i class="fas fa-ban me-1"></i>&nbsp;&nbsp;
                                                        @if($saldo['sudah_pernah_tarik'])
                                                            Sudah Diajukan
                                                        @elseif($saldo['saldo_tersedia'] < 1000)
                                                            Minimal Rp 1.000
                                                        @else
                                                            Tidak Ada Saldo
                                                        @endif
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-wallet fa-3x mb-3"></i>
                                <h5>Belum Ada Saldo</h5>
                                <p>Anda belum memiliki saldo dari kelas tutorial yang dibuat.</p>
                                <a href="{{ route('kom.kelas_tutor_cabang.index') }}" class="btn btn-primary">
                                    <i class="fas fa-chalkboard-teacher me-1 mr-3"></i>Lihat Kelas Tutorial
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Riwayat Penarikan --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>&nbsp;&nbsp;Riwayat Penarikan
                        <span class="badge bg-secondary ms-2">{{ $riwayatPenarikan->count() }} transaksi</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if($riwayatPenarikan->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="25%">Kelas Tutorial</th>
                                        <th width="15%">Jumlah</th>
                                        <th width="15%">Tanggal</th>
                                       
                                        <th width="15%">Status</th>
                                        <th width="20%">Catatan</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($riwayatPenarikan as $index => $penarikan)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div>
                                                    <strong>{{ $penarikan->userClassTutorial->classTutorial->kelas->name ?? '-' }}</strong>
                                                    <br><small class="text-muted">{{ $penarikan->userClassTutorial->classTutorial->modul->name ?? '-' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary text-white fs-6">
                                                    {{ $penarikan->formatted_jumlah }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $penarikan->tanggal_penarikan->format('d/m/Y') }}
                                                    <br>{{ $penarikan->tanggal_penarikan->format('H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge {{ $penarikan->status_badge_class }}">
                                                    {{ $penarikan->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>{{ $penarikan->catatan ?? '-' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-info btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#detailModal{{ $penarikan->id }}"
                                                        title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-history fa-3x mb-3"></i>
                                <h5>Belum Ada Riwayat Penarikan</h5>
                                <p>Anda belum pernah melakukan penarikan saldo.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

{{-- Modal Detail Penarikan (di luar struktur tabel, satu modal per transaksi) --}}
                        @foreach($riwayatPenarikan as $penarikan)
                                        <div class="modal fade" id="detailModal{{ $penarikan->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-wallet me-2"></i>Detail Penarikan Saldo
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row g-4">
                                                            <div class="col-md-6">
                                                                <div class="card border-info">
                                                                    <div class="card-header bg-info text-white">
                                                                        <h6 class="mb-0">
                                                                            <i class="fas fa-chalkboard-teacher me-2"></i>Informasi Kelas
                                                                        </h6>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <table class="table table-borderless table-sm">
                                                                            <tr>
                                                                                <td width="40%"><strong>Kelas:</strong></td>
                                                                                <td>{{ $penarikan->userClassTutorial->classTutorial->kelas->name ?? '-' }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Modul:</strong></td>
                                                                                <td>{{ $penarikan->userClassTutorial->classTutorial->modul->name ?? '-' }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Pengajar:</strong></td>
                                                                                <td>{{ $penarikan->userClassTutorial->classTutorial->pengajar->name ?? '-' }}</td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="card border-success">
                                                                    <div class="card-header bg-success text-white">
                                                                        <h6 class="mb-0">
                                                                            <i class="fas fa-money-bill me-2"></i>Detail Penarikan
                                                                        </h6>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <table class="table table-borderless table-sm">
                                                                            <tr>
                                                                                <td width="40%"><strong>Jumlah:</strong></td>
                                                                                <td>
                                                                                    <span class="badge bg-success fs-6">
                                                                                        {{ $penarikan->formatted_jumlah }}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Status:</strong></td>
                                                                                <td>
                                                                                    <span class="badge {{ $penarikan->status_badge_class }}">
                                                                                        {{ $penarikan->status_label }}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Tanggal:</strong></td>
                                                                                <td>{{ $penarikan->tanggal_penarikan->format('d F Y H:i') }}</td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if($penarikan->catatan)
                                                                <div class="col-12">
                                                                    <div class="card border-warning">
                                                                        <div class="card-header bg-warning text-dark">
                                                                            <h6 class="mb-0">
                                                                                <i class="fas fa-sticky-note me-2"></i>Catatan
                                                                            </h6>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <p class="mb-0">{{ $penarikan->catatan }}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
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

  {{-- Modal Tarik Saldo --}}
<div class="modal fade" id="tarikSaldoModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-money-bill-wave me-2"></i>Tarik Saldo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tarikSaldoForm" novalidate>
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="classTutorialId" name="class_tutorial_id">
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Kelas Tutorial:</strong></label>
                        <div id="kelasName" class="form-control-plaintext"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Saldo Tersedia:</strong></label>
                        <div id="saldoTersedia" class="form-control-plaintext text-success fw-bold"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="jumlahTarik" class="form-label"><strong>Jumlah Penarikan:</strong></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control" 
                                   id="jumlahTarik" 
                                   name="jumlah" 
                                   placeholder="Masukkan jumlah" 
                                   min="1000" 
                                   step="1000" 
                                   required>
                        </div>
                        <small class="text-muted">Minimal penarikan Rp 1.000. Maksimal sesuai saldo tersedia.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan (Opsional):</label>
                        <textarea class="form-control" 
                                  id="catatan" 
                                  name="catatan" 
                                  rows="3" 
                                  placeholder="Tambahkan catatan jika diperlukan (maksimal 500 karakter)"
                                  maxlength="500"></textarea>
                        <small class="text-muted">Catatan akan terlihat oleh admin untuk proses persetujuan.</small>
                    </div>
                    
                    <div class="alert alert-info" role="alert" id="infoAlert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Informasi:</strong> Penarikan akan diproses setelah mendapat persetujuan dari admin. Status akan terupdate di dashboard Anda.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success" id="submitTarikSaldo">
                        <i class="fas fa-paper-plane me-1"></i>Ajukan Penarikan
                    </button>
                </div>
            </form>
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
    
    .bg-gradient-info {
        background: linear-gradient(180deg, #36b9cc 10%, #258391 100%);
    }
    
    .text-xs {
        font-size: 0.7rem;
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
    
    .text-gray-300 {
        color: #dddfeb !important;
    }
    
    .text-gray-800 {
        color: #5a5c69 !important;
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
    
    .border-end {
        border-right: 1px solid #e3e6f0;
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
        
        .table-responsive {
            font-size: 0.875rem;
        }
    }
</style>
<script>
    let maxSaldo = 0; // Global OK untuk sederhana, tapi bisa di-closure jika multiple modal

    // Fungsi menampilkan modal tarik saldo dengan data yang diisi
    function showTarikSaldoModal(classTutorialId, kelasName, saldoTersedia) {
        console.log('[showTarikSaldoModal] dipanggil dengan:', { classTutorialId, kelasName, saldoTersedia });

        const classTutorialInput = document.getElementById('classTutorialId');
        const kelasNameElem = document.getElementById('kelasName');
        const saldoTersediaElem = document.getElementById('saldoTersedia');
        const jumlahTarikInput = document.getElementById('jumlahTarik');
        const catatanInput = document.getElementById('catatan');

        if (!classTutorialInput || !kelasNameElem || !saldoTersediaElem || !jumlahTarikInput || !catatanInput) {
            console.error('[showTarikSaldoModal] Elemen modal tidak ditemukan:', {
                classTutorialInput, kelasNameElem, saldoTersediaElem, jumlahTarikInput, catatanInput
            });
            return;
        }

        classTutorialInput.value = classTutorialId;
        kelasNameElem.textContent = kelasName;
        saldoTersediaElem.textContent = formatRupiah(saldoTersedia);
        jumlahTarikInput.max = saldoTersedia;
        jumlahTarikInput.value = '';
        catatanInput.value = '';

        maxSaldo = saldoTersedia;
        console.log('[showTarikSaldoModal] maxSaldo di-set ke:', maxSaldo);

        const modalElem = document.getElementById('tarikSaldoModal');
        if (!modalElem) {
            console.error('[showTarikSaldoModal] Modal tarik saldo tidak ditemukan');
            return;
        }
        const modal = new bootstrap.Modal(modalElem);
        modal.show();
        console.log('[showTarikSaldoModal] Modal ditampilkan');
    }

    // Fungsi menampilkan alert dengan tipe dan pesan tertentu
    function showAlert(type, message) {
        console.log(`[showAlert] Tipe: ${type}, Pesan: ${message}`);

        const alertClass = {
            success: 'alert-success',
            error: 'alert-danger',
            warning: 'alert-warning',
            info: 'alert-info'
        }[type] || 'alert-info';

        const iconClass = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-triangle',
            warning: 'fa-exclamation-circle',
            info: 'fa-info-circle'
        }[type] || 'fa-info-circle';

        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show shadow" role="alert" data-tarik-saldo-alert
                 style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 20000; min-width: 320px; max-width: 90vw;">
                <i class="fas ${iconClass} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        // Hapus alert lama buatan fungsi ini saja (bukan alert session/info lain di halaman/modal),
        // lalu tampilkan mengambang di atas modal (fixed + z-index tinggi) supaya tidak ketutup
        // backdrop modal saat modal Tarik Saldo masih terbuka.
        document.querySelectorAll('[data-tarik-saldo-alert]').forEach(alert => alert.remove());

        document.body.insertAdjacentHTML('beforeend', alertHtml);
        console.log('[showAlert] Alert ditampilkan mengambang di atas modal');

        setTimeout(() => {
            const alertElem = document.querySelector('[data-tarik-saldo-alert]');
            if (alertElem) {
                alertElem.remove();
                console.log('[showAlert] Alert otomatis dihapus setelah 5 detik');
            }
        }, 5000);
    }

    // Format angka ke Rupiah
    function formatRupiah(amount) {
        if (typeof amount !== 'number') amount = parseInt(amount) || 0;
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    // Animasi counter untuk saldo
    function animateCounter(element, target, duration = 2000) {
        console.log('[animateCounter] Memulai animasi dari 0 ke', target);
        let start = 0;
        const stepTime = 16;
        const steps = duration / stepTime;
        const increment = target / steps;

        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                start = target;
                clearInterval(timer);
                console.log('[animateCounter] Animasi selesai');
            }
            element.textContent = formatRupiah(Math.floor(start));
        }, stepTime);
    }

    // Refresh data dengan SweetAlert loading
    function refreshData() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Memperbarui Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => Swal.showLoading()
            });
            console.log('[refreshData] SweetAlert loading ditampilkan');
        } else {
            console.warn('[refreshData] SweetAlert tidak ditemukan');
        }
        setTimeout(() => {
            console.log('[refreshData] Memulai reload halaman');
            location.reload();
        }, 1000);
    }

    // Validasi jumlah penarikan
    function validateAmount(amount) {
        if (isNaN(amount) || amount < 1000) return 'Jumlah minimal Rp 1.000';
        if (amount > maxSaldo) return 'Jumlah melebihi saldo tersedia';
        return null;
    }

    // Inisialisasi SEMUA event listeners dan effects di satu DOMContentLoaded
    document.addEventListener('DOMContentLoaded', () => {
        console.log('[DOMContentLoaded] Halaman Tarik Saldo siap');

        // Inisialisasi counter saldo
        const totalSaldoElement = document.getElementById('totalSaldoTersedia');
        if (totalSaldoElement) {
            const targetText = totalSaldoElement.textContent;
            const target = parseInt(targetText.replace(/[^\d]/g, '')) || 0;
            console.log('[DOMContentLoaded] Target saldo ditemukan:', target);
            if (target > 0) {
                totalSaldoElement.textContent = formatRupiah(0);
                animateCounter(totalSaldoElement, target);
            }
        } else {
            console.warn('[DOMContentLoaded] Elemen totalSaldoTersedia tidak ditemukan');
        }

        // Inisialisasi tooltip Bootstrap
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
            console.log('[DOMContentLoaded] Tooltip Bootstrap diinisialisasi untuk', tooltipTriggerList.length, 'elemen');
        }

        // Efek hover card
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-2px)';
                card.style.transition = 'all 0.3s ease';
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
            });
        });

        // Smooth scroll anchor link
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', e => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    console.log('[SmoothScroll] Scroll ke:', anchor.getAttribute('href'));
                } else {
                    console.warn('[SmoothScroll] Target anchor tidak ditemukan:', anchor.getAttribute('href'));
                }
            });
        });

        // Highlight baris tabel saat diklik (kecuali klik tombol)
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('click', e => {
                if (!e.target.closest('.btn')) {
                    document.querySelectorAll('.table tbody tr').forEach(r => r.classList.remove('table-active'));
                    row.classList.add('table-active');
                    console.log('[Table] Baris tabel dipilih:', row);
                }
            });
        });

        // Event listener untuk input jumlah tarik saldo
        const jumlahTarikInput = document.getElementById('jumlahTarik');
        if (jumlahTarikInput) {
            jumlahTarikInput.addEventListener('input', function () {
                let value = this.value.trim(); // Tambah trim untuk spasi
                value = parseInt(value) || 0; // Handle empty/NaN
                console.log('[Input jumlahTarik] Nilai input:', value);

                if (value > maxSaldo) {
                    this.value = maxSaldo;
                    showAlert('warning', 'Jumlah tidak boleh melebihi saldo tersedia');
                    console.warn('[Input jumlahTarik] Nilai melebihi maxSaldo, di-reset ke', maxSaldo);
                }
                if (value < 0) {
                    this.value = '';
                    console.warn('[Input jumlahTarik] Nilai negatif, dikosongkan');
                }
            });
        } else {
            console.warn('[Init] Elemen jumlahTarik tidak ditemukan');
        }

        // Event listener form submit tarik saldo - LENGKAP DENGAN ERROR HANDLING
        const tarikSaldoForm = document.getElementById('tarikSaldoForm');
        if (tarikSaldoForm) {
            tarikSaldoForm.addEventListener('submit', function (e) {
                e.preventDefault();
                console.log('[Form Submit] Form tarik saldo disubmit');

                const formData = new FormData(this);
                const jumlah = parseInt(formData.get('jumlah')) || 0;
                console.log('[Form Submit] Data form lengkap:', Object.fromEntries(formData)); // Log full data

                // Validasi jumlah
                const validationError = validateAmount(jumlah);
                if (validationError) {
                    console.error('[Form Submit] Validasi gagal:', validationError);
                    showAlert('error', validationError);
                    return;
                }

                const submitBtn = document.getElementById('submitTarikSaldo');
                if (!submitBtn) {
                    console.error('[Form Submit] Tombol submit tidak ditemukan');
                    showAlert('error', 'Elemen tombol tidak ditemukan');
                    return;
                }
                const originalText = submitBtn.innerHTML;

                // Loading state tombol
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
                submitBtn.disabled = true;
                console.log('[Form Submit] Mengirim request AJAX ke route tarik saldo');

                // CSRF check (ambil dari input hidden @csrf di dalam form ini, bukan meta tag
                // global yang memang tidak ada di layout halaman ini)
                const csrfInput = tarikSaldoForm.querySelector('input[name="_token"]');
                if (!csrfInput || !csrfInput.value) {
                    console.error('[Form Submit] CSRF token tidak ditemukan');
                    showAlert('error', 'CSRF token hilang. Refresh halaman.');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    return;
                }

                // Kirim request AJAX dengan timeout dan detail error
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000); // 30s timeout

                fetch('{{ route("kom.tarik-saldo_cabang.tarik") }}', {
                    method: 'POST',
                    body: formData,
                    signal: controller.signal,
                    headers: {
                        'X-CSRF-TOKEN': csrfInput.value,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest' // Laravel detect AJAX
                    }
                })
                .then(response => {
                    clearTimeout(timeoutId);
                    console.log('[AJAX Response] Status:', response.status, 'OK:', response.ok);
                    console.log('[AJAX Response] Content-Type:', response.headers.get('content-type'));

                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('[AJAX Error] Response body (text):', text.substring(0, 500)); // Log snippet
                            let errorMsg = `HTTP ${response.status}`;
                            if (response.status === 419) errorMsg += ' (CSRF Token Mismatch - Refresh halaman)';
                            if (response.status === 403) errorMsg += ' (Akses Ditolak - Cek role user)';
                            if (response.status === 404) errorMsg += ' (Route Tidak Ditemukan - Cek routes/web.php)';
                            if (response.status === 500) errorMsg += ' (Server Error - Cek log Laravel)';
                            throw new Error(errorMsg + (text ? ': ' + text.substring(0, 100) : ''));
                        });
                    }

                    // Cek jika response bukan JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        return response.text().then(text => {
                            console.error('[AJAX Error] Response bukan JSON:', text.substring(0, 200));
                            throw new Error('Server return HTML/error page, bukan JSON. Lihat console untuk detail.');
                        });
                    }

                    return response.json();
                })
                .then(data => {
                    console.log('[AJAX Success] Data JSON diterima:', data);
                    if (data.success) {
                        showAlert('success', data.message || 'Penarikan saldo berhasil diajukan.');
                        const modalElem = document.getElementById('tarikSaldoModal');
                        if (modalElem) {
                            const modal = bootstrap.Modal.getInstance(modalElem);
                            if (modal) modal.hide();
                        }
                        console.log('[AJAX Success] Penarikan berhasil, modal ditutup dan halaman akan reload');
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        console.error('[AJAX] Server return false success:', data.message || 'Unknown error');
                        showAlert('error', data.message || 'Gagal memproses penarikan. Lihat console.');
                    }
                })
                .catch(error => {
                    clearTimeout(timeoutId);
                    console.error('[AJAX Catch] Full error object:', error);
                    let errorMsg = error.message || 'Terjadi kesalahan jaringan atau server.';
                    if (error.name === 'AbortError') {
                        errorMsg = 'Request timeout (30 detik). Coba lagi atau cek koneksi.';
                    } else if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
                        errorMsg = 'Tidak bisa terhubung ke server. Cek internet atau route.';
                    }
                    showAlert('error', 'Gagal mengirim request: ' + errorMsg);
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    console.log('[AJAX Finally] Request selesai, tombol reset');
                });
            });
        } else {
            console.warn('[Init] Form tarikSaldoForm tidak ditemukan');
        }

        // Keyboard shortcut: Escape untuk tutup modal, Ctrl+R untuk refresh (FIX: e.key sudah lowercase)
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                const modalElem = document.getElementById('tarikSaldoModal');
                if (modalElem) {
                    const modal = bootstrap.Modal.getInstance(modalElem);
                    if (modal) {
                        modal.hide();
                        console.log('[Keyboard] Escape ditekan, modal ditutup');
                    }
                }
            }
            if (e.ctrlKey && e.key === 'r') { // Fix: e.key sudah lowercase, tidak perlu toLowerCase()
                e.preventDefault();
                console.log('[Keyboard] Ctrl+R ditekan, refreshData dipanggil');
                refreshData();
            }
        });

        // Page visibility API untuk log status tab
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                console.log('[PageVisibility] Tab tersembunyi, auto-refresh dijeda');
            } else {
                console.log('[PageVisibility] Tab aktif kembali, auto-refresh dilanjutkan');
            }
        });

        // Tangani error JavaScript global
        window.addEventListener('error', e => {
            console.error('[GlobalError] JavaScript error:', e.error, 'File:', e.filename, 'Line:', e.lineno);
        });

        // Animasi fade-in modal Bootstrap (hapus custom opacity, Bootstrap sudah handle fade)
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('show.bs.modal', () => {
                console.log('[Modal] Modal show event triggered (Bootstrap handle fade)');
            });
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

