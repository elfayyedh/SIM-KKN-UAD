@extends('layouts.index')

@section('title', 'Daftar Mahasiswa')
@section('styles')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">DAFTAR MAHASISWA</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Pengguna</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Daftar Mahasiswa</a></li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <p class="fw-bold">Total Mahasiswa <span class="text-muted">({{ $mahasiswa->count() }})</span></p>
                </div>
            </div>

            <div class="row">
                <div class="col-12 table-responsive">
                    <x-alert-component />
                    <table id="datatable" class="table table-bordered nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Jenis Kelamin</th>
                                <th>No HP</th>
                                <th>KKN</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mahasiswa as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->userRole->user->nama ?? '-' }}</td>
                                    <td>{{ $item->userRole->user->email ?? '-' }}</td>
                                    <td>{{ $item->userRole->user->jenis_kelamin ?? '-' }}</td>
                                    <td><a target="_blank"
                                            href="https://wa.me/{{ $item->userRole->user->no_telp }}">{{ $item->userRole->user->no_telp ?? '-' }}</a></td>
                                    <td>{{ $item->kkn->nama_kkn ?? '-' }}</td>
                                    <td>
                                        <a class="btn btn-secondary btn-sm" href="{{ route('user.edit', $item->userRole->user->id) }}"><i
                                                class="bx bx-edit me-1">Edit</i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('pageScript')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#datatable").DataTable();
        });
    </script>
@endsection
