@php
    $activeRoleName = ''; // Default
    if (Auth::check()) {
        if (session('user_is_dosen', false)) {
            $activeRoleName = session('active_role'); // Ini akan berisi 'dpl' atau 'monev'
        } else {
            $activeUserRole = Auth::user()->userRoles->find(session('selected_role'));
            if ($activeUserRole && $activeUserRole->role) {
                $activeRoleName = $activeUserRole->role->nama_role;
            }
        }
    }
@endphp
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('dashboard') }}" title="Dashboard" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo.svg') }}" alt="" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-light-full.svg') }}" alt="" height="34">
                    </span>
                </a>

                <a href="{{ route('dashboard') }}" title="Dashboard" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo.svg') }}" alt="" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-dark-full.svg') }}" alt="" height="34">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item" title="Toggle Sidebar"
                id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>

        </div>

        <div class="d-flex">

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item" title="Toggle dark mode" id="mode-setting-btn">
                    <i class="bx bx-moon icon-lg layout-mode-dark" style="font-size: 19px;"></i>
                    <i class="bx bx-sun icon-lg layout-mode-light" style="font-size: 19px;"></i>
                </button>
            </div>

            <div class="dropdown">
                <button type="button" title="application" class="btn header-item d-none d-sm-inline-block"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-grid icon-lg" style="font-size: 19px;"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <div class="p-2">
                        <div class="row g-0">


                            <div class="row g-0">
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="{{ asset('') }}" alt="uad">
                                        <span>LPPM UAD</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="{{ asset('') }}" alt="uad">
                                        <span>PORTAL UAD</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="{{ asset('') }}" alt="uad">
                                        <span>KKN UAD</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="dropdown d-inline-block">
                    <button type="button" class="btn header-item noti-icon position-relative"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="bx bx-bell icon-lg"></i>
                        <span class="badge bg-danger rounded-pill">5</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-3">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0"> Notifications </h6>
                                </div>
                                <div class="col-auto">
                                    <a href="#!" class="small text-reset text-decoration-underline">
                                        Unread (3)</a>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 230px;">
                            <a href="#!" class="text-reset notification-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <img src="{{ asset('assets/images/users/avatar-3.jpg') }}"
                                            class="rounded-circle avatar-sm" alt="user-pic">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">James_Lemire</h6>
                                        <div class="font-size-13 text-muted">
                                            <p class="mb-1">
                                                It_will_seem_like_simplified_English.</p>
                                            <p class="mb-0"><i class="mdi mdi-clock-outline"></i>
                                                <span>1_hours_ago</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <a href="#!" class="text-reset notification-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-sm me-3">
                                        <span class="avatar-title bg-primary rounded-circle font-size-16">
                                            <i class="bx bx-cart"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Your_order_is_placed</h6>
                                        <div class="font-size-13 text-muted">
                                            <p class="mb-1">
                                                If_several_languages_coalesce_the_grammar</p>
                                            <p class="mb-0"><i class="mdi mdi-clock-outline"></i>
                                                <span>3_min_ago</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <a href="#!" class="text-reset notification-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-sm me-3">
                                        <span class="avatar-title bg-success rounded-circle font-size-16">
                                            <i class="bx bx-badge-check"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Your_item_is_shipped</h6>
                                        <div class="font-size-13 text-muted">
                                            <p class="mb-1">
                                                If_several_languages_coalesce_the_grammar</p>
                                            <p class="mb-0"><i class="mdi mdi-clock-outline"></i>
                                                <span>3_min_ago</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <a href="#!" class="text-reset notification-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <img src="{{ asset('assets/images/users/avatar-6.jpg') }}"
                                            class="rounded-circle avatar-sm" alt="user-pic">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Salena_Layfield</h6>
                                        <div class="font-size-13 text-muted">
                                            <p class="mb-1">
                                                As_a_skeptical_Cambridge_friend_of_mine_occidental.
                                            </p>
                                            <p class="mb-0"><i class="mdi mdi-clock-outline"></i>
                                                <span>1_hours_ago</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="p-2 border-top d-grid">
                            <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
                                <i class="mdi mdi-arrow-right-circle me-1"></i>
                                <span>View_More</span>
                            </a>
                        </div>
                    </div>
                </div> --}}
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn header-item bg-light-subtle border-start border-end"
                        id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <div class="d-flex">
                            <i class="mdi mdi-account-circle icon-lg me-2" style="font-size: 27px"></i>
                            <div class="d-flex flex-column align-items-start">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium"
                                    style="max-width: 100px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ Auth::user()->nama }}
                                </span>

                                @php
                                    if (session('user_is_dosen')) {
                                        $activeRoleName = (session('active_role') == 'dpl') ? 'DPL' : 'Tim Monev';
                                    } else {
                                        $activeRole = Auth::user()->userRoles->find(session('selected_role'));
                                        $activeRoleName = $activeRole ? $activeRole->role->nama_role : 'Guest';
                                    }
                                @endphp
                                <span
                                    class="d-none d-xl-inline-block ms-1 text-muted fw-light font-size-10">
                                    {{ $activeRoleName }}
                                </span>
                            </div>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </div>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('user.show') }}">
                            <i class="mdi mdi-account-circle-outline font-size-16 align-middle me-1"></i>
                            <span>Profil</span>
                        </a>
                        @if(session('user_is_dosen'))
                            @if(session('user_has_role_dpl') && session('user_has_role_monev'))
                                <div class="dropdown-divider"></div>
                                @if(session('active_role') == 'dpl')
                                    <form action="{{ route('dosen.role.switch') }}" method="POST" class="dropdown-item">
                                        @csrf
                                        <input type="hidden" name="role" value="monev">
                                        <button type="submit" class="btn btn-link p-0 m-0">
                                            <i class="mdi mdi-account-switch-outline"></i>
                                            <span>Masuk sebagai Tim Monev</span>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('dosen.role.switch') }}" method="POST" class="dropdown-item">
                                        @csrf
                                        <input type="hidden" name="role" value="dpl">
                                        <button type="submit" class="btn btn-link p-0 m-0">
                                            <i class="mdi mdi-account-switch-outline"></i>
                                            <span>Masuk sebagai DPL</span>
                                        </button>
                                    </form>
                                @endif
                            @endif

                        @else
                            @php
                                $allRoles = Auth::user()->userRoles()->with('role')->get();
                                $currentRoleId = session('selected_role');
                            @endphp
                            @if ($allRoles->count() > 1)
                                <div class="dropdown-divider"></div>
                                @foreach ($allRoles as $availableRole)
                                    @if ($availableRole->id != $currentRoleId)
                                        <a class="dropdown-item" href="{{ route('set.role', $availableRole->id) }}">
                                            <i class="mdi mdi-account-switch-outline"></i>
                                            <span>Masuk sebagai {{ $availableRole->role->nama_role }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        @endif
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}"><i
                                class="mdi mdi-logout font-size-16 align-middle me-1"></i>
                            Logout</a>
                    </div>
                </div>
            </div>
        </div>
