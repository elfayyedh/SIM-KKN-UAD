@extends('layouts.index')

@section('title', 'Dashboard Tim Monev | SIM KKN UAD')

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
                    <p class="fw-bold mb-1">Halo, User Tim Monev</p>
                    <p>Selamat datang di SIM KKN UAD</p>
                </div>
                <div class="col-12 col-md-6 d-flex justify-content-md-end">
                    <div class="form-group w-100">
                        <label for="periode" class="form-label">Pilih periode KKN</label>
                        <select name="periode" id="periode" class="form-select">
                            @foreach ($kkn as $item)
                                <option value="{{ $item->id }}" {{ $item->id == $id_kkn ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
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
                                    <h2 class="total-mahasiswa">
                                        <div class="placeholder-glow"><span class="placeholder col-12"></span></div>
                                    </h2>
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
                                    <h2 class="total-unit">
                                        <div class="placeholder-glow"><span class="placeholder col-12"></span></div>
                                    </h2>
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

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="card shadow">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title flex-grow-1 mb-0">Total Mahasiswa yang Belum Dinilai</h4>
                            <div class="flex-shrink-0">
                                <button class="btn btn-outline-primary btn-sm" type="button"><i
                                        class="bx bx-download me-1"></i>Download Report</button>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0" data-simplebar style="max-height: 350px;">
                            <table class="table w-100 nowrap">
                                <thead class="table-light fixed top-0">
                                    <tr>
                                        <th>Nama Mahasiswa</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="data-belum-dinilai" style="overflow-y: auto; width: 100%;">

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
    <script>
        $(document).ready(function() {
            // Load data on page load
            loadAllData();

            // Reload data when periode changes
            $('#periode').on('change', function() {
                loadAllData();
            });
        });

        function loadAllData() {
            loadCardValues();
            loadLokasiData();
            loadBelumDinilaiData();
        }

        function loadCardValues() {
            const periode = $('#periode').val();

            $.ajax({
                url: '/card-value',
                type: 'GET',
                data: { periode: periode },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('.total-mahasiswa').html(response.total_mahasiswa);
                        $('.total-unit').html(response.total_unit);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading card values:', error);
                    $('.total-mahasiswa').html('0');
                    $('.total-unit').html('0');
                }
            });
        }

        function loadLokasiData() {
            const periode = $('#periode').val();

            $.ajax({
                url: '/get-unit-data',
                type: 'GET',
                data: { periode: periode },
                dataType: 'json',
                success: function(data) {
                    let html = '';
                    if (data.length > 0) {
                        data.forEach(function(item) {
                            html += '<tr>';
                            html += '<td>' + item.total_unit + '</td>';
                            html += '<td>' + item.kecamatan + '</td>';
                            html += '<td>' + item.kabupaten + '</td>';
                            html += '</tr>';
                        });
                    } else {
                        html = '<tr><td colspan="3" class="text-center text-muted">Tidak ada data lokasi</td></tr>';
                    }
                    $('#data-lokasi').html(html);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading lokasi data:', error);
                    $('#data-lokasi').html('<tr><td colspan="3" class="text-center text-danger">Error loading data</td></tr>');
                }
            });
        }

        function loadBelumDinilaiData() {
            const periode = $('#periode').val();

            $.ajax({
                url: '/get-belum-dinilai-data',
                type: 'GET',
                data: { periode: periode },
                dataType: 'json',
                success: function(data) {
                    let html = '';
                    if (data.length > 0) {
                        data.forEach(function(item) {
                            html += '<tr>';
                            html += '<td>' + item.nama_mahasiswa + '</td>';
                            html += '<td>' + item.nama_unit + '</td>';
                            html += '<td><span class="badge bg-warning">Belum Dinilai</span></td>';
                            html += '</tr>';
                        });
                    } else {
                        html = '<tr><td colspan="3" class="text-center text-muted">Semua mahasiswa sudah dinilai</td></tr>';
                    }
                    $('#data-belum-dinilai').html(html);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading belum dinilai data:', error);
                    $('#data-belum-dinilai').html('<tr><td colspan="3" class="text-center text-danger">Error loading data</td></tr>');
                }
            });
        }
    </script>
@endsection
