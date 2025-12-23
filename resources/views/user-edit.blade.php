@extends('layouts.index')

@section('title', 'EDIT PENGGUNA | SIM KKN UAD')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Page title --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">EDIT PENGGUNA</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Profil</a></li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            {{-- End Page title --}}

            <div class="row mb-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form action="{{ route('user.update', $user->id) }}" id="user_form" method="POST">
                                        @method('PUT')
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label for="nama" class="form-label">Nama <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}"
                                                value="{{ $user->nama }}" required name="nama" id="nama">
                                            @if ($errors->has('nama'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('nama') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="no_telp" class="form-label">No HP <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control {{ $errors->has('no_telp') ? 'is-invalid' : '' }}"
                                                value="{{ $user->no_telp }}" required name="no_telp" id="no_telp">
                                            @if ($errors->has('no_telp'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('no_telp') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="jenis_kelamin" class="form-label">Gender <span
                                                    class="text-danger">*</span></label>
                                            <select
                                                class="form-select {{ $errors->has('jenis_kelamin') ? 'is-invalid' : '' }}"
                                                required name="jenis_kelamin" id="jenis_kelamin">
                                                <option value="">--Pilih Gender--</option>
                                                <option value="L" {{ $user->jenis_kelamin == 'L' ? 'selected' : '' }}>
                                                    Laki-laki</option>
                                                <option value="P" {{ $user->jenis_kelamin == 'P' ? 'selected' : '' }}>
                                                    Perempuan</option>
                                            </select>
                                            @if ($errors->has('jenis_kelamin'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('jenis_kelamin') }}
                                                </div>
                                            @endif
                                        </div>
                                        
                                        @if(isset($mahasiswa) && $mahasiswa)
                                        <div class="form-group mb-3">
                                            <label for="status" class="form-label">Status Mahasiswa <span
                                                    class="text-danger">*</span></label>
                                            <select
                                                class="form-select {{ $errors->has('status') ? 'is-invalid' : '' }}"
                                                required name="status" id="status">
                                                <option value="1" {{ $mahasiswa->status == 1 ? 'selected' : '' }}>
                                                    Aktif</option>
                                                <option value="0" {{ $mahasiswa->status == 0 ? 'selected' : '' }}>
                                                    Tidak Aktif</option>
                                            </select>
                                            <small class="text-muted">Mahasiswa yang tidak aktif tidak dapat login ke sistem</small>
                                            @if ($errors->has('status'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('status') }}
                                                </div>
                                            @endif
                                        </div>
                                        @endif
                                        
                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary w-md"><i
                                                    class="bx bx-save me-1"></i>Simpan</button>
                                        </div>
                                        <x-alert-with-button :sessionError="'error_user'" :sessionSuccess="'success_user'" />
                                    </form>
                                </div>
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end  card body -->
                    </div>
                    <!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <div class="row mb-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Ganti Password</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form action="{{ route('user.update.password', $user->id) }}" method="POST"
                                        id="passwordForm">
                                        @method('PUT')
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label for="old-password" class="form-label">Password sebelumnya <span
                                                    class="text-danger">*</span></label>
                                            <input type="password"
                                                class="form-control {{ $errors->has('old-password') ? 'is-invalid' : '' }}"
                                                required name="old-password" id="old-password">
                                            @if ($errors->has('old-password'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('old-password') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="new_password" class="form-label">Password baru <span
                                                    class="text-danger">*</span></label>
                                            <input type="password"
                                                class="form-control {{ $errors->has('new_password') ? 'is-invalid' : '' }}"
                                                required name="new_password" id="new_password">
                                            @if ($errors->has('new_password'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('new_password') }}
                                                </div>
                                            @endif
                                            <small id="new-password-error" class="text-danger"></small>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="confirm_password" class="form-label">Konfirmasi Password
                                                <span class="text-danger">*</span></label>
                                            <input type="password"
                                                class="form-control {{ $errors->has('confirm_password') ? 'is-invalid' : '' }}"
                                                required name="confirm_password" id="confirm_password">
                                            @if ($errors->has('confirm_password'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('confirm_password') }}
                                                </div>
                                            @endif
                                            <small id="confirm-password-error" class="text-danger"></small>
                                        </div>
                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary w-md"><i
                                                    class="bx bx-save me-1"></i>Simpan</button>
                                        </div>
                                        <x-alert-with-button :sessionError="'error_pw'" :sessionSuccess="'success_pw'" />
                                    </form>
                                </div>
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end  card body -->
                    </div>
                    <!-- end card -->
                </div>
                <!-- end col -->
            </div>
        </div>
    </div>
@endsection
@section('pageScript')
    <script src="{{ asset('assets/js/init/user-edit.init.js') }}"></script>
@endsection
