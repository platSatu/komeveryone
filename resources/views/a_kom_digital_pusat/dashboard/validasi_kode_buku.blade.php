@extends('layouts.KomDigital')

@section('content')
    <div class="container-fluid px-3">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-gradient-primary text-white py-4 text-center">
                        <i class="fas fa-book-open fa-2x mb-2"></i>
                        <h4 class="mb-1 fw-bold">Validasi E-Book KOM</h4>
                        <small class="opacity-75">Verifikasi nomor e-book Anda</small>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <form id="ebookValidationForm" autocomplete="off">
                            @csrf

                            {{-- Input No Buku --}}
                            <div class="mb-4">
                                <label for="no_books" class="form-label fw-bold mb-3">
                                    <i class="fas fa-hashtag me-2 text-primary"></i>Nomor E-Book
                                </label>

                                <div class="input-wrapper">
                                    <span class="input-icon">
                                        <i class="fas fa-book text-muted"></i>
                                    </span>
                                    <input type="text" name="no_books" id="no_books" class="form-control-custom"
                                        placeholder="Masukkan nomor e-book" required maxlength="20"
                                        aria-describedby="kode_feedback">
                                </div>
                                <small id="kode_feedback" class="form-text mt-1 text-danger"></small>
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Masukkan nomor yang tertera pada e-book Anda
                                </div>
                            </div>

                            {{-- Contoh Format --}}
                            <div class="mb-4">
                                <button class="btn btn-link p-0 text-decoration-none" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#formatExamples" aria-expanded="false">
                                    <i class="fas fa-question-circle me-2"></i>
                                    <small>Lihat contoh format nomor e-book</small>
                                </button>

                                <div class="collapse mt-2" id="formatExamples">
                                    <div class="card bg-light border-0">
                                        <div class="card-body py-3">
                                            <h6 class="text-muted mb-2">Contoh Format:</h6>
                                            <div class="format-examples">
                                                <div class="format-item">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    <code>abc123</code>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Hasil Validasi --}}
                            <div id="validationStatus" class="mb-4" style="display: none;">
                                <div class="success-card">
                                    <div class="success-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="success-content">
                                        <h6 class="success-title">Selamat E-Book Berhasil di Validasi !</h6>
                                        <p class="success-details" id="ebookDetails"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Akses (muncul saat valid) --}}
                            <div class="button-group">
                                <button type="button" id="submitBtn" class="btn-primary-custom" style="display: none;">
                                    <i class="fas fa-unlock me-2"></i>Aktifkan Akses
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer bg-light text-center py-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Validasi untuk memastikan akses legal ke e-book
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <style>
        .input-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .form-control-custom {
            width: 100%;
            padding-left: 45px;
        }

        #kode_feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        /* Mobile-First Input Container */
        .input-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
            pointer-events: none;
        }

        .form-control-custom {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 16px;
            /* Prevent zoom on iOS */
            font-weight: 500;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: #f8f9ff;
        }

        .btn-validate {
            width: 100%;
            padding: 15px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-validate:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-validate:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Format Examples */
        .format-examples {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .format-item {
            display: flex;
            align-items: center;
            padding: 8px 0;
            font-size: 14px;
        }

        .format-item code {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 13px;
        }

        /* Success Card */
        .success-card {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 2px solid #28a745;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .success-icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .success-content {
            flex-grow: 1;
        }

        .success-title {
            color: #155724;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .success-details {
            color: #155724;
            margin: 0;
            font-size: 14px;
            opacity: 0.8;
        }

        /* Button Group */
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-primary-custom {
            width: 100%;
            padding: 15px 20px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }

        .btn-secondary-custom {
            width: 100%;
            padding: 12px 20px;
            background: #f8f9fa;
            color: #6c757d;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-secondary-custom:hover {
            background: #e9ecef;
            border-color: #dee2e6;
            color: #495057;
        }

        /* Card Styling */
        .card {
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-10px);
            }

            75% {
                transform: translateX(10px);
            }
        }

        /* Tablet and Desktop Styles */
        @media (min-width: 768px) {
            .input-container {
                flex-direction: row;
                align-items: stretch;
            }

            .input-wrapper {
                flex: 1;
            }

            .btn-validate {
                width: auto;
                min-width: 120px;
                flex-shrink: 0;
            }

            .button-group {
                flex-direction: row;
            }

            .btn-primary-custom,
            .btn-secondary-custom {
                flex: 1;
            }

            .format-examples {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 15px;
            }

            .format-item {
                flex: 1;
                min-width: 200px;
            }
        }

        /* Large Desktop */
        @media (min-width: 1200px) {
            .form-control-custom {
                font-size: 18px;
                padding: 18px 18px 18px 50px;
            }

            .btn-validate {
                padding: 18px 25px;
                font-size: 18px;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .form-control-custom {
                background: #2d3748;
                border-color: #4a5568;
                color: #e2e8f0;
            }

            .form-control-custom:focus {
                background: #2d3748;
                border-color: #667eea;
            }
        }
    </style>
    <script>
        function validateEbookFormat(kode) {
            return /^[a-z]{3}\d{3}$/.test(kode);
        }

        async function validateNoBuku(kode) {
            const feedback = document.getElementById('kode_feedback');
            const validationStatus = document.getElementById('validationStatus');
            const detailsP = document.getElementById('ebookDetails');
            const submitBtn = document.getElementById('submitBtn');

            // Reset
            feedback.textContent = '';
            feedback.className = 'form-text mt-1';
            validationStatus.style.display = 'none';
            detailsP.textContent = '';
            submitBtn.style.display = 'none';

            if (!kode) {
                feedback.textContent = 'Nomor e-book tidak boleh kosong.';
                feedback.classList.add('text-danger');
                return;
            }

            if (!validateEbookFormat(kode)) {
                feedback.textContent = 'Format salah. Contoh: abc123 (3 huruf kecil + 3 angka).';
                feedback.classList.add('text-danger');
                return;
            }

            try {
                const response = await fetch("{{ route('kom.validasi.no_buku.validate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        no_books: kode
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    feedback.textContent = data.message;
                    feedback.classList.remove('text-danger');
                    feedback.classList.add('text-success');

                    validationStatus.style.display = 'block';
                    submitBtn.style.display = 'inline-flex';

                    // Optional: kalau ingin menampilkan detail buku (jika controller mengirimkannya nanti)
                    if (data.ebook) {
                        detailsP.textContent = `${data.ebook.title} - ${data.ebook.author}`;
                    }

                } else {
                    feedback.textContent = data.message || 'Terjadi kesalahan.';
                    feedback.classList.remove('text-success');
                    feedback.classList.add('text-danger');
                    validationStatus.style.display = 'none';
                    submitBtn.style.display = 'none';
                }


            } catch (error) {
                feedback.textContent = 'Gagal memvalidasi. Silakan coba lagi.';
                feedback.classList.add('text-danger');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('no_books');
            let timeout = null;

            input.addEventListener('input', function() {
                const kode = this.value.trim();
                clearTimeout(timeout);

                timeout = setTimeout(() => {
                    if (kode.length >= 3) {
                        validateNoBuku(kode);
                    }
                }, 500);
            });

            // Jika ingin tetap validasi saat tombol diklik (opsional)
            document.getElementById('submitBtn').addEventListener('click', function() {
                alert('Akses berhasil diaktifkan! (simulasi)');
            });
        });
    </script>
@endsection
