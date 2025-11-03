@extends('layouts.index')

@section('title', 'Dashboard | SIM KKN UAD')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            {{-- Page title --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">DASHBOARD</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">SIM KKN UAD</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            {{-- End Page title --}}

            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <p class="fw-bold mb-1">Halo, {{ $dpl->userRole->user->nama ?? 'User DPL' }}</p>
                    <p>Selamat datang di SIM KKN UAD</p>
                </div>
                <div class="col-12 col-md-6 justify-content-md-end">
                    <div class="form-group">
                        <label for="periode" class="form-label">Pilih periode KKN</label>
                        <select name="periode" id="periode" class="form-select">
                            <option value="semua">Semua Periode</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-xl-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <p class="fw-bold font-size-17 card-title">Total Mahasiswa</p>
                            <div class="d-flex w-100 position-relative">
                                <div class="konten">
                                    <h2 class="total-mahasiswa">{{ $total_mahasiswa }}</h2>
                                    <a class="text-decoration-none" href="#">Lihat semua mahasiswa</a>
                                </div>
                                <div class="position-absolute d-flex justify-content-center align-items-center bottom-0 end-0 rounded bg-sublte-success"
                                    style="width: 50px; height: 50px;">
                                    <i class="bx bx-user font-size-24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <p class="fw-bold font-size-17 card-title">Total Unit</p>
                            <div class="d-flex w-100 position-relative">
                                <div class="konten">
                                    <h2 class="total-unit">{{ $total_unit }}</h2>
                                    <a class="text-decoration-none" href="#">Lihat semua unit</a>
                                </div>
                                <div class="position-absolute d-flex justify-content-center align-items-center bottom-0 end-0 rounded bg-sublte-info"
                                    style="width: 50px; height: 50px;">
                                    <i class="mdi mdi-account-group-outline font-size-24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 col-xl-6">
                    <div class="card shadow card-h-100">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title flex-grow-1 mb-0">Lokasi KKN</h4>
                            <div class="flex-shrink-0">
                                <button class="btn btn-outline-primary btn-sm" type="button"><i
                                        class="bx bx-download me-1"></i>Download Report</button>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0" data-simplebar style="max-height: 350px;">
                            <table class="table w-100 nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>Total Unit</th>
                                        <th>Kecamatan</th>
                                        <th>Kabupaten</th>
                                    </tr>
                                </thead>
                                <tbody id="data-lokasi">
                                    @forelse($data_lokasi as $lokasi)
                                        <tr>
                                            <td>{{ $lokasi->total_unit }}</td>
                                            <td>{{ $lokasi->kecamatan }}</td>
                                            <td>{{ $lokasi->kabupaten }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Tidak ada data lokasi</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="card shadow">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title flex-grow-1 mb-0">Program Studi</h4>
                            <div class="flex-shrink-0">
                                <button class="btn btn-outline-primary btn-sm" type="button"><i
                                        class="bx bx-download me-1"></i>Download Report</button>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0" data-simplebar style="max-height: 350px;">
                            <table class="table w-100 nowrap">
                                <thead class="table-light fixed top-0">
                                    <tr>
                                        <th>Program Studi</th>
                                        <th>Total Unit</th>
                                        <th>Total Mahasiswa</th>
                                    </tr>
                                </thead>
                                <tbody id="data-prodi" style="overflow-y: auto; width: 100%;">
                                    @forelse($data_prodi as $prodi)
                                        <tr>
                                            <td>{{ $prodi->nama_prodi }}</td>
                                            <td>{{ $prodi->total_unit }}</td>
                                            <td>{{ $prodi->total_mahasiswa }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Tidak ada data program studi</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pageScript')
    {{-- Script untuk dashboard DPL --}}
@endsection