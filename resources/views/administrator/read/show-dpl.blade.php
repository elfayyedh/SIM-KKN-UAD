@extends('layouts.index')

@section('title', 'Daftar DPL')
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
                        <h4 class="mb-sm-0 font-size-18">Daftar DPL</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen DPL</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('dpl.index') }}">Daftar DPL</a></li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <p class="fw-bold">Total DPL <span class="text-muted">({{ $dpl->count() }})</span></p>
                </div>
                <div class="col-12 col-md-6 d-flex justify-content-md-end">
                    <a href="{{ route('dpl.create') }}" class="btn btn-light btn-lg"><i
                            class="bx bx-plus me-1"></i>Tambah DPL</a>
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
                                <th>Jenis Kelamin</th>
                                <th>No HP</th>
                                <th>KKN</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dpl as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->dosen->user->nama ?? 'N/A' }}</td>
                                    <td>{{ $item->dosen->nip ?? 'N/A' }}</td>
                                    <td>{{ $item->dosen->user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    <td><a target="_blank"
                                            href="https://wa.me/{{ $item->dosen->user->no_telp }}">{{ $item->dosen->user->no_telp }}</a></td>
                                    <td>{{ $item->kkn->nama }}</td>
                                    <td>
                                        <a class="btn btn-secondary btn-sm" href="{{ route('dpl.edit', $item->id) }}"><i
                                                class="bx bx-edit me-1">Edit</i></a>
                                        <form action="{{ route('dpl.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus?')"><i class="bx bx-trash me-1"></i>Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('pageScript')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#datatable").DataTable();
        });
    </script>
@endsection
