@extends('layouts.index')
@section('title', 'Tambah Data KKN')
@section('styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Tambah data KKN</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen KKN</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('kkn.index') }}">Daftar KKN</a></li>
                                <li class="breadcrumb-item active">Tambah data KKN</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="basic-pills-wizard" class="twitter-bs-wizard">
                                <ul class="twitter-bs-wizard-nav">
                                    <li class="nav-item">
                                        <a href="#informasi" class="nav-link" data-toggle="tab">
                                            <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Informasi KKN">
                                                <i class="bx bx-list-ul"></i>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#import-excel" class="nav-link" data-toggle="tab">
                                            <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Import Excel">
                                                <i class="mdi mdi-file-excel-outline"></i>
                                            </div>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="#bidang-proker" class="nav-link" data-toggle="tab">
                                            <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Data Bidang KKN">
                                                <i class="mdi mdi-clipboard-list-outline"></i>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                                <!-- wizard-nav -->

                                <div class="tab-content twitter-bs-wizard-tab-content">
                                    <div class="tab-pane" id="informasi">
                                        <div class="text-center mb-4">
                                            <h5>Informasi KKN</h5>
                                            <p class="card-title-desc">Lengkapi semua informasi mengenai KKN di bawah ini!
                                            </p>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card bg-info-subtle">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Jika anda mengisi data KKN dengan nama yang
                                                            sudah
                                                            tersedia pada database, maka data tidak akan ditambahkan!</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="nama" class="form-label">Nama
                                                        <span class="text-danger" id="text-nama">*</span></label>
                                                    <input type="text" class="form-control" id="nama"
                                                        placeholder="e.g KKN Reguler 99">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="thn_ajaran" class="form-label">Tahun ajaran
                                                        <span class="text-danger" id="text-thn_ajaran">*</span></label>
                                                    <input type="text" class="form-control" id="thn_ajaran"
                                                        placeholder="e.g 2024/2025">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai<span
                                                            class="text-danger" id="text-tanggal_mulai">*</span></label>
                                                    <input type="date" class="form-control datepicker-basic"
                                                        id="tanggal_mulai" placeholder="Masukkan tanggal mulai">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai<span
                                                            class="text-danger" id="text-tanggal_selesai">*</span></label>
                                                    <input type="date" class="form-control datepicker-basic"
                                                        id="tanggal_selesai" placeholder="Masukkan tanggal selesai">
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="pager wizard twitter-bs-wizard-pager-link">
                                            <li class="next"><a href="javascript: void(0);"
                                                    class="btn btn-primary">Berikutnya
                                                    <i class="bx bx-chevron-right ms-1"></i></a></li>
                                        </ul>
                                    </div>
                                    <!-- tab pane -->
                                    <div class="tab-pane" id="import-excel">
                                        <div>
                                            <div class="text-center mb-4">
                                                <h5>Import Excel Data Pengguna</h5>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card bg-info-subtle">
                                                        <div class="card-body">
                                                            <h6>Silahkan upload file excel data KKN sesuai format. Untuk
                                                                melihat format file excel data KKN <a
                                                                    href="{{ route('file.download', ['filename' => 'example.xlsx']) }}">klik
                                                                    disini</a></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="mb-3">
                                                        <label for="file_excel" class="form-label">Import file
                                                            (.xlsx) <span class="text-danger"
                                                                id="text-file"></span></label>
                                                        <input type="file" class="form-control" id="file_excel"
                                                            placeholder="Masukkan file">
                                                    </div>
                                                </div>
                                            </div>
                                            <ul class="pager wizard twitter-bs-wizard-pager-link">
                                                <li class="previous"><a href="javascript: void(0);"
                                                        class="btn btn-primary"><i class="bx bx-chevron-left me-1"></i>
                                                        Sebelumnya</a></li>
                                                <li class="next"><a href="javascript: void(0);"
                                                        class="btn btn-primary">Berikutnya <i
                                                            class="bx bx-chevron-right ms-1"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- tab pane -->
                                    <div class="tab-pane" id="bidang-proker">
                                        <div>
                                            <div class="text-center mb-4">
                                                <h5>Bidang Program Kerja</h5>
                                                <p class="card-title-desc">Apakah bidang proker KKN di bawah ini sudah
                                                    benar?</p>
                                            </div>
                                            <div id="fieldsContainer">

                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <button class="btn btn-soft-primary" id="tambah-bidang"><i
                                                            class="bx bx-plus"></i> Tambah
                                                        bidang</button>
                                                </div>
                                            </div>
                                            <ul class="pager wizard twitter-bs-wizard-pager-link">
                                                <li class="previous"><a href="javascript: void(0);"
                                                        class="btn btn-primary"><i class="bx bx-chevron-left me-1"></i>
                                                        Sebelumnya</a></li>
                                                <li class="float-end"><a href="javascript: void(0);"
                                                        class="btn btn-primary" id="save-change" data-bs-toggle="modal"
                                                        data-bs-target=".confirmModal">Simpan Data</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- tab pane -->
                                </div>
                                <!-- end tab content -->
                            </div>
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->
    <!-- Modal -->
    <div class="modal fade confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center" id="modal-status">

                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-light w-md" data-bs-dismiss="modal">Kembali</button>
                    <button type="button" class="btn btn-primary w-md" id="btn-confirm">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end modal -->
@endsection
@section('pageScript')
    <!-- twitter-bootstrap-wizard js -->
    <script src="{{ asset('assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}"></script>
    <script src="{{ asset('assets/libs/twitter-bootstrap-wizard/prettify.js') }}"></script>

    <!-- form wizard init -->
    <script src="{{ asset('assets/js/pages/form-wizard.init.js') }}"></script>
    <!-- datepicker js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>


    <script src="{{ asset('assets/js/init/administrator/create-kkn.init.js') }}"></script>

@endsection
