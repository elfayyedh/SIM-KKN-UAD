<meta charset="utf-8" />
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $roleName = 'Guest';
    if (Auth::check()) {
        if (session('user_is_dosen', false)) {
            $activeDosenRole = session('active_role'); // 'dpl' atau 'monev'
            if ($activeDosenRole == 'dpl') {
                $roleName = 'DPL';
            } elseif ($activeDosenRole == 'monev') {
                $roleName = 'Tim Monev';
            }
        } else {
            $activeUserRole = Auth::user()->userRoles->find(session('selected_role'));
            if ($activeUserRole && $activeUserRole->role) {
                $roleName = $activeUserRole->role->nama_role;
            }
        }
    }
@endphp
<meta name="user-role" content="{{ $roleName }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta content="Sistem Informasi Manajemen Kuliah Kerja Nyata Universitas Ahmad Dahlan" name="description" />
<meta content="Themesbrand" name="author" />
<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
