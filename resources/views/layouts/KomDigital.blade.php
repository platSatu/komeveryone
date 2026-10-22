<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    
    <title>GBI GATSU | KOM EVERYONE</title>
    <meta content="Dashboard" name="description" />
    <meta content="Mannatthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="{{ asset('home') }}/96.png">

    <!-- jvectormap -->
    <link href="{{ asset('be') }}/assets/plugins/jvectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet">
    <link href="{{ asset('be') }}/assets/plugins/fullcalendar/vanillaCalendar.css" rel="stylesheet" type="text/css" />

    <link href="{{ asset('be') }}/assets/plugins/morris/morris.css" rel="stylesheet">

    <link href="{{ asset('be') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('be') }}/assets/css/icons.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('be') }}/assets/css/style.css" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://vjs.zencdn.net/7.21.1/video-js.css" rel="stylesheet" />
    
    <!-- Font Awesome 5 Free CDN -->
    
    <style>
        /* kom_v02: dropdown bahasa */
        .kom-lang-toggle { font-weight:600; }
        .kom-lang-menu { min-width:230px; padding:6px 0; border-radius:12px; }
        .kom-lang-menu .dropdown-item { gap:10px; padding:8px 16px; }
        .kom-lang-menu .dropdown-item.active, .kom-lang-menu .dropdown-item:active { background:#eef3fc; color:#1f2a44; }
        .kom-lang-menu .dropdown-item:disabled { opacity:1; }
        .kom-lang-mini { display:inline-flex; align-items:center; justify-content:center; width:30px; height:24px; border-radius:6px; background:#eef3fc; color:#2a5298; font-size:.72rem; font-weight:800; margin-right:10px; }
    </style>
</head>


<body class="fixed-left">

    <!-- Loader -->
    {{-- <div id="preloader"><div id="status"><div class="spinner"></div></div></div> --}}

    <!-- Begin page -->
    <div id="wrapper">

        <!-- ========== Left Sidebar Start ========== -->
        <div class="left side-menu">
            <button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect">
                <i class="ion-close"></i>
            </button>

            <!-- LOGO -->
            <div class="topbar-left">
                <div class="text-center">
                    <!--<a href="index.html" class="logo"><i class="mdi mdi-assistant"></i>Zoter</a>-->
                    <a href="" class="logo">
                        <img src="{{asset('home')}}/Logo-GBIGATSU.svg" alt="gbigatsu" class="" width="200">
                    </a>
                </div>
            </div>

            <div class="sidebar-inner niceScrollleft">

                <div id="sidebar-menu">
                    @include('sidebar.menu_kom_digital')
                </div>
                <div class="clearfix"></div>
            </div> <!-- end sidebarinner -->
        </div>
        <!-- Left Sidebar End -->

        <!-- Start right Content here -->

        <div class="content-page">
            <!-- Start content -->
            <div class="content">

                <!-- Top Bar Start -->
                <div class="topbar">

                    <nav class="navbar-custom">

                        <ul class="list-inline float-right mb-0">
                            <!-- language-->
                            <!-- <li class="list-inline-item hide-phone app-search">
                                <form role="search" class="">
                                    <input type="text" placeholder="Search..." class="form-control">
                                    <a href=""><i class="fa fa-search"></i></a>
                                </form>
                            </li> -->
                            <!-- <li class="list-inline-item dropdown notification-list hide-phone">
                                <a class="nav-link dropdown-toggle arrow-none waves-effect text-white"
                                    data-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                    aria-expanded="false">
                                    <img src="{{ asset('be') }}/assets/images/flags/us_flag.jpg" class="ml-2"
                                        height="16" alt="" />
                                </a>
                                <div class="dropdown-menu dropdown-menu-right language-switch">
                                    <a class="dropdown-item" href="#"><img
                                            src="{{ asset('be') }}/assets/images/flags/italy_flag.jpg" alt=""
                                            height="16" /><span> Italian </span></a>



                                </div>
                            </li> -->


                            <!-- <li class="list-inline-item dropdown notification-list">
                                <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown"
                                    href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <i class="ti-bell noti-icon"></i>
                                    <span class="badge badge-success noti-icon-badge">23</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg">
                                    
                                    <div class="dropdown-item noti-title">
                                        <h5><span class="badge badge-danger float-right">87</span>Notification</h5>
                                    </div>






                                </div>
                            </li> -->
                            {{-- kom_v02: pilihan bahasa belajar --}}
                            @auth
                                @php
                                    $__langs = \App\Models\Kmd\Language::activeList();
                                    $__langNow = app()->getLocale();
                                    $__kelasJalan = \App\Models\Kmd\KmdUserClass::where('user_id', auth()->id())
                                        ->where('status', 'active')->latest('enrolled_at')->first();
                                @endphp
                                @if ($__langs->count() > 1)
                                    <li class="list-inline-item dropdown notification-list">
                                        <a class="nav-link dropdown-toggle arrow-none waves-effect text-white kom-lang-toggle"
                                           data-toggle="dropdown" href="#" role="button"
                                           aria-haspopup="true" aria-expanded="false" title="{{ __('Bahasa Belajar') }}">
                                            <i class="fas fa-language mr-1"></i>{{ strtoupper($__langNow) }}
                                            <i class="fas fa-chevron-down ml-1" style="font-size:.7rem"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right kom-lang-menu">
                                            <h6 class="dropdown-header">{{ __('Bahasa Belajar') }}</h6>
                                            @foreach ($__langs as $__l)
                                                @php $__kunci = $__kelasJalan && $__kelasJalan->language_code !== $__l->code; @endphp
                                                <form method="POST" action="{{ route('kom.bahasa.store') }}" class="m-0">
                                                    @csrf
                                                    <input type="hidden" name="language_code" value="{{ $__l->code }}">
                                                    <button type="submit" class="dropdown-item d-flex align-items-center {{ $__l->code === $__langNow ? 'active' : '' }}"
                                                            {{ $__kunci || $__l->code === $__langNow ? 'disabled' : '' }}>
                                                        <span class="kom-lang-mini">{{ strtoupper($__l->code) }}</span>
                                                        <span class="flex-grow-1">{{ $__l->native_name ?: $__l->name }}</span>
                                                        @if ($__l->code === $__langNow)
                                                            <i class="fas fa-check ml-2"></i>
                                                        @elseif ($__kunci)
                                                            <i class="fas fa-lock ml-2 text-muted"></i>
                                                        @endif
                                                    </button>
                                                </form>
                                            @endforeach
                                            @if ($__kelasJalan)
                                                <div class="dropdown-divider"></div>
                                                <small class="d-block px-3 pb-2 text-muted" style="max-width:240px;white-space:normal">
                                                    <i class="fas fa-info-circle mr-1"></i>{{ __('Bahasa terkunci sampai kelas yang sedang berjalan selesai.') }}
                                                </small>
                                            @endif
                                        </div>
                                    </li>
                                @endif
                            @endauth

                            <li class="list-inline-item dropdown notification-list">
                                {{-- icon opsional --}}
                                <span class="text-white">{{ __('Halo,') }}
                                    
                                    {{ Auth::user()->name ?? 'Guest' }}
                                </span>
                                    {{-- <pre>{{ print_r(Auth::guard('kom')->user()) }}</pre> --}}
                            </li>



                            <li class="list-inline-item dropdown notification-list">
                                @php
                                    $user = Auth::user();
                                    $foto = $user && $user->image
                                        ? asset('kom/profile/' . $user->image)
                                        : asset('be/assets/images/users/avatar-1.jpg');
                                @endphp

                                <a class="nav-link dropdown-toggle arrow-none waves-effect nav-user"
                                    data-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                    aria-expanded="false">
                                    <img src="{{ $foto }}" alt="user" class="rounded-circle">
                                </a>

                                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                    <!-- item-->
                                    @php
                                        $user = Auth::user();
                                        
                                    @endphp

                                    <!-- <div class="dropdown-item noti-title">
                                        <h5>Welcome, {{ $user->username ?? 'Guest' }}</h5>
                                    </div> -->
                                    <a class="dropdown-item" href="{{ route('kom.profile_kom') }}"><i
                                            class="mdi mdi-account-circle m-r-5 text-muted"></i> {{ __('Profil Saya') }}</a>




                                    <form method="POST" action="{{ route('kom.logout') }}" style="display: none;"
                                        id="logout-form">
                                        @csrf
                                    </form>

                                    <a class="dropdown-item" href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="mdi mdi-logout m-r-5 text-muted"></i> {{ __('Keluar') }}
                                    </a>

                                </div>
                            </li>

                        </ul>

                        <ul class="list-inline menu-left mb-0">
                            <li class="float-left">
                                <button class="button-menu-mobile open-left waves-light waves-effect">
                                    <i class="mdi mdi-menu"></i>
                                </button>
                            </li>
                        </ul>

                        <div class="clearfix"></div>

                    </nav>

                </div>
                <!-- Top Bar End -->

                <div class="page-content-wrapper ">

                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="page-title-box">
                                    @php $judulHalaman = trim($__env->yieldContent('page_title', __('Beranda'))); @endphp
                                    @if ($judulHalaman !== __('Beranda'))
                                    <div class="btn-group float-right">
                                        <ol class="breadcrumb hide-phone p-0 m-0">
                                            <li class="breadcrumb-item"><a href="{{ route('kom.dashboard_kom') }}">{{ __('Beranda') }}</a></li>
                                            <li class="breadcrumb-item active">{{ $judulHalaman }}</li>
                                        </ol>
                                    </div>
                                    @endif
                                    <h4 class="page-title">@yield('page_title', __('Beranda'))</h4>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <!-- end page title end breadcrumb -->

                        {{-- kom_v02: pita bahasa yang dikelola di halaman konten superadmin --}}
                        @if (request()->routeIs('kom.super_kom-modul.*', 'kom.super_videos.*', 'kom.super_pertanyaan.*', 'kom.super_jawaban.*', 'kom.super_pertanyaan-video.*'))
                            @include('a_kom_digital_pusat.superadmin._bahasa_kelola')
                        @endif

                        @yield('content')


                    </div><!-- container -->

                </div> <!-- Page content Wrapper -->

            </div> <!-- content -->

            <footer class="footer">
                © 2025 GBI GATSU
            </footer>

        </div>
        <!-- End Right content here -->

    </div>
    <!-- END wrapper -->


    <!-- jQuery  -->
    {{-- <script src="{{ asset('be') }}/assets/js/kmd.js"></script> --}}
    <script src="{{ asset('be') }}/assets/js/jquery.min.js"></script>
    <script src="{{ asset('be') }}/assets/js/popper.min.js"></script>
    <script src="{{ asset('be') }}/assets/js/bootstrap.min.js"></script>
    <script src="{{ asset('be') }}/assets/js/modernizr.min.js"></script>
    <script src="{{ asset('be') }}/assets/js/detect.js"></script>
    <script src="{{ asset('be') }}/assets/js/fastclick.js"></script>
    <script src="{{ asset('be') }}/assets/js/jquery.blockUI.js"></script>
    <script src="{{ asset('be') }}/assets/js/waves.js"></script>
    <script src="{{ asset('be') }}/assets/js/jquery.nicescroll.js"></script>

    <script src="{{ asset('be') }}/assets/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="{{ asset('be') }}/assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

    <script src="{{ asset('be') }}/assets/plugins/skycons/skycons.min.js"></script>
    <script src="{{ asset('be') }}/assets/plugins/fullcalendar/vanillaCalendar.js"></script>

    <script src="{{ asset('be') }}/assets/plugins/raphael/raphael-min.js"></script>
    <script src="{{ asset('be') }}/assets/plugins/morris/morris.min.js"></script>

    <script src="{{ asset('be') }}/assets/pages/dashborad.js"></script>

    <!-- App js -->
    <script src="{{ asset('be') }}/assets/js/app.js"></script>

    <script src="{{ asset('be') }}/assets/plugins/alertify/js/alertify.js"></script>
    <script src="{{ asset('be') }}/assets/pages/alertify-init.js"></script>

    <script src="{{ asset('be') }}/assets/pages/modal-animation.init.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- kom_v02: pop-up wajib pilih bahasa untuk user baru --}}
    @if (!empty($komPopupPilihBahasa) && !request()->routeIs('kom.bahasa.*'))
        @include('a_kom_digital_pusat.partials._popup_bahasa', ['mode' => 'baru'])
    @endif

    
    

</body>

</html>
