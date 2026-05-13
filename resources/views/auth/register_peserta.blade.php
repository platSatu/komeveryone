<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>REGISTER | HMMINISTRY</title>
    <meta content="HMMINISTRY" name="description" />
    <meta content="Mannatthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="{{ asset('/public/home') }}/96.png">
    <link href="{{ asset('be') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('be') }}/assets/css/icons.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('be') }}/assets/css/style.css" rel="stylesheet" type="text/css">
</head>

<body class="fixed-left">

    <!-- Begin page -->
    {{-- <div class="accountbg"></div> --}}
    <div class="wrapper-page">

        <div class="card">
            <div class="card-body">

                <div class="text-center">
                    <a href="" class="logo logo-admin"><img src="{{ asset('home') }}/kom/Logo-kom.png"
                            height="100" alt="gbigatsu"></a>
                </div>

                <div class="p-3">
                    <form method="POST" action="{{ route('registerPesertaKom_form') }}" class="form-horizontal">
                        @csrf

                        <!-- Name -->
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" id="name" type="text" name="name"
                                    :value="old('name')" required autofocus placeholder="Name">
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" id="email" type="email" name="email"
                                    :value="old('email')" required placeholder="Email">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" id="kode_buku" type="text" name="kode_buku"
                                    value="{{ old('kode_buku') }}" required placeholder="Kode Buku">
                                <x-input-error :messages="$errors->get('kode_buku')" class="mt-2" />
                                <!-- Tempat untuk menampilkan status validitas kode buku -->
                                <div id="kodeBukuStatus" class="mt-2"></div>
                            </div>
                        </div>

                        <!-- Dropdown -->
                        <!-- Dropdown Pilih Rayon -->
                        <div class="form-group row">
                            <div class="col-12">
                                <label for="rayon" class="form-label">Pilih Rayon</label>
                                <select class="form-control" id="rayon" name="rayon" required onchange="showOtherInput()">
                                    <option value="JIS">JIS</option>
                                    <option value="RAYON 3">RAYON 3</option>
                                    <option value="RAYON 7">RAYON 7</option>
                                    <option value="RAYON 9">RAYON 9</option>
                                    <option value="Lain-lain">Lain-lain</option> <!-- Opsi Lain-lain -->
                                </select>
                                <x-input-error :messages="$errors->get('rayon')" class="mt-2" />
                            </div>
                        </div>
                        
                        <!-- Textbox untuk Lain-lain -->
                        <div class="form-group row" id="other-input-group" style="display: none;">
                            <div class="col-12">
                                <label for="rayon" class="form-label">Silahkan input dibawah ini</label>
                                <input class="form-control" id="other-rayon" type="text" name="rayon" placeholder="Masukkan rayon lain-lain">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" id="password" type="password" name="password" required
                                    placeholder="Password">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                    <span class="badge bg-info mt-2">Password harus minimal 8 kombinasi huruf dan angka</span>    
                            </div>
                        </div>

                        <!-- Confirm Password -->
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
                                    <input class="form-check-input" type="checkbox" id="required-checkbox" name="required_checkbox" required>
                                    <label class="form-check-label" for="required-checkbox">
                                        Saya setuju dengan <a href="#">syarat dan ketentuan</a>.
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

                        {{-- <div class="form-group m-t-10 mb-0 row">
                            <div class="col-12 m-t-20 text-center">
                                <a href="{{ route('login') }}" class="text-muted">Already have account?</a>
                            </div>
                        </div> --}}

                    </form>
                </div>

            </div>
        </div>
    </div>
    <script>
        function showOtherInput() {
            var rayonSelect = document.getElementById('rayon');
            var otherInputGroup = document.getElementById('other-input-group');
            var selectedValue = rayonSelect.value;
    
            // Menampilkan text box jika "Lain-lain" dipilih
            if (selectedValue === 'Lain-lain') {
                otherInputGroup.style.display = 'block';
            } else {
                otherInputGroup.style.display = 'none';
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#kode_buku').on('input', function() {
            var kodeBuku = $(this).val(); // Mengambil nilai input kode_buku
            if (kodeBuku.length > 0) {
                // Kirim request AJAX ke server untuk memeriksa kode buku
                $.ajax({
                    url: '{{ route('check.kode_buku') }}', // Route untuk pengecekan kode buku
                    method: 'GET',
                    data: { kode_buku: kodeBuku },
                    success: function(response) {
                        // Jika kode buku valid
                        if (response.valid) {
                            $('#kodeBukuStatus').html('<span class="text-success">Kode buku benar!</span>');
                        } else {
                            $('#kodeBukuStatus').html('<span class="text-danger">Kode buku salah!</span>');
                        }
                    },
                    error: function() {
                        $('#kodeBukuStatus').html('<span class="text-danger">Terjadi kesalahan, coba lagi!</span>');
                    }
                });
            } else {
                $('#kodeBukuStatus').html(''); // Kosongkan status jika input kosong
            }
        });
    });
    </script>
    <!-- jQuery  -->
    <script src="{{ asset('be') }}/assets/js/jquery.min.js"></script>
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

</body>

</html>
