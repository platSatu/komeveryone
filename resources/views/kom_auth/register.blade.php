<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>REGISTER | KOM EVERYONE</title>
    <meta content="KOM" name="description" />
    <meta content="Mannatthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="{{ asset('be') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('be') }}/assets/css/icons.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('be') }}/assets/css/style.css" rel="stylesheet" type="text/css">

    <!-- Select2 CSS -->
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />




</head>

<body class="fixed-left">

    <!-- Begin page -->

    <div class="wrapper-page">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">


            <div class="card-body">

                <div class="text-center">
                    <a href="" class="logo logo-admin"><img src="{{ asset('home') }}/gatsu_text_black.png"
                            height="50" alt="Kom"></a>
                </div>

                <div class="p-3">
                    <form method="POST" action="{{ route('kom_register') }}" class="form-horizontal">
                        @csrf

                        <!-- Name -->
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" id="name" type="text" name="name"
                                    :value="old('name')" required autofocus placeholder="Name">
                                <small class="form-text text-muted">Nama lengkap sesuai dengan KTP</small>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" id="email" type="email" name="email"
                                    :value="old('email')" required placeholder="Email">
                                <small class="form-text text-muted">Pastikan Email Anda Aktif</small>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Pertanyaan gereja -->
                        <div class="form-group row">
                            <div class="col-12">
                                <label class="form-label">Apakah Anda berjemaat di gereja kami?</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="dariGerejaKami"
                                        name="dari_gereja_kami" onchange="toggleGerejaOptions(true)">
                                    <label class="form-check-label" for="dariGerejaKami">Ya</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="bukanDariGerejaKami"
                                        name="bukan_dari_gereja_kami" onchange="toggleGerejaOptions(false)">
                                    <label class="form-check-label" for="bukanDariGerejaKami">Tidak</label>
                                </div>
                            </div>
                        </div>

                        <!-- Pilih Rayon (jika Ya) -->
                        <div class="form-group row" id="select-rayon-group" style="display: none;">
                            <div class="col-12">
                                <label for="rayon_id" class="form-label">⁠Pilih Nama Gereja Anda</label>
                                <select class="form-control" id="select-rayon" style="width: 100%;">
                                    <!-- data ajax select2 -->
                                </select>
                                <x-input-error :messages="$errors->get('asal_gereja')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Input manual Rayon jika pilih "Other" -->
                        <div class="form-group row" id="other-rayon-input" style="display: none;">
                            <div class="col-12">
                                <label class="form-label">Masukkan Nama Rayon</label>
                                <input type="text" class="form-control" id="rayon-text-input"
                                    placeholder="Contoh: Rayon 99">
                                <x-input-error :messages="$errors->get('asal_gereja')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Input manual jika Tidak berjemaat di gereja kami -->
                        <div class="form-group row" id="not-from-church-group" style="display: none;">
                            <div class="col-12">
                                <label class="form-label">Silakan isi nama gereja Anda</label>
                                <input type="text" class="form-control" id="manual-gereja-input"
                                    placeholder="Contoh: Gereja ABC">
                                <x-input-error :messages="$errors->get('asal_gereja')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Hidden input yang akan dikirim -->
                        <input type="hidden" name="asal_gereja" id="asal_gereja" value="">

                        <!-- Tahun lahir dan input lainnya... (saya tidak ubah) -->
                        @php
                            $currentYear = date('Y');
                            $minYear = $currentYear - 12;
                        @endphp

                        <div class="form-group row">
                            <div class="col-12">
                                <label for="thn_lahir" class="form-label">Tahun Lahir</label>
                                <select class="form-control" id="thn_lahir" name="tahun_lahir" required
                                    onchange="validateAge()">
                                    <option value="" disabled selected>-- Pilih Tahun --</option>
                                    @for ($year = $currentYear; $year >= 1945; $year--)
                                        <option value="{{ $year }}"
                                            {{ old('thn_lahir') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                <x-input-error :messages="$errors->get('thn_lahir')" class="mt-2" />
                                <small id="umur-error" class="text-danger" style="display: none;">
                                    Umur minimal harus 12 tahun.
                                </small>
                            </div>
                        </div>

                        <!-- Password dan konfirmasi -->
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" id="password" type="password" name="password" required
                                    placeholder="Password">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                <span class="badge bg-info mt-2">Password harus minimal 8 kombinasi huruf dan
                                    angka</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" id="password_confirmation" type="password"
                                    name="password_confirmation" required placeholder="Confirm Password">
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="required-checkbox"
                                        name="required_checkbox" required>
                                    <label class="form-check-label" for="required-checkbox">
                                        Saya setuju dengan <a href="{{route('kom.snk')}}" target="_blank">syarat dan ketentuan</a>.
                                    </label>
                                </div>
                                <x-input-error :messages="$errors->get('required_checkbox')" class="mt-2" />
                            </div>
                        </div>

                        <div class="form-group text-center row m-t-20">
                            <div class="col-12">
                                <button class="btn btn-danger btn-block waves-effect waves-light"
                                    type="submit">Register</button>
                            </div>
                        </div>

                        <div class="form-group m-t-10 mb-0 row">
                            <div class="col-12 m-t-20 text-center">
                                <a href="{{ route('kom.login') }}" class="text-muted">Already have account?</a>
                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('be') }}/assets/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxYa = document.getElementById('dariGerejaKami');
            const checkboxTidak = document.getElementById('bukanDariGerejaKami');
            const selectRayonGroup = document.getElementById('select-rayon-group');
            const inputOtherRayon = document.getElementById('other-rayon-input');
            const inputNotFromChurch = document.getElementById('not-from-church-group'); // pastikan ada di HTML
            const hiddenInput = document.getElementById('asal_gereja');

            const selectRayon = document.getElementById('select-rayon');
            const inputRayonManual = document.getElementById('rayon-text-input');
            const inputManualGereja = document.getElementById('manual-gereja-input');

            // Fungsi toggle tampilan opsi berdasarkan pilihan checkbox
            function toggleGerejaOptions(isFromGerejaKami) {
                if (isFromGerejaKami) {
                    checkboxTidak.checked = false;
                    selectRayonGroup.style.display = 'block';
                    if (inputNotFromChurch) inputNotFromChurch.style.display = 'none';
                    inputOtherRayon.style.display = 'none';

                    // Update hidden input berdasarkan pilihan select
                    hiddenInput.value = selectRayon.value || '';
                } else {
                    checkboxYa.checked = false;
                    selectRayonGroup.style.display = 'none';
                    inputOtherRayon.style.display = 'none';
                    if (inputNotFromChurch) inputNotFromChurch.style.display = 'block';

                    // Update hidden input berdasarkan input manual
                    hiddenInput.value = inputManualGereja.value.trim() || '';
                }
            }

            // Event checkbox Ya / Tidak
            checkboxYa.addEventListener('change', () => toggleGerejaOptions(true));
            checkboxTidak.addEventListener('change', () => toggleGerejaOptions(false));

            // Inisialisasi Select2 untuk selectRayon
            $(selectRayon).select2({
                placeholder: "-- Pilih Rayon --",
                allowClear: true,
                ajax: {
                    url: 'https://gbigatsu.id/api/kom-digital/api/rayon-search',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data.results
                    }),
                    cache: true
                },
                minimumInputLength: 1,
                width: '100%'
            });

            // Ketika select2 memilih opsi
            $(selectRayon).on('select2:select', function(e) {
                const val = e.params.data.id;
                if (val === 'other') {
                    inputOtherRayon.style.display = 'block';
                    hiddenInput.value = inputRayonManual.value.trim();
                } else {
                    inputOtherRayon.style.display = 'none';
                    hiddenInput.value = val;
                }
            });

            // Event input manual jika "Tidak" dicentang
            inputManualGereja.addEventListener('input', function() {
                if (checkboxTidak.checked) {
                    hiddenInput.value = this.value.trim();
                }
            });

            // Event input manual Rayon 'other' jika "Ya" dicentang dan select 'other'
            inputRayonManual.addEventListener('input', function() {
                if (checkboxYa.checked && selectRayon.value === 'other') {
                    hiddenInput.value = this.value.trim();
                }
            });

            // Jika selectRayon berubah ke selain 'other'
            selectRayon.addEventListener('change', function() {
                if (this.value !== 'other') {
                    inputOtherRayon.style.display = 'none';
                    hiddenInput.value = this.value;
                }
            });

            // Event submit form: pastikan hidden input terupdate
            // document.querySelector('form').addEventListener('submit', function(e) {
            //     if (checkboxYa.checked) {
            //         if (selectRayon.value === 'other') {
            //             hiddenInput.value = inputRayonManual.value.trim();
            //             if (!hiddenInput.value) {
            //                 e.preventDefault();
            //                 alert('Silakan isi nama Rayon.');
            //                 inputRayonManual.focus();
            //             }
            //         } else {
            //             hiddenInput.value = selectRayon.value;
            //         }
            //     } else if (checkboxTidak.checked) {
            //         hiddenInput.value = inputManualGereja.value.trim();
            //         if (!hiddenInput.value) {
            //             e.preventDefault();
            //             alert('Silakan isi nama Gereja.');
            //             inputManualGereja.focus();
            //         }
            //     } else {
            //         // Jika tidak memilih Ya/Tidak, block submit juga
            //         e.preventDefault();
            //         alert('Silakan pilih apakah Anda berjemaat di gereja kami atau tidak.');
            //     }
            // });
            document.querySelector('form').addEventListener('submit', function(e) {

    if (checkboxYa.checked) {

        const selectedValue = $(selectRayon).val();

        if (!selectedValue) {
            e.preventDefault();
            alert('Silakan pilih nama gereja.');
            return;
        }

        if (selectedValue === 'other') {

            hiddenInput.value = inputRayonManual.value.trim();

            if (!hiddenInput.value) {
                e.preventDefault();
                alert('Silakan isi nama Rayon.');
                inputRayonManual.focus();
                return;
            }

        } else {
            hiddenInput.value = selectedValue;
        }

    } else if (checkboxTidak.checked) {

        hiddenInput.value = inputManualGereja.value.trim();

        if (!hiddenInput.value) {
            e.preventDefault();
            alert('Silakan isi nama Gereja.');
            inputManualGereja.focus();
            return;
        }

    } else {

        e.preventDefault();
        alert('Silakan pilih apakah Anda berjemaat di gereja kami atau tidak.');
        return;
    }

    console.log('asal_gereja = ', hiddenInput.value);
});
        });
    </script>



    <script>
        function validateAge() {
            const select = document.getElementById('thn_lahir');
            const selectedYear = parseInt(select.value);
            const currentYear = new Date().getFullYear();
            const umurError = document.getElementById('umur-error');

            if ((currentYear - selectedYear) < 12) {
                umurError.style.display = 'block';
                select.setCustomValidity('Umur minimal harus 12 tahun');
            } else {
                umurError.style.display = 'none';
                select.setCustomValidity('');
            }
        }
    </script>

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <!-- jQuery  -->
    {{-- <script src="{{ asset('be') }}/assets/js/jquery.min.js"></script> --}}
    <script src="{{ asset('be') }}/assets/js/popper.min.js"></script>
    <script src="{{ asset('be') }}/assets/js/bootstrap.min.js"></script>
    <script src="{{ asset('be') }}/assets/js/modernizr.min.js"></script>
    <script src="{{ asset('be') }}/assets/js/detect.js"></script>
    <script src="{{ asset('be') }}/assets/js/fastclick.js"></script>
    <script src="{{ asset('be') }}/assets/js/jquery.blockUI.js"></script>
    <script src="{{ asset('be') }}/assets/js/waves.js"></script>
    <script src="{{ asset('be') }}/assets/js/jquery.nicescroll.js"></script>

    <!-- App js -->
    <script src="{{ asset('be') }}/assets/js/app.js"></script>

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}

</body>

</html>
