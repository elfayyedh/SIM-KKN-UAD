<meta charset="utf-8" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="user-role" content="{{ Auth::check() ? Auth::user()->userRoles->find(session('selected_role'))->role->nama_role : 'guest' }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta content="Sistem Informasi Manajemen Kuliah Kerja Nyata Universitas Ahmad Dahlan" name="description" />
<meta content="Themesbrand" name="author" />
<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
