@extends('layouts.index')
@section('title', $kkn->nama)
@section('styles')
    <link href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
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
                        <h4 class="mb-sm-0 font-size-18">DETAIL KKN</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen KKN</a></li>
                                <li class="breadcrumb-item"><a href="/kkn">Daftar KKN</a></li>
                                <li class="breadcrumb-item active">Detail KKN</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm order-2 order-sm-1">
                                    <div class="d-flex align-items-start">
                                        <div class="gap-0">
                                            <h5 class="font-size-22 mb-1">{{ $kkn->nama }}</h5>
                                            <p class="text-muted fd-flexont-size-13">Thn. Ajaran {{ $kkn->thn_ajaran }}</p>
                                            <p class="text-muted mb-1">Mulai : <span
                                                    class="formatTanggal">{{ $kkn->tanggal_mulai }}</span>
                                            </p>
                                            <p class="text-muted mb-1 ">Selesai : <span
                                                    class="formatTanggal">{{ $kkn->tanggal_selesai }}</span></p>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-auto order-1 order-sm-2">
                                    <div class="d-flex align-items-start justify-content-end gap-2">
                                        <div>
                                            <div class="dropdown">
                                                <a class="btn-link text-muted font-size-16 shadow-none dropdown-toggle"
                                                    href="#" role="button" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="bx bx-dots-horizontal-rounded"></i>
                                                </a>

                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('kkn.edit', $kkn->id) }}">Edit</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <ul class="nav nav-tabs-custom card-header-tabs border-top mt-4" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link px-3 active" data-bs-toggle="tab" href="#mahasiswa"
                                        role="tab">Mahasiswa</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link px-3" data-bs-toggle="tab" href="#dpl" role="tab">DPL</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link px-3" data-bs-toggle="tab" href="#unit" role="tab">Unit</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link px-3" data-bs-toggle="tab" href="#tim-monev" role="tab">Tim
                                        Monev</a>
                                </li>
                            </ul>
                        </div>
                        <!-- end card body -->
                    </div> <!-- end card detail unit -->
                    {{-- Tab content --}}
                    <div class="tab-content">
                        <div class="tab-pane active" id="mahasiswa" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <x-mahasiswa-table :mahasiswa="$kkn->mahasiswa" />
                                </div>
                            </div>
                        </div>
                        <!-- end tab pane -->

                        <div class="tab-pane" id="dpl" role="tabpanel">
                            <div class="card">
                                <div class="card-body" style="overflow-x: auto;">
                                    <x-dpl-table :dpl="$kkn->dpl" />
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end tab pane -->

                        <div class="tab-pane" id="unit" role="tabpanel">
                            <div class="card">
                                <div class="card-body" style="overflow-x: auto;">
                                    <x-unit-table :units="$kkn->units" />
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end tab pane -->

                        <div class="tab-pane" id="tim-monev" role="tabpanel">
                            <div class="card">
                                <div class="card-body" style="overflow-x: auto;">
                                    <x-tim-monev-table :timMonev="$kkn->timMonev" />
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end tab pane -->


                    </div>
                    <!-- end tab content -->
                </div>
            </div>

        </div> <!-- container-fluid -->
    </div>


@endsection
@section('pageScripts')
@section('pageScript')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Buttons examples -->
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    {{-- Init --}}
    <script src="{{ asset('assets/js/init/administrator/detail-kkn.init.js') }}"></script>

@endsection
@endsection