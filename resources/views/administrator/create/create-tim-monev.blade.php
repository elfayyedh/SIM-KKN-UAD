@extends('layouts.index')
@section('title', 'Tambah Tim Monev')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Tambah Tim Monev</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Tim Monev</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('tim-monev.index') }}">Daftar Tim Monev</a></li>
                                <li class="breadcrumb-item active">Tambah Tim Monev</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('tim-monev.store') }}" method="POST">
                @csrf
                
                <input type="hidden" name="id_kkn" id="id_kkn_global">

                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-information-variant me-2"></i>
                    Sistem otomatis <b>menyembunyikan Unit</b> dimana Dosen yang dipilih menjabat sebagai DPL.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title text-muted fw-bold mb-3">Pilih Dosen Monev</div>
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
                                        <div class="text-muted"><i class="mdi mdi-check-circle-outline me-1"></i> Centang unit yang akan dimonev.</div>
                                        <div id="filter-status" class="badge bg-secondary">Menampilkan Semua Unit</div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="datatable-unit" class="table table-bordered table-striped dt-responsive nowrap w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%" class="text-center">Pilih</th> <th>Periode KKN</th>                          <th>Nama Unit</th>                            <th>Lokasi</th>                               <th>DPL</th>                                  <th>Status Monev</th>                         <th>ID_DPL</th>                               </tr>
                                            </thead>
                                            <tbody id="unit-list-body">
                                                </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan Data</button>
                                    <a href="{{ route('tim-monev.index') }}" class="btn btn-secondary">Batal</a>
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
        $('#datatable-dosen').DataTable({
            responsive: true, lengthChange: true, pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            language: { emptyTable: "Tidak ada data dosen", search: "Cari Dosen:" }
        });
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            // Filter hanya untuk tabel unit
            if (settings.nTable.id !== 'datatable-unit') return true;
            
            // Jika belum pilih dosen, tampilkan semua
            if (selectedDosenId == null) return true;

            // Ambil ID DPL dari Kolom Index 6
            var dplIdInRow = data[6] || '0'; 

            if (dplIdInRow.toString() === selectedDosenId.toString()) {
                return false; 
            }
            return true;
        });

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
                    console.log("Jumlah Unit:", response.length); // Cek di Console
                    
                    loadingState.addClass('d-none');
                    tableContainer.removeClass('d-none'); 
                    
                    if (!response || response.length === 0) {
                        tbody.html('<tr><td colspan="7" class="text-center text-danger py-4">Data Unit Kosong. Pastikan KKN Aktif dipilih.</td></tr>');
                    } else {
                        if(response[0] && response[0].id_kkn){
                            $('#id_kkn_global').val(response[0].id_kkn);
                        }
                        let rowsHTML = ""; 
                        
                        $.each(response, function(index, unit) {
                            let statusBadge = '<span class="badge bg-light text-secondary">Belum Diplot</span>';
                            let rowClass = '';
                            
                            let dplName = '<span class="text-danger fst-italic">Belum ada DPL</span>';
                            let dplId = '0'; 

                            if (unit.dpl && unit.dpl.dosen && unit.dpl.dosen.user) {
                                dplName = unit.dpl.dosen.user.nama;
                                dplId = unit.dpl.dosen.id; 
                            }

                            let kknName = (unit.kkn) ? unit.kkn.nama : '-';
                            let lokasiName = (unit.lokasi) ? unit.lokasi.nama : '-';

                            if (unit.tim_monev && unit.tim_monev.dosen && unit.tim_monev.dosen.user) {
                                let monevName = unit.tim_monev.dosen.user.nama;
                                statusBadge = `<span class="badge bg-warning text-dark">Milik: ${monevName}</span>`;
                                rowClass = 'table-warning'; 
                            }

                            // Tambahkan string HTML ke variable
                            rowsHTML += `
                                <tr class="${rowClass}">
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <input type="checkbox" name="units[]" value="${unit.id}" class="form-check-input" style="transform: scale(1.3); cursor: pointer;">
                                        </div>
                                    </td>
                                    <td>${kknName}</td>
                                    <td class="fw-bold">${unit.nama}</td>
                                    <td><small>${lokasiName}</small></td>
                                    <td><small class="text-primary fw-bold">${dplName}</small></td>
                                    <td>${statusBadge}</td>
                                    <td>${dplId}</td>
                                </tr>
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
                                { targets: 6, visible: false, searchable: true } 
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
                    alert("Gagal load data unit. Cek console browser.");
                }
            });
        }

        $('#datatable-dosen tbody').on('change', '.dosen-selector', function() {
            if(this.checked) {
                $('.dosen-selector').not(this).prop('checked', false);
                $('#datatable-dosen tbody tr').removeClass('table-success');
                $(this).closest('tr').addClass('table-success');
                
                selectedDosenId = $(this).val();
                
                $('#filter-status')
                    .removeClass('bg-secondary').addClass('bg-success')
                    .html('<i class="bx bx-filter-alt"></i> Filter Aktif: Menyembunyikan Unit Bimbingan Dosen Terpilih');

                if (tableUnit) {
                    tableUnit.draw();
                }
            }
        });
    });
</script>
@endsection