@extends('layouts.index')
@section('title', 'Daftar KKN')
@section('styles')
    <!-- DataTables -->
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
                        <h4 class="mb-sm-0 font-size-18">Daftar KKN</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen KKN</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('kkn.index') }}">Daftar KKN</a></li>
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
                            <a href="{{ route('kkn.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i>
                                Tambah data KKN</a>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Status</th>
                                        <th>Evaluasi Mahasiswa</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kkn as $k)
                                        <tr>
                                            <td>{{ $k->nama }}</td>
                                            <td>{{ $k->thn_ajaran }}</td>
                                            <td class="formatTanggal">{{ $k->tanggal_mulai }}</td>
                                            <td class="formatTanggal">{{ $k->tanggal_selesai }}</td>
                                            <td>
                                                <span class="badge bg-{{ $k->status_color }}">
                                                    {{ $k->status_text }}
                                                </span>
                                            </td>
                                            <td>
                                                <a class="btn btn-info btn-sm text-decoration-none"
                                                    href="{{ route('admin.evaluasi.index', ['kkn_id' => $k->id]) }}"><i
                                                        class="bx bx-show me-1"></i>Evaluasi
                                                    Mahasiswa</a>
                                            </td>
                                            <td>
                                                <a class="btn btn-warning text-decoration-none"
                                                    href="/kkn/edit/{{ $k->id }}"><i
                                                        class="bx bx-pencil me-1"></i>Edit
                                                    KKN</a>
                                                <a class="btn btn-primary text-decoration-none"
                                                    href="/kkn/detail/{{ $k->id }}"><i
                                                        class="bx bx-detail me-1"></i>Detail
                                                    KKN</a>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

        <script>
            $(document).ready(function () {
                $('#datatable').DataTable({
                    initComplete: function () {
                        // Add export button to the left of search filter
                        var exportBtn = '<a href="{{ route("admin.evaluasi.export", ["kkn_id" => "all"]) }}" class="btn btn-success btn-sm me-2"><i class="bx bx-download me-1"></i>Export All Excel</a>';
                        $('.dataTables_filter').prepend(exportBtn);
                    }
                });
            });
        </script>
    @endsection
