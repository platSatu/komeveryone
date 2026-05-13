<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <title>REGISTER | KOM</title>
    <meta content="KOM" name="description" />
    <meta content="Mannatthemes" name="author" />
    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <link href="{{ asset('be') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('be') }}/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('be') }}/assets/css/style.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body class="fixed-left">

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
                <div class="text-center mb-3">
                    <a href="#" class="logo logo-admin"><img src="{{ asset('home/gatsu_text_black.png') }}" height="50" alt="Kom"></a>
                </div>

                <form method="POST" action="{{ route('cek.sim') }}" class="form-horizontal">
                    @csrf
                    
                    <div class="form-group row">
                        <div class="col-12">
                            <input
                                class="form-control"
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                placeholder="Masukkan No Kartu"
                            >
                            <small class="form-text text-muted">No Kartu</small>
                        </div>
                    </div>

                    <div class="form-group text-center row m-t-20">
                        <div class="col-12">
                            <button class="btn btn-danger btn-block waves-effect waves-light" type="submit">
                                Cek No Kartu
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('be/assets/js/bootstrap.min.js') }}"></script>
</body>

</html>
