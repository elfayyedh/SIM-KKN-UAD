@extends('layouts.index')

@section('title', 'Proker Bersama | ' . $unit->nama)
@section('styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <style>
        .vertical-text {
            -webkit-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg);
            -ms-transform: rotate(-90deg);
            -o-transform: rotate(-90deg);
        }

        .table-proker {
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
                        <h4 class="mb-sm-0 font-size-18">PROGRAM KERJA BERSAMA</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Unit</a></li>
                                <li class="breadcrumb-item">Program kerja</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row mb-3">
                <div class="col">
                    {{-- Button to toggle modal add --}}
                    <a href="{{ route('proker.create.unit') }}" class="btn btn-primary  waves-effect waves-light"><i
                            class="mdi mdi-plus me-1"></i> Tambah Proker</a>
                </div>
            </div>
            <x-alert-component />

            <div class="row">
                <div class="col-12" id="proker-container">
                    <table class="table table-bordered w-100 nowrap">
                        <tbody>
                            @for ($i = 0; $i < 3; $i++)
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
                                    <td>
                                        <div class="placeholder-glow"><span class="placeholder col-12"></span></div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Edit proker modal --}}
    <div class="modal fade modal-lg" aria-hidden="true" tabindex="-1" aria-labelledby="modal-edit-proker"
        id="editProkerModal" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('proker.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="proker" class="form-label">Proker</label>
                                    <input type="text" id="proker" name="nama" class="form-control" required
                                        placeholder="Masukkan nama proker...">
                                    <input type="hidden" name="id">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light w-md" data-bs-dismiss="modal">Kembali</button>
                        <button type="submit" class="btn btn-primary w-md" id="btn-confirm-edit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal-delete-proker-unit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="modal-status">
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
                    <button type="button" class="btn btn-danger w-md modal_delete" id="btn-confirm-delete">Hapus</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pageScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
    <script src="{{ asset('assets/js/init/mahasiswa/read_proker_bersama.init.js') }}"></script>
    <script src="{{ asset('assets/js/init/mahasiswa/delete-proker-unit.init.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.edit-proker', function() {
                var id = $(this).data('id');
                var nama = $(this).data('nama');
                var bidang = $(this).data('bidang');
                var tempat = $(this).data('tempat');
                var sasaran = $(this).data('sasaran');
                var idTempat = $(this).data('id-tempat');

                $('#editProkerModal').find('input[name="id"]').val(id);
                $('#editProkerModal').find('input[name="tempat"]').val(tempat);
                $('#editProkerModal').find('input[name="id_tempat_sasaran"]').val(idTempat);
                $('#editProkerModal').find('input[name="sasaran"]').val(sasaran);
                $('#editProkerModal').find('input[name="nama"]').val(nama);
                // Selected option where bidang = value
                $("#bidangEdit").val(bidang);
            })
        });
    </script>
@endsection
