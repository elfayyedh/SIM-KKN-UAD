@extends('layouts.index')

@section('title', 'Evaluasi Mahasiswa | SIM KKN UAD')

@section('pageStyle')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
    <style>
        .nilai-input {
            min-width: 70px;
            max-width: 90px;
        }
        thead th {
            vertical-align: middle !important;
            text-align: center;
            font-weight: 600;
            background-color: #f8f9fa;
        }
    </style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">EVALUASI MAHASISWA</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">SIM KKN UAD</a></li>
                            <li class="breadcrumb-item active">Evaluasi Mahasiswa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="row mb-3">
            <div class="col-12">
                {{-- ID FORM DITAMBAHKAN UNTUK HANDLE SUBMIT --}}
                <form id="form-evaluasi" action="{{ route('admin.evaluasi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="kkn_id" value="{{ $kkn->id ?? '' }}">
                    
                    <div class="card shadow">
                        {{-- Card Header --}}
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title flex-grow-1 mb-0">Evaluasi Mahasiswa</h4>
                            <div class="flex-shrink-0">
                                <a href="{{ route('admin.evaluasi.export', $kkn->id ?? '') }}" class="btn btn-outline-primary btn-sm me-2">
                                    <i class="bx bx-download me-1"></i> Export Excel
                                </a>
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bx bx-save me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="table-evaluasi" class="table table-bordered dt-responsive nowrap w-100 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 200px;" class="text-center">Mahasiswa</th>
                                        @foreach($kriteriaList as $kriteria)
                                            <th style="min-width: 100px;" class="text-center">
                                                {{ $kriteria->judul }}<br>
                                                <small class="text-muted" style="font-weight: normal;">(1.0 - 3.0)</small>
                                            </th>
                                        @endforeach
                                        
                                        <th style="min-width: 100px;" class="text-center">Nilai Akhir</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($mahasiswa as $mhs)
                                        <tr>
                                            {{-- 1. Nama + Identitas --}}
                                            <td>
                                                <strong>{{ $mhs->userRole->user->nama ?? '-' }}</strong><br>
                                                <small class="text-muted">{{ $mhs->nim }}</small><br>
                                                <small class="text-muted">{{ $mhs->no_hp ?? '' }}</small>
                                            </td>

                                            {{-- 2. Input Nilai per Kriteria --}}
                                            @php
                                                $totalNilai = 0;
                                                $jumlahKriteria = 0;
                                            @endphp
                                            @foreach($kriteriaList as $kriteria)
                                                @php
                                                    $nilaiDefault = $mappedNilai[$mhs->id][$kriteria->id] ?? '';
                                                    if ($nilaiDefault !== '' && is_numeric($nilaiDefault)) {
                                                        $totalNilai += (float)$nilaiDefault;
                                                        $jumlahKriteria++;
                                                    }
                                                @endphp
                                                <td class="text-center">
                                                    <input 
                                                        type="number" 
                                                        name="evaluasi[{{ $mhs->id }}][{{ $kriteria->id }}]" 
                                                        class="form-control form-control-sm text-center nilai-input" 
                                                        data-mahasiswa="{{ $mhs->id }}"
                                                        value="{{ $nilaiDefault }}" 
                                                        min="1" max="3" step="0.01"
                                                        placeholder="-">
                                                </td>
                                            @endforeach

                                            {{-- 3. Nilai Akhir (Auto Calculate) --}}
                                            @php
                                                $nilaiAkhir = $jumlahKriteria > 0 ? number_format($totalNilai / $jumlahKriteria, 2) : '-';
                                            @endphp
                                            <td class="text-center">
                                                <strong class="nilai-akhir" data-mahasiswa="{{ $mhs->id }}">
                                                    {{ $nilaiAkhir }}
                                                </strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageScript')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#table-evaluasi').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": false, 
                "info": true, 
                "autoWidth": false, 
                "responsive": false,
                "scrollX": true,
                "language": {
                    "search": "Cari Mahasiswa:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Tidak ada data mahasiswa ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data tersedia",
                    "paginate": {
                        "first": "Awal",
                        "last": "Akhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
            $('#table-evaluasi tbody').on('input', '.nilai-input', function() {
                var row = $(this).closest('tr');
                var inputs = row.find('.nilai-input');
                var total = 0;
                var count = 0;

                inputs.each(function() {
                    var val = parseFloat($(this).val());
                    if (!isNaN(val) && val > 0) {
                        total += val;
                        count++;
                    }
                });

                var avg = count > 0 ? (total / count).toFixed(2) : '-';
                row.find('.nilai-akhir').text(avg);
            });

            $('#form-evaluasi').on('submit', function(e){
                var form = this;

                table.$('input').each(function(){
                    if(!$.contains(document, this)){
                        $(form).append(
                            $('<input>')
                                .attr('type', 'hidden')
                                .attr('name', this.name)
                                .attr('value', this.value)
                        );
                    }
                });
            });
        });
    </script>
@endsection