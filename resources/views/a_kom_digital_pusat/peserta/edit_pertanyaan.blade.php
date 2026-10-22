@extends('layouts.KomDigital')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                {{-- Back Button & Header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('kom.peserta.pertanyaan') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('Kembali') }}
                    </a>
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-edit me-1"></i> {{ __('Sisa Edit') }}: {{ 1 - ($pertanyaan->edit_count ?? 0) }}x
                    </span>
                </div>

                {{-- Edit Form Card --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                            <i class="fas fa-edit text-primary"></i> {{ __('Edit Pertanyaan') }}
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        {{-- Info Section --}}
                        <div class="alert alert-info d-flex align-items-start mb-4" role="alert">
                            <i class="fas fa-info-circle fa-lg me-3 mt-2 mr-2"></i>
                            <div>
                                <strong>{{ __('Informasi') }}:</strong>
                                <ul class="mb-0 mt-2">
                                    <!-- <li>Anda hanya dapat mengedit pertanyaan ini <strong>1 (satu) kali</strong>.</li> -->
                                    <li>{{ __('Pastikan pertanyaan Anda jelas dan lengkap sebelum disimpan.') }}</li>
                                    <!-- <li>Pertanyaan yang sudah diedit tidak dapat diedit kembali.</li> -->
                                </ul>
                            </div>
                        </div>

                        {{-- Original Question Info --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">
                                <i class="fas fa-book me-1"></i> {{ __('Modul') }}
                            </label>
                            <div class="form-control-plaintext">
                                {{ $pertanyaan->modul->name ?? '-' }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">
                                <i class="fas fa-graduation-cap me-1"></i> {{ __('Kelas') }}
                            </label>
                            <div class="form-control-plaintext">
                                {{ $pertanyaan->class->name ?? '-' }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">
                                <i class="fas fa-calendar-alt me-1"></i> {{ __('Tanggal Submit') }}
                            </label>
                            <div class="form-control-plaintext">
                                @if ($pertanyaan->tgl_submit)
                                    {{ \Carbon\Carbon::parse($pertanyaan->tgl_submit)->translatedFormat('d F Y, H:i') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Edit Form --}}
                        <form action="{{ route('kom.peserta.pertanyaan.update', $pertanyaan->id) }}" method="POST" id="editForm">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="pertanyaan" class="form-label fw-bold">
                                    <i class="fas fa-comment me-1 text-primary"></i> {{ __('Pertanyaan Anda') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea 
                                    class="form-control @error('pertanyaan') is-invalid @enderror" 
                                    id="pertanyaan" 
                                    name="pertanyaan" 
                                    rows="6"
                                    placeholder="{{ __('Tulis pertanyaan Anda di sini...') }}"
                                    required
                                    minlength="5"
                                    maxlength="1000"
                                >{{ old('pertanyaan', $pertanyaan->pertanyaan) }}</textarea>
                                
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    @error('pertanyaan')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                        </div>
                                    @else
                                        <small class="text-muted">{{ __('Minimal 5 karakter, maksimal 1000 karakter') }}</small>
                                    @enderror
                                    <small class="text-muted char-counter">
                                        <span id="charCount">{{ strlen(old('pertanyaan', $pertanyaan->pertanyaan)) }}</span>/1000
                                    </small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <a href="{{ route('kom.peserta.pertanyaan') }}" class="btn btn-outline-secondary mr-3">
                                    <i class="fas fa-times me-1"></i> {{ __('Batal') }}
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save me-1"></i> {{ __('Simpan Perubahan') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            border-radius: 12px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        .form-label {
            margin-bottom: 0.5rem;
        }

        .btn {
            border-radius: 8px;
            padding: 0.5rem 1rem;
        }

        .alert-info {
            border-radius: 8px;
            background-color: rgba(13, 202, 240, 0.1);
            border: 1px solid rgba(13, 202, 240, 0.3);
            color: #0c5c6e;
        }

        .char-counter {
            font-size: 0.8rem;
        }

        .border-top {
            border-top: 1px solid #dee2e6 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('pertanyaan');
            const charCounter = document.getElementById('charCount');
            const form = document.getElementById('editForm');
            const submitBtn = document.getElementById('submitBtn');

            // Character counter
            textarea.addEventListener('input', function() {
                const count = this.value.length;
                charCounter.textContent = count;
                
                if (count > 1000) {
                    charCounter.classList.add('text-danger');
                } else {
                    charCounter.classList.remove('text-danger');
                }
            });

            // Form submission with confirmation
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: @json(__('Konfirmasi Perubahan')),
                    text: @json(__('Apakah Anda yakin ingin menyimpan perubahan? Perubahan tidak dapat dikembalikan.')),
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check me-1"></i> ' + @json(__('Ya, Simpan')),
                    cancelButtonText: '<i class="fas fa-times me-1"></i> ' + @json(__('Batal'))
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection