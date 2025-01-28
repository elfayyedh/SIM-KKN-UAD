@extends('layouts.index')
@section('title', 'Logbook Kegiatan | ' . $data['mahasiswa']->userRole->user->nama)
@section('styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
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
                        <h4 class="mb-sm-0 font-size-18">LOGBOOK SHOLAT BERJAMAAH<span data-bs-toggle="modal"
                                data-bs-target=".bs-example-modal-center"><i
                                    class="bx bx-help-circle text-secondary"></i></span>
                        </h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Logbook</a></li>
                                <li class="breadcrumb-item">Logbook sholat berjamaah</li>
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
                                        <div class="d-flex flex-column align-items-center" title="Sholat Berjamaah">
                                            <div class="icons-Subuh"></div>
                                            <p>Subuh</p>
                                        </div>
                                        <div class="d-flex flex-column align-items-center" title="Sholat Berjamaah">
                                            <div class="icons-Dzuhur"></div>
                                            <p>Dzuhur</p>
                                        </div>
                                        <div class="d-flex flex-column align-items-center"
                                            title="Belum diisi atau tidak Sholat Berjamaah">
                                            <div class="icons-Ashar"></div>
                                            <p>Ashar</p>
                                        </div>
                                        <div class="d-flex flex-column align-items-center" title="Sedang halangan">
                                            <div class="icons-Maghrib"></div>
                                            <p>Maghrib</p>
                                        </div>
                                        <div class="d-flex flex-column align-items-center" title="Sedang halangan">
                                            <div class="icons-Isya"></div>
                                            <p>Isya'</p>
                                        </div>
                                    </div>
                                    <p class="font-size-10 mb-0 text-muted" style="opacity: 0.6;">Isi data logbook sholat
                                        jamaah dibawah ini</p>
                                    @if ($data['mahasiswa']->userRole->user->jenis_kelamin == 'P')
                                        <div class="d-flex gap-3">
                                            <button id="btn-halangan-hari-ini" class="btn btn-secondary btn-sm mt-3">Hari
                                                ini
                                                saya
                                                sedang halangan</button>
                                            <div class="spinner"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="row">
                                @foreach (['Isya', 'Maghrib', 'Ashar', 'Dzuhur', 'Subuh'] as $waktu)
                                    <div class="col-12 mb-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4>{{ $waktu }}</h4>
                                                <select disabled name="logbook[{{ $waktu }}][status]"
                                                    class="form-select logbook-select" data-waktu="{{ $waktu }}">
                                                    <option value="belum diisi">--Belum diisi--</option>
                                                    <option value="sholat berjamaah">Sholat Berjamaah</option>
                                                    @if ($data['mahasiswa']->userRole->user->jenis_kelamin == 'P')
                                                        <option value="sedang halangan">Sedang Halangan</option>
                                                    @endif
                                                    <option value="tidak sholat berjamaah">Tidak Sholat Berjamaah</option>
                                                </select>
                                                <div class="row mt-4 d-none logbook-details"
                                                    id="details-{{ $waktu }}">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="nama_imam_{{ $waktu }}"
                                                                class="form-label">Nama Imam</label>
                                                            <input type="text" class="form-control detail-input"
                                                                data-waktu="{{ $waktu }}"
                                                                name="logbook[{{ $waktu }}][nama_imam]"
                                                                id="nama_imam_{{ $waktu }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="jumlah_jamaah_{{ $waktu }}"
                                                                class="form-label">Jumlah Jamaah</label>
                                                            <select name="logbook[{{ $waktu }}][jumlah_jamaah]"
                                                                data-waktu="{{ $waktu }}"
                                                                class="form-select detail-input"
                                                                id="jumlah_jamaah_{{ $waktu }}">
                                                                <option value="<20">&lt;20</option>
                                                                <option value="20-50">20 - 50</option>
                                                                <option value=">50">&gt;50</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
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
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Tanda</th>
                                <th>Deskripsi</th>
                            </tr>
                            <tr>
                                <td><i class="bx bx-check-circle text-success"></i></td>
                                <td>Logbook sholat dilengkapi</td>
                            </tr>
                            <tr>
                                <td><i class="bx bx-x-circle text-danger"></i></td>
                                <td>Tidak sholat berjamaah</td>
                            </tr>
                            <tr>
                                <td><i class="bx bx-info-circle text-warning"></i></td>
                                <td>Belum diisi</td>
                            </tr>
                            <tr>
                                <td><i class="bx bx-minus-circle text-secondary"></i></td>
                                <td>Sedang halangan</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <input type="hidden" id="id_mahasiswa" value="{{ $data['mahasiswa']->id }}">
    <input type="hidden" id="kota" value="{{ $data['mahasiswa']->unit->lokasi->kecamatan->kabupaten->nama }}">
    <input type="hidden" id="tanggal" value="{{ $data['tanggal'] }}">
@endsection
@section('pageScript')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>

    <!-- alertify js -->
    <script src="{{ asset('assets/libs/alertifyjs/build/alertify.min.js') }}"></script>
    <script src="{{ asset('assets/js/init/mahasiswa/logbook-sholat/read-add-logbook.init.js') }}"></script>
@endsection
