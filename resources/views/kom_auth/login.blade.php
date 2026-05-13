<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>LOGIN | GBIGATSU</title>
    <meta content="GBIGATSU" name="description" />
    <meta content="Mannatthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="{{ asset('home') }}/96.png">
    <link href="{{asset('be')}}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="{{asset('be')}}/assets/css/icons.css" rel="stylesheet" type="text/css">
    <link href="{{asset('be')}}/assets/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>

    <!-- Begin page -->

    <div class="wrapper-page">

        <div class="card">
            <div class="card-body">

                <div class="text-center">
                    <a href="" class="logo logo-admin"><img src="{{asset('home')}}/gatsu_text_black.png" height="50"
                            alt="logo"></a>
                </div>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {!! session('error') !!}
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
                <div class="px-3 pb-3">
                    <form method="POST" action="{{ route('kom_login') }}" class="form-horizontal m-t-20">
                        @csrf

                        <!-- Email Address -->
                        <div class="form-group row">
                            <div class="col-12">
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="form-control" type="email" name="email"
                                    :value="old('email')" required autofocus autocomplete="username" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="form-group row">
                            <div class="col-12">
                                <x-input-label for="password" :value="__('Password')" />
                                <x-text-input id="password" class="form-control" type="password" name="password"
                                    required autocomplete="current-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="form-group row">
                            <div class="col-12">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="remember_me"
                                        name="remember">
                                    <label class="custom-control-label" for="remember_me">Remember me</label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group text-center row m-t-20">
                            <div class="col-12">
                                <x-primary-button class="btn btn-danger btn-block waves-effect waves-light">
                                    {{ __('Log In') }}
                                </x-primary-button>
                            </div>
                        </div>

                        <div class="form-group m-t-10 mb-0 row">
                            <div class="col-sm-7 m-t-20">
                                <a href="{{ route('kom.password.request') }}" class="text-muted"><i class="mdi mdi-lock"></i> <small>Forgot your
                                        password?</small></a>
                            </div>
                            <div class="col-sm-5 m-t-20">
                                <a href="{{ route('kom.register') }}" class="text-muted"><i
                                        class="mdi mdi-account-circle"></i> <small>Create an account?</small></a>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- jQuery -->
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