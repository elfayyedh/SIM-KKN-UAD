@extends('layouts.index')
@section('title', 'Edit | ' . $kkn->nama)

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">{{ $kkn->nama }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen KKN</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('kkn.index') }}">Daftar KKN</a></li>
                                <li class="breadcrumb-item actve">Edit Data KKN</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Informasi Umum</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <x-alert-with-button :sessionError="'error_kkn'" :sessionSuccess="'success_kkn'" />
                                </div>
                            </div>
                            <form action="{{ route('kkn.update', $kkn->id) }}" method="POST" id="kkn_form">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="mb-3">
                                            <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                                id="nama" required name="nama"
                                                value="{{ old('nama', $kkn->nama) }}">
                                            @error('nama')
                                                <div class="invalid-feedback"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="mb-3">
                                            <label for="thn_ajaran" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('thn_ajaran') is-invalid @enderror"
                                                id="thn_ajaran" required name="thn_ajaran"
                                                value="{{ old('thn_ajaran', $kkn->thn_ajaran) }}">
                                            @error('thn_ajaran')
                                                <div class="invalid-feedback"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="mb-3">
                                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control datepicker-basic @error('tanggal_mulai') is-invalid @enderror"
                                                id="tanggal_mulai" required name="tanggal_mulai"
                                                value="{{ old('tanggal_mulai', $kkn->tanggal_mulai) }}">
                                            @error('tanggal_mulai')
                                                <div class="invalid-feedback"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="mb-3">
                                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control datepicker-basic @error('tanggal_selesai') is-invalid @enderror"
                                                id="tanggal_selesai" required name="tanggal_selesai"
                                                value="{{ old('tanggal_selesai', $kkn->tanggal_selesai) }}">
                                            @error('tanggal_selesai')
                                                <div class="invalid-feedback"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary w-md" type="submit"><i class="bx bx-save"></i> Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Daftar Bidang Proker</h4>
                        </div>
                        <div class="card-body">
                            <x-alert-with-button :sessionError="'error_bidang'" :sessionSuccess="'success_bidang'" />
                            @foreach ($kkn->bidangProker as $item)
                                <x-bidang-proker :bidangProker="$item" />
                            @endforeach
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary w-md" data-bs-toggle="modal" data-bs-target="#addModal">
                                        <i class="bx bx-plus"></i> Tambah Bidang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Daftar Kriteria Penilaian Monev</h4>
                        </div>
                        <div class="card-body">
                            <x-alert-with-button :sessionError="'error_kriteria'" :sessionSuccess="'success_kriteria'" />
                            @forelse ($kkn->kriteriaMonev as $index => $kriteria)
                                <form action="{{ route('kriteria.update', $kriteria->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="row border mb-3 row-kriteria-inline align-items-start">                                        
                                        {{-- TEMPLATE --}}
                                        <div class="col-lg-3">
                                            <div class="mb-2">
                                                <div class="row fw-bold mb-2 d-none d-lg-flex px-3">Tipe Template</div>
                                                <label class="form-label d-lg-none">Tipe</label>
                                                <select class="form-select edit-template-inline">
                                                    <option value="custom" selected>-- Custom / Manual --</option>
                                                    <option value="jkem">Penilaian JKEM</option>
                                                    <option value="sholat">Penilaian Sholat</option>
                                                    <option value="form1">Penilaian Form 1</option>
                                                    <option value="form2">Penilaian Form 2</option>
                                                    <option value="form3">Penilaian Form 3</option>
                                                    <option value="form4">Penilaian Form 4</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- JUDUL + CUSTOM INPUTS --}}
                                        <div class="col-lg-4">
                                            <div class="mb-2">
                                                <div class="row fw-bold mb-2 d-none d-lg-flex px-3">Judul Kriteria</div>
                                                <label class="form-label d-lg-none">Judul</label>
                                                <input type="text" class="form-control input-judul" name="judul" value="{{ $kriteria->judul }}" required placeholder="Judul Kriteria">
                                                <input type="hidden" class="input-var" name="variable_key" value="{{ $kriteria->variable_key }}">
                                                <input type="hidden" class="input-url" name="link_url" value="{{ $kriteria->link_url }}">
                                                <input type="hidden" class="input-text" name="link_text" value="{{ $kriteria->link_text }}">
                                            </div>
                                        </div>

                                        {{-- KETERANGAN --}}
                                        <div class="col-lg-3">
                                            <div class="mb-2">
                                                <div class="row fw-bold mb-2 d-none d-lg-flex px-3">Keterangan Skala</div>
                                                <label class="form-label d-lg-none">Keterangan</label>
                                                <input type="text" class="form-control input-ket" name="keterangan" value="{{ $kriteria->keterangan }}" required placeholder="Skala 1-3">
                                            </div>
                                        </div>

                                        {{-- AKSI --}}
                                        <div class="col-lg-2">
                                            <label class="form-label d-none d-lg-block">&nbsp;</label> 
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-soft-success" title="Simpan Perubahan">
                                                    <i class="bx bx-save"></i> <span class="d-lg-none">Simpan</span>
                                                </button>
                                                
                                                <button type="button" class="btn btn-soft-danger btn-delete-kriteria"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteKriteriaModal"
                                                        data-id="{{ $kriteria->id }}">
                                                    <i class="bx bx-trash"></i> <span class="d-lg-none">Hapus</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @empty
                                <div class="text-center py-4 text-muted border rounded bg-light">
                                    <i class="mdi mdi-alert-circle-outline fs-3 d-block mb-2"></i>
                                    Belum ada kriteria penilaian. Silakan tambah baru.
                                </div>
                            @endforelse

                            {{-- Tombol Tambah --}}
                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary w-md" data-bs-toggle="modal" data-bs-target="#addKriteriaModal">
                                        <i class="bx bx-plus"></i> Tambah Kriteria
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="deleteKriteriaModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-bottom-0">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <div class="row flex-column align-items-center justify-content-center">
                                    <div class="col-3">
                                        <i class="bx bx-error-circle display-4 text-danger"></i>
                                    </div>
                                    <div class="col-9">
                                        <h5 class="text-danger">Hapus Kriteria Ini?</h5>
                                        <p class="text-muted">Semua data penilaian yang terkait dengan kriteria ini akan ikut terhapus permanen.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <form id="formDeleteKriteria" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-light w-md" data-bs-dismiss="modal">Kembali</button>
                                <button type="submit" class="btn btn-danger w-md">Ya, Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div> 
    </div>

    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title">Tambah Bidang Proker</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('bidang.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="nama_bidang" class="form-label">Nama Bidang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_bidang" required name="nama">
                                </div>
                            </div>
                            <input type="hidden" name="id_kkn" value="{{ $kkn->id }}">
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="tipe_bidang" class="form-label">Tipe <span class="text-danger">*</span></label>
                                    <select id="tipe_bidang" class="form-select" name="tipe">
                                        <option value="individu">Individu</option>
                                        <option value="unit">Bersama</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="syarat_jkem_bidang" class="form-label">Minimal JKEM <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="syarat_jkem_bidang" required name="syarat_jkem">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn-light w-md" data-bs-dismiss="modal">Kembali</a>
                        <button type="submit" class="btn btn-primary w-md" id="btn-add-bidang">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="row flex-column align-items-center justify-content-center">
                            <div class="col-3">
                                <i class="bx bx-error-circle display-4 text-danger"></i>
                            </div>
                            <div class="col-9">
                                <h5 class="text-danger">Apakah anda yakin?</h5>
                                <p>Total <strong id="total_proker">50</strong> proker menggunakan bidang ini</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-light w-md" data-bs-dismiss="modal">Kembali</button>
                    <button type="submit" class="btn btn-danger w-md" id="btn-delete-bidang-proker">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addKriteriaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kriteria Monev</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('kriteria.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_kkn" value="{{ $kkn->id }}">
                    <div class="modal-body">
                        {{-- DROPDOWN TEMPLATE --}}
                        <div class="mb-3">
                            <label class="form-label text-primary fw-bold">Pilih Template (Opsional)</label>
                            <select class="form-select" id="add_template">
                                <option value="custom" selected>-- Custom / Manual --</option>
                                <option value="jkem">Penilaian JKEM</option>
                                <option value="sholat">Penilaian Sholat</option>
                                <option value="form1">Penilaian Form 1</option>
                                <option value="form2">Penilaian Form 2</option>
                                <option value="form3">Penilaian Form 3</option>
                                <option value="form4">Penilaian Form 4</option>
                            </select>
                        </div>
                        <hr>

                        <div class="mb-3">
                            <label class="form-label">Judul Kriteria <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_judul" name="judul" required placeholder="Cth: Penilaian JKEM">
                            <input type="hidden" id="add_variable" name="variable_key">
                            <input type="hidden" id="add_url" name="link_url">
                            <input type="hidden" id="add_text" name="link_text">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan Skala <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_keterangan" name="keterangan" required placeholder="Cth: 1:<30%, 2:Cukup...">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteKriteriaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <i class="bx bx-trash-alt display-4 text-danger"></i>
                    </div>
                    <h4>Hapus Kriteria Ini?</h4>
                    <p class="text-muted">Data penilaian yang sudah masuk untuk kriteria ini akan ikut terhapus.</p>
                    
                    <form id="formDeleteKriteria" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="d-flex gap-2 justify-content-center mt-4">
                            <button type="button" class="btn btn-light w-md" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger w-md">Ya, Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('pageScript')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script src="{{ asset('assets/js/init/administrator/update-kkn.init.js') }}"></script>
@endsection