@extends('layouts.index')
@section('title', 'Logbook Kegiatan | ' . $data['mahasiswa']->userRole->user->nama)
@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.default.min.css" rel="stylesheet" />
    <style>
        .clickable-card:hover .card {
            background-color: #f8f9fa;
            /* Change to your desired hover background color */
            transition: background-color 0.3s ease;
        }

        .dark-mode .clickable-card:hover .card {
            background-color: #343a40;
            /* Change to your desired hover background color */
            transition: background-color 0.3s ease;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />



    <!-- alertifyjs Css -->
    <link href="{{ asset('assets/libs/alertifyjs/build/css/alertify.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- alertifyjs default themes  Css -->
    <link href="{{ asset('assets/libs/alertifyjs/build/css/themes/default.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">LOGBOOK KEGIATAN<span data-bs-toggle="modal"
                                data-bs-target=".bs-example-modal-center"><i
                                    class="bx bx-help-circle text-secondary"></i></span>
                        </h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Logbook</a></li>
                                <li class="breadcrumb-item">Logbook kegiatan</li>
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

                            <div class="gap-0">
                                <h5 class="font-size-22 mb-1 formatTanggal">{{ $data['tanggal'] }}</h5>
                                <p class="text-muted fd-flexont-size-13">{{ $data['mahasiswa']->userRole->user->nama }}</p>
                                <div>
                                    <div class="d-flex gap-3">
                                        <button id="addNew" class="btn btn-success mb-3" data-bs-target="#dataModal"
                                            data-bs-toggle="modal">Tambah kegiatan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Content --}}
                <div class="row">
                    @if (session('error') || session('success'))
                        <div class="col-12">
                            <div class="alert {{ session('error') ? 'alert-danger' : 'alert-success' }} alert-dismissible fade show"
                                role="alert">
                                {{ session('error') ?? session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        </div>
                    @endif
                    <div class="col-12">
                        <div class="table-responsive">
                            <table
                                class="table table-bordered table-striped table-hover dt-responsive nowrap w-100 datatable">
                                <thead class="align-middle text-center">
                                    <tr>
                                        <th class="text-center p-1">No</th>
                                        <th class="text-center p-1">Jam</th>
                                        <th class="text-center p-1">Kegiatan</th>
                                        <th class="text-center p-1">Bidang</th>
                                        <th class="text-center p-1">Jenis</th>
                                        <th class="text-center p-1">Total JKEM</th>
                                        <th class="text-center p-1">Deskripsi</th>
                                        <th class="text-center p-1">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['kegiatan'] as $k)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $k->jam_mulai . ' - ' . $k->jam_selesai }}</td>
                                            <td>{{ $k->kegiatan->nama }}</td>
                                            <td>{{ $k->kegiatan->proker->bidang->nama }}</td>
                                            <td>{{ ucfirst($k->jenis) }}</td>
                                            <td>{{ $k->total_jkem }}</td>
                                            <td class="text-wrap">{{ $k->deskripsi ? $k->deskripsi : '-' }}</td>
                                            <td>
                                                <a class="btn btn-primary  btn-edit" data-id="{{ $k->id }}"
                                                    data-jam_mulai="{{ $k->jam_mulai }}"
                                                    data-jam_selesai="{{ $k->jam_selesai }}"
                                                    data-id_kegiatan="{{ $k->id_kegiatan }}"
                                                    data-kegiatan="{{ $k->kegiatan->nama }}"
                                                    data-bidang="{{ $k->kegiatan->proker->bidang->nama }}"
                                                    data-total_jkem="{{ $k->total_jkem }}"
                                                    data-deskripsi="{{ $k->deskripsi }}"><i class="bx bx-pencil"></i></a>
                                                <a href="javascript:void(0)" data-id="{{ $k->id }}"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    class="btn btn-danger  btn-delete"><i class="bx bx-trash"></i></a>
                                                <button class="btn btn-success" data-bs-toggle="modal" id="table-dana"
                                                    data-bs-target="#modalPendanaan" data-id="{{ $k->id }}">
                                                    <i class="bx bx-money"></i>
                                                </button>

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

        <div class="modal fade modal-xl" id="dataModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dataModalLabel">Add/Edit Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('logbook.saveKegiatan') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" id="id" name="id">
                            <div class="form-group">
                                <input type="hidden" id="status" name="status" value="tambah">
                                <input type="hidden" id="id_logbook_kegiatan" name="id_logbook_kegiatan">
                                <label for="jam">Jam</label>
                                <div class="d-flex gap-1 align-items-center">
                                    <input type="text" id="start-time" placeholder="Jam mulai"
                                        class="form-control datepicker-timepicker" id="jam" name="jam_mulai"
                                        required>
                                    <i class="bx bx-chevron-right"></i>
                                    <input id="end-time" type="text" placeholder="Jam selesai"
                                        class="form-control datepicker-timepicker" id="waktu" name="jam_selesai"
                                        required>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label for="kegiatan">Pilih Kegiatan</label>
                                <select class="" id="kegiatan_select" name="id_kegiatan" required>
                                    <option value="">--Pilih Kegiatan--</option>
                                </select>
                            </div>
                            <input type="hidden" id="id_mahasiswa" name="id_mahasiswa"
                                value="{{ $data['mahasiswa']->id }}">
                            <input type="hidden" id="id_logbook_harian" name="id_logbook_harian"
                                value="{{ $data['logbook']->id }}">
                            <div class="row mt-3 mb-3">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="bidang">Bidang</label>
                                        <input type="text" class="form-control" readonly id="bidang" required>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="jkem" class="form-label">JKEM</label>
                                        <input type="number" class="form-control" readonly id="jkem"
                                            name="total_jkem" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="des" class="form-label">Deskripsi <span
                                        class="text-muted">(opsional)</span></label>
                                <textarea name="deskripsi" class="form-control" id="des" cols="30" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Petunjuk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-center">Belum ada petunjuk</p>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus kegiatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="bx bx-error-circle font-size-24 text-danger"></i>
                        <p class="text-center">Apakah anda yakin ingin menghapus kegiatan ini? data pendanaan juga akan
                            dihapus!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form action="{{ route('logbook.deleteKegiatan') }}" method="POST">
                            @csrf
                            @method('delete')
                            <input type="hidden" name="id" id="id_delete">
                            <button type="submit" class="btn btn-danger" id="btn-confirm-delete">Hapus</button>
                        </form>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade" aria-hidden="true" tabindex="-1" role="dialog" aria-labelledby="modalPendanaan"
            id="modalPendanaan" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Dana Kegiatan</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <div class="row mb-3 mt-3">
                            <div class="col-12">
                                <button class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addPendanaanModal">
                                    Tambah pendanaan
                                </button>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jumlah</th>
                                    <th>Sumber</th>
                                </tr>
                            </thead>
                            <tbody id="table_pendanaan_modal">
                                <tr>
                                    <td>
                                        <div class="placeholder-glow"><span class="placeholder col-12"></span></div>
                                    </td>
                                    <td>
                                        <div class="placeholder-glow"><span class="placeholder col-12"></span></div>
                                    </td>
                                    <td>
                                        <div class="placeholder-glow"><span class="placeholder col-12"></span></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <div class="placeholder-glow"><span class="placeholder col-12"></span></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Modal Tambah Pendanaan -->
        <div class="modal fade" id="addPendanaanModal" tabindex="-1" role="dialog"
            aria-labelledby="addPendanaanModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPendanaanModalLabel">Tambah Pendanaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('logbook.savePendanaan') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_logbook_kegiatan" id="id_logbook_kegiatan_modal">
                            <div class="form-group">
                                <label for="currency-mask" class="form-label">Jumlah <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Masukkan Jumlah"
                                    id="currency-mask" required>
                                <input type="hidden" name="jumlah" id="hidden-amount">
                                <input type="hidden" name="id_unit" value="{{ $data['mahasiswa']->id_unit }}">
                            </div>
                            <div class="form-group">
                                <label for="sumber">Sumber dana <span class="text-danger">*</span></label>
                                <select class="form-select" id="sumber" name="sumber" required>
                                    <option value="PT">Perguruan Tinggi</option>
                                    <option value="Mhs">Mahasiswa/Pribadi</option>
                                    <option value="Mas">Masyarakat</option>
                                    <option value="Pem">Pemerintah</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <input type="hidden" id="kota" value="{{ $data['mahasiswa']->unit->lokasi->kecamatan->kabupaten->nama }}">
        <input type="hidden" id="tanggal" value="{{ $data['tanggal'] }}">
        <input type="hidden" id="id_unit" value="{{ $data['mahasiswa']->unit->id }}">
    @endsection
    @section('pageScript')
        <!-- Required datatable js -->
        <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

        <!-- Responsive examples -->
        <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>


        <!-- datepicker js -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
        <!-- alertify js -->
        <script src="{{ asset('assets/libs/imask/imask.min.js') }}"></script>

        <script src="{{ asset('assets/js/pages/selectize.min.js') }}"></script>

        <script src="{{ asset('assets/libs/alertifyjs/build/alertify.min.js') }}"></script>
        <script src="{{ asset('assets/js/init/mahasiswa/logbook-kegiatan/create-logbook-kegiatan.init.js') }}"></script>

    @endsection
