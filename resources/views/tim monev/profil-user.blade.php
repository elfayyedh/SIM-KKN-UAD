@extends('layouts.index')
@php
    $activeRoleName = ''; // Default
    if (Auth::check()) {
        if (session('user_is_dosen', false)) {
            $activeRoleName = session('active_role'); // 'dpl' atau 'monev'
        } else {
            $activeUserRole = Auth::user()->userRoles->find(session('selected_role'));
            if ($activeUserRole && $activeUserRole->role) {
                $activeRoleName = $activeUserRole->role->nama_role;
            }
        }
    }
@endphp

@section('pageStyle')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('title', 'Profil Tim Monev | ' . ($user->nama ?? 'Tim Monev')) 
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">PROFIL TIM MONEV</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Profil Tim Monev</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>         
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm order-2 order-sm-1">
                                <div class="d-flex align-items-start">
                                    <div class="gap-0">
                                        <h5 class="font-size-22 mb-1">{{ $user->nama ?? 'Nama Tim Monev' }}</h5>
                                        <p class="text-muted fd-flexont-size-13">{{ $user->email ?? '-' }}</p>
                                        <p class="text-muted fd-flexont-size-13 mb-0">NIP: {{ $dosen->nip ?? '-' }}</p> 
                                        <p class="text-muted fd-flexont-size-13 mb-0">Nomor Telpon :
                                            @if($user->no_telp)
                                                <a href="https://wa.me/{{ $user->no_telp }}" target="_blank">{{ $user->no_telp }}</a>
                                            @else
                                                -
                                            @endif
                                        </p>
                                        <p class="text-muted fd-flexont-size-13 mb-0">Gender :
                                            @if($user->jenis_kelamin)
                                                {{ $user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div> 
                                </div>
                            </div>
                            <div class="col-sm-auto order-1 order-sm-2">
                                <div class="d-flex align-items-start justify-content-end gap-2">
                                    <div>
                                        @if ($activeRoleName == 'monev')
                                            <a class="btn btn-secondary"
                                                href="{{ route('user.edit', $user->id) }}">Edit</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div> 
@endsection
@section('pageScript')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.datatable-buttons').DataTable({
                "responsive": true, 
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/English.json"
                }
            });
        });
    </script>
@endSection