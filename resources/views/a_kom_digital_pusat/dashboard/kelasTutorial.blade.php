@extends('layouts.KomDigital')
@section('content')
{{-- kom_v02: tampilan modern & minimalis, seragam dengan halaman pertanyaan & persembahan --}}
@include('a_kom_digital_pusat.partials._kom_ui')
<div class="kom-kt-wrap">
  <div class="kom-ui-card">
    @include('a_kom_digital_pusat.partials._langkah_tutorial', ['aktif' => 1])

    <div class="kom-ui-head">
      <div class="kom-ui-icon"><i class="fas fa-chalkboard-teacher"></i></div>
      <h3>{{ __('Daftar Kelas Tutorial') }}</h3>
      <p>{{ __('Pilih jadwal yang paling cocok untuk Anda.') }}</p>
    </div>

    {{-- kom_v02: info titik tutorial yang sedang dipilih dari halaman belajar --}}
    @if (!empty($titikInfo))
        @php
            $statusTitik = $titikInfo['status'];
            $warna = [
                'belum' => 'info', 'proses' => 'warning', 'terdaftar' => 'primary',
                'tidak_hadir' => 'warning', 'hadir' => 'success',
            ][$statusTitik] ?? 'info';
        @endphp
        <div class="alert kom-ui-alert alert-{{ $warna }} d-flex align-items-start">
            <div class="kom-kt-info-icon"><i class="fas fa-flag-checkered"></i></div>
            <div>
                <div class="font-weight-bold mb-1">
                    {{ __('Kelas Tutorial') }}: {{ optional($titikInfo['modul'])->name }} — {{ $titikInfo['label'] }}
                </div>
                @if ($statusTitik === 'hadir')
                    {{ __('Anda sudah hadir di kelas tutorial ini. Terima kasih!') }}
                @elseif ($statusTitik === 'terdaftar')
                    {{ __('Anda sudah terdaftar. Silakan ikuti kelasnya sesuai jadwal di menu Kelas Tutorial Saya.') }}
                @elseif ($statusTitik === 'proses')
                    {{ __('Anda sudah memilih jadwal. Silakan selesaikan pertanyaan dan persembahan.') }}
                @elseif ($statusTitik === 'tidak_hadir')
                    {{ __('Anda tercatat tidak hadir. Silakan pilih jadwal pengganti — tidak perlu memberikan persembahan lagi.') }}
                @else
                    {{ __('Pilih salah satu jadwal di bawah ini. Setelah memilih, Anda akan mengirim 2 pertanyaan lalu memberikan persembahan.') }}
                @endif
            </div>
        </div>
    @endif

    {{-- Filter Form --}}
    <form id="filterForm" class="kom-kt-filter">
        <div class="kom-kt-field">
            <label for="rayon_id" class="kom-ui-label">{{ __('Filter Rayon') }}</label>
            <select name="rayon_id" id="rayon_id" class="form-control kom-ui-input">
                <option value="">{{ __('Semua Rayon') }}</option>
                @foreach ($rayons as $rayon)
                    <option value="{{ $rayon->id }}" {{ request('rayon_id') == $rayon->id ? 'selected' : '' }}>
                        {{ $rayon->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="kom-kt-field">
            <label for="modul_id" class="kom-ui-label">{{ __('Filter Modul') }}</label>
            <select name="modul_id" id="modul_id" class="form-control kom-ui-input">
                <option value="">{{ __('Semua Modul') }}</option>
                @foreach ($moduls as $modul)
                    <option value="{{ $modul->id }}" {{ request('modul_id') == $modul->id ? 'selected' : '' }}>
                        {{ $modul->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- kom_v02: pertahankan titik tutorial saat filter --}}
        @if (request()->filled('titik'))
            <input type="hidden" name="titik" value="{{ request('titik') }}">
        @endif

        <div class="kom-kt-field tombol">
            <button type="submit" class="kom-btn-primary w-100" id="filterBtn">{{ __('Filter') }}</button>
        </div>
    </form>

    {{-- Pilihan tampilan: kartu / tabel --}}
    <div class="kom-kt-toolbar">
        <div class="kom-kt-toggle" id="viewTab" role="tablist">
            <button class="nav-link active" id="card-tab" data-target="#cardView" type="button" role="tab"
                aria-controls="cardView" aria-selected="true" title="{{ __('Tampilan Kartu') }}">
                <i class="fas fa-th-large"></i>
            </button>
            <button class="nav-link" id="table-tab" data-target="#tableView" type="button" role="tab"
                aria-controls="tableView" aria-selected="false" title="{{ __('Tampilan Tabel') }}">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>

    {{-- Alert Messages --}}
    <div id="alertContainer">
        @if (session('success'))
            <div class="alert kom-ui-alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert kom-ui-alert alert-danger">{{ session('error') }}</div>
        @endif
    </div>

    {{-- Tab Contents --}}
    <div class="tab-content" id="viewTabContent">
        {{-- Card View --}}
        <div class="tab-pane show active" id="cardView" role="tabpanel" aria-labelledby="card-tab">
            <div id="kelasListCard" class="row">
                @include('a_kom_digital_pusat.dashboard.partials.kelas_list_card', [
                    'kelasTutorials' => $kelasTutorials,
                ])
            </div>
        </div>

        {{-- Table View --}}
        <div class="tab-pane" id="tableView" role="tabpanel" aria-labelledby="table-tab">
            <div id="kelasListTable" class="table-responsive">
                @include('a_kom_digital_pusat.dashboard.partials.kelas_list_table', [
                    'kelasTutorials' => $kelasTutorials,
                ])
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <nav id="paginationNav" aria-label="Page navigation" class="mt-4 d-flex justify-content-center">
        {{ $kelasTutorials->withQueryString()->links() }}
    </nav>
  </div>
</div>
<style>
  .kom-kt-wrap { padding: 16px 0 40px; }
  .kom-kt-info-icon {
    width:36px; height:36px; flex:0 0 36px; border-radius:12px; margin-right:14px;
    display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,.7);
  }
  .kom-kt-filter {
    display:flex; flex-wrap:wrap; gap:16px; align-items:flex-end;
    background:#f7f9fc; border:1px solid #eef2f8; border-radius:18px; padding:18px; margin-bottom:20px;
  }
  .kom-kt-filter .kom-ui-input { background:#fff; }
  .kom-kt-field { flex:1 1 220px; min-width:0; }
  .kom-kt-field.tombol { flex:0 0 160px; }
  .kom-kt-toolbar { display:flex; justify-content:flex-end; margin-bottom:16px; }
  .kom-kt-toggle { display:inline-flex; background:#eef2f8; border-radius:12px; padding:4px; gap:4px; }
  .kom-kt-toggle .nav-link {
    border:0; background:transparent; color:#8a96a8; border-radius:9px; padding:7px 14px; line-height:1;
    transition: background .15s, color .15s, box-shadow .15s;
  }
  .kom-kt-toggle .nav-link.active { background:#fff; color:#2f6fed; box-shadow:0 2px 8px -2px rgba(30,60,120,.25); }
  #viewTabContent > .tab-pane { display:none; }
  #viewTabContent > .tab-pane.active { display:block; }
  @media (max-width: 576px) {
    .kom-kt-field.tombol { flex:1 1 100%; }
  }
</style>
    {{-- <div class="container py-4">
        
    </div> --}}

    {{-- Font Awesome CDN --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- kom_v02: teks JavaScript mengikuti bahasa peserta --}}
@php
    $komTeksJs = [
        'Memuat...' => __('Memuat...'),
        'Oops...' => __('Oops...'),
        'Gagal memuat data kelas tutorial. Silakan coba lagi.' => __('Gagal memuat data kelas tutorial. Silakan coba lagi.'),
        'Filter' => __('Filter'),
        'Yakin Anda memilih kelas ini?' => __('Yakin Anda memilih kelas ini?'),
        'Ya, ikut kelas' => __('Ya, ikut kelas'),
        'Batal' => __('Batal'),
        'Memproses...' => __('Memproses...'),
        'Berhasil!' => __('Berhasil!'),
        'Anda berhasil mendaftar kelas tutorial.' => __('Anda berhasil mendaftar kelas tutorial.'),
        'Ups!' => __('Ups!'),
        'Terjadi kesalahan saat mendaftar kelas.' => __('Terjadi kesalahan saat mendaftar kelas.'),
        'Lanjutkan' => __('Lanjutkan'),
        'OK' => __('OK'),
        'Lihat Kelas Tutorial Saya' => __('Lihat Kelas Tutorial Saya'),
        'Lanjut ke Pertanyaan' => __('Lanjut ke Pertanyaan'),
        'Ikut Kelas' => __('Ikut Kelas'),
        'Oops!' => __('Oops!'),
        'Terjadi kesalahan jaringan. Silakan coba lagi.' => __('Terjadi kesalahan jaringan. Silakan coba lagi.'),
    ];
@endphp
@include('a_kom_digital_pusat.partials._teks_js', ['teks' => $komTeksJs])
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filterForm');
            const kelasListCard = document.getElementById('kelasListCard');
            const kelasListTable = document.getElementById('kelasListTable');
            const paginationNav = document.getElementById('paginationNav');
            const alertContainer = document.getElementById('alertContainer');
            const filterBtn = document.getElementById('filterBtn');
            const viewTab = document.getElementById('viewTab');

            // URL endpoint untuk filter dan pagination (ganti sesuai route Anda)
            const kelasListUrl = "{{ route('kom.kelas_tutorial.index') }}";

            // Fungsi untuk load data via AJAX
            function loadData(url, params = {}) {
                filterBtn.disabled = true;
                filterBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${komT('Memuat...')}`;

                fetch(url + '?' + new URLSearchParams(params), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal memuat data');
                        return response.text();
                    })
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        // Update card & table lists
                        const newCardList = doc.getElementById('kelasListCard');
                        const newTableList = doc.getElementById('kelasListTable');
                        const newPagination = doc.getElementById('paginationNav');
                        const newAlerts = doc.getElementById('alertContainer');

                        if (newCardList) kelasListCard.innerHTML = newCardList.innerHTML;
                        if (newTableList) kelasListTable.innerHTML = newTableList.innerHTML;
                        if (newPagination) paginationNav.innerHTML = newPagination.innerHTML;
                        if (newAlerts) alertContainer.innerHTML = newAlerts.innerHTML;

                        attachJoinHandlers();

                        // Scroll ke atas daftar kelas
                        kelasListCard.scrollIntoView({
                            behavior: 'smooth'
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: komT('Oops...'),
                            text: komT('Gagal memuat data kelas tutorial. Silakan coba lagi.'),
                        });
                        console.error(error);
                    })
                    .finally(() => {
                        filterBtn.disabled = false;
                        filterBtn.innerHTML = komT('Filter');
                    });
            }

            // Submit filter form via AJAX
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(filterForm);
                const params = Object.fromEntries(formData.entries());

                // Sertakan tab aktif agar data sesuai tampilan
                const activeTab = document.querySelector('#viewTab .nav-link.active');
                if (activeTab) {
                    params.view = activeTab.id === 'card-tab' ? 'card' : 'table';
                }

                loadData(kelasListUrl, params);
            });

            // Pagination AJAX
            paginationNav.addEventListener('click', function(e) {
                if (e.target.tagName === 'A') {
                    e.preventDefault();
                    const url = e.target.href;

                    // Sertakan tab aktif agar data sesuai tampilan
                    const activeTab = document.querySelector('#viewTab .nav-link.active');
                    const params = {};
                    if (activeTab) {
                        params.view = activeTab.id === 'card-tab' ? 'card' : 'table';
                    }

                    loadData(url, params);
                }
            });

            // kom_v02: ganti tampilan kartu / tabel (tanpa bergantung versi Bootstrap)
            viewTab.querySelectorAll('.nav-link').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (btn.classList.contains('active')) return;
                    viewTab.querySelectorAll('.nav-link').forEach(b => {
                        b.classList.toggle('active', b === btn);
                        b.setAttribute('aria-selected', b === btn ? 'true' : 'false');
                    });
                    document.querySelectorAll('#viewTabContent > .tab-pane').forEach(p => {
                        const aktif = '#' + p.id === btn.dataset.target;
                        p.classList.toggle('active', aktif);
                        p.classList.toggle('show', aktif);
                    });
                    viewTab.dispatchEvent(new CustomEvent('kom:ganti-tampilan', { detail: { id: btn.id } }));
                });
            });

            // Switch tab reload data agar sesuai tampilan
            viewTab.addEventListener('kom:ganti-tampilan', function(e) {
                const activeTabId = e.detail.id;
                const params = {
                    rayon_id: filterForm.rayon_id.value,
                    modul_id: filterForm.modul_id.value,
                    view: activeTabId === 'card-tab' ? 'card' : 'table',
                };
                if (filterForm.titik) params.titik = filterForm.titik.value; // kom_v02
                loadData(kelasListUrl, params);
            });

            // Attach SweetAlert confirmation ke form join kelas
            function attachJoinHandlers() {
                document.querySelectorAll('form.joinClassForm').forEach(form => {
                    form.removeEventListener('submit', handleJoinSubmit);
                    form.addEventListener('submit', handleJoinSubmit);
                });
            }

            function handleJoinSubmit(e) {
                e.preventDefault();
                const form = e.target;
                const kelasDescElem = form.closest('.card-body')?.querySelector('.card-title') ||
                    form.closest('tr')?.querySelector('td:nth-child(3)');
                const kelasDesc = kelasDescElem ? kelasDescElem.textContent.trim() : '';
                const submitBtn = form.querySelector('button[type="submit"]');

                Swal.fire({
                    title: komT('Yakin Anda memilih kelas ini?'),
                    text: kelasDesc,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: komT('Ya, ikut kelas'),
                    cancelButtonText: komT('Batal'),
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${komT('Memproses...')}`;

                        fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: new URLSearchParams(new FormData(form)).toString(),
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: komT('Berhasil!'),
                                        text: data.message ||
                                            komT('Anda berhasil mendaftar kelas tutorial.'),
                                        // kom_v02: jadwal pengganti tidak perlu pertanyaan & persembahan lagi
                                        confirmButtonText: data.tanpa_pertanyaan ? komT('Lihat Kelas Tutorial Saya') : komT('Lanjut ke Pertanyaan'),
                                        allowOutsideClick: false,
                                    }).then(() => {
                                        window.location.href = data.redirect_url ||
                                            "{{ route('kom.kelas_tutorial.pertanyaan.form') }}";
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: komT('Ups!'),
                                        text: data.message ||
                                            komT('Terjadi kesalahan saat mendaftar kelas.'),
                                        // kom_v02: arahkan ke langkah yang belum selesai
                                        confirmButtonText: data.redirect_url ? komT('Lanjutkan') : komT('OK'),
                                    }).then(() => {
                                        if (data.redirect_url) window.location.href = data.redirect_url;
                                    });
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = komT('Ikut Kelas');
                                }
                            })
                            .catch(() => {
                                Swal.fire({
                                    icon: 'error',
                                    title: komT('Oops!'),
                                    text: komT('Terjadi kesalahan jaringan. Silakan coba lagi.'),
                                });
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = komT('Ikut Kelas');
                            });
                    }
                });
            }

            attachJoinHandlers();
        });
    </script>
@endsection
