<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | GBIGATSU</title>
    <link rel="shortcut icon" href="{{ asset('home') }}/96.png">
    <link href="{{ asset('be') }}/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('be') }}/assets/css/icons.css" rel="stylesheet">
    <link href="{{ asset('be') }}/assets/css/style.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper-page">
        <div class="card">
            <div class="card-body">

                <div class="text-center mb-4">
                    <a href="{{ route('kom.login') }}" class="logo logo-admin">
                        <img src="{{ asset('home') }}/gatsu_text_black.png" height="50" alt="logo">
                    </a>
                </div>

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Error Message --}}
                @if (session('error'))
                    <div class="alert alert-danger">
                        {!! session('error') !!}
                    </div>
                @endif

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form --}}
                <div class="p-3">
                    <form method="POST" action="{{ route('kom.password.email') }}" class="form-horizontal">
                        @csrf

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" class="form-control"
                                   value="{{ old('email') }}" required autofocus placeholder="Masukkan Email Anda">
                            <small class="form-text text-muted">Kami akan mengirimkan link reset ke email ini.</small>
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-danger btn-block waves-effect waves-light">
                                Kirim Link Reset Password
                            </button>
                        </div>

                        <div class="form-group text-center mt-3">
                            <a href="{{ route('kom.login') }}" class="text-muted">
                                <i class="mdi mdi-login"></i> <small>Kembali ke Login</small>
                            </a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('be') }}/assets/js/jquery.min.js"></script>
    <script src="{{ asset('be') }}/assets/js/bootstrap.min.js"></script>
</body>

</html>
