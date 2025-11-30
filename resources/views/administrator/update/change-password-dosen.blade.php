@extends('layouts.index')

@section('title', 'Ganti Password Dosen')
@section('styles')

@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Ganti Password Dosen</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Pengguna</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Daftar Dosen</a></li>
                                <li class="breadcrumb-item active">Ganti Password</li>
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
                            <div class="card-title text-muted fw-bold">Form Ganti Password</div>
                            <div class="row">
                                <div class="col-12">
                                    <form action="{{ route('dosen.update.password', $dosen->id) }}" method="POST"
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
                                            <a href="{{ route('dosen.index') }}" class="btn btn-secondary">Batal</a>
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
