@extends('layouts.index')

@section('title', 'Evaluasi Mahasiswa | SIM KKN UAD')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            {{-- Page title --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">EVALUASI MAHASISWA</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">SIM KKN UAD</a></li>
                                <li class="breadcrumb-item active">Evaluasi Mahasiswa</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            {{-- End Page title --}}

            <div class="row mb-3">
                <div class="col-12">
                    <p class="fw-bold mb-1">Halo, Administrator</p>
                    <p>Selamat datang di SIM KKN UAD</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-xl-3 col-md-6">
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
                <div class="col-xl-3 col-md-6">
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
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <p class="fw-bold font-size-17 card-title">Sudah Dinilai</p>
                            <div class="d-flex w-100 position-relative">
                                <div class="konten">
                                    <h2 class="total-dinilai">
                                        <div class="placeholder-glow"><span class="placeholder col-12"></span></div>
                                    </h2>
                                    <a class="text-decoration-none" href="#">Lihat mahasiswa dinilai</a>
                                </div>
                                <div class="position-absolute d-flex justify-content-center align-items-center bottom-0 end-0 rounded bg-success"
                                    style="width: 50px; height: 50px;">
                                    <i class="bx bx-check-circle font-size-24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <p class="fw-bold font-size-17 card-title">Belum Dinilai</p>
                            <div class="d-flex w-100 position-relative">
                                <div class="konten">
                                    <h2 class="total-belum-dinilai">
                                        <div class="placeholder-glow"><span class="placeholder col-12"></span></div>
                                    </h2>
                                    <a class="text-decoration-none" href="#">Lihat mahasiswa belum dinilai</a>
                                </div>
                                <div class="position-absolute d-flex justify-content-center align-items-center bottom-0 end-0 rounded bg-warning"
                                    style="width: 50px; height: 50px;">
                                    <i class="bx bx-time-five font-size-24"></i>
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
                            <h4 class="card-title flex-grow-1 mb-0">Mahasiswa Belum Dinilai</h4>
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
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="data-belum-dinilai" style="overflow-y: auto; width: 100%;">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title flex-grow-1 mb-0">Evaluasi Mahasiswa</h4>
                            <div class="flex-shrink-0">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-success btn-sm" type="button" id="save-evaluations">
                                            <i class="bx bx-save me-1"></i>Simpan Evaluasi
                                        </button>
                                        <a href="{{ route('admin.evaluasi.export', $kkn->id ?? '') }}" id="export-excel-btn" class="btn btn-outline-primary btn-sm">
                                            <i class="bx bx-download me-1"></i>Export Excel
                                        </a>
                                    </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0" data-simplebar style="max-height: 500px;">
                            {{-- Export button restored to header (semula) --}}
                            <form id="evaluation-form" method="POST" action="{{ route('admin.evaluasi.store') }}">
                                @csrf
                                <input type="hidden" name="kkn_id" value="{{ $kkn->id ?? '' }}">
                                <table id="evaluation-table" class="table w-100 nowrap">
                                    <thead class="table-light fixed top-0">
                                        <tr>
                                            <th>Nama Mahasiswa</th>
                                            <th>NIM</th>
                                            <th>Unit</th>
                                            <th>Tim Monev</th>
                                            @foreach($kriteriaList as $kriteria)
                                                <th>{{ Str::limit($kriteria->judul, 20) }}</th>
                                            @endforeach
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($mahasiswa as $mhs)
                                            <tr>
                                                <td>{{ $mhs->userRole->user->nama ?? '-' }}</td>
                                                <td>{{ $mhs->nim ?? '-' }}</td>
                                                <td>{{ $mhs->unit->nama ?? '-' }}</td>
                                                <td>{{ $timMonevInfo[$mhs->id] ?? '-' }}</td>
                                                @foreach($kriteriaList as $kriteria)
                                                    <td class="text-center">
                                                        <select name="evaluasi[{{ $mhs->id }}][{{ $kriteria->id }}]" class="form-select form-select-sm">
                                                            <option value="">-</option>
                                                            <option value="1" {{ ($mappedNilai[$mhs->id][$kriteria->id] ?? '') == '1' ? 'selected' : '' }}>1</option>
                                                            <option value="2" {{ ($mappedNilai[$mhs->id][$kriteria->id] ?? '') == '2' ? 'selected' : '' }}>2</option>
                                                            <option value="3" {{ ($mappedNilai[$mhs->id][$kriteria->id] ?? '') == '3' ? 'selected' : '' }}>3</option>
                                                        </select>
                                                    </td>
                                                @endforeach
                                                <td>
                                                    @if(isset($mappedNilai[$mhs->id]) && !empty(array_filter($mappedNilai[$mhs->id])))
                                                        <span class="badge bg-success">Sudah Dinilai</span>
                                                    @else
                                                        <span class="badge bg-warning">Belum Dinilai</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('pageScript')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/init/administrator/read-kkn.init.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize KKN list DataTable if present
            if ($('#datatable').length) {
                $('#datatable').DataTable();
            }

            // Initialize evaluation DataTable and move export button to its search area
            var dtEval = null;
            if ($('#evaluation-table').length) {
                dtEval = $('#evaluation-table').DataTable({
                    pageLength: 25,
                    lengthChange: false,
                    searching: true,
                    ordering: false,
                    autoWidth: false
                });
            }

            // Bind custom search input (optional) to evaluation DataTable
            $(document).on('input', '#custom-dt-search', function() {
                if (dtEval) dtEval.search(this.value).draw();
            });

            // Load initial data if KKN is selected
            @if($kkn)
                loadDashboardData('{{ $kkn->id }}');
            @endif

            // Handle save evaluations
            $('#save-evaluations').on('click', function() {
                const form = $('#evaluation-form');
                const formData = new FormData(form[0]);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success('Evaluasi berhasil disimpan!');
                        // Reload data
                        loadDashboardData('{{ $kkn->id ?? "" }}');
                    },
                    error: function(xhr) {
                        toastr.error('Gagal menyimpan evaluasi: ' + (xhr.responseJSON?.message || xhr.statusText));
                    }
                });
            });
        });

        function loadDashboardData(kknId) {
            if (!kknId) return;

            // Load card values
            $.get('{{ route("admin.evaluasi.index") }}', { kkn_id: kknId }, function(data) {
                $('.total-mahasiswa').html(data.cardValues.total_mahasiswa);
                $('.total-unit').html(data.cardValues.total_unit);
                $('.total-dinilai').html(data.cardValues.total_dinilai);
                $('.total-belum-dinilai').html(data.cardValues.total_belum_dinilai);

                // Load unit data
                let unitHtml = '';
                data.unitData.forEach(function(unit) {
                    unitHtml += `
                        <tr>
                            <td>${unit.total_unit}</td>
                            <td>${unit.kecamatan}</td>
                            <td>${unit.kabupaten}</td>
                        </tr>
                    `;
                });
                $('#data-lokasi').html(unitHtml);

                // Load belum dinilai data
                let belumDinilaiHtml = '';
                data.belumDinilaiData.forEach(function(mhs) {
                    belumDinilaiHtml += `
                        <tr>
                            <td>${mhs.nama_mahasiswa}</td>
                            <td>${mhs.nama_unit}</td>
                            <td><span class="badge bg-warning">Belum Dinilai</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="scrollToEvaluation('${mhs.id}')">
                                    <i class="bx bx-edit"></i> Nilai
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#data-belum-dinilai').html(belumDinilaiHtml);
            });
        }

        function scrollToEvaluation(studentId) {
            // Scroll to evaluation table and highlight the row
            const row = $(`select[name*="[${studentId}]"]`).closest('tr');
            if (row.length) {
                row[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                row.addClass('table-warning');
                setTimeout(() => row.removeClass('table-warning'), 3000);
            }
        }
    </script>
@endsection
