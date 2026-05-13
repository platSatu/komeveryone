<ul>
    <li class="menu-title">Main Menu</li>

    <li>
        <a href="{{ route('dashboard') }}" class="waves-effect">
            <i class="mdi mdi-airplay"></i>
            <span> Dashboard</span>
        </a>
    </li>
    @if (auth()->check())
        @php
            $user = auth()->user();

            // Ambil semua setting menu berdasarkan user_id dan status aktif
            $accessibleMenus = \App\Models\SettingMenu::where('user_id', $user->id)
                ->where('status', 'active')
                ->with('menu')
                ->get();

            // Ambil semua menu_id yang valid dari relasi menu
            $menuIds = $accessibleMenus
                ->filter(function ($item) {
                    return $item->menu !== null;
                })
                ->pluck('menu.id')
                ->toArray();
        @endphp

        {{-- Tampilkan menu berdasarkan menu_id --}}
        @foreach ($menuIds as $menuId)
            @switch($menuId)
                @case(2)
                    <li class="menu-title">EVENTS HMMINISTRY</li>
                    <li><a href="{{ route('registerevents.list') }}" class="waves-effect" target="_blank"><i
                                class="mdi mdi-application"></i><span>Reg Festival</span></a></li>
                     <li><a href="{{ route('registerevents.tribun') }}" class="waves-effect" target="_blank"><i
                                class="mdi mdi-application"></i><span>Reg Tribun</span></a></li>            
                    <!-- <li><a href="{{ route('getListUserEvents') }}" class="waves-effect"><i
                                class="mdi mdi-application"></i><span>User Register</span></a></li>
                    <li><a href="{{ route('showDuplicateRegisterCodes') }}" class="waves-effect"><i
                                class="mdi mdi-application"></i><span>User Barcode</span></a></li> -->
                    <li><a href="{{ route('getListUser') }}" class="waves-effect"><i
                                class="mdi mdi-application"></i><span>Data Ticket</span></a></li>
                    <li><a href="{{ route('scan_logs') }}" class="waves-effect"><i
                                class="mdi mdi-application"></i><span>History</span></a></li>            
                    {{-- <li><a href="{{route('tickets.indexTickets.manual')}}" class="waves-effect"><i
                                class="mdi mdi-application"></i><span>Ticket Manual</span></a></li>             --}}
                @break

                <!-- @case(3)
                    <li class="menu-title">KOM EVERYONE</li>
                    <li><a href="" class="waves-effect"><i
                                class="mdi mdi-account-multiple"></i><span>KOM 100</span></a></li>
                @break

                @case(4)
                    <li><a href="javascript:void(0);" class="waves-effect"><i
                                class="mdi mdi-account-multiple"></i><span>Pengajar</span></a></li>
                @break

                @case(5)
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-account-multiple"></i><span>Admin
                                Cabang</span><span class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="list-unstyled">
                            <li><a href="javascript:void(0);">Daftar Kelas</a></li>
                            <li><a href="javascript:void(0);">Daftar Peserta</a></li>
                            <li><a href="javascript:void(0);">Pengajar</a></li>
                            <li><a href="javascript:void(0);">Donasi</a></li>
                        </ul>
                    </li>
                @break

                @case(6)
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="mdi mdi-settings"></i><span>Setting</span><span class="float-right"><i
                                    class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="list-unstyled">
                            <li><a href="javascript:void(0);">Category Modul</a></li>
                            <li><a href="javascript:void(0);">Modul</a></li>
                            <li><a href="{{ route('cabang.index') }}">Daftar Cabang</a></li>
                            <li><a href="{{ route('listpeserta.index') }}">Daftar Peserta</a></li>
                            <li><a href="{{ route('listpengajar.index') }}">Daftar Pengajar</a></li>
                            <li><a href="javascript:void(0);">Persembahan</a></li>
                        </ul>
                    </li>
                @break -->

                @case(7)
                    <li class="menu-title">HMMINISTRY</li>
                    <li><a href="{{ route('uploads.index') }}" class="waves-effect"><i
                                class="mdi mdi-email"></i><span>Surat</span></a></li>
                @break

                @case(8)
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-apps"></i><span>Warta</span><span
                                class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('warta.index') }}">Warta Edisi</a></li>
                            <li><a href="{{ route('content.index') }}">Warta Article</a></li>
                            <li><a href="{{ route('videowarta.index') }}">Warta Video</a></li>
                            <li><a href="{{ route('eventswarta.index') }}">Warta Special Events</a></li>
                            <li><a href="{{ route('acara.index') }}">Warta Events</a></li>
                            <li><a href="{{ route('popupwarta.index') }}">Warta Popup</a></li>
                        </ul>
                    </li>
                @break

                @case(9)
                    <li><a href="{{ route('subcategoryfile.index') }}" class="waves-effect"><i
                                class="mdi mdi-apps"></i><span>VOP Article</span></a></li>
                @break

                @case(10)
                    <li><a href="{{ route('articles.index') }}" class="waves-effect"><i
                                class="mdi mdi-apps"></i><span>Article</span></a></li>
                @break

                @case(11)
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-apps"></i><span>Media</span><span
                                class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('media.index') }}">Youtube</a></li>
                            <li><a href="{{ route('programtv.index') }}">Program TV</a></li>
                            <li><a href="{{ route('highlights.index') }}">Highlights</a></li>
                        </ul>
                    </li>
                @break

                @case(12)
                    <li><a href="{{ route('kommarketplace.index') }}" class="waves-effect"><i
                                class="mdi mdi-apps"></i><span>Kom Marketplace</span></a></li>
                @break

                @case(13)
                    <li><a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-link"></i><span>Link
                                Shortener</span></a></li>
                @break

                @case(14)
                    <li><a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-link"></i><span>HMMO</span></a>
                    </li>
                @break

                @default
                    {{-- Menu ID tidak dikenal --}}
            @endswitch
        @endforeach
    @endif



    @if (auth()->check() && auth()->user()->role_id == 1)
        <li class="menu-title">Events</li>

         <li><a href="{{ route('registerevents.list') }}" class="waves-effect" target="_blank"><i
                                class="mdi mdi-application"></i><span>Reg Festival</span></a></li>
        <li><a href="{{ route('registerevents.tribun') }}" class="waves-effect" target="_blank"><i
                                class="mdi mdi-application"></i><span>Reg Tribun</span></a></li> 
        <li>
            <a href="{{ route('getListUserEvents') }}" class="waves-effect"><i
                    class="mdi mdi-application"></i><span>User Register </span></a>
        </li>
        <li>
            <a href="{{ route('showDuplicateRegisterCodes') }}" class="waves-effect"><i
                    class="mdi mdi-application"></i><span>Register</span></a>
        </li>
        <li>
            <a href="{{ route('showDuplicateQrcode') }}" class="waves-effect"><i
                    class="mdi mdi-application"></i><span>Qr Code </span></a>
        </li>
        <li>
            <a href="{{ route('getListUser') }}" class="waves-effect"><i
                    class="mdi mdi-application"></i><span>Pembelian Ticket </span></a>
        </li>
        <li>
            <a href="{{route('tickets.indexTickets.manual')}}" class="waves-effect"><i
                    class="mdi mdi-application"></i><span>Ticket Manual </span></a>
        </li>
         <li>
            <a href="{{route('scan_logs')}}" class="waves-effect"><i
                    class="mdi mdi-application"></i><span>History </span></a>
        </li>
        <!-- <li class="menu-title">KOM EVERYONE</li>
        <li>
            <a href="" class="waves-effect"><i
                    class="mdi mdi-account-multiple"></i><span>
                    KOM 100 </span></a>
        </li>
        <li>
            <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-account-multiple"></i><span>
                    Pengajar </span></a>
        </li>
        <li class="has_sub">
            <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-account-multiple"></i> <span>
                    Admin</span> <span class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
            <ul class="list-unstyled">
                <li><a href="javascript:void(0);">Daftar Kelas</a></li>
                <li><a href="javascript:void(0);">Daftar Peserta</a></li>
                <li><a href="javascript:void(0);">Pengajar</a></li>
                <li><a href="javascript:void(0);">Donasi</a></li>
            </ul>
        </li> -->
        <li class="menu-title">GBI GATSU</li>
        <li>
            <a href="{{ route('uploads.index') }}" class="waves-effect"><i class="mdi mdi-email"></i><span> Surat
                </span></a>
        </li>

        <li class="has_sub">
            <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-apps"></i><span> Warta </span>
                <span class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
            <ul class="list-unstyled">
                <li><a href="{{ route('warta.index') }}">Warta Edisi</a></li>
                <li><a href="{{ route('content.index') }}">Warta Article</a></li>
                <li><a href="{{ route('videowarta.index') }}">Warta Video</a></li>
                <li><a href="{{ route('eventswarta.index') }}">Warta Special Events</a></li>
                <li><a href="{{ route('acara.index') }}">Warta events</a></li>
                <li><a href="{{ route('popupwarta.index') }}">Warta Popup</a></li>
            </ul>
        </li>

        <li>
            <a href="{{ route('subcategoryfile.index') }}" class="waves-effect"><i class="mdi mdi-apps"></i><span>
                    VOP Article </span></a>
        </li>

        <li>
            <a href="{{ route('articles.index') }}" class="waves-effect"><i class="mdi mdi-apps"></i><span>
                    Article </span></a>
        </li>

        <li class="has_sub">
            <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-apps"></i><span> Media </span>
                <span class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
            <ul class="list-unstyled">
                <li><a href="{{ route('media.index') }}">Youtube</a></li>
                <li><a href="{{ route('programtv.index') }}">Program Tv</a></li>
                <li><a href="{{ route('highlights.index') }}">Highlights</a></li>
                <li><a href="{{ route('our_services.index') }}">Our Ministry</a></li>
            </ul>
        </li>

        <li>
            <a href="{{ route('kommarketplace.index') }}" class="waves-effect"><i class="mdi mdi-apps"></i><span>
                    Kom Marketplace </span></a>
        </li>

        <li>
            <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-link"></i><span> Link Shortener
                </span></a>
        </li>
        <li>
            <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-link"></i><span> HMMO
                </span></a>
        </li>
        <li class="has_sub">
            <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-settings"></i> <span>
                    Kom Digital</span> <span class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
            <ul class="list-unstyled">
                <li><a href="javascript:void(0);">Category Modul</a></li>
                <li><a href="javascript:void(0);">Modul</a></li>
                <li><a href="{{ route('cabang.index') }}">Daftar Cabang</a></li>
                <li><a href="javascript:void(0);">Daftar Peserta</a></li>
                <li><a href="javascript:void(0);">Daftar Pengajar</a></li>
                <li><a href="javascript:void(0);">Persembahan</a></li>
            </ul>
        </li>

        <li class="has_sub">
            <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-settings"></i><span> Settings
                </span> <span class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
            <ul class="list-unstyled">
                <li><a href="{{ route('user.index') }}">Daftar User</a></li>
                <li><a href="{{ route('roles.index') }}">Roles</a></li>
                <li><a href="{{ route('menus.index') }}">Menu</a></li>
                <li><a href="{{ route('setting_menu.index') }}">Setting Menu</a></li>
                <li><a href="{{ route('visitor.index') }}">Visitor</a></li>
            </ul>
        </li>
    @endif

</ul>
