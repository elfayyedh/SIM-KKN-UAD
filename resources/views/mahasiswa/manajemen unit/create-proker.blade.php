@extends('layouts.index')
@section('title', 'Tambah Proker | ' . $unit->nama)
@section('styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <style>
        table.table-detail-info {
            width: 100%;
        }

        table.table-detail-info tbody tr {
            border-bottom: 1px solid #dee2e6;
        }

        table.table-detail-info tbody tr:hover {
            background-color: #f8f9fa;
        }

        h6._info {
            opacity: 0.7;
        }
    </style>
    <!-- Script Selectize.js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.default.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">TAMBAH PROKER</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Unit</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('proker.unit') }}">Proker Bersama</a></li>
                                <li class="breadcrumb-item active">Tambah proker</li>
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
                                        <a href="#tab_proker" class="nav-link" data-toggle="tab">
                                            <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Program Kerja">
                                                <i class="mdi mdi-folder-outline"></i>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#tab_kegiatan" class="nav-link" data-toggle="tab">
                                            <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Kegiatan">
                                                <i class="mdi mdi-view-list-outline"></i>
                                            </div>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="#tab_peran" class="nav-link" data-toggle="tab">
                                            <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Peran mahasiswa">
                                                <i class="mdi mdi-account-multiple-check-outline"></i>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#tab_review" class="nav-link" data-toggle="tab">
                                            <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="review">
                                                <i class="mdi mdi-clipboard-list-outline"></i>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                                <!-- wizard-nav -->

                                <div class="tab-content twitter-bs-wizard-tab-content">
                                    {{-- Tab proker --}}
                                    <div class="tab-pane" id="tab_proker">
                                        <div class="text-center mb-4">
                                            <h5>Program Kerja</h5>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="bidang_proker" class="form-label">Bidang
                                                        Proker<span class="text-danger" id="error_bidang">*</span></label>
                                                    <select id="bidang_proker" class="form-select">
                                                        @foreach ($bidang_proker as $bidang)
                                                            <option value="{{ $bidang->id }}">
                                                                {{ $bidang->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="program" class="form-label">Program<span
                                                            class="text-danger" id="error_program">*</span></label>
                                                    {{-- Select2 --}}
                                                    <select id="program" class="proker w-100">

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="program" class="form-label">Tempat<span class="text-danger"
                                                            id="error_tempat">*</span></label>
                                                    <input type="text" id="tempat" required class="form-control"
                                                        placeholder="contoh: Rumah pak dukuh">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="bidang_program" class="form-label">Sasaran<span
                                                            class="text-danger" id="error_sasaran">*</span></label>
                                                    <input type="text" class="form-control" name="sasaran"
                                                        id="sasaran" placeholder="contoh: Ibu rumah tangga ">
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="pager wizard twitter-bs-wizard-pager-link">
                                            <li class="next"><a href="javascript: void(0);"
                                                    class="btn btn-primary">Berikutnya
                                                    <i class="bx bx-chevron-right ms-1"></i></a></li>
                                        </ul>
                                    </div>

                                    {{-- Tab kegiatan --}}
                                    <div class="tab-pane" id="tab_kegiatan">
                                        <div class="text-center mb-4">
                                            <h5>Kegiatan proker</h5>
                                        </div>
                                        <div id="listKegiatan">
                                            <div class="row border kegiatan-row pt-3 mb-3">
                                                <div class="col-12">
                                                    <div class="row">
                                                        <div class="col">
                                                            <h6 class="mb-3 text-secondary">#Kegiatan
                                                                ke-1</h6>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label for="kegiatan" class="form-label">Nama kegiatan
                                                                    <span class="text-danger"
                                                                        id="error_kegiatan">*</span></label>
                                                                <input type="text" required class="form-control"
                                                                    name="kegiatan" id="kegiatan"
                                                                    placeholder="Nama kegiatan">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <div class="mb-3">
                                                                <label for="frekuensi" class="form-label">Frekuensi
                                                                    <span class="text-danger"
                                                                        id="error_frekuensi">*</span></label>
                                                                <input type="number" min="1"
                                                                    class="form-control frekuensi" name="frekuensi"
                                                                    required id="frekuensi">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <div class="mb-3">
                                                                <label for="jkem" class="form-label">JKEM
                                                                    <span class="text-danger" id="error_jkem"
                                                                        id="error_jkem">*</span></label>
                                                                <select name="jkem" required id="jkem"
                                                                    class="form-select jkem">
                                                                    <option value="50">50</option>
                                                                    <option value="100">100</option>
                                                                    <option value="150">150</option>
                                                                    <option value="200">200</option>
                                                                    <option value="250">250</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="mb-3">
                                                                <label for="totalJKEM" class="form-label">Total
                                                                    JKEM <span id="error_totalJKEM"></span></label>
                                                                <input type="text" min="1" readonly disabled
                                                                    class="form-control totalJKEM" name="totalJKEM"
                                                                    id="totalJKEM">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label for="tanggal_kegiatan" class="form-label">Tanggal
                                                                    Kegiatan
                                                                    <span class="text-danger"
                                                                        id="error_tanggal_kegiatan">*</span> <span
                                                                        class="text-muted small">(Pilih tanggal
                                                                        tanggal sesuai jumlah
                                                                        frekuensi)</span></label>
                                                                <input type="text" required data-flatpickr
                                                                    class="form-control tanggal_kegiatan"
                                                                    name="tanggal_kegiatan" id="tanggal_kegiatan">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3 mb-3">
                                            <div class="col">
                                                <button class="btn btn-soft-primary" id="addKegiatan"><i
                                                        class="bx bx-plus me-1"></i>Tambah
                                                    Kegiatan</button>
                                            </div>
                                        </div>
                                        <ul class="pager wizard twitter-bs-wizard-pager-link">
                                            <li class="previous"><a href="javascript: void(0);"
                                                    class="btn btn-primary"><i class="bx bx-chevron-left me-1"></i>
                                                    Sebelumnya</a></li>
                                            <li class="next" ><a href="javascript: void(0);"
                                                    class="btn btn-primary">Berikutnya <i
                                                        class="bx bx-chevron-right ms-1"></i></a></li>
                                        </ul>
                                    </div>

                                    {{-- Tab peran --}}
                                    <div class="tab-pane" id="tab_peran">
                                        {{-- Konten --}}
                                        <div class="text-center mb-4">
                                            <h5>Peran Mahasiswa</h5>
                                        </div>
                                        <div class="peran_Mahasiswa">
                                            <div class="row">
                                                @foreach ($mahasiswa as $item)
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <input type="hidden" class="nama_anggota"
                                                                value="{{ $item->userRole->user->nama }}">
                                                            <label for="peran_{{ $item->id }}" class="form-label">
                                                                Peran <span
                                                                    class="text-muted">{{ $item->userRole->user->nama }}</span>
                                                            </label>
                                                            <input id="peran_{{ $item->id }}"
                                                                class="select_peran nama_peran w-100 form-control">
                                                        </div>
                                                    </div>
                                                    @if (($loop->index + 1) % 3 == 0 && !$loop->last)
                                            </div>
                                            <div class="row">
                                                @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        <ul class="pager wizard twitter-bs-wizard-pager-link">
                                            <li class="previous"><a href="javascript: void(0);"
                                                    class="btn btn-primary"><i class="bx bx-chevron-left me-1"></i>
                                                    Sebelumnya</a></li>
                                            <li class="next" id="peranNextButton"><a href="javascript: void(0);"
                                                    class="btn btn-primary">Berikutnya <i
                                                        class="bx bx-chevron-right ms-1"></i></a></li>
                                        </ul>
                                    </div>

                                    {{-- Tab Review --}}
                                    <div class="tab-pane" id="tab_review">
                                        <div class="text-center mb-4">
                                            <h5>Review</h5>
                                        </div>
                                        {{-- Konten --}}
                                        <div id="reviewsContainer">
                                            <div class="row">
                                                <div class="col-12">
                                                    <table class="table-detail-info">
                                                        <tbody>
                                                            <tr>
                                                                <td class="p-1">Bidang</td>
                                                                <td class="p-1">:</td>
                                                                <td class="p-1" id="data-review_bidang">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="p-1">Program</td>
                                                                <td class="p-1">:</td>
                                                                <td class="p-1" id="data-review_program">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="p-1">Total JKEM</td>
                                                                <td class="p-1">:</td>
                                                                <td class="p-1" id="data-review_totalJKEM"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="p-1">Tempat</td>
                                                                <td class="p-1">:</td>
                                                                <td class="p-1" id="data-review_tempat">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="p-1">Sasaran</td>
                                                                <td class="p-1">:</td>
                                                                <td class="p-1" id="data-review_sasaran">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                    <h6 class="_info mt-4 fw-bold mb-3 text-primary">
                                                        # Daftar
                                                        Kegiatan</h6>
                                                    <div class="review_daftar-kegiatain w-100" style="overflow-x: auto;">
                                                        <table
                                                            class="table table-bordered table-responsive text-nowrap nowrap w-100 mt-1">
                                                            <thead>
                                                                <tr>
                                                                    <th class="p-1 text-center align-middle"
                                                                        rowspan="2">
                                                                        Kegiatan</th>
                                                                    <th class="p-1 text-center align-middle"
                                                                        colspan="3">Ekuivalensi JKEM
                                                                        (menit)
                                                                    </th>
                                                                    <th rowspan="2"
                                                                        class="p-1 text-center align-middle">
                                                                        Tanggal Rencana</th>
                                                                    <th rowspan="2"
                                                                        class="p-1 text-center align-middle">
                                                                        Peran mahasiswa</th>

                                                                </tr>
                                                                <tr>
                                                                    <th class="p-1 text-center align-middle">
                                                                        Frekuensi
                                                                    </th>
                                                                    <th class="p-1 text-center align-middle">
                                                                        JKEM</th>
                                                                    <th class="p-1 text-center align-middle">
                                                                        Total JKEM
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="pager wizard twitter-bs-wizard-pager-link">
                                            <li class="previous"><a href="javascript: void(0);"
                                                    class="btn btn-primary"><i class="bx bx-chevron-left me-1"></i>
                                                    Sebelumnya</a></li>
                                            <li class="float-end btnConfirm"><a href="javascript: void(0);"
                                                    class="btn btn-primary" id="save-change" data-bs-toggle="modal"
                                                    data-bs-target=".confirmModal">Simpan
                                                    Data</a></li>
                                        </ul>
                                    </div>
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
    <div class="modal fade confirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
    <input type="hidden" id="tanggal_penerjunan-unit" value="{{ $unit->tanggal_penerjunan }}">
    <input type="hidden" id="id_kkn" value="{{ $unit->id_kkn }}">
    <input type="hidden" id="id_unit" value="{{ $unit->id }}">
    <input type="hidden" id="id_mahasiswa"
        value="{{ Auth::user()->userRoles->find(session('selected_role'))->mahasiswa->id }}">
@endsection
@section('pageScript')
    <!-- twitter-bootstrap-wizard js -->
    <script src="{{ asset('assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}"></script>
    <script src="{{ asset('assets/libs/twitter-bootstrap-wizard/prettify.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>


    <!-- form wizard init -->
    <script src="{{ asset('assets/js/pages/form-wizard.init.js') }}"></script>
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    {{-- Selectize --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js"></script>
    <!-- datepicker js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

    <!-- glightbox js -->
    <script src="{{ asset('assets/libs/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/lightbox.init.js') }}"></script>

    <script src="{{ asset('assets/js/init/mahasiswa/proker-create/wizard-kegiatan.init.js') }}"></script>
    <script src="{{ asset('assets/js/init/mahasiswa/proker-create/wizard-peran.init.js') }}"></script>
    <script src="{{ asset('assets/js/init/mahasiswa/proker-create/wizard-review.init.js') }}"></script>
    <script src="{{ asset('assets/js/init/mahasiswa/proker-create/save-data.init.js') }}"></script>
    <script>
        // Selectize
        const $program = $("#program").selectize({
            create: true,
            sortField: "text",
            placeholder: "Masukkan nama program",
        });
    </script>

@endsection
