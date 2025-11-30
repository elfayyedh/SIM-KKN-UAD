@extends('layouts.index')

@section('title', 'Daftar Dosen')
@section('styles')
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
                        <h4 class="mb-sm-0 font-size-18">Daftar Dosen</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Pengguna</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Daftar Dosen</a></li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <p class="fw-bold">Total Dosen <span class="text-muted">({{ $dosen->count() }})</span></p>
                </div>
                <div class="col-12 col-md-6 d-flex justify-content-md-end">
                    <a href="{{ route('dosen.create') }}" class="btn btn-light btn-lg"><i
                            class="bx bx-plus me-1"></i>Tambah Dosen</a>
                </div>
            </div>

            <div class="row">
                <div class="col-12 table-responsive">
                    <x-alert-component />
                    <table id="datatable" class="table table-bordered nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Dosen</th>
                                <th>NIP</th>
                                <th>Email</th>
                                <th>Jenis Kelamin</th>
                                <th>No HP</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dosen as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->user->nama ?? 'N/A' }}</td>
                                    <td>{{ $item->nip ?? 'N/A' }}</td>
                                    <td>{{ $item->user->email ?? 'N/A' }}</td>
                                    <td>{{ $item->user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    <td>
                                        @if($item->user->no_telp)
                                            <a target="_blank" href="https://wa.me/{{ $item->user->no_telp }}">{{ $item->user->no_telp }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-secondary btn-sm" href="{{ route('dosen.edit', $item->id) }}"><i
                                                class="bx bx-edit me-1"></i>Edit</a>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $item->id }}">
                                            <i class="bx bx-trash me-1"></i>Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
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

    <!-- Datatable init js -->
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();

            // Delete handler
            $('.delete-btn').on('click', function() {
                const dosenId = $(this).data('id');
                
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data dosen akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/dosen/${dosenId}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.success,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                let errorMsg = 'Terjadi kesalahan saat menghapus data.';
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    errorMsg = xhr.responseJSON.error;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: errorMsg
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
