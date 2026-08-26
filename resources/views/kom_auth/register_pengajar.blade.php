<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>REGISTER | KOM</title>
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
                    <form method="POST" action="{{ route('kom_registerTutor') }}" class="form-horizontal">
                        @csrf

                        <!-- Name -->
                        {{-- <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" id="name" type="text" name="name"
                                    value="{{ request()->get('nama') ?? old('name') }}" required autofocus
                                    placeholder="Name">
                                <small class="form-text text-muted">Nama lengkap sesuai dengan KTP</small>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div> --}}
                        <!-- Name (readonly) -->
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" id="name" type="text" name="name"
                                    value="{{ session('sim.namalengkap') ?? old('name') }}" required autofocus readonly
                                    placeholder="Name">

                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div>

                        <!-- idnkp (readonly) -->
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" id="idnkp" type="text" name="idnkp"
                                    value="{{ session('sim.idnkp') ?? old('idnkp') }}" readonly placeholder="ID NKP">
                                <x-input-error :messages="$errors->get('idnkp')" class="mt-2" />
                            </div>
                        </div>

                        <!-- statactive (readonly) -->
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" id="statactive" type="text" name="statactive"
                                    value="{{ session('sim.statactive') ?? old('statactive') }}" readonly
                                    placeholder="Status Active">
                                <x-input-error :messages="$errors->get('statactive')" class="mt-2" />
                            </div>
                        </div>

                        <!-- jenis (readonly) -->
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" id="jenis" type="text" name="jenis"
                                    value="{{ session('sim.jenis') ?? old('jenis') }}" readonly placeholder="Jenis">
                                <x-input-error :messages="$errors->get('jenis')" class="mt-2" />
                            </div>
                        </div>

                        <!-- no_sim (readonly) -->
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" id="no_sim" type="text" name="no_sim"
                                    value="{{ session('sim.no_sim') ?? old('no_sim') }}" readonly placeholder="No SIM">
                                <x-input-error :messages="$errors->get('no_sim')" class="mt-2" />
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

                        
                        <!-- Pilih Rayon -->
                        <div class="form-group row">
                            <div class="col-12">
                                <label for="rayon_id" class="form-label">Pilih Rayon / CK Anda </label>
                                <select class="form-control" id="select-rayon" style="width: 100%;" name="asal_gereja" required>
                                    <!-- Data dari API Select2 -->
                                </select>
                                <x-input-error :messages="$errors->get('asal_gereja')" class="mt-2" />
                            </div>
                        </div>

                      
                      
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
                                        Saya setuju dengan <a href="{{ route('kom.snk') }}" target="_blank">syarat
                                            dan ketentuan</a>.
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const nameParam = urlParams.get('nama');

            if (nameParam) {
                const nameInput = document.getElementById('name');
                if (nameInput) {
                    nameInput.value = decodeURIComponent(nameParam.replace(/\+/g, ' '));
                }
            }
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectRayon = document.getElementById('select-rayon');
        // const hiddenInput = document.getElementById('asal_gereja');

        // Inisialisasi Select2
        $(selectRayon).select2({
            placeholder: "-- Pilih CK / Rayon --",
            allowClear: false,
            ajax: {
                url: 'https://gbigatsu.id/api/kom-digital/api/asal-rayon',
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

        // Saat pilihan dipilih → update hidden input
        // $(selectRayon).on('select2:select', function (e) {
        //     const val = e.params.data.id;
        //     hiddenInput.value = val;
        // });
        $(selectRayon).on('select2:select', function (e) {
            console.log('Selected:', e.params.data.id);
        });

        // Saat form disubmit, validasi harus sudah pilih rayon
        // document.querySelector('form').addEventListener('submit', function (e) {
        //     if (!selectRayon.value) {
        //         e.preventDefault();
        //         alert("Silakan pilih nama gereja Anda.");
        //         $(selectRayon).focus();
        //     }
        // });
        document.querySelector('form').addEventListener('submit', function (e) {

            console.log('Native:', selectRayon.value);
            console.log('jQuery:', $('#select-rayon').val());

        });
    });
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
