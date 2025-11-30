@extends('layouts.index')
@section('title', 'Evaluasi Monev Mahasiswa')
@section('styles')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Evaluasi Monev Mahasiswa</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Administrator</a></li>
                                <li class="breadcrumb-item active">Evaluasi Monev</li>
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
                            <div class="row">
                                <div class="col-md-6">
                                    <form method="GET" action="{{ route('evaluasi.index') }}" class="d-flex">
                                        <select name="kkn_id" class="form-select me-2" onchange="this.form.submit()">
                                            <option value="">Pilih Periode KKN</option>
                                            @foreach ($kkns as $kkn)
                                                <option value="{{ $kkn->id }}" {{ $selectedKkn == $kkn->id ? 'selected' : '' }}>
                                                    {{ $kkn->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                                <div class="col-md-6 text-end">
                                    @if($selectedKkn)
                                        <a href="{{ route('evaluasi.export', ['kkn_id' => $selectedKkn]) }}" class="btn btn-success">
                                            <i class="bx bx-download me-1"></i>Export ke Excel
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($selectedKkn && $evaluations->isNotEmpty())
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>NIM</th>
                                                <th>Nama Mahasiswa</th>
                                                <th>Unit</th>
                                                <th>Lokasi</th>
                                                <th>DPL</th>
                                                <th>Tim Monev</th>
                                                <th>Catatan</th>
                                                @php
                                                    $kriteria = \App\Models\KriteriaMonev::where('id_kkn', $selectedKkn)->orderBy('urutan')->get();
                                                @endphp
                                                @foreach($kriteria as $k)
                                                    <th>{{ $k->judul }}</th>
                                                @endforeach
                                                <th>Rata-rata</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($evaluations as $evaluasi)
                                                <tr>
                                                    <td>{{ $evaluasi->mahasiswa->userRole->user->username ?? '-' }}</td>
                                                    <td>{{ $evaluasi->mahasiswa->userRole->user->nama ?? '-' }}</td>
                                                    <td>{{ $evaluasi->mahasiswa->unit->nama ?? '-' }}</td>
                                                    <td>
                                                        @php
                                                            $lokasi = $evaluasi->mahasiswa->unit->lokasi;
                                                            echo $lokasi ? ($lokasi->kecamatan->kabupaten->nama . ', ' . $lokasi->kecamatan->nama) : '-';
                                                        @endphp
                                                    </td>
                                                    <td>{{ $evaluasi->mahasiswa->unit->dpl->dosen->user->nama ?? '-' }}</td>
                                                    <td>{{ $evaluasi->timMonev->dosen->user->nama ?? '-' }}</td>
                                                    <td>{{ $evaluasi->catatan_monev ?? '-' }}</td>
                                                    @php
                                                        $totalScore = 0;
                                                        $count = 0;
                                                    @endphp
                                                    @foreach($kriteria as $k)
                                                        @php
                                                            $detail = $evaluasi->details->where('id_kriteria_monev', $k->id)->first();
                                                            $score = $detail ? $detail->nilai : '-';
                                                            if (is_numeric($score)) {
                                                                $totalScore += $score;
                                                                $count++;
                                                            }
                                                        @endphp
                                                        <td>{{ $score }}</td>
                                                    @endforeach
                                                    <td>{{ $count > 0 ? round($totalScore / $count, 2) : '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @elseif($selectedKkn)
                                <div class="text-center py-4">
                                    <p class="text-muted">Belum ada data evaluasi untuk periode KKN ini.</p>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-muted">Pilih periode KKN untuk melihat hasil evaluasi.</p>
                                </div>
                            @endif
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

    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json'
                },
                columnDefs: [
                    { orderable: false, targets: -1 } // Disable sorting on last column (Rata-rata)
                ]
            });
        });
    </script>
@endsection
