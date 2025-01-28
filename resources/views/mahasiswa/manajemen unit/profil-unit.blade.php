@extends('layouts.index')

@section('title', 'Profil Unit')
@section('styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <style>
        /* Default light mode */
        .card-anggota:hover {
            background-color: #f8f9fa;
        }

        /* Dark mode */
        .dark-mode .card-anggota:hover {
            background-color: #343a40;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <style>
        .vertical-text {
            -webkit-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg);
            -ms-transform: rotate(-90deg);
            -o-transform: rotate(-90deg);
        }

        .table-proker,
        .table-content-border {
            width: 100%;
        }

        .table-proker thead tr th {
            border: solid 1px #aaaaaa;
        }

        .table-proker tbody tr td {
            border: solid 1px #aaaaaa;
        }
    </style>
    <style>
        .highlight {

            background-color: yellow;
        }

        .dark-mode .highlight {
            background-color: #D0DB5E;
        }

        .table-content-border th,
        .table-content-border td {
            border: 1px solid black;
        }

        .align-middle {
            vertical-align: middle;
        }

        .bidang_row {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .dark-mode .bidang_row {
            background-color: #343a40;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }
    </style>
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">PROFIL UNIT</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Unit</a></li>
                                <li class="breadcrumb-item">Profil unit</li>
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
                                            <h5 class="font-size-22 mb-1">Unit {{ $unit->nama }}</h5>
                                            <p class="text-muted fd-flexont-size-13">{{ $unit->dpl->userRole->user->nama }}
                                            </p>
                                            <p class="text-muted mb-1"> <i class="mdi mdi-map-marker"></i>
                                                {{ $unit->lokasi->nama }}, {{ $unit->lokasi->kecamatan->kabupaten->nama }}
                                            </p>
                                            <p class="text-muted mb-1"> <i class="mdi mdi-marker"></i>
                                                {{ $unit->kkn->nama }}
                                            </p>
                                            <p class="text-muted mb-1 "><i
                                                    class="mdi mdi-calendar-arrow-left"></i>Penerjunan : <span
                                                    class="formatTanggal">
                                                    {{ $unit->tanggal_penerjunan }}</span>
                                            </p>
                                            <p class="text-muted mb-1">
                                                <i class="mdi mdi-calendar-arrow-right"></i> Penarikan:
                                                {!! $unit->tanggal_penarikan != null
                                                    ? '<span class="formatTanggal">' . $unit->tanggal_penarikan . '</span>'
                                                    : '-' !!}
                                            </p>


                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-auto order-1 order-sm-2">
                                    <div class="d-flex align-items-start justify-content-end gap-2">
                                        <div>
                                            <a href="{{ route('unit.edit', $unit->id) }}"
                                                class="btn btn-link text-secondary rounded border">Edit Unit</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <ul class="nav nav-tabs-custom card-header-tabs border-top mt-4" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link px-3 active" data-bs-toggle="tab" href="#program_kerja"
                                        role="tab">Program kerja</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link px-3" data-bs-toggle="tab" href="#anggota" role="tab">Anggota</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link px-3 " data-bs-toggle="tab" href="#matriks" role="tab">Matriks
                                        kegiatan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link px-3" data-bs-toggle="tab" href="#rekap" role="tab">Rekap
                                        kegiatan</a>
                                </li>
                            </ul>
                        </div>
                        <!-- end card body -->
                    </div> <!-- end card detail unit -->

                    {{-- Tab content --}}
                    <div class="tab-content">
                        <div class="tab-pane active" id="program_kerja" role="tabpanel">
                            <div class="card">
                                <div class="card-header">
                                    <a class="btn btn-dark"
                                        href="{{ route('proker.exportProker', ['id' => $unit->id]) }}"><i
                                            class="bx bxs-file-export"></i>Export Excel</a>
                                    <a class="btn btn-dark"
                                        href="{{ route('proker.exportProkerPDF', ['id' => $unit->id]) }}"><i
                                            class="mdi mdi-file-export"></i>Export PDF</a>
                                </div>
                                <div class="card-body" id="program_kerja_unit">
                                    <input type="hidden" id="id_unit" value="{{ $unit->id }}">
                                    <input type="hidden" id="id_kkn" value="{{ $unit->id_kkn }}">
                                    @for ($i = 0; $i < 5; $i++)
                                        <table
                                            class="table table-bordered table-hover table-responsive text-nowrap nowrap w-100">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <!-- end tab pane -->

                        <div class="tab-pane" id="anggota" role="tabpanel">
                            <div class="card">

                                <div class="card-body table-anggota" style="overflow-x: auto">

                                </div>
                            </div>
                        </div>
                        <!-- end tab pane -->

                        <div class="tab-pane" id="matriks" role="tabpanel">
                            <div class="card">
                                <div class="card-header">
                                    {{-- <a class="btn btn-dark"
                                        href="{{ route('unit.export-matriks', ['id_unit' => $unit->id, 'id_kkn' => $unit->id_kkn]) }}"><i
                                            class="bx bxs-file-export"></i>Export Excel</a> --}}
                                </div>
                                <div class="card-body" style="overflow-x: auto;">
                                    <table class="table-content-border" style="width: 100%;">
                                        <thead class="bidang_row">
                                            <tr class="align-middle text-center">
                                                <th rowspan="2" class="fw-bold fs-3">PROGRAM</th>
                                                <th>Hari</th>
                                                <!-- Tanggal kolom akan diisi oleh jQuery -->
                                            </tr>
                                            <tr class="align-middle text-center">
                                                <th class="text-nowrap">Tanggal/Bulan</th>
                                                <!-- Tanggal kolom akan diisi oleh jQuery -->
                                            </tr>
                                        </thead>
                                        <tbody id="program-body">
                                            <!-- Konten tbody akan diisi oleh jQuery -->
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                        <!-- end tab pane -->

                        <div class="tab-pane" id="rekap" role="tabpanel">
                            <div class="card">
                                <div class="card-body table-responsive" id="rekap_kegiatan">
                                    @for ($i = 0; $i < 5; $i++)
                                        <table
                                            class="table table-bordered table-hover table-responsive text-nowrap nowrap w-100">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <!-- end tab pane -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="id_kkn" value="{{ $unit->id_kkn }}">
    <input type="hidden" id="id_unit" value="{{ $unit->id }}">
    <input type="hidden" id="tanggal_penerjunan-unit" value="{{ $unit->tanggal_penerjunan }}">
    <input type="hidden" id="tanggal_penarikan-unit"
        value="{{ $unit->tanggal_penarikan != null ? $unit->tanggal_penarikan : $unit->kkn->tanggal_selesai }}">
@endSection
@section('pageScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
    <script src="{{ asset('assets/js/init/mahasiswa/unit/getProker.init.js') }}"></script>
    <script src="{{ asset('assets/js/init/mahasiswa/unit/getProkerUnitPdf.init.js') }}"></script>
    <script src="{{ asset('assets/js/init/mahasiswa/unit/getAnggota.init.js') }}"></script>
    <script src="{{ asset('assets/js/init/mahasiswa/unit/matriks.init.js') }}"></script>
    <script src="{{ asset('assets/js/init/mahasiswa/unit/read-rekap-kegiatan.init.js') }}"></script>
@endsection