</header>

<!-- ========== Left Sidebar Start ========== -->
<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Menu</li>
                <li>
                    <a href="{{ route('dashboard') }}">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>
                @if ($activeRoleName == 'Admin')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="grid"></i>
                            <span data-key="t-apps">Manajemen KKN</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('kkn.create') }}">
                                    <span data-key="t-kkn">Tambah data KKN</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('kkn.index') }}">
                                    <span data-key="t-chat">Daftar data KKN</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="users"></i>
                            <span data-key="t-users">Manajemen Pengguna</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('user.admin') }}" data-key="t-akun">Admin</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="users"></i>
                            <span data-key="t-dpl">Manajemen DPL</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('dpl.index') }}" data-key="t-daftar-dpl">Daftar DPL</a></li>
                            <li><a href="{{ route('dpl.create') }}" data-key="t-tambah-dpl">Tambah DPL</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="user-check"></i>
                            <span data-key="t-tim-monev">Manajemen Tim Monev</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('tim-monev.index') }}" data-key="t-daftar-tim-monev">Daftar Tim Monev</a></li>
                            <li><a href="{{ route('tim-monev.create') }}" data-key="t-tambah-tim-monev">Tambah Tim Monev</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="file-text"></i>
                            <span data-key="t-pages">Manajemen Informasi</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('informasi.pengumuman') }}" data-key="t-starter-page">Pengumuman
                                </a></li>
                            <li><a href="{{ route('informasi.faq') }}" data-key="t-maintenance">FAQ</a></li>
                        </ul>
                    </li>
                @elseif ($activeRoleName == 'Mahasiswa')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="layers"></i>
                            <span data-key="t-pages">Manajemen Unit</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('unit.show') }}" data-key="t-starter-page">Profil unit </a></li>
                            <li><a href="{{ route('kalender') }}" data-key="t-starter-page">Kalender kegiatan </a>
                            </li>
                        </ul>
                    </li>
                    <li><a href="javascript: void(0);" class="has-arrow"> <i data-feather="briefcase"></i><span
                                data-key="t-apps">Program
                                kerja </span></a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('proker.unit') }}" data-key="t-level-2-1">Bersama</a></li>
                            <li><a href="{{ route('proker.individu') }}" data-key="t-level-2-2">Individu</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('logbook.index') }}">
                            <i data-feather="clipboard"></i>
                            <span data-key="t-pages">Logbook Harian</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('logbook.sholat') }}">
                            <i data-feather="clock"></i>
                            <span data-key="t-pages">Logbook Sholat</span>
                        </a>
                    </li>
                @elseif ($activeRoleName == 'DPL')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="layers"></i>
                            <span data-key="t-pages">Manajemen Unit</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('unit.index') }}" data-key="t-starter-page">Unit Bimbingan </a></li>
                            <li><a href="{{ route('kalender') }}" data-key="t-starter-page">Kalender kegiatan </a></li>
                            <li><a href="{{ route('dpl.unit.index') }}" data-key="t-starter-page">Unit Bimbingan </a></li>
                        </ul>
                    </li>
                @elseif ($activeRoleName == 'Tim Monev')
                    <li>
                        <a href="{{ route('monev.evaluasi.index') }}"> 
                            <i data-feather="check-square"></i>
                            <span data-key="t-pages">Evaluasi Unit</span>
                        </a>
                    </li>
                @endif
                @if (!session('user_is_dosen', false))
                    @php
                        $allRoles = Auth::user()->userRoles()->with('role')->get();
                        $currentRoleId = session('selected_role');
                    @endphp

                    @if ($allRoles->count() > 1)
                        <li class="menu-title" data-key="t-ganti-peran">Ganti Peran</li>
                        @foreach ($allRoles as $availableRole)
                            @if ($availableRole->id != $currentRoleId)
                                <li>
                                    <a href="{{ route('set.role', $availableRole->id) }}">
                                        <i class="mdi mdi-account-switch-outline"></i>
                                        <span>{{ $availableRole->role->nama_role }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endif
                <li class="menu-title" data-key="t-menu">Informasi</li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="book"></i>
                        <span data-key="t-pages">Informasi</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('informasi.pengumuman.view') }}" data-key="t-starter-page">Pengumuman
                            </a></li>
                </li>
                <li><a href="{{ route('informasi.faq.view') }}" data-key="t-starter-page">FAQ </a></li>
            </ul>
            </li>
            </ul>
        </div>
    </div>
</div>
<!-- Left Sidebar End -->