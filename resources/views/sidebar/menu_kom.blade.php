@php
    $role = Auth::user()->role;
@endphp

<ul>
    <li class="menu-title">Main Menu</li>

    <li>
        <a href="{{ route('kom.dashboard_kom') }}" class="waves-effect">
            <i class="mdi mdi-airplay"></i>
            <span> Dashboard</span>
        </a>
    </li>

    @php
        $user = Auth::guard('kom')->user();
    @endphp

    @if (in_array($user->role, ['siswa', 'admin_kom_pusat']))
        <li>
            <a href="{{ route('kom.kode_buku.formValidate') }}" class="waves-effect">
                <i class="mdi mdi-apps"></i>
                <span>Input Kode Buku</span>
            </a>
        </li>
    @endif

    @php
        // Ambil semua kom_class_id yang dimiliki oleh user_id yang statusnya 'aktif'
        $komClasses = \App\Models\Kom\KomBook::where('kom_user_id', auth('web')->id())
            ->where('status', 'aktif')
            ->pluck('kom_class_id'); // Mengambil semua kom_class_id yang aktif

        // Pastikan kom_class_id yang dimiliki oleh user memiliki nilai
        $komClasses = $komClasses->toArray(); // Mengkonversi koleksi menjadi array agar bisa diproses
    @endphp

    @foreach ($komClasses as $komClassId)
        @php
            $komClass = \App\Models\Kom\KomClass::find($komClassId); // Dapatkan data KomClass berdasarkan ID
        @endphp

        @if ($komClass)
            <li>
                <a href="{{ route('kom.kom100') }}" class="waves-effect">
                    <i class="mdi mdi-apps"></i>
                    <span>{{ $komClass->name }}</span>
                </a>
            </li>
            <li class="has_sub">
                <a href="javascript:void(0);" class="waves-effect">
                    <i class="mdi mdi-settings"></i><span> Peserta</span>
                    <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                </a>
                <ul class="list-unstyled">
                    <li><a href="{{ route('kom.peserta.booking.kelas') }}">Kelas Tutorial</a></li>
                    <li><a href="{{ route('kom.peserta.pertanyaanSaya') }}">Pertanyaan Saya</a></li>
                    <li><a href="{{ route('kom.peserta.zoomSaya') }}">Link Zoom</a></li>
                    <li><a href="{{ route('kom.peserta.donasiSaya') }}">Persembahan</a></li>
                    <li><a href="{{ route('kom.peserta.bukuSaya') }}">Kode Buku</a></li>
                    <li><a href="{{ route('kom.peserta.videoProgressSaya') }}">Progress</a></li>
                    <li><a href="{{ route('kom.peserta.sertifikatSaya') }}">e-Sertifikat</a></li>
                </ul>
            </li>
        @endif
    @endforeach



    {{-- role = pengajar --}}
    @if ($role === 'pengajar' || $role === 'admin_kom_pusat')
        <li class="has_sub">
            <a href="javascript:void(0);" class="waves-effect">
                <i class="mdi mdi-settings"></i><span> Pengajar </span>
                <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
            </a>
            <ul class="list-unstyled">

                {{-- <li><a href="{{ route('kom.pertanyaan_peserta_pengajar.index') }}">Daftar Pertanyaan</a></li> --}}
                <li><a href="{{ route('kom.jadwal_pengajar') }}">Jadwal Kelas Tutorial</a></li>
                {{-- <li><a href="{{route('kom.linkZoomPengajar')}}">Link Zoom</a></li> --}}

            </ul>
        </li>
    @endif

    {{-- role = admin_cabang --}}
    @if ($role === 'admin_cabang' || $role === 'admin_kom_pusat')
        <li class="has_sub">
            <a href="javascript:void(0);" class="waves-effect">
                <i class="mdi mdi-settings"></i><span> Admin Cabang </span>
                <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
            </a>
            <ul class="list-unstyled">
                <li><a href="{{ route('kom.peserta_by_rayon.index') }}">Daftar User</a></li>
                <li><a href="{{ route('kom.listPengajar') }}">Daftar Guru</a></li>
                <li><a href="{{ route('kom.schedulesbyuser.index') }}">Buka Kelas Tutorial</a></li>
                <li><a href="{{ route('kom.zoom.index') }}">Link Zoom</a></li>
                <li><a href="{{ route('kom.indexCabang') }}">Donasi</a></li>
                <li><a href="{{ route('kom.showSaldo') }}">Saldo</a></li>
            </ul>
        </li>
    @endif

    {{-- role = admin_kom --}}
    @if ($role === 'admin_kom_pusat')
        <li class="has_sub">
            <a href="javascript:void(0);" class="waves-effect">
                <i class="mdi mdi-settings"></i><span> Admin Kom </span>
                <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
            </a>
            <ul class="list-unstyled">
                <li><a href="{{ route('kom.users.index') }}">Daftar User</a></li>
                <li><a href="{{ route('kom.schedules.index') }}">Daftar Kelas</a></li>
                <li><a href="{{ route('kom.pertanyaan.index') }}">Daftar Pertanyaan</a></li>
                <li><a href="{{ route('kom.jawaban.index') }}">Daftar Jawaban</a></li>
                <li><a href="{{ route('kom.video_pertanyaan.index') }}">Pertanyaan di Video</a></li>
                <li><a href="{{ route('kom.user_video_progress.index') }}">Progress / Video</a></li>
                <li><a href="{{ route('kom.user_modul_progress.index') }}">Progress / Modul</a></li>
                <li><a href="{{ route('kom.user_jawaban_pertanyaan.index') }}">Jawaban User</a></li>
                <li><a href="{{ route('kom.rayon.index') }}">Daftar Rayon</a></li>
                <li><a href="{{ route('kom.kode_buku.index') }}">Input Kode Buku</a></li>
                <li><a href="{{ route('kom.certificates.index') }}">Sertifikat</a></li>
                <li><a href="{{ route('kom.kom_class.index') }}">Input Kelas Kom</a></li>
                <li><a href="{{ route('kom.modul.index') }}">Input Modul</a></li>
                <li><a href="{{ route('kom.video.index') }}">Input Video</a></li>
                <li><a href="{{ route('kom.viewMutasiAdminKom') }}">Donasi</a></li>
            </ul>
        </li>
    @endif
</ul>
