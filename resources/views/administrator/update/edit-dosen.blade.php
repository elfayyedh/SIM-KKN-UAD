@extends('layouts.index')

@section('title', 'Edit Dosen')
@section('styles')

@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Edit Dosen</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Pengguna</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Daftar Dosen</a></li>
                                <li class="breadcrumb-item active">Edit Dosen</li>
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
                            <div class="card-title text-muted fw-bold">Form Edit Dosen</div>
                            <form id="form-edit-dosen">
                                @csrf
                                @method('PUT')
                                <div class="form-group mb-3">
                                    <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" id="nama" class="form-control" value="{{ $dosen->user->nama }}" required>
                                    <small class="text-danger" id="error-nama"></small>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="nidn" class="form-label">NIP <span class="text-danger">*</span></label>
                                    <input type="text" name="nidn" id="nidn" class="form-control" value="{{ $dosen->nip }}" required>
                                    <small class="text-danger" id="error-nidn"></small>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ $dosen->user->email }}" required>
                                    <small class="text-danger" id="error-email"></small>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="no_telp" class="form-label">Nomor HP</label>
                                    <input type="text" name="no_telp" id="no_telp" class="form-control" value="{{ $dosen->user->no_telp }}">
                                    <small class="text-danger" id="error-no_telp"></small>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-select">
                                        <option value="L" {{ $dosen->user->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ $dosen->user->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    <small class="text-danger" id="error-jenis_kelamin"></small>
                                </div>
                                <div class="form-group mb-3">
                                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>Simpan</button>
                                    <a href="{{ route('dosen.index') }}" class="btn btn-secondary">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title text-muted fw-bold">Ganti Password</div>
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
<script>
    $('#form-edit-dosen').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.text-danger').text('');
        
        const formData = {
            _token: '{{ csrf_token() }}',
            _method: 'PUT',
            nama: $('#nama').val(),
            nidn: $('#nidn').val(),
            email: $('#email').val(),
            no_telp: $('#no_telp').val(),
            jenis_kelamin: $('#jenis_kelamin').val()
        };

        $.ajax({
            url: '{{ route('dosen.update', $dosen->id) }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.success,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = '{{ route('dosen.index') }}';
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $(`#error-${key}`).text(value[0]);
                    });
                } else {
                    let errorMsg = 'Terjadi kesalahan saat menyimpan data.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: errorMsg
                    });
                }
            }
        });
    });
</script>
@endsection
