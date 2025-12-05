@extends('layouts.index')
@section('title', 'Evaluasi Mahasiswa KKN')
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
                        <h4 class="mb-sm-0 font-size-18">Evaluasi Mahasiswa KKN - {{ $kkn->nama }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen KKN</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('kkn.index') }}">Daftar KKN</a></li>
                                <li class="breadcrumb-item active">Evaluasi Mahasiswa</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('kkn.evaluasi.export', $kkn->id) }}" class="btn btn-success"><i class="bx bx-download"></i>
                                Export to Excel</a>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Nama Mahasiswa</th>
                                        <th>NIM</th>
                                        <th>Tim Monev</th>
                                        <th>Evaluasi JKEM</th>
                                        <th>Evaluasi Form 1</th>
                                        <th>Evaluasi Form 2</th>
                                        <th>Evaluasi Form 3</th>
                                        <th>Evaluasi Form 4</th>
                                        <th>Evaluasi Sholat</th>
                                        <th>Catatan Monev</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($evaluasi as $eval)
                                        <tr>
                                            <td>{{ $eval->mahasiswa->userRole->user->name ?? '' }}</td>
                                            <td>{{ $eval->mahasiswa->nim ?? '' }}</td>
                                            <td>{{ $eval->timMonev->dosen->user->name ?? '' }}</td>
                                            <td>{{ $eval->eval_jkem }}</td>
                                            <td>{{ $eval->eval_form1 }}</td>
                                            <td>{{ $eval->eval_form2 }}</td>
                                            <td>{{ $eval->eval_form3 }}</td>
                                            <td>{{ $eval->eval_form4 }}</td>
                                            <td>{{ $eval->eval_sholat }}</td>
                                            <td>{{ $eval->catatan_monev }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('pageScript')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/init/administrator/read-kkn.init.js') }}"></script>
@endsection
