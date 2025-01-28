@extends('layouts.index')
@section('title', 'Buat Pengguna Baru | Admin')
@section('content')
    <div class="page-content">
        <div class="container-flui">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">BUAT PENGGUNA BARU</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Pengguna</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Buat pengguna baru</a></li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Buat manual</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <x-alert-with-button :sessionError="'error_kkn'" :sessionSuccess="'success_kkn'" />
                                </div>
                            </div>
                            <form action="" method="POST" id="kkn_form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role <span
                                                    class="text-danger">*</span></label>
                                            <select name="role" id="role" class="form-select" required>
                                                <option value="Admin">Admin</option>
                                                <option value="Mahasiswa">Mahasiswa</option>
                                                <option value="DPL">Dosen Pembimbing Lapangan (DPL)</option>
                                                <option value="Tim Monev">Tim Monev</option>
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="mb-3">
                                            <label for="kkn" class="form-label">Periode KKN <span
                                                    class="text-danger">*</span></label>
                                            <select name="kkn" id="kkn" class="form-select">
                                                @foreach ($kkn as $k)
                                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                                @endforeach
                                            </select>
                                            @error('kkn')
                                                <div class="invalid-feedback"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="mb-3">
                                            <label for="nama" class="form-label">Nama <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                                id="nama" required name="nama" value="{{ old('nama') }}"
                                                placeholder="Masukkan Nama">
                                            @error('nama')
                                                <div class="invalid-feedback"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="mb-3">
                                            <label for="nimnip" class="form-label">NIM/NIP <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nimnip') is-invalid @enderror"
                                                id="nimnip" required name="nimnip" value="{{ old('nimnip') }}"
                                                placeholder="Masukkan NIM atau NIP">
                                            @error('nimnip')
                                                <div class="invalid-feedback"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('email') is-invalid @enderror"
                                                id="email" required name="email" value="{{ old('email') }}"
                                                placeholder="Masukkan email">
                                            @error('email')
                                                <div class="invalid-feedback"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" id="password"
                                                required name="password" placeholder="Masukkan password">
                                            @error('password')
                                                <div class="invalid-feedback"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="mb-3">
                                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span
                                                    class="text-danger">*</span></label>
                                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                                                <option value="L">Laki-laki</option>
                                                <option value="P">Perempuan</option>
                                            </select>
                                            @error('jenis_kelamin')
                                                <div class="invalid-feedback"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="mb-3">
                                            <label for="no_telp" class="form-label">No Telpon <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('no_telp') is-invalid @enderror" id="no_telp"
                                                required name="no_telp" value="{{ old('no_telp') }}"
                                                placeholder="08xxxxxxxx">
                                            @error('no_telp')
                                                <div class="invalid-feedback"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary w-md" type="submit"><i class="bx bx-save"></i>
                                            Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('pageScript')
    <script src="{{ asset('assets/js/init/administrator/create-user.init.js') }}"></script>
@endsection
