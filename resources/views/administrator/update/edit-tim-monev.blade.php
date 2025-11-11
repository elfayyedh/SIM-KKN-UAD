@extends('layouts.index')

@section('title', 'Edit Tim Monev')
@section('styles')

@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Edit Tim Monev</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Tim Monev</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('tim-monev.index') }}">Daftar Tim Monev</a></li>
                                <li class="breadcrumb-item active">Edit Tim Monev</li>
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
                            <div class="card-title text-muted fw-bold">Form Edit Tim Monev</div>
                            <form action="{{ route('tim-monev.update', $timMonev->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group mb-3">
                                    <label for="id_kkn" class="form-label">KKN <span class="text-danger">*</span></label>
                                    <select name="id_kkn" id="id_kkn" class="form-select @error('id_kkn') is-invalid @enderror" required>
                                        <option value="">Pilih KKN</option>
                                        @foreach ($kkn as $item)
                                            <option value="{{ $item->id }}" {{ $timMonev->id_kkn == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_kkn')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="id_dosen" class="form-label">Dosen <span class="text-danger">*</span></label>
                                    <select name="id_dosen" id="id_dosen" class="form-select @error('id_dosen') is-invalid @enderror" required>
                                        <option value="">Pilih Dosen</option>
                                        @foreach ($dosen as $item)
                                            <option value="{{ $item->id }}" {{ $timMonev->id_dosen == $item->id ? 'selected' : '' }}>{{ $item->user->nama }} ({{ $item->nip }})</option>
                                        @endforeach
                                    </select>
                                    @error('id_dosen')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>Simpan</button>
                                    <a href="{{ route('tim-monev.index') }}" class="btn btn-secondary">Batal</a>
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

@endsection
