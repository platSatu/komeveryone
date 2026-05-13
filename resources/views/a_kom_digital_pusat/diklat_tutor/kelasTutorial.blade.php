@extends('layouts.KomDigital')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm rounded-3">
                <div class="card-body p-4">
                    {{-- <h3 class="mb-4 text-left fw-bold">Khusus untuk Diklat Tutor KOM EVERYONE</h3> --}}
                     <div class="alert alert-warning text-center">
                            <h5><i class="mdi mdi-alert-circle-outline"></i> KHUSUS UNTUK DIKLAT TUTOR KOM EVERYONE</h5>
                            <p class="">
                               Anda hanya dapat memilih Doktrinal atau Practical sesuai dengan bidang Anda.
                            </p>
                        </div>
                    {{-- Filter Form --}}
                    <form id="filterForm" class="row g-3 mb-4" method="GET">
                        <div class="col-md-5">
                            {{-- <label for="category_diklat" class="form-label fw-semibold">Anda hanya dapat memilih Doktrinal atau Practical sesuai dengan bidang Anda.</label> --}}
                            <select name="category_diklat" id="category_diklat" class="form-select form-control">
                                <option value="">Semua Kategori</option>
                                <option value="Doktrinal" {{ request('category_diklat') == 'Doktrinal' ? 'selected' : '' }}>
                                    Doktrinal</option>
                                <option value="Praktikal" {{ request('category_diklat') == 'Praktikal' ? 'selected' : '' }}>
                                    Praktikal</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 fw-semibold" id="filterBtn">Filter</button>
                        </div>
                    </form>


                    {{-- Tabs --}}
                    <ul class="nav nav-tabs mb-4" id="viewTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="card-tab" data-bs-toggle="tab" data-bs-target="#cardView"
                                type="button" role="tab" aria-controls="cardView" aria-selected="true"
                                title="Card View">
                                <i class="fas fa-th-large me-1"></i>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="table-tab" data-bs-toggle="tab" data-bs-target="#tableView"
                                type="button" role="tab" aria-controls="tableView" aria-selected="false"
                                title="Table View">
                                <i class="fas fa-table me-1"></i>
                            </button>
                        </li>
                    </ul>

                    {{-- Alert Messages --}}
                    <div id="alertContainer">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                    </div>

                    {{-- Tab Contents --}}
                    <div class="tab-content" id="viewTabContent">
                        {{-- Card View --}}
                        <div class="tab-pane fade show active" id="cardView" role="tabpanel" aria-labelledby="card-tab">
                            <div id="kelasListCard" class="row g-4">
                                @include('a_kom_digital_pusat.diklat_tutor.partials.kelas_list_card', [
                                    'kelasTutorials' => $kelasTutorials,
                                ])
                            </div>
                        </div>

                        {{-- Table View --}}
                        <div class="tab-pane fade" id="tableView" role="tabpanel" aria-labelledby="table-tab">
                            <div id="kelasListTable" class="table-responsive">
                                @include('a_kom_digital_pusat.diklat_tutor.partials.kelas_list_table', [
                                    'kelasTutorials' => $kelasTutorials,
                                ])
                            </div>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    <nav id="paginationNav" aria-label="Page navigation" class="mt-4 d-flex justify-content-center">
                        <!-- {{ $kelasTutorials->withQueryString()->links() }} -->
                        {{ $kelasTutorials->withQueryString()->links('pagination::bootstrap-4') }}
                    </nav>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="container py-4">
        
    </div> --}}

    {{-- Font Awesome CDN --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            const kelasListUrl = "{{ route('kom.diklat_tutor.index') }}";

            // Fungsi untuk load data via AJAX
            function loadData(url, params = {}) {
                filterBtn.disabled = true;
                filterBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat...`;

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
                            title: 'Oops...',
                            text: 'Gagal memuat data kelas tutorial. Silakan coba lagi.',
                        });
                        console.error(error);
                    })
                    .finally(() => {
                        filterBtn.disabled = false;
                        filterBtn.innerHTML = 'Filter';
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

            // Switch tab reload data agar sesuai tampilan
            viewTab.addEventListener('shown.bs.tab', function(e) {
                const activeTabId = e.target.id;
                const params = {
                    rayon_id: filterForm.rayon_id.value,
                    modul_id: filterForm.modul_id.value,
                    view: activeTabId === 'card-tab' ? 'card' : 'table',
                };
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
                    title: 'Yakin Anda memilih kelas ini?',
                    text: kelasDesc,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, ikut kelas',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...`;

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
                                        title: 'Berhasil!',
                                        text: data.message ||
                                            'Anda berhasil mendaftar kelas tutorial.',
                                        confirmButtonText: 'Lanjut ke Pertanyaan',
                                        allowOutsideClick: false,
                                    }).then(() => {
                                        window.location.href = data.redirect_url ||
                                            "{{ route('kom.diklat_tutor.pertanyaan.form') }}";
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Ups!',
                                        text: data.message ||
                                            'Terjadi kesalahan saat mendaftar kelas.',
                                    });
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = 'Ikut Kelas';
                                }
                            })
                            .catch(() => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops!',
                                    text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
                                });
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = 'Ikut Kelas';
                            });
                    }
                });
            }

            attachJoinHandlers();
        });
    </script>
@endsection
