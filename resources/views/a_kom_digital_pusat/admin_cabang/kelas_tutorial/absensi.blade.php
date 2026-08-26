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
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i>&nbsp;&nbsp;
                        <h5 class="mb-0 pb-2"> Absensi Kelas Tutorial</h5>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Info Kelas --}}
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="fas fa-info-circle me-2"></i> Informasi Kelas
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
                                            <span class="text-muted">{{ $classTutorial->start_time }}

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Absensi --}}
                    <form
                        action="{{ route('kom.kelas_tutor_cabang.absensi.store', Crypt::encryptString($classTutorial->id)) }}"
                        method="POST">
                        @csrf

                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-users me-2"></i>&nbsp;&nbsp; Daftar Peserta
                                    ({{ $userClassTutorials->count() }}
                                    orang)
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="30%">Nama Peserta</th>
                                                <th width="35%">Status Kehadiran</th>
                                                <th width="30%">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($userClassTutorials as $index => $userClass)
                                                @php
                                                    // Data absensi yang sudah pernah diinput admin cabang sebelumnya
                                                    // (null kalau peserta ini belum pernah diabsen sama sekali)
                                                    $existingAbsensi = $userClass->absensi;
                                                    $currentStatus = $existingAbsensi->status ?? 'normal';
                                                    // Begitu peserta pernah ditandai HADIR, terkunci permanen --
                                                    // tidak bisa diubah lagi lewat form ini (lihat KelasService::storeAbsensi)
                                                    $isLocked = (bool) optional($existingAbsensi)->hadir;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2 mr-3">
                                                                <i class="fas fa-user text-white"></i>
                                                            </div>
                                                            <strong>{{ $userClass->user->name ?? '-' }}</strong>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="hidden"
                                                            name="absensi[{{ $userClass->id }}][userclasstutorial_id]"
                                                            value="{{ $userClass->id }}">
                                                        <input type="hidden" name="absensi[{{ $userClass->id }}][user_id]"
                                                            value="{{ $userClass->user_id }}">

                                                        @if ($isLocked)
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-lock me-1"></i>Hadir (Terkunci)
                                                            </span>
                                                            {{-- Tetap kirim value lama supaya validasi request tetap lolos;
                                                                 backend akan mengabaikan perubahan untuk peserta ini --}}
                                                            <input type="hidden"
                                                                name="absensi[{{ $userClass->id }}][kehadiran]"
                                                                value="hadir">
                                                            <input type="hidden"
                                                                name="absensi[{{ $userClass->id }}][status]"
                                                                value="{{ $currentStatus }}">
                                                        @else
                                                            <div class="btn-group" role="group">
                                                                <input type="radio" class="btn-check"
                                                                    name="absensi[{{ $userClass->id }}][kehadiran]"
                                                                    id="hadir_{{ $userClass->id }}" value="hadir" required
                                                                    {{ optional($existingAbsensi)->hadir ? 'checked' : '' }}>
                                                                <label class="btn btn-outline-success"
                                                                    for="hadir_{{ $userClass->id }}">
                                                                    <i class="fas fa-check me-1"></i>Hadir
                                                                </label>
                                                                &nbsp;&nbsp;&nbsp;
                                                                <input type="radio" class="btn-check"
                                                                    name="absensi[{{ $userClass->id }}][kehadiran]"
                                                                    id="tidak_hadir_{{ $userClass->id }}"
                                                                    value="tidak_hadir"
                                                                    {{ optional($existingAbsensi)->tidak_hadir ? 'checked' : '' }}>
                                                                <label class="btn btn-outline-danger"
                                                                    for="tidak_hadir_{{ $userClass->id }}">
                                                                    <i class="fas fa-times me-1"></i>Tidak Hadir
                                                                </label>

                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($isLocked)
                                                            <span class="text-muted">{{ ucfirst($currentStatus) }}</span>
                                                        @else
                                                            <select name="absensi[{{ $userClass->id }}][status]"
                                                                class="form-select form-select-sm form-control">
                                                                <option value="normal" @selected($currentStatus === 'normal')>Normal</option>
                                                                <option value="sakit" @selected($currentStatus === 'sakit')>Sakit</option>
                                                                <option value="izin" @selected($currentStatus === 'izin')>Izin</option>
                                                                <option value="alpha" @selected($currentStatus === 'alpha')>Alpha</option>
                                                            </select>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-users fa-2x mb-2"></i>
                                                            <p>Belum ada peserta yang terdaftar</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('kom.kelas_tutor_cabang.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>&nbsp;&nbsp; Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>&nbsp;&nbsp; Simpan Absensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        .avatar-sm {
            width: 32px;
            height: 32px;
        }

        .btn-check:checked+.btn-outline-success {
            background-color: #198754;
            border-color: #198754;
            color: white;
        }

        .btn-check:checked+.btn-outline-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }
    </style>
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

