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
            </div>
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
                                class="d-none d-xl-inline-block ms-1 text-muted fw-light font-size-10">
                                @if ($activeRoleName == 'dpl')
                                    DPL
                                @elseif ($activeRoleName == 'monev')
                                    Tim Monev
                                @else
                                    {{ $activeRoleName }}
                                @endif
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
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}"><i
                            class="mdi mdi-logout font-size-16 align-middle me-1"></i>
                        Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>

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
                            <li><a href="{{ route('kkn.create') }}"><span data-key="t-kkn">Tambah data KKN</span></a></li>
                            <li><a href="{{ route('kkn.index') }}"><span data-key="t-chat">Daftar data KKN</span></a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="users"></i>
                            <span data-key="t-users">Manajemen Pengguna</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('user.admin') }}" data-key="t-akun">Admin</a></li>
                            <li><a href="{{ route('dosen.index') }}" data-key="t-dosen">Dosen</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i class="mdi mdi-account-tie-outline"></i>
                            <span data-key="t-dpl">Manajemen DPL</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('dpl.index') }}" data-key="t-daftar-dpl">Daftar DPL</a></li>
                            <li><a href="{{ route('dpl.create') }}" data-key="t-tambah-dpl">Tambah DPL</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i class="mdi mdi-account-tie-voice-outline"></i>
                            <span data-key="t-tim-monev">Manajemen Tim Monev</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('tim-monev.index') }}" data-key="t-daftar-tim-monev">Daftar Tim Monev</a></li>
                            <li><a href="{{ route('tim-monev.create') }}" data-key="t-tambah-tim-monev">Tambah Tim Monev</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('evaluasi.index') }}">
                            <i data-feather="check-square"></i>
                            <span data-key="t-evaluasi">Evaluasi Monev</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="file-text"></i>
                            <span data-key="t-pages">Manajemen Informasi</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('informasi.pengumuman') }}" data-key="t-starter-page">Pengumuman</a></li>
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
                            <li><a href="{{ route('kalender') }}" data-key="t-starter-page">Kalender kegiatan </a></li>
                        </ul>
                    </li>
                    <li><a href="javascript: void(0);" class="has-arrow"> <i data-feather="briefcase"></i><span
                                data-key="t-apps">Program kerja </span></a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('proker.unit') }}" data-key="t-level-2-1">Bersama</a></li>
                            <li><a href="{{ route('proker.individu') }}" data-key="t-level-2-2">Individu</a></li>
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
                @elseif ($activeRoleName == 'dpl')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="layers"></i>
                            <span data-key="t-pages">Manajemen Unit</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('unit.index') }}" data-key="t-starter-page">Unit Bimbingan </a></li>
                            <li><a href="{{ route('kalender') }}" data-key="t-starter-page">Kalender kegiatan </a></li> 
                        </ul>
                    </li>
                @elseif ($activeRoleName == 'monev')
                    <li>
                        <a href="{{ route('monev.evaluasi.index') }}"> 
                            <i data-feather="check-square"></i>
                            <span data-key="t-pages">Evaluasi Unit</span>
                        </a>
                    </li>
                @endif
                @if (session('user_is_dosen', false))
                    @if(session('user_has_role_dpl') && session('user_has_role_monev'))
                        <li class="menu-title" data-key="t-ganti-peran">Ganti Peran Dosen</li>
                        @if(session('active_role') == 'dpl')
                            <li>
                                <form action="{{ route('dosen.role.switch') }}" method="POST" id="switch-to-monev-form">
                                    @csrf
                                    <input type="hidden" name="role" value="monev">
                                    <a href="javascript:;" onclick="document.getElementById('switch-to-monev-form').submit();">
                                        <i class="mdi mdi-account-switch-outline"></i>
                                        <span>Masuk sebagai Tim Monev</span>
                                    </a>
                                </form>
                            </li>
                        @else
                            <li>
                                <form action="{{ route('dosen.role.switch') }}" method="POST" id="switch-to-dpl-form">
                                    @csrf
                                    <input type="hidden" name="role" value="dpl">
                                    <a href="javascript:;" onclick="document.getElementById('switch-to-dpl-form').submit();">
                                        <i class="mdi mdi-account-switch-outline"></i>
                                        <span>Masuk sebagai DPL</span>
                                    </a>
                                </form>
                            </li>
                        @endif
                    @endif
                @else
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
