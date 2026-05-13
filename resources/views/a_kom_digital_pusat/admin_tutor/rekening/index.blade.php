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
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-university me-2"></i>&nbsp;&nbsp;
                            <h5 class="mb-0 pb-2">Data Rekening Bank</h5>
                        </div>
                    </div>
                </div>
            </div>

            @if ($userRekening)
                {{-- Tambahkan setelah @if ($userRekening) untuk debugging --}}
                @if (!$userRekening)
                    <div class="alert alert-info">
                        <strong>Debug:</strong> Form should be rendered (userRekening is null)
                    </div>
                @else
                    <div class="alert alert-warning">
                        <strong>Debug:</strong> Form NOT rendered (userRekening exists)
                    </div>
                @endif
                {{-- Data Rekening Sudah Ada --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>&nbsp;&nbsp;Data Rekening Anda
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-primary h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user me-2"></i>&nbsp;&nbsp;Informasi Pemilik
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="40%"><strong>Nama Pemilik:</strong></td>
                                                <td>{{ $userRekening->nama_rek }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>User:</strong></td>
                                                <td>{{ $userRekening->user->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td>{{ $userRekening->user->email ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Cabang:</strong></td>
                                                <td>{{ $userRekening->cabang->name ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success h-100">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-university me-2"></i>Informasi Bank
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="40%"><strong>Bank:</strong></td>
                                                <td>{{ $userRekening->nama_bank }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Cabang Bank:</strong></td>
                                                <td>{{ $userRekening->cabang_bank }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>No. Rekening:</strong></td>
                                                <td>
                                                    <span class="badge bg-info fs-6" id="noRekening">
                                                        {{ $userRekening->masked_no_rek }}
                                                    </span>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                                        onclick="toggleNoRekening()" id="toggleBtn">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Terdaftar:</strong></td>
                                                <td>{{ $userRekening->created_at->format('d F Y H:i') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Informasi:</strong> Data rekening sudah tersimpan dan tidak dapat diubah.
                            Jika ada kesalahan, silakan hubungi administrator.
                        </div>
                    </div>
                </div>
            @else
                {{-- Form Input Rekening --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>&nbsp;&nbsp;Input Data Rekening Bank
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong> Data rekening hanya dapat diinput sekali.
                            Pastikan data yang Anda masukkan sudah benar sebelum menyimpan.
                        </div>
                        
                       
                        <form id="rekeningForm">
                            @csrf 
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-user me-2"></i>&nbsp;&nbsp;Data Pemilik Rekening
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="nama_rek" class="form-label">
                                                    <strong>Nama Pemilik Rekening <span
                                                            class="text-danger">*</span></strong>
                                                </label>
                                                <input type="text" class="form-control" id="nama_rek" name="nama_rek"
                                                    placeholder="Masukkan nama sesuai rekening bank" required
                                                    maxlength="100">
                                                <small class="text-muted">Nama harus sesuai dengan yang tertera di rekening
                                                    bank</small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label"><strong>User:</strong></label>
                                                <input type="text" class="form-control-plaintext"
                                                    value="{{ Auth::user()->name }}" readonly>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label"><strong>Email:</strong></label>
                                                <input type="text" class="form-control-plaintext"
                                                    value="{{ Auth::user()->email }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-university me-2"></i>&nbsp;&nbsp;Data Bank
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="nama_bank" class="form-label">
                                                    <strong>Nama Bank <span class="text-danger">*</span></strong>
                                                </label>
                                                <select class="form-select form-control" id="nama_bank" name="nama_bank" required>
                                                    <option value="">Pilih Bank</option>
                                                    @foreach ($listBank as $code => $name)
                                                        <option value="{{ $code }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="cabang_bank" class="form-label">
                                                    <strong>Cabang Bank <span class="text-danger">*</span></strong>
                                                </label>
                                                <input type="text" class="form-control" id="cabang_bank"
                                                    name="cabang_bank"
                                                    placeholder="Contoh: Jakarta Pusat, Surabaya Tunjungan" required
                                                    maxlength="100">
                                                <small class="text-muted">Nama cabang bank tempat rekening dibuka</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="no_rek" class="form-label">
                                                    <strong>Nomor Rekening <span class="text-danger">*</span></strong>
                                                </label>
                                                <input type="text" class="form-control" id="no_rek" name="no_rek"
                                                    placeholder="Masukkan nomor rekening" required maxlength="50"
                                                    pattern="[0-9]+" title="Hanya boleh berisi angka">
                                                <small class="text-muted">Hanya boleh berisi angka, tanpa spasi atau tanda
                                                    baca</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card border-warning">
                                        <div class="card-body">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="confirmData"
                                                    required>
                                                <label class="form-check-label" for="confirmData">
                                                    <strong>Saya menyatakan bahwa data yang saya masukkan sudah benar dan
                                                        sesuai dengan rekening bank yang saya miliki.</strong>
                                                </label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="confirmOnce"
                                                    required>
                                                <label class="form-check-label" for="confirmOnce">
                                                    <strong>Saya memahami bahwa data rekening hanya dapat diinput sekali dan
                                                        tidak dapat diubah setelah disimpan.</strong>
                                                </label>
                                            </div>

                                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                                <button type="button" class="btn btn-secondary me-md-2"
                                                    onclick="resetForm()">
                                                    <i class="fas fa-undo me-1"></i>&nbsp;&nbsp;Reset Form
                                                </button>&nbsp;&nbsp;
                                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                                    <i class="fas fa-save me-1"></i>&nbsp;&nbsp;Simpan Data Rekening
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <script>
let isNoRekVisible = false;
const actualNoRek = '{{ $userRekening->no_rek ?? '' }}';
const maskedNoRek = '{{ $userRekening->masked_no_rek ?? '' }}';

// Toggle visibility nomor rekening
function toggleNoRekening() {
    const noRekElement = document.getElementById('noRekening');
    const toggleBtn = document.getElementById('toggleBtn');

    if (isNoRekVisible) {
        noRekElement.textContent = maskedNoRek;
        toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
        isNoRekVisible = false;
    } else {
        noRekElement.textContent = actualNoRek;
        toggleBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
        isNoRekVisible = true;
    }
}

// Show alert function
function showAlert(type, message) {
    console.log(`Showing alert: ${type} - ${message}`);

    const alertClass = type === 'success' ? 'alert-success' :
        type === 'error' ? 'alert-danger' :
        type === 'warning' ? 'alert-warning' : 'alert-info';

    const icon = type === 'success' ? 'fa-check-circle' :
        type === 'error' ? 'fa-exclamation-triangle' :
        type === 'warning' ? 'fa-exclamation-circle' : 'fa-info-circle';

    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    // Remove existing alerts
    document.querySelectorAll('.alert').forEach(alert => alert.remove());

    // Add new alert at the top
    const targetElement = document.querySelector('.col-12');
    if (targetElement) {
        targetElement.insertAdjacentHTML('afterbegin', alertHtml);
    }

    // Auto remove after 5 seconds
    setTimeout(() => {
        document.querySelector('.alert')?.remove();
    }, 5000);
}

// Validation function
function validateForm() {
    console.log('=== FORM VALIDATION START ===');

    const namaRek = document.getElementById('nama_rek')?.value.trim();
    const namaBank = document.getElementById('nama_bank')?.value;
    const cabangBank = document.getElementById('cabang_bank')?.value.trim();
    const noRek = document.getElementById('no_rek')?.value.trim();
    const confirmData = document.getElementById('confirmData')?.checked;
    const confirmOnce = document.getElementById('confirmOnce')?.checked;

    console.log('Form values:', {
        namaRek, namaBank, cabangBank, noRek, confirmData, confirmOnce
    });

    if (!namaRek) {
        showAlert('error', 'Nama pemilik rekening harus diisi');
        return false;
    }

    if (!namaBank) {
        showAlert('error', 'Nama bank harus dipilih');
        return false;
    }

    if (!cabangBank) {
        showAlert('error', 'Cabang bank harus diisi');
        return false;
    }

    if (!noRek) {
        showAlert('error', 'Nomor rekening harus diisi');
        return false;
    }

    if (!/^[0-9]+$/.test(noRek)) {
        showAlert('error', 'Nomor rekening hanya boleh berisi angka');
        return false;
    }

    if (!confirmData) {
        showAlert('error', 'Anda harus mengkonfirmasi kebenaran data');
        return false;
    }

    if (!confirmOnce) {
        showAlert('error', 'Anda harus memahami bahwa data hanya dapat diinput sekali');
        return false;
    }

    console.log('=== FORM VALIDATION PASSED ===');
    return true;
}

// Display validation errors
function displayValidationErrors(errors) {
    Object.keys(errors).forEach(field => {
        const input = document.getElementById(field);
        if (input) {
            input.classList.add('is-invalid');
            
            const existingError = input.parentNode.querySelector('.invalid-feedback');
            if (existingError) {
                existingError.remove();
            }
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = errors[field][0];
            input.parentNode.appendChild(errorDiv);
        }
    });
}

// DOM Content Loaded - HANYA SATU EVENT LISTENER
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOM LOADED ===');
    console.log('Store route:', '{{ route("kom.rekening_cabang.store") }}');
    console.log('CSRF token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
    
    const form = document.getElementById('rekeningForm');
    console.log('Form found:', form);
    
    if (!form) {
        console.error('Form rekeningForm not found!');
        return;
    }

    // HANYA SATU EVENT LISTENER
    form.addEventListener('submit', function(e) {
        console.log('=== FORM SUBMITTED ===');
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        
        // Log form data
        console.log('Form data:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        // Validasi form
        if (!validateForm()) {
            console.log('Form validation failed');
            return;
        }
        
        // Konfirmasi
        if (!confirm('Apakah Anda yakin data yang dimasukkan sudah benar? Data rekening tidak dapat diubah setelah disimpan.')) {
            console.log('User cancelled submission');
            return;
        }
        
        // Loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
        submitBtn.disabled = true;
        
        console.log('Sending AJAX request to:', '{{ route("kom.rekening_cabang.store") }}');
        
        // PERBAIKI ROUTE NAME DI SINI
        fetch('{{ route("kom.rekening_cabang.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response OK:', response.ok);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return response.text();
        })
        .then(text => {
            console.log('Raw response:', text);
            
            try {
                const data = JSON.parse(text);
                console.log('Parsed data:', data);
                
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showAlert('error', data.message);
                    if (data.errors) {
                        displayValidationErrors(data.errors);
                    }
                }
            } catch (parseError) {
                console.error('JSON parse error:', parseError);
                showAlert('error', 'Response tidak valid dari server');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showAlert('error', 'Terjadi kesalahan: ' + error.message);
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});
</script>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
        }

        .card {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .table-borderless td {
            border: none;
            padding: 0.5rem 0.75rem;
        }

        .fs-6 {
            font-size: 1rem !important;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .form-select:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .btn-outline-secondary:hover {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
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
        }
    </style>
@endsection

@push('styles')
@endpush

@push('scripts')
@endpush
