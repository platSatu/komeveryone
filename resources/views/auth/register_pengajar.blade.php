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

        <link rel="shortcut icon" href="assets/images/favicon.ico">
        <link href="{{asset('be')}}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="{{asset('be')}}/assets/css/icons.css" rel="stylesheet" type="text/css">
        <link href="{{asset('be')}}/assets/css/style.css" rel="stylesheet" type="text/css">
    </head>

    <body class="fixed-left">

        <!-- Begin page -->
        <div class="accountbg"></div>
        <div class="wrapper-page">

            <div class="card">
                <div class="card-body">

                    <div class="text-center">
                        <a href="index.html" class="logo logo-admin"><img src="{{asset('be')}}/assets/images/e-logo.png" height="20" alt="logo"></a>
                    </div>

                    <div class="p-3">
                        <form method="POST" action="{{ route('registerPengajarKom_form') }}" class="form-horizontal">
                            @csrf

                            <!-- Name -->
                            <div class="form-group row">
                                <div class="col-12">
                                    <input class="form-control" id="name" type="text" name="name" :value="old('name')" required autofocus placeholder="Name">
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Email Address -->
                            <div class="form-group row">
                                <div class="col-12">
                                    <input class="form-control" id="email" type="email" name="email" :value="old('email')" required placeholder="Email">
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="form-group row">
                                <div class="col-12">
                                    <input class="form-control" id="password" type="password" name="password" required placeholder="Password">
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-group row">
                                <div class="col-12">
                                    <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required placeholder="Confirm Password">
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>

                            <div class="form-group text-center row m-t-20">
                                <div class="col-12">
                                    <button class="btn btn-danger btn-block waves-effect waves-light" type="submit">Register</button>
                                </div>
                            </div>

                            <div class="form-group m-t-10 mb-0 row">
                                <div class="col-12 m-t-20 text-center">
                                    <a href="{{ route('login') }}" class="text-muted">Already have account?</a>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- jQuery  -->
        <script src="{{asset('be')}}/assets/js/jquery.min.js"></script>
        <script src="{{asset('be')}}/assets/js/popper.min.js"></script>
        <script src="{{asset('be')}}/assets/js/bootstrap.min.js"></script>
        <script src="{{asset('be')}}/assets/js/modernizr.min.js"></script>
        <script src="{{asset('be')}}/assets/js/detect.js"></script>
        <script src="{{asset('be')}}/assets/js/fastclick.js"></script>
        <script src="{{asset('be')}}/assets/js/jquery.blockUI.js"></script>
        <script src="{{asset('be')}}/assets/js/waves.js"></script>
        <script src="{{asset('be')}}/assets/js/jquery.nicescroll.js"></script>

        <!-- App js -->
        <script src="{{asset('be')}}/assets/js/app.js"></script>

    </body>
</html>
