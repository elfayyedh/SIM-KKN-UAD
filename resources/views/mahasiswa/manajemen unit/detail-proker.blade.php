@extends('layouts.index')
@section('title', 'Detail Proker')
@section('styles')
    <style>
        table.table-detail-info {
            width: 100%;
        }

        table.table-detail-info tbody tr {
            border-bottom: 1px solid #dee2e6;
        }

        .dark-mode .table-detail-info tbody tr {
            border-bottom: 1px solid #6c757d;
        }

        table.table-detail-info tbody tr:hover {
            background-color: #f8f9fa;
        }

        .dark-mode table.table-detail-info tbody tr:hover {
            background-color: #343a40;
        }

        h6._info {
            opacity: 0.7;
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
                        <h4 class="mb-sm-0 font-size-18">DETAIL PROKER</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Unit</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('proker.unit') }}">Proker Bersama</a></li>
                                <li class="breadcrumb-item active">Detail Proker</li>
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
                            <h5>Unit <a title="Klik untuk melihat profil unit"
                                    href="{{ route('unit.show', ['id' => $proker->unit->id]) }}">{{ $proker->unit->nama }}</a>
                            </h5>
                            <h6 class="_info mt-4 fw-bold mb-3 text-primary">
                                # Info Program</h6>
                            <table class="table-detail-info">
                                <tbody class="align-top">
                                    <tr>
                                        <td class="p-1">Bidang</td>
                                        <td class="p-1">:</td>
                                        <td class="p-1">
                                            {{ $proker->bidang->nama }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">Program</td>
                                        <td class="p-1">:</td>
                                        <td class="p-1">
                                            {{ $proker->nama }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">Total JKEM</td>
                                        <td class="p-1">:</td>
                                        <td class="p-1">{{ $proker->total_jkem }} menit</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">Tempat</td>
                                        <td class="p-1">:</td>
                                        <td class="p-1">
                                            @foreach ($proker->tempatDanSasaran as $item)
                                                <ul class="list-unstyled">
                                                    <li>{{ $item->tempat }}</li>
                                                </ul>
                                            @endforeach
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">Sasaran</td>
                                        <td class="p-1">:</td>
                                        <td class="p-1">
                                            @foreach ($proker->tempatDanSasaran as $item)
                                                <ul class="list-unstyled">
                                                    <li>{{ $item->sasaran }}</li>
                                                </ul>
                                            @endforeach
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <x-alert-component />

                            <nav class="mt-3">
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-daftar-kegiatan-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-daftar-kegiatan" type="button" role="tab"
                                        aria-controls="nav-daftar-kegiatan" aria-selected="true">Daftar kegiatan</button>
                                    <button class="nav-link" id="nav-peran-anggota-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-peran-anggota" type="button" role="tab"
                                        aria-controls="nav-peran-anggota" aria-selected="false">Peran anggota</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-daftar-kegiatan" role="tabpanel"
                                    aria-labelledby="nav-daftar-kegiatan-tab">
                                    <h6 class="_info mt-4 fw-bold mb-3 text-primary">
                                        # Daftar
                                        Kegiatan</h6>
                                    <div class="col-12 mb-3 mt-3">
                                        <button class="btn btn-primary tambah-kegiatan" data-bs-toggle="modal"
                                            data-bs-target="#addKegiatan"><i class="fas fa-plus me-1"></i>Tambah
                                            kegiatan</button>
                                    </div>
                                    <div class="review_daftar-kegiatain w-100" style="overflow-x: auto;">
                                        <table class="table table-bordered table-responsive text-nowrap nowrap w-100 mt-1">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">No</th>
                                                    <th class="p-1 text-center align-middle" rowspan="2">
                                                        Kegiatan</th>
                                                    <th class="p-1 text-center align-middle" colspan="3">Ekuivalensi JKEM
                                                        (menit)
                                                    </th>
                                                    <th rowspan="2" class="p-1 text-center align-middle">
                                                        Tanggal Rencana</th>
                                                    <th rowspan="2" class="p-1 text-center align-middle">
                                                        Aksi</th>

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
                                            <tbody>
                                                @foreach ($proker->kegiatan as $item)
                                                    <tr>
                                                        <td class="p-1 align-top">{{ $loop->iteration }}</td>
                                                        <td class="p-1 align-top">
                                                            {{ $item->nama }}
                                                        </td>
                                                        <td class="p-1 align-top">
                                                            {{ $item->frekuensi }}
                                                        </td>
                                                        <td class="p-1 align-top">
                                                            {{ $item->jkem }}
                                                        </td>
                                                        <td class="p-1 align-top">
                                                            {{ $item->total_jkem }}
                                                        </td>
                                                        <td class="p-1 align-top">
                                                            @foreach ($item->tanggalRencanaProker as $tanggal)
                                                                <ul>
                                                                    <li class="p-0 formatTanggal">{{ $tanggal->tanggal }}
                                                                    </li>
                                                                </ul>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            @php
                                                                $tanggalRencanaProker = implode(
                                                                    ', ',
                                                                    $item->tanggalRencanaProker
                                                                        ->pluck('tanggal')
                                                                        ->toArray(),
                                                                );
                                                            @endphp
                                                            <button class="btn btn-warning edit-kegiatan"
                                                                data-bs-toggle="modal" data-bs-target="#editKegiatanModal"
                                                                data-id="{{ $item->id }}"
                                                                data-nama="{{ $item->nama }}"
                                                                data-frekuensi="{{ $item->frekuensi }}"
                                                                data-jkem="{{ $item->jkem }}"
                                                                data-total_jkem="{{ $item->total_jkem }}"
                                                                data-tanggal="{{ $tanggalRencanaProker }}">Edit</button>
                                                            <button class="btn btn-danger delete-kegiatan"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteKegiatanModal"
                                                                data-id="{{ $item->id }}">Hapus</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-peran-anggota" role="tabpanel"
                                    aria-labelledby="nav-peran-anggota-tab">
                                    <h6 class="_info mt-4 fw-bold mb-3 text-primary">
                                        # Peran
                                        Anggota</h6>
                                    <button class="btn btn-warning mb-3" data-bs-toggle="modal"
                                        data-bs-target="#editPeranModal">Edit peran</button>
                                    <div class="w-100 table-responsive" style="overflow-x: auto">
                                        <table class="table table-bordered table-striped table-hover w-100 nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Peran</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($proker->organizer as $item)
                                                    <tr>
                                                        <td>{{ $item->nama }}</td>
                                                        <td>{{ $item->peran }}</td>
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
            </div>
        </div>
    </div>



    <div class="modal fade modal-xl" id="editKegiatanModal" aria-labelledby="exampleModalLabel" tabindex="-1"
        role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('kegiatan.edit') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row kegiatan-row">
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="kegiatan" class="form-label">Nama kegiatan
                                        <span class="text-danger" id="error_kegiatan">*</span></label>
                                    <input type="hidden" id="id_kegiatan" name="id_kegiatan" value="">
                                    <input type="text" value="" required class="form-control kegiatan"
                                        name="nama" id="kegiatan" placeholder="Nama kegiatan">
                                </div>
                            </div>
                            <input type="hidden" name="id_kkn" value="{{ $proker->bidang->id_kkn }}">
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="frekuensi" class="form-label">Frekuensi
                                        <span class="text-danger" id="error_frekuensi">*</span></label>
                                    <input type="number" min="1" value="" class="form-control frekuensi"
                                        name="frekuensi" required id="frekuensi">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="jkem" class="form-label">JKEM
                                        <span class="text-danger" id="error_jkem">*</span></label>
                                    <select name="jkem" required id="jkem" class="form-select jkem">
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
                                    <input type="text" min="1" readonly class="form-control totalJKEM"
                                        name="totalJKEM" id="totalJKEM">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="tanggal_kegiatan" class="form-label">Tanggal
                                        Kegiatan
                                        <span class="text-danger" id="error_tanggal_kegiatan">*</span> <span
                                            class="text-muted small">(Pilih tanggal
                                            tanggal sesuai jumlah
                                            frekuensi)</span></label>
                                    <input type="text" required data-flatpickr class="form-control tanggal_kegiatan"
                                        value="" name="tanggal_kegiatan" id="tanggal_kegiatan">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light w-md" data-bs-dismiss="modal">Kembali</button>
                        <button type="submit" class="btn btn-primary w-md modal_submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade modal-xl" id="addKegiatan" aria-labelledby="addKegiatanLabel" aria-hidden="true"
        tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('kegiatan.add') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row kegiatan-row">
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="kegiatan" class="form-label">Nama kegiatan
                                        <span class="text-danger" id="error_kegiatan">*</span></label>
                                    <input type="hidden" id="id_kegiatan" name="id_kegiatan" value="">
                                    <input type="text" value="" required class="form-control kegiatan"
                                        name="nama" id="kegiatan" placeholder="Nama kegiatan">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="frekuensi" class="form-label">Frekuensi
                                        <span class="text-danger" id="error_frekuensi">*</span></label>
                                    <input type="number" min="1" value="" class="form-control frekuensi"
                                        name="frekuensi" required id="frekuensi">
                                </div>
                            </div>
                            <input type="hidden" name="id_mahasiswa" value="{{ $id_mahasiswa }}">
                            <input type="hidden" name="id_proker" value="{{ $proker->id }}">
                            <input type="hidden" name="id_kkn" value="{{ $proker->bidang->id_kkn }}">
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="jkem" class="form-label">JKEM
                                        <span class="text-danger" id="error_jkem">*</span></label>
                                    <select name="jkem" required id="jkem" class="form-select jkem">
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
                                    <input type="text" min="1" readonly class="form-control totalJKEM"
                                        name="totalJKEM" id="totalJKEM">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="tanggal_kegiatan" class="form-label">Tanggal
                                        Kegiatan
                                        <span class="text-danger" id="error_tanggal_kegiatan">*</span> <span
                                            class="text-muted small">(Pilih tanggal
                                            tanggal sesuai jumlah
                                            frekuensi)</span></label>
                                    <input type="text" required data-flatpickr class="form-control tanggal_kegiatan"
                                        value="" name="tanggal_kegiatan" id="tanggal_kegiatan">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light w-md" data-bs-dismiss="modal">Kembali</button>
                        <button type="submit" class="btn btn-primary w-md modal_submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal delete --}}
    <div class="modal fade" id="deleteKegiatanModal" aria-labelledby="deleteKegiatanModalLabel" aria-hidden="true"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="deleteKegiatan">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="text-center" id="modal-status-delete">
                        Melakukan pengecekan kegiatan harian...
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-light w-md" data-bs-dismiss="modal">Kembali</button>

                    <form action="{{ route('proker.delete.kegiatan') }}" class="modal_submit_delete" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="id">
                        <input type="hidden" name="id_proker" value="{{ $proker->id }}">
                        <button type="submit" class="btn btn-danger w-md">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPeranModal" aria-hidden="true" aria-labelledby="editPeranAnggotaLabelly"
        tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('proker.organizer.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @foreach ($proker->organizer as $index => $item)
                            <div class="form-group mb-3">
                                <label for="peran" class="form-label">Peran <span
                                        class="fw-bold">{{ $item->nama }}</span></label>
                                <input type="text" required value="{{ $item->peran }}"
                                    name="organizers[{{ $index }}][peran]" class="form-control">
                                <input type="hidden" name="organizers[{{ $index }}][id]"
                                    value="{{ $item->id }}">
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light w-md" data-bs-dismiss="modal">Kembali</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <input type="hidden" id="tanggal_penerjunan_unit" value="{{ $proker->unit->tanggal_penerjunan }}">
@endsection
@section('pageScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>

    <!-- datepicker js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

    <script src="{{ asset('assets/js/init/mahasiswa/showEditKegiatanModal.init.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.delete-kegiatan', function() {
                var id = $(this).data('id');
                $('#deleteKegiatanModal').find('input[name="id"]').val(id);
            })

            $(document).on('click', '.delete-kegiatan', function() {
                const id = $(this).data('id');
                $(".modal_submit_delete").addClass("d-none");
                $.ajax({
                    type: "GET",
                    url: "/proker/checkStatusKegiatan",
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.status === "success") {
                            $('#deleteKegiatan').html(`
                            <div class="text-center">
                                <div class="row flex-column align-items-center justify-content-center">
                                    <div class="col-3">
                                        <i class="bx bx-error-circle display-4 text-danger"></i>
                                    </div>
                                    <div class="col-9">
                                        <h5 class="text-danger">Apakah anda yakin menghapus kegiatan ini?</h5>
                                    </div>
                                </div>
                            </div>`);
                            $('.modal_submit_delete').removeClass('d-none');
                        } else {

                            $('#deleteKegiatan').html(response.message);
                        }
                    }
                });
            })
        });
    </script>
@endsection
