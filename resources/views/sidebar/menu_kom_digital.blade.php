@php
    $user = auth()->user();
    $role = $user->role ?? null;

    // Default
    $isSiswaWithActiveBook = false;

    // Jika peserta dan punya buku aktif di relasi kmd_books
    if ($role === 'peserta') {
        $book = $user->books()->where('status', 'active')->first();
        $isSiswaWithActiveBook = $book !== null;
    }

    if ($role === 'pengajar') {
        $book = $user->books()->where('status', 'active')->first();
        $isSiswaWithActiveBook = $book !== null;
    }

    if ($role === 'admin_cabang') {
        $book = $user->books()->where('status', 'active')->first();
        $isSiswaWithActiveBook = $book !== null;
    }

    if ($role === 'superadmin') {
        $book = $user->books()->where('status', 'active')->first();
        $isSiswaWithActiveBook = $book !== null;
    }

    $isSuperadmin = $role === 'superadmin';
@endphp

<ul>
    <li class="menu-title">Main Menu</li>

    {{-- Menu default untuk semua user --}}
    <li>
        <a href="{{ route('kom.dashboard_kom') }}" class="waves-effect">
            <i class="mdi mdi-airplay"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li>
        <a href="{{ route('kom.validasi.form_validasi') }}" class="waves-effect">
            <i class="mdi mdi-apps"></i>
            <span>Input Kode Buku</span>
        </a>
    </li>
    @if (($role === 'peserta' && $isSiswaWithActiveBook) || $isSuperadmin || $role === 'pengajar')
        <li>
            <a href="{{ route('kom.kelas_lecture.index') }}" class="waves-effect">
                <i class="mdi mdi-apps"></i>
                <span>KOM 100</span>
            </a>
        </li>
    @endif
    {{-- Menu untuk siswa --}}
    @if (($role === 'peserta' && $isSiswaWithActiveBook) || $isSuperadmin)
        <li class="has_sub">
            <a href="javascript:void(0);" class="waves-effect">
                <i class="mdi mdi-settings"></i><span> Peserta </span>
                <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
            </a>
            <ul class="list-unstyled">
                <li><a href="{{ route('kom.peserta.kelas_tutorial') }}">Kelas Tutorial</a></li>
                <li><a href="{{ route('kom.peserta.pertanyaan') }}">Pertanyaan Saya</a></li>
                <li><a href="{{ route('kom.peserta.persembahan') }}">Persembahan</a></li>
                <li><a href="{{ route('kom.peserta.buku') }}">No Buku</a></li>
                <li><a href="{{ route('kom.peserta.progress_kom') }}">Progress</a></li>
                <li><a href="{{ route('kom.peserta.sertifikat') }}">e-Sertifikat</a></li>
            </ul>
        </li>
    @endif

    {{-- Menu untuk pengajar --}}
    @if (($role === 'pengajar' ) || $isSuperadmin)
        <li class="has_sub">
            <a href="javascript:void(0);" class="waves-effect">
                <i class="mdi mdi-settings"></i><span> Pengajar </span>
                <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
            </a>
            <ul class="list-unstyled">
                <li><a href="{{ route('kom.pengajar.index') }}">Kelas Tutorial</a></li>
                <li><a href="{{ route('kom.diklat_tutor.index') }}">Diklat Tutor</a></li>
                <li><a href="{{ route('kom.pengajar.tutor.diklat_saya') }}">Diklat Saya</a></li>
                <li><a href="{{ route('kom.pengajar.tutor.pertanyaan') }}">Pertanyaan Saya</a></li>
                <li><a href="{{ route('kom.pengajar.tutor.persembahan') }}">Persembahan</a></li>
                <li><a href="{{ route('kom.pengajar.tutor.buku') }}">No Buku</a></li>
                <li><a href="{{ route('kom.pengajar.tutor.progress_kom') }}">Progress</a></li>

            </ul>
        </li>
    @endif

    {{-- Menu untuk admin_cabang --}}
    @if (($role === 'admin_cabang') || $isSuperadmin)
        <li class="has_sub">
            <a href="javascript:void(0);" class="waves-effect">
                <i class="mdi mdi-settings"></i><span> Admin Cabang </span>
                <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
            </a>
            <ul class="list-unstyled">
                <li><a href="{{ route('kom.admin_cabang_users.index') }}">Daftar User</a></li>
                {{-- <li><a href="{{ route('kom.admin_cabang_users.pengajar') }}">Daftar Guru</a></li> --}}
                <li><a href="{{ route('kom.kelas_tutor_cabang.index') }}">Kelas Tutorial</a></li>
                <li><a href="{{ route('kom.data-statik.index') }}">Data</a></li>
                <li><a href="{{ route('kom.absensi.index') }}">Absensi</a></li>
                <li><a href="{{ route('kom.review-pengajar_cabang.getAllReviews') }}">Review Pengajar</a></li>
                <li><a href="{{ route('kom.persembahan_cabang.index') }}">Info Persembahan</a></li>
                <li><a href="{{ route('kom.tarik-saldo_cabang.index') }}">Tarik Dana</a></li>
                <li><a href="{{ route('kom.rekening_cabang.index') }}">Info Rekening</a></li>
            </ul>
        </li>
    @endif

    {{-- Menu untuk admin_kom (superadmin) --}}
    @if ($role === 'superadmin')
        <li>
            <a href="{{ route('kom.super_visitor.index') }}" class="waves-effect">
                <i class="mdi mdi-chart-line"></i>
                <span>Statistik Pengunjung</span>
            </a>
        </li>

        <li>
            <a href="{{ route('kom.super_nginxlog.index') }}" class="waves-effect">
                <i class="mdi mdi-chart-areaspline"></i>
                <span>Statistik Pengunjung (Log Nginx)</span>
            </a>
        </li>

        <li class="has_sub">
            <a href="javascript:void(0);" class="waves-effect">
                <i class="mdi mdi-settings"></i><span> Diklat Tutor</span>
                <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
            </a>
            <ul class="list-unstyled">
               <li><a href="{{ route('kom.kelas_tutor_diklat.index') }}">Diklat Tutor</a></li>
                {{-- <li><a href="javascript:void(0);">Data</a></li>
                <li><a href="javascript:void(0);">Absensi</a></li>
                <li><a href="javascript:void(0);">Review Pengajar</a></li>
                <li><a href="javascript:void(0);">Info Persembahan</a></li> --}}
            </ul>
        </li>
        <li class="has_sub">
            <a href="javascript:void(0);" class="waves-effect">
                <i class="mdi mdi-settings"></i><span> Admin Kom </span>
                <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
            </a>
            <ul class="list-unstyled">
                <li><a href="{{ route('kom.super_user.index') }}">Daftar User</a></li>
                <li><a href="{{ route('kom.inputMassal.index') }}">Input Massal</a></li>
                <li><a href="{{ route('kom.super_books.index') }}">Input Kode Buku</a></li>
                <li><a href="{{ route('kom.super_kelas-kom.index') }}">Input Kelas Kom</a></li>
                <li><a href="{{ route('kom.super_kom-modul.index') }}">Input Modul</a></li>
                <li><a href="{{ route('kom.super_pertanyaan.index') }}">Daftar Pertanyaan</a></li>
                <li><a href="{{ route('kom.super_jawaban.index') }}">Daftar Jawaban</a></li>
                <li><a href="{{ route('kom.super_pertanyaan-video.index') }}">Pertanyaan di Video</a></li>
                <li><a href="{{ route('kom.super_kelas_tutorial.index') }}">Kelas Tutorial</a></li>
                <li><a href="{{ route('kom.super_userclasstutorial.index') }}">User Kelas Tutorial</a></li>
                <li><a href="{{ route('kom.super_pertanyaan_user.index') }}">Pertanyaan Peserta</a></li>
                <li><a href="{{ route('kom.super_videos.index') }}">Input Video</a></li>
                <li><a href="{{ route('kom.super_progress-video.index') }}">Progress / Video</a></li>
                <li><a href="{{ route('kom.super_progress-modul.index') }}">Progress / Modul</a></li>
                <li><a href="{{ route('kom.super_jawaban-user.index') }}">Jawaban User</a></li>
                <li><a href="{{ route('kom.super_daftar-rayon.index') }}">Daftar Rayon</a></li>
                <li><a href="{{ route('kom.super_e-sertifikat.index') }}">Sertifikat</a></li>
                <li><a href="{{ route('kom.super_daftar-rekening.index') }}">Daftar Rekening</a></li>
                <li><a href="{{ route('kom.super_persembahan.index') }}">Persembahan</a></li>
                <li><a href="{{ route('kom.super_tarik-saldo.index') }}">Request Saldo</a></li>
            </ul>
        </li>
    @endif
</ul>
