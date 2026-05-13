@extends('layouts.KomDigital')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Header Section --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">❓ Pertanyaan Peserta</h4>
                    <p class="text-muted mb-0">Lihat semua pertanyaan dari peserta di kelas tutorial Anda</p>
                </div>
                <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-1"></i>&nbsp;&nbsp;Refresh
                </button>
            </div>

            {{-- Stats Cards --}}
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 mr-3">
                                    <i class="fas fa-question-circle fa-2x opacity-75"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-bold fs-4">{{ $data->total() }}</div>
                                    <div class="small opacity-75">Total Pertanyaan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm bg-gradient-success text-white">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 mr-3">
                                    <i class="fas fa-users fa-2x opacity-75"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-bold fs-4">{{ $data->groupBy('user_id')->count() }}</div>
                                    <div class="small opacity-75">Peserta Bertanya</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm bg-gradient-info text-white">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 mr-3">
                                    <i class="fas fa-book fa-2x opacity-75"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-bold fs-4">{{ $data->groupBy('modul_id')->count() }}</div>
                                    <div class="small opacity-75">Modul Berbeda</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 mr-3">
                                    <i class="fas fa-calendar fa-2x opacity-75"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-bold fs-4">{{ $data->where('tgl_submit', '>=', now()->startOfDay())->count() }}</div>
                                    <div class="small opacity-75">Hari Ini</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm bg-light">
                        <div class="card-body py-3">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" 
                                       placeholder="Cari pertanyaan, nama peserta, atau modul..." 
                                       id="searchInput">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-list me-2 text-primary"></i>&nbsp;&nbsp;Daftar Pertanyaan Peserta
                    </h5>
                </div>

                <div class="card-body p-0">
                    @if($data->isEmpty())
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-comments fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted mb-3">Belum Ada Pertanyaan</h4>
                                <p class="text-muted mb-4">Belum ada pertanyaan dari peserta di kelas tutorial Anda.</p>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 fw-bold">#</th>
                                        <th class="border-0 fw-bold">
                                            <i class="fas fa-user me-1 text-primary"></i>&nbsp;&nbsp;Nama Peserta
                                        </th>
                                        <th class="border-0 fw-bold">
                                            <i class="fas fa-book me-1 text-success"></i>&nbsp;&nbsp;Modul
                                        </th>
                                        <th class="border-0 fw-bold">
                                            <i class="fas fa-graduation-cap me-1 text-info"></i>&nbsp;&nbsp;Kelas
                                        </th>
                                        <th class="border-0 fw-bold">
                                            <i class="fas fa-map-marker-alt me-1 text-warning"></i>&nbsp;&nbsp;Rayon
                                        </th>
                                        <th class="border-0 fw-bold">
                                            <i class="fas fa-comment me-1 text-secondary"></i>&nbsp;&nbsp;Pertanyaan
                                        </th>
                                        <th class="border-0 fw-bold">
                                            <i class="fas fa-calendar-alt me-1 text-dark"></i>&nbsp;&nbsp;Tanggal Submit
                                        </th>
                                        <th class="border-0 fw-bold">
                                            <i class="fas fa-info-circle me-1 text-danger"></i>&nbsp;&nbsp;Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                        <tr class="align-middle">
                                            <td class="fw-bold text-muted">
                                                {{ $data->firstItem() + $loop->index }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                   
                                                    <div>
                                                        <div class="fw-bold">{{ $item->user->name ?? 'Unknown' }}</div>
                                                        <small class="text-muted">{{ $item->user->email ?? '-' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-3 py-2">
                                                    {{ $item->modul->name ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info border px-3 py-2">
                                                    {{ $item->class->name ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                   
                                                    <div>
                                                        <div class="fw-bold">{{ $item->rayon->name ?? '-' }}</div>
                                                        <small class="text-muted">Lokasi</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="question-content" style="max-width: 300px;">
                                                    <div class="fw-bold">{{ Str::limit($item->pertanyaan, 60) }}</div>
                                                    @if(strlen($item->pertanyaan) > 60)
                                                        <button class="btn btn-link btn-sm p-0 text-decoration-none" 
                                                                onclick="showFullQuestion('{{ addslashes($item->pertanyaan) }}', '{{ $item->user->name ?? 'Unknown' }}')">
                                                            <small>Lihat selengkapnya...</small>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($item->tgl_submit)
                                                    <div class="fw-bold">{{ $item->tgl_submit->format('d M Y') }}</div>
                                                    <small class="text-muted">{{ $item->tgl_submit->format('H:i') }}</small>
                                                    <div class="small text-muted">{{ $item->tgl_submit->diffForHumans() }}</div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match(strtolower($item->status ?? '')) {
                                                        'answered' => 'bg-success',
                                                        'pending' => 'bg-warning text-dark',
                                                        'active' => 'bg-primary',
                                                        'closed' => 'bg-secondary',
                                                        default => 'bg-info'
                                                    };
                                                    $statusIcon = match(strtolower($item->status ?? '')) {
                                                        'answered' => 'fa-check-circle',
                                                        'pending' => 'fa-clock',
                                                        'active' => 'fa-play-circle',
                                                        'closed' => 'fa-times-circle',
                                                        default => 'fa-info-circle'
                                                    };
                                                    $statusText = match(strtolower($item->status ?? '')) {
                                                        'answered' => 'Dijawab',
                                                        'pending' => 'Menunggu',
                                                        'active' => 'Aktif',
                                                        'closed' => 'Ditutup',
                                                        default => ucfirst($item->status ?? 'Unknown')
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusClass }} px-3 py-2 text-white">
                                                    <i class="fas {{ $statusIcon }} me-1"></i>&nbsp;&nbsp;{{ $statusText }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Pagination --}}
                @if($data->hasPages())
                    <div class="card-footer bg-white border-top-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} pertanyaan
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

{{-- Modal untuk Pertanyaan Lengkap --}}
<div class="modal fade" id="fullQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-comment-dots me-2 text-primary"></i>Pertanyaan Lengkap
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="text-muted mb-2">Pertanyaan dari:</h6>
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-sm bg-primary-subtle rounded-circle me-3 d-flex align-items-center justify-content-center">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-bold" id="modalStudentName"></div>
                            <small class="text-muted">Peserta</small>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-muted mb-2">Isi Pertanyaan:</h6>
                    <div class="bg-light rounded p-3">
                        <p class="mb-0" id="modalQuestionText" style="white-space: pre-wrap; line-height: 1.6;"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
    
    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.1);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
        transform: scale(1.01);
        transition: all 0.2s ease;
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
        line-height: 1.4;
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
        
        .question-content {
            max-width: 200px !important;
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
    }
    
    .pagination .page-link:hover {
        background-color: #e9ecef;
        color: #0d6efd;
    }
</style>
<script>
    // Search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm) || searchTerm === '') {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update visible count
            const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
            updateSearchResults(visibleRows.length, rows.length);
        });
    });

    // Update search results info
    function updateSearchResults(visible, total) {
        const cardTitle = document.querySelector('.card-title');
        if (visible < total) {
            cardTitle.innerHTML = `
                <i class="fas fa-list me-2 text-primary"></i>Daftar Pertanyaan Peserta
                <small class="text-muted ms-2">(${visible} dari ${total} pertanyaan)</small>
            `;
        } else {
            cardTitle.innerHTML = `
                <i class="fas fa-list me-2 text-primary"></i>Daftar Pertanyaan Peserta
            `;
        }
    }

    // Show full question in modal
    function showFullQuestion(question, studentName) {
        const modal = new bootstrap.Modal(document.getElementById('fullQuestionModal'));
        
        document.getElementById('modalStudentName').textContent = studentName;
        document.getElementById('modalQuestionText').textContent = question;
        
        modal.show();
    }

    // Stats animation
    document.addEventListener('DOMContentLoaded', function() {
        const statNumbers = document.querySelectorAll('.fs-4');
        statNumbers.forEach(number => {
            const finalValue = parseInt(number.textContent);
            if (!isNaN(finalValue)) {
                animateNumber(number, 0, finalValue, 1000);
            }
        });
    });

    // Animate numbers
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

    // Enhanced table interactions
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

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + F for search
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            document.getElementById('searchInput').focus();
        }
        
        // Escape to close modal
        if (e.key === 'Escape') {
            const modal = bootstrap.Modal.getInstance(document.getElementById('fullQuestionModal'));
            if (modal) {
                modal.hide();
            }
        }
    });

    // Export functionality (simple)
    function exportData() {
        const table = document.querySelector('table');
        const rows = Array.from(table.querySelectorAll('tr'));
        
        let csvContent = "data:text/csv;charset=utf-8,";
        
        rows.forEach(row => {
            const cells = Array.from(row.querySelectorAll('th, td'));
            const rowData = cells.map(cell => {
                let text = cell.textContent.trim();
                text = text.replace(/\s+/g, ' '); // Replace multiple spaces
                text = text.replace(/"/g, '""'); // Escape quotes
                return `"${text}"`;
            }).join(',');
            csvContent += rowData + "\n";
        });
        
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', `pertanyaan_peserta_${new Date().toISOString().split('T')[0]}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showNotification('success', 'Export Berhasil', 'Data pertanyaan berhasil diexport ke CSV.');
    }

    // Simple notification function
    function showNotification(type, title, message) {
        const iconClass = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-triangle',
            'warning': 'fa-exclamation-circle',
            'info': 'fa-info-circle'
        };
        
        const bgClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        };
        
        const notification = document.createElement('div');
        notification.className = `alert ${bgClass[type]} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="fas ${iconClass[type]} me-2"></i>
            <strong>${title}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 4000);
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Pertanyaan page loaded - View Only Mode');
        
        // Show info about view-only mode
        setTimeout(() => {
            showNotification('info', 'Mode Tampilan', 'Anda dapat melihat semua pertanyaan peserta di kelas tutorial Anda.');
        }, 1500);
    });

    // Performance monitoring
    window.addEventListener('load', function() {
        const loadTime = performance.now();
        console.log(`Pertanyaan page loaded in ${loadTime.toFixed(2)}ms`);
    });
</script>
@endsection

