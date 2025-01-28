@extends('layouts.index')
@section('title', 'Logbook Kegiatan | ' . $data['mahasiswa']->userRole->user->nama)
@section('styles')
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
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">LOGBOOK SHOLAT BERJAMAAH<span data-bs-toggle="modal"
                                data-bs-target=".bs-example-modal-center" style="cursor: pointer"><i
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
                                <h5 class="font-size-22 mb-1">{{ $data['mahasiswa']->userRole->user->nama }}</h5>
                                <p class="text-muted fd-flexont-size-13">{{ $data['mahasiswa']->nim }}</p>
                                <p class="text-muted fd-flexont-size-13 mb-0">Program Studi :
                                    {{ $data['mahasiswa']->prodi->nama_prodi }}</p>
                                </p>
                                <p class="text-muted fd-flexont-size-13 mb-0">Gender :
                                    {{ $data['mahasiswa']->userRole->user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </p>
                                <p class="text-muted fd-flexont-size-13 mb-0">Unit : {{ $data['mahasiswa']->unit->nama }}
                                </p>
                                <p class="text-muted mb-1 "><i class="mdi mdi-calendar-arrow-left"></i>Penerjunan : <span
                                        class="formatTanggal">
                                        {{ $data['mahasiswa']->unit->tanggal_penerjunan }}</span>
                                </p>
                                <p class="text-muted mb-1">
                                    <i class="mdi mdi-calendar-arrow-right"></i> Penarikan:
                                    {!! $data['mahasiswa']->unit->tanggal_penarikan != null
                                        ? '<span class="formatTanggal">' . $data['mahasiswa']->unit->tanggal_penarikan . '</span>'
                                        : '-' !!}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert info dismissable button -->
            <div class="alert alert-danger alert-dismissible" role="alert">
                <strong>Mohon isi data logbook sholat berjamaah anda!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <!-- Logbook container -->
            <div id="logbook-container">
                @for ($i = 0; $i < 3; $i++)
                    <div>
                        <div class="card">
                            <div class="card-body">
                                <div class="row placeholder-glow mb-2">
                                    <span class="placeholder col-4 col-xl-3 placeholder-lg" style="height: 30px"></span>
                                </div>
                                <div class="row placeholder-glow mb-4">
                                    <span class="placeholder col-5"></span>
                                </div>
                                <div class="row placeholder-glow mb-4 gap-2">
                                    <span class="placeholder col-1"></span>
                                    <span class="placeholder col-1"></span>
                                    <span class="placeholder col-1"></span>
                                    <span class="placeholder col-1"></span>
                                    <span class="placeholder col-1"></span>
                                </div>
                                <div class="row placeholder-glow mb-3">
                                    <span class="placeholder col-4"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
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
    <input type="hidden" id="tanggal_penerjunan" value="{{ $data['tanggal_penerjunan'] }}">
    <input type="hidden" id="tanggal_penarikan" value="{{ $data['tanggal_penarikan'] }}">
@endsection
@section('pageScript')
    <script src="{{ asset('assets/js/init/mahasiswa/logbook-sholat/check-logbook.init.js') }}"></script>
@endsection
