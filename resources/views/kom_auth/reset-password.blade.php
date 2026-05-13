<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | GBIGATSU</title>
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

                {{-- Success --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Error --}}
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

                {{-- Form Reset --}}
                <div class="p-3">
                    <form method="POST" action="{{ route('kom.password.update') }}" class="form-horizontal">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                class="form-control" placeholder="Masukkan Email">
                        </div>

                        <div class="form-group">
                            <label for="password">Password Baru</label>
                            <input id="password" type="password" name="password" required
                                class="form-control" placeholder="Password baru">
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                class="form-control" placeholder="Ulangi password baru">
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-danger btn-block waves-effect waves-light">
                                Reset Password
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
