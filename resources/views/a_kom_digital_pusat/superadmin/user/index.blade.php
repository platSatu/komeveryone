@extends('layouts.KomDigital')
@section('content')
    @include('a_kom_digital_pusat.superadmin.user.components._dataHeader')
    <div class="row">
        <div class="col-12">
            <div class="card">
                @include('a_kom_digital_pusat.superadmin.user.components._header')
                <div class="card-body">
                    <!-- Form Filter dan Pencarian -->
                    @include('a_kom_digital_pusat.superadmin.user.components._searching')
                    <!-- Tabel Daftar Pengguna -->
                    <div class="table-rep-plugin">
                        <div class="table-responsive b-0" data-pattern="priority-columns">
                            @include('a_kom_digital_pusat.superadmin.user.components._dataTable')
                        </div>
                    </div>
                </div>
                <!-- Pagination -->
                @include('a_kom_digital_pusat.superadmin.user.components._pagination')
            </div>
        </div>
    </div>
    </div>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const userCheckboxes = document.querySelectorAll('.user-checkbox');
            const btnMassAction = document.getElementById('btnMassAction');

            function toggleButton() {
                const anyChecked = Array.from(userCheckboxes).some(cb => cb.checked);
                btnMassAction.disabled = !anyChecked;
            }

            selectAllCheckbox.addEventListener('change', function() {
                userCheckboxes.forEach(cb => cb.checked = this.checked);
                toggleButton();
            });

            userCheckboxes.forEach(cb => {
                cb.addEventListener('change', toggleButton);
            });

            toggleButton(); // inisialisasi tombol saat load
        });
        document.getElementById('massActionForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const action = this.querySelector('select[name="action"]').value;
            const checkedCount = Array.from(this.querySelectorAll('.user-checkbox')).filter(cb => cb.checked)
            .length;

            if (checkedCount === 0) {
                Swal.fire('Pilih minimal satu pengguna!');
                return;
            }

            Swal.fire({
                title: 'Yakin?',
                text: `Anda akan menjalankan aksi "${action}" pada ${checkedCount} pengguna.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            });
        });
    </script> --}}

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tampilkan pesan sukses jika ada
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses',
                    text: @json(session('success')),
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            // Tampilkan pesan error jika ada
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: @json(session('error')),
                    confirmButtonText: 'OK'
                });
            @endif

            // Tampilkan pesan error validasi jika ada
            @if ($errors->any())
                let errorMessages = '';
                @foreach ($errors->all() as $error)
                    errorMessages += `- {{ $error }}<br>`;
                @endforeach

                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    html: errorMessages,
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>

<!-- ============================ -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

<script>
    // Fungsi untuk memuat Select2 secara dinamis jika gagal
    function loadSelect2Dynamically() {
        return new Promise((resolve, reject) => {
            if (window.jQuery && window.jQuery.fn.select2) {
                resolve(); // Sudah loaded
                return;
            }

            // Load jQuery jika belum ada
            if (!window.jQuery) {
                const jqScript = document.createElement('script');
                jqScript.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
                jqScript.onload = function() {
                    loadSelect2();
                };
                jqScript.onerror = reject;
                document.head.appendChild(jqScript);
            } else {
                loadSelect2();
            }

            function loadSelect2() {
                const s2Script = document.createElement('script');
                s2Script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js';
                s2Script.onload = function() {
                    if (window.jQuery.fn.select2) {
                        resolve();
                    } else {
                        reject(new Error('Select2 failed to initialize'));
                    }
                };
                s2Script.onerror = reject;
                document.head.appendChild(s2Script);
            }
        });
    }

    // Tunggu hingga semua library ter-load, lalu inisialisasi Select2
    $(document).ready(function () {
        console.log("✅ jQuery version:", $.fn.jquery);
        console.log("✅ Select2 check:", $.fn.select2 ? 'Select2 OK' : 'Select2 MISSING');

        const $selectRayon = $('#select-rayon');

        // Fungsi untuk inisialisasi dengan check kuat
        async function initializeSelect2() {
            try {
                // Pastikan jQuery dan Select2 tersedia
                if (!window.jQuery) {
                    throw new Error('jQuery not loaded');
                }
                if (!window.jQuery.fn.select2) {
                    console.warn("⚠️ Select2 not available, loading dynamically...");
                    await loadSelect2Dynamically();
                }

                let retryCount = 0;
                const maxRetries = 3;

                while (retryCount < maxRetries) {
                    try {
                        console.log(`🔄 Attempting to fetch data (attempt ${retryCount + 1})`);

                        // Ambil data dari API dengan timeout
                        const controller = new AbortController();
                        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 detik timeout

                        // kom_v02: lewat server sendiri (disimpan 6 jam), bukan langsung ke gbigatsu.id dari browser.
                        // Kalau route-nya belum terdaftar di routes/web.php, halaman tetap jalan (filter Rayon saja yang kosong).
                        const asalRayonUrl = @json(\Illuminate\Support\Facades\Route::has('kom.super_user.asalRayon') ? route('kom.super_user.asalRayon') : null);
                        if (!asalRayonUrl) {
                            $selectRayon.html('<option value="">Daftar rayon belum tersedia</option>');
                            return;
                        }
                        const response = await fetch(asalRayonUrl, {
                            signal: controller.signal,
                            headers: { 'Accept': 'application/json' }
                        });

                        clearTimeout(timeoutId);

                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }

                        const data = await response.json();
                        console.log("✅ Data asal-rayon berhasil diambil:", data);

                        // Deteksi struktur data API
                        let items = [];
                        if (data.results) items = data.results;
                        else if (data.data) items = data.data;
                        else if (Array.isArray(data)) items = data;
                        else items = [];

                        // Hapus isi awal
                        $selectRayon.empty().append('<option value="">-- Pilih CK / Rayon --</option>');

                        // Isi dropdown
                        items.forEach(item => {
                            const id = item.id || item.value || item.kode || item.nama;
                            const text = item.nama || item.name || item.text || 'Tanpa Nama';
                            $selectRayon.append(new Option(text, id));
                        });

                        // Pilihan filter yang sedang dipakai tetap terpilih setelah daftar dimuat.
                        const terpilih = @json(request('asal_gereja'));
                        if (terpilih) {
                            if (!$selectRayon.find('option').filter(function () { return this.value == terpilih; }).length) {
                                $selectRayon.append(new Option(@json(request('nama_rayon') ?? request('asal_gereja')), terpilih));
                            }
                            $selectRayon.val(terpilih);
                        }

                        // Aktifkan Select2
                        $selectRayon.select2({
                            placeholder: "-- Pilih CK / Rayon --",
                            allowClear: true,
                            width: '100%'
                        });

                        console.log("✅ Select2 berhasil diinisialisasi");
                        return; // Sukses, keluar dari loop

                    } catch (err) {
                        retryCount++;
                        console.error(`❌ Attempt ${retryCount} failed:`, err.message);

                        if (retryCount >= maxRetries) {
                            // kom_v02: tanpa popup. Filter Rayon saja yang belum tersedia; halaman & data user tetap bisa dipakai.
                            $selectRayon.html('<option value="">Daftar rayon belum tersedia</option>');
                        } else {
                            // Tunggu sebelum retry
                            await new Promise(resolve => setTimeout(resolve, 2000)); // Tunggu 2 detik
                        }
                    }
                }
            } catch (initErr) {
                console.error("❌ Initialization error:", initErr);
                $selectRayon.html('<option value="">Error loading Select2</option>');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat Select2. Refresh halaman atau hubungi admin.',
                    confirmButtonText: 'OK'
                });
            }
        }

        // Jalankan inisialisasi
        initializeSelect2();
    });
</script>

@endsection

