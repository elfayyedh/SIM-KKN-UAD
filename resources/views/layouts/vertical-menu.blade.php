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

                                <span
                                    class="d-none d-xl-inline-block ms-1 text-muted fw-light font-size-10">{{ Auth::user()->roles()->first()->nama_role }}</span>
                            </div>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </div>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <a class="dropdown-item" href="{{ route('user.show') }}"><i
                                class="mdi mdi mdi-face-man font-size-16 align-middle me-1"></i>
                            Profile</a>
                        @if (Auth::user()->userRoles->count() > 1)
                            <a class="dropdown-item" href="{{ route('choose.role') }}"><i
                                    class="mdi mdi-repeat-variant font-size-16 align-middle me-1"></i>
                                Ganti role </a>
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

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Menu</li>

                <li>
                    <a href="{{ route('dashboard') }}">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                @if (Auth::user()->userRoles->find(session('selected_role'))->role->nama_role == 'Admin')
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
                        {{-- <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('user.create') }}" data-key="t-akun">Tambah Pengguna baru</a></li>
                        </ul> --}}
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
                @elseif (Auth::user()->userRoles->find(session('selected_role'))->role->nama_role == 'Mahasiswa')
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
                @elseif (Auth::user()->userRoles->find(session('selected_role'))->role->nama_role == 'DPL')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="layers"></i>
                            <span data-key="t-pages">Manajemen Unit</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('unit.index') }}" data-key="t-starter-page">Unit Bimbingan </a></li>
                            <!-- <li><a href="{{ route('kalender') }}" data-key="t-starter-page">Kalender kegiatan </a> -->
                            </li>
                        </ul>
                    </li>

                {{-- 👇👇 ----- TAMBAHAN UNTUK TIM MONEV ----- 👇👇 --}}
                @elseif (Auth::user()->userRoles->find(session('selected_role'))->role->nama_role == 'Tim Monev')
                    {{-- Ini menu-menu khusus Tim Monev --}}
                    <li>
                        {{-- GANTI route('...') dengan route Monev-mu yang asli --}}
                        <a href="#"> 
                            <i data-feather="check-square"></i>
                            <span data-key="t-pages">Evaluasi DPL</span>
                        </a>
                    </li>
                    <li>
                        {{-- GANTI route('...') dengan route Monev-mu yang asli --}}
                        <a href="#"> 
                            <i data-feather="file-text"></i>
                            <span data-key="t-pages">Laporan Monev</span>
                        </a>
                    </li>
                {{-- 👆👆 ----- SELESAI BAGIAN TIM MONEV ----- 👆👆 --}}

                @endif

                {{-- ================================================= --}}
                {{-- === BAGIAN GANTI PERAN (KODE BARU DARI SNIPPET) === --}}
                {{-- ================================================= --}}

                @php
                    // 1. Ambil SEMUA role yang dimiliki user ini (dari tabel user_role)
                    // (Asumsi di Model User ada relasi 'userRoles',
                    // dan di Model UserRole ada relasi 'role' untuk dapat 'nama_role')
                    $allRoles = Auth::user()->userRoles()->with('role')->get();
                    
                    // 2. Ambil ID user_role yang lagi AKTIF dari session
                    $currentRoleId = session('selected_role');
                @endphp

                {{-- 3. Cuma tampilkan menu "Ganti Peran" kalo dia punya > 1 role --}}
                @if ($allRoles->count() > 1)
                    
                    {{-- Ini adalah judul pemisah (kayak "Menu" di sidebar) --}}
                    <li class="menu-title" data-key="t-ganti-peran">Ganti Peran</li>

                    {{-- 4. Loop semua role-nya --}}
                    @foreach ($allRoles as $availableRole)
                    
                        {{-- 5. Tampilkan link HANYA untuk role yang TIDAK AKTIF --}}
                        @if ($availableRole->id != $currentRoleId)
                            <li>
                                {{-- Link ini manggil route 'set.role' yang udah kita bikin --}}
                                <a href="{{ route('set.role', $availableRole->id) }}">
                                    <i data-feather="refresh-cw"></i> {{-- Ganti icon-nya kalau perlu --}}
                                    <span>Masuk sebagai {{ $availableRole->role->nama_role }}</span>
                                </a>
                            </li>
                        @endif

                    @endforeach
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
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
