@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-12">
            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-star me-2"></i>&nbsp;&nbsp;
                        <h4 class="mb-0 pb-2">Review Pengajar</h4>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Info Kelas --}}
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-title text-success mb-3">
                                        <i class="fas fa-info-circle me-2"></i>&nbsp;&nbsp;Informasi Kelas
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Pengajar:</strong><br>
                                            <span class="text-muted">{{ $classTutorial->pengajar->name ?? '-' }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Kelas:</strong><br>
                                            <span class="text-muted">{{ $classTutorial->kelas->name ?? '-' }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Modul:</strong><br>
                                            <span class="text-muted">{{ $classTutorial->modul->name ?? '-' }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Waktu:</strong><br>
                                            <span class="text-muted">{{ $classTutorial->date_class }} {{ $classTutorial->time_class }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Review --}}
                    <form action="{{ route('kom.kelas_tutor_cabang.review.store', Crypt::encryptString($classTutorial->id)) }}" method="POST">
                        @csrf
                        
                        @foreach($userClassTutorials as $index => $userClass)
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user me-2"></i>{{ $index + 1 }}. {{ $userClass->user->name ?? '-' }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <input type="hidden" name="reviews[{{ $userClass->id }}][userclasstutorial_id]" value="{{ $userClass->id }}">
                                    <input type="hidden" name="reviews[{{ $userClass->id }}][user_id]" value="{{ $userClass->user_id }}">
                                    <input type="hidden" name="reviews[{{ $userClass->id }}][pengajar_id]" value="{{ $classTutorial->pengajar_id }}">
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-book me-1"></i>Isi Pengajaran
                                            </label>
                                            <select name="reviews[{{ $userClass->id }}][isi_pengajaran]" class="form-select" required>
                                                <option value="">Pilih Penilaian</option>
                                                <option value="sangat_baik">⭐⭐⭐⭐⭐ Sangat Baik</option>
                                                <option value="baik">⭐⭐⭐⭐ Baik</option>
                                                <option value="cukup">⭐⭐⭐ Cukup</option>
                                                <option value="kurang">⭐⭐ Kurang</option>
                                                <option value="sangat_kurang">⭐ Sangat Kurang</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-cross me-1"></i>Kebenaran Teologis
                                            </label>
                                            <select name="reviews[{{ $userClass->id }}][kebenaran_teologis]" class="form-select" required>
                                                <option value="">Pilih Penilaian</option>
                                                <option value="sangat_baik">⭐⭐⭐⭐⭐ Sangat Baik</option>
                                                <option value="baik">⭐⭐⭐⭐ Baik</option>
                                                <option value="cukup">⭐⭐⭐ Cukup</option>
                                                <option value="kurang">⭐⭐ Kurang</option>
                                                <option value="sangat_kurang">⭐ Sangat Kurang</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-check-circle me-1"></i>Pengajar Sampaikan Seluruh Materi
                                            </label>
                                            <select name="reviews[{{ $userClass->id }}][pengajar_sampaikan_seluruh_materi]" class="form-select" required>
                                                <option value="">Pilih</option>
                                                <option value="ya">✅ Ya</option>
                                                <option value="tidak">❌ Tidak</option>
                                                <option value="sebagian">⚠️ Sebagian</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-bullseye me-1"></i>Pengajar Sampaikan Materi Utama
                                            </label>
                                            <select name="reviews[{{ $userClass->id }}][pengajar_sampaikan_materi_utama]" class="form-select" required>
                                                <option value="">Pilih</option>
                                                <option value="ya">✅ Ya</option>
                                                <option value="tidak">❌ Tidak</option>
                                                <option value="sebagian">⚠️ Sebagian</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-play-circle me-1"></i>Pembukaan Materi
                                            </label>
                                            <select name="reviews[{{ $userClass->id }}][pembukaan_materi]" class="form-select" required>
                                                <option value="">Pilih Penilaian</option>
                                                <option value="sangat_baik">⭐⭐⭐⭐⭐ Sangat Baik</option>
                                                <option value="baik">⭐⭐⭐⭐ Baik</option>
                                                <option value="cukup">⭐⭐⭐ Cukup</option>
                                                <option value="kurang">⭐⭐ Kurang</option>
                                                <option value="sangat_kurang">⭐ Sangat Kurang</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-link me-1"></i>Kesinambungan Materi
                                            </label>
                                            <select name="reviews[{{ $userClass->id }}][kesinambungan_materi]" class="form-select" required>
                                                <option value="">Pilih Penilaian</option>
                                                <option value="sangat_baik">⭐⭐⭐⭐⭐ Sangat Baik</option>
                                                <option value="baik">⭐⭐⭐⭐ Baik</option>
                                                <option value="cukup">⭐⭐⭐ Cukup</option>
                                                <option value="kurang">⭐⭐ Kurang</option>
                                                <option value="sangat_kurang">⭐ Sangat Kurang</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-heart me-1"></i>Kesaksian Pribadi
                                            </label>
                                            <select name="reviews[{{ $userClass->id }}][kesaksian_pribadi]" class="form-select" required>
                                                <option value="">Pilih Penilaian</option>
                                                <option value="sangat_baik">⭐⭐⭐⭐⭐ Sangat Baik</option>
                                                <option value="baik">⭐⭐⭐⭐ Baik</option>
                                                <option value="cukup">⭐⭐⭐ Cukup</option>
                                                <option value="kurang">⭐⭐ Kurang</option>
                                                <option value="sangat_kurang">⭐ Sangat Kurang</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-brain me-1"></i>Materi Dipahami
                                            </label>
                                            <select name="reviews[{{ $userClass->id }}][materi_dipahami]" class="form-select" required>
                                                <option value="">Pilih</option>
                                                <option value="sangat_mudah">😊 Sangat Mudah Dipahami</option>
                                                <option value="mudah">🙂 Mudah Dipahami</option>
                                                <option value="cukup">😐 Cukup Dipahami</option>
                                                <option value="sulit">😕 Sulit Dipahami</option>
                                                <option value="sangat_sulit">😞 Sangat Sulit Dipahami</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-clipboard-check me-1"></i>Sesuai Dengan Materi
                                            </label>
                                            <select name="reviews[{{ $userClass->id }}][sesuai_dengan_materi]" class="form-select" required>
                                                <option value="">Pilih</option>
                                                <option value="sangat_sesuai">✅ Sangat Sesuai</option>
                                                <option value="sesuai">☑️ Sesuai</option>
                                                <option value="cukup_sesuai">⚠️ Cukup Sesuai</option>
                                                <option value="kurang_sesuai">❌ Kurang Sesuai</option>
                                                <option value="tidak_sesuai">🚫 Tidak Sesuai</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($userClassTutorials->isEmpty())
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <h5>Belum Ada Peserta</h5>
                                        <p>Belum ada peserta yang terdaftar dalam kelas ini</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('kom.kelas_tutor_cabang.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>&nbsp;&nbsp;Kembali
                            </a>
                            @if($userClassTutorials->isNotEmpty())
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i>&nbsp;&nbsp;Simpan Review
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .form-label {
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .card {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
    }
    .card-header {
        border-bottom: 1px solid #e3e6f0;
    }
    .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
</style>
@endpush