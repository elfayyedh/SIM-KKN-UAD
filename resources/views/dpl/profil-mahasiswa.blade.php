@extends('layouts.index')

@section('title', 'Profil | ' . $user->userRole->user->nama)
@section('styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <style>
        .dark-mode .table-secondary {
            background-color: #343a40;
            color: #fff;
        }

        .dark-mode .table-light {
            background-color: #343a40;
            color: #fff;
        }
    </style>
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
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">PROFIL MAHASISWA</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Unit</a></li>
                                <li class="breadcrumb-item">Profil mahasiswa</li>
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
                                            <h5 class="font-size-22 mb-1">{{ $user->userRole->user->nama }}</h5>
                                            <p class="text-muted fd-flexont-size-13">{{ $user->nim }}</p>
                                            <p class="text-muted fd-flexont-size-13 mb-0">Program Studi :
                                                {{ $user->prodi->nama_prodi }}</p>
                                            <p class="text-muted fd-flexont-size-13 mb-0">Nomor Telpon :
                                                <a
                                                    href="https://wa.me/{{ $user->userRole->user->no_telp }}">{{ $user->userRole->user->no_telp }}</a>
                                            </p>
                                            <p class="text-muted fd-flexont-size-13 mb-0">Gender :
                                                {{ $user->userRole->user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                            </p>
                                            <p class="text-muted fd-flexont-size-13 mb-0">Unit : {{ $user->unit->nama }}</p>
                                            <p class="text-muted fd-flexont-size-13 mb-0">Total JKEM :
                                                {{ $user->total_jkem }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-auto order-1 order-sm-2">
                                    <div class="d-flex align-items-start justify-content-end gap-2">
                                        <div>

                                            @if (auth()->user()->userRoles->find(session('selected_role'))->role->nama_role == 'Mahasiswa' &&
                                                    auth()->user()->userRoles->find(session('selected_role'))->mahasiswa->id == $user->id)
                                                <a class="btn btn-secondary"
                                                    href="{{ route('user.edit', $user->userRole->user->id) }}">Edit</a>
                                            @endif
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
                                    <a class="nav-link px-3" data-bs-toggle="tab" href="#logbook_harian"
                                        role="tab">Logbook
                                        harian</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link px-3" data-bs-toggle="tab" href="#logbook_sholat"
                                        role="tab">Logbook
                                        sholat</a>
                                </li>
                            </ul>
                        </div>
                        <!-- end card body -->
                    </div> <!-- end card detail unit -->

                    {{-- Tab content --}}
                    <div class="tab-content">
                        <div class="tab-pane active" id="program_kerja" role="tabpanel">
                            <div class="card">
                                <div class="card-body" id="program_kerja_mahasiswa">
                                    <table
                                        class="table table-bordered table-hover table-responsive text-nowrap nowrap w-100">
                                        <tbody>
                                            @for ($i = 0; $i < 5; $i++)
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
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- end tab pane -->

                        <div class="tab-pane" id="logbook_harian" role="tabpanel">
                            <div class="card">
                                <div class="card-body table-responsive">
                                    <table
                                        class="table table-bordered table-hover table-responsive text-nowrap nowrap w-100">
                                        <thead class="table-secondary text-center text-nowrap">
                                            <tr>
                                                <th class="p-0 align-middle" rowspan="2">Kegiatan</th>
                                                <th class="p-0 align-middle" rowspan="2">Jam</th>
                                                <th class="p-0 align-middle" rowspan="2">Bidang</th>
                                                <th class="p-0 align-middle" rowspan="2">Jenis</th>
                                                <th class="p-0 align-middle" rowspan="2">Total JKEM</th>
                                                <th class="p-0 align-middle" rowspan="2">Deskripsi</th>
                                                <th class="p-0 align-middle" colspan="5">Pendanaan</th>
                                            </tr>
                                            <tr>
                                                <th class="p-0 align-middle">Mas</th>
                                                <th class="p-0 align-middle">Mhs</th>
                                                <th class="p-0 align-middle">PT</th>
                                                <th class="p-0 align-middle">Pem</th>
                                                <th class="p-0 align-middle">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="logbook_harian-container">
                                            @for ($i = 0; $i < 5; $i++)
                                                <tr>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span></div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span></div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span></div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span></div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span></div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span></div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span></div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span></div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span></div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span></div>
                                                    </td>
                                                    <td>
                                                        <div class="placeholder-glow"><span
                                                                class="placeholder col-12"></span></div>
                                                    </td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- end tab pane -->

                        <div class="tab-pane mb-3" id="logbook_sholat" role="tabpanel">
                            <div class="col-12 mb-3 mt-3">
                                <a href="{{ route('logbook.sholat.getPDF', ['id' => $user->id, 'tanggal_penerjunan' => $user->unit->tanggal_penerjunan, 'tanggal_penarikan' => $user->unit->tanggal_penarikan != null ? $user->unit->tanggal_penarikan : $user->kkn->tanggal_selesai]) }}"
                                    class="btn btn-dark btn-md"><i class="bx bx-printer me-1"></i>Print</a>
                            </div>
                            <div class="card mb-0" style="overflow-x: auto">
                                <table
                                    class="table table-bordered table-hover table-striped table-responsive text-nowrap nowrap w-100">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Keterangan</th>
                                            <th>Jumlah Jamaah</th>
                                            <th>Nama Imam</td>
                                        </tr>
                                    </thead>
                                    <tbody id="logbook-sholat-container">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- end tab pane -->
                    </div> {{-- End Tab content --}}
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="id_mahasiswa" value="{{ $user->id }}">
    <input type="hidden" id="id_unit" value="{{ $user->id_unit }}">
    <input type="hidden" id="id_kkn" value="{{ $user->id_kkn }}">
    <input type="hidden" id="tanggal_penerjunan" value="{{ $user->unit->tanggal_penerjunan }}">
    <input type="hidden" id="tanggal_penarikan"
        value="{{ $user->unit->tanggal_penarikan != null ? $user->unit->tanggal_penarikan : $user->kkn->tanggal_selesai }}">
@endSection
@section('pageScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
    <script src="{{ asset('assets/js/init/mahasiswa/profil-mahasiswa/getProkerMahasiswa.init.js') }}"></script>
    <script src="{{ asset('assets/js/init/mahasiswa/profil-mahasiswa/read-logbook-sholat.init.js') }}"></script>
    <script src="{{ asset('assets/js/init/mahasiswa/profil-mahasiswa/read-logbook-harian.init.js') }}"></script>
@endsection
