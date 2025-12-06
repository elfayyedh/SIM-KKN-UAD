@extends('layouts.index')
@section('title', 'Tambah DPL')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Tambah Dosen Pembimbing Lapangan (DPL)</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen DPL</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('dpl.index') }}">Daftar DPL</a></li>
                                <li class="breadcrumb-item active">Tambah DPL</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('dpl.store') }}" method="POST">
                @csrf
                
                <input type="hidden" name="id_kkn" id="id_kkn_global">

                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-information-variant me-2"></i>
                    Silahkan pilih Dosen, lalu centang Unit yang akan dibimbing oleh dosen tersebut.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title text-muted fw-bold mb-3">Pilih Dosen Calon DPL</div>
                                <div class="table-responsive">
                                    <table id="datatable-dosen" class="table table-bordered dt-responsive nowrap w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Nama Dosen</th>
                                                <th>NIP</th>
                                                <th>Email</th>
                                                <th>Jenis Kelamin</th>
                                                <th width="10%" class="text-center bg-primary text-white">Pilih</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dosen as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->user->nama ?? 'N/A' }}</td>
                                                    <td>{{ $item->nip ?? 'N/A' }}</td>
                                                    <td>{{ $item->user->email ?? 'N/A' }}</td>
                                                    <td>{{ $item->user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                                    <td class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input dosen-selector" type="radio" name="id_dosen" id="dosen_{{ $item->id }}" value="{{ $item->id }}" style="transform: scale(1.3); cursor: pointer;">
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Plotting Unit Bimbingan</h5>

                                <div id="loading-state" class="text-center py-5 d-none">
                                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                                    <p class="mt-2">Memuat Semua Data Unit...</p>
                                </div>

                                <div id="table-container">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="text-muted"><i class="mdi mdi-check-circle-outline me-1"></i> Centang unit yang akan dibimbing.</div>
                                        <div id="filter-status" class="badge bg-secondary">Menampilkan Semua Unit</div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="datatable-unit" class="table table-bordered table-striped dt-responsive nowrap w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%" class="text-center">Pilih</th>
                                                    <th>Periode KKN</th>
                                                    <th>Nama Unit</th>
                                                    <th>Lokasi</th>
                                                    <th>DPL Saat Ini</th>
                                                    <th>ID_DPL</th> </tr>
                                            </thead>
                                            <tbody id="unit-list-body">
                                                </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan DPL</button>
                                    <a href="{{ route('dpl.index') }}" class="btn btn-secondary">Batal</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pageScript')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        var selectedDosenId = null; 
        var tableUnit = null; 

        // INIT TABLE DOSEN
        $('#datatable-dosen').DataTable({
            responsive: true, lengthChange: true, pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            language: { emptyTable: "Tidak ada data dosen", search: "Cari Dosen:" }
        });

        // FILTER
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'datatable-unit') return true;
            if (selectedDosenId == null) return true;

            // Ambil ID DPL dari Kolom Index 5
            var rowNode = settings.aoData[dataIndex].nTr;
            var dplIdInRow = $(rowNode).attr('data-dpl-id');

            // Jika Dosen terpilih == DPL Unit tersebut, Sembunyikan
            if (dplIdInRow && dplIdInRow.toString() === selectedDosenId.toString()) {
                return false; 
            }
            return true;
        });

        // LOAD DATA UNIT
        loadAllUnits();

        function loadAllUnits() {
            let loadingState = $('#loading-state');
            let tableContainer = $('#table-container');
            let tbody = $('#unit-list-body');

            tableContainer.addClass('d-none');
            loadingState.removeClass('d-none');

            if ($.fn.DataTable.isDataTable('#datatable-unit')) {
                $('#datatable-unit').DataTable().destroy();
            }
            tbody.empty();

            let ajaxUrl = "/tim-monev/get-all-active-units"; 

            $.ajax({
                url: ajaxUrl,
                type: "GET",
                success: function(response) {
                    loadingState.addClass('d-none');
                    tableContainer.removeClass('d-none'); 
                    
                    if (!response || response.length === 0) {
                        tbody.html('<tr><td colspan="6" class="text-center text-danger py-4">Data Unit Kosong. Pastikan KKN Aktif dipilih.</td></tr>');
                    } else {
                        // Set ID KKN ke Input Hidden
                        if(response[0] && response[0].id_kkn){
                            $('#id_kkn_global').val(response[0].id_kkn);
                        }

                        let rowsHTML = ""; 
                        
                        $.each(response, function(index, unit) {
                            let statusBadge = '<span class="badge bg-light text-secondary">Belum Ada</span>';
                            let rowClass = '';
                            
                            let dplName = '<span class="text-danger fst-italic">Belum ada DPL</span>';
                            let dplId = '0'; 

                            if (unit.dpl && unit.dpl.dosen && unit.dpl.dosen.user) {
                                dplName = unit.dpl.dosen.user.nama;
                                dplId = unit.dpl.dosen.id; 
                                statusBadge = `<span class="badge bg-info">Terisi</span>`;
                            }

                            let kknName = (unit.kkn) ? unit.kkn.nama : '-';
                            let lokasiName = (unit.lokasi) ? unit.lokasi.nama : '-';

                            // Generate HTML
                            rowsHTML += `
                                <tr class="${rowClass}" data-dpl-id="${dplId}">
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <input type="checkbox" name="units[]" value="${unit.id}" class="form-check-input" style="transform: scale(1.3); cursor: pointer;">
                                        </div>
                                    </td>
                                    <td>${kknName}</td>
                                    <td class="fw-bold">${unit.nama}</td>
                                    <td><small>${lokasiName}</small></td>
                                    <td><small class="fw-bold">${dplName}</small></td>
                                    <td>${dplId}</td> </tr>
                            `;
                        });

                        tbody.html(rowsHTML);

                        tableUnit = $('#datatable-unit').DataTable({
                            responsive: true, 
                            destroy: true,
                            lengthChange: true, 
                            pageLength: 10, 
                            autoWidth: false,
                            columnDefs: [
                                { targets: 5, visible: false, searchable: true } // Hide ID_DPL
                            ],
                            language: { 
                                search: "Cari Unit:", 
                                emptyTable: "Tidak ada data unit", 
                                zeroRecords: "Unit tidak ditemukan" 
                            }
                        });
                    }
                },
                error: function(xhr) {
                    loadingState.addClass('d-none');
                    console.error("ERROR AJAX:", xhr);
                    alert("Gagal load data unit.");
                }
            });
        }

        // EVENT PILIH DOSEN
        $('#datatable-dosen tbody').on('change', '.dosen-selector', function() {
            if(this.checked) {
                $('.dosen-selector').not(this).prop('checked', false);
                $('#datatable-dosen tbody tr').removeClass('table-success');
                $(this).closest('tr').addClass('table-success');
                
                selectedDosenId = $(this).val();
                
                $('#filter-status')
                    .removeClass('bg-secondary').addClass('bg-success')
                    .html('<i class="bx bx-filter-alt"></i> Filter: Unit yang sudah dipegang dosen ini disembunyikan');

                if (tableUnit) {
                    tableUnit.draw();
                }
            }
        });
    });
</script>
@endsection