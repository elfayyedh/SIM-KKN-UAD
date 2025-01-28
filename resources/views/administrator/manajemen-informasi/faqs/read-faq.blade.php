@extends('layouts.index')

@section('title', 'FAQ | Admin')
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
            <div class="row" id="tambah_faq">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">FAQs</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen informasi</a></li>
                                <li class="breadcrumb-item active">FAQ</li>
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
                            <div class="card-title text-muted fw-bold">Buat/Edit FAQ</div>
                            <form action="{{ route('informasi.faq.store') }}" id="faq_form" method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="judul" class="form-label">Pertanyaan <span
                                            class="text-danger">*</span></label>
                                    <input type="text" required class="form-control @error('judul') is-invalid @enderror"
                                        id="pertanyaan" name="judul" placeholder="Masukkan Pertanyaan">
                                    @error('judul')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <input type="hidden" name="type" value="faq">
                                <input type="hidden" name="id" id="id_faq">
                                <div class="form-group mb-3">
                                    <label for="isi" class="form-label">Jawaban <span
                                            class="text-danger">*</span></label>
                                    <textarea required class="form-control @error('isi') is-invalid @enderror" id="jawaban" name="isi"
                                        placeholder="Masukkan isi" rows="5" cols="30"></textarea>
                                    @error('isi')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <button type="submit" class="btn btn-primary"><i
                                            class="bx bx-save me-1"></i>Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @if (session('error') || session('success'))
                    <div class="col-12">
                        <div class="alert {{ session('error') ? 'alert-danger' : 'alert-success' }} alert-dismissible fade show"
                            role="alert">
                            {{ session('error') ?? session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title text-muted fw-bold">Daftar FAQs</div>
                            <div class="table-responsive">
                                <table id="datatable"
                                    class="table table-bordered table-striped table-hover w-100 nowrap dt-responsive">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Pertanyaan</th>
                                            <th>Jawaban</th>
                                            <th>Preview</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($faqs as $faq)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="text-wrap" style="max-width: 400px; min-width: 150px;">
                                                    {{ $faq->judul }}</td>
                                                <td class="text-wrap" style="max-width: 400px; min-width: 150px;">
                                                    {{ $faq->isi }}</td>
                                                <td>
                                                    <form action="{{ route('informasi.faq.setFaqStatus') }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $faq->id }}">
                                                        <input type="hidden" name="status" value="{{ $faq->status }}">
                                                        <button
                                                            class="btn btn-{{ $faq->status == true ? 'success' : 'warning' }} btn-sm">
                                                            {{ $faq->status == true ? 'Sembunyikan' : 'Tampilkan' }}
                                                        </button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <a href="#tambah_faq" data-pertanyaan="{{ $faq->judul }}"
                                                        data-jawaban="{{ $faq->isi }}" data-id="{{ $faq->id }}"
                                                        class="btn btn-primary edit"><i class="bx bx-edit"></i></a
                                                        href="#">
                                                    <button class="btn btn-danger btn-delete" data-id="{{ $faq->id }}"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"><i
                                                            class="bx bx-trash"></i></button>
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
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Faq</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bx bx-error-circle font-size-24 text-danger"></i>
                    <p class="text-center">Apakah anda yakin ingin menghapus faq ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('informasi.faq.deleteFaq') }}" method="POST">
                        @csrf
                        @method('delete')
                        <input type="hidden" name="id" id="id_delete">
                        <button type="submit" class="btn btn-danger" id="btn-confirm-delete">Ya, hapus</button>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@section('pageScript')

    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/init/administrator/read-faq.init.js') }}"></script>
@endsection
