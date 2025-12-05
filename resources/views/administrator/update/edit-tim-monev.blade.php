@extends('layouts.index')
@section('title', 'Edit Tim Monev')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            
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

            <form action="{{ route('tim-monev.update', $timMonev->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                {{-- ID KKN HIDDEN --}}
                <input type="hidden" name="id_kkn" id="id_kkn" value="{{ $timMonev->id_kkn }}">

                {{-- TABEL PILIH DOSEN --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title text-muted fw-bold mb-3">Dosen Tim Monev</div>
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information me-1"></i> Silahkan <b>centang</b> dosen yang akan ditugaskan.
                                </div>

                                <div class="table-responsive">
                                    <table id="datatable-dosen" class="table table-bordered table-striped dt-responsive nowrap w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Nama Dosen</th>
                                                <th>NIP</th>
                                                <th>Email</th>
                                                <th>Jenis Kelamin</th>
                                                <th>No HP</th>
                                                <th width="10%">Pilih</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dosen as $item)
                                                <tr class="{{ $timMonev->id_dosen == $item->id ? 'table-success' : '' }}">
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->user->nama ?? 'N/A' }}</td>
                                                    <td>{{ $item->nip ?? 'N/A' }}</td>
                                                    <td>{{ $item->user->email ?? 'N/A' }}</td>
                                                    <td>{{ $item->user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                                    <td>{{ $item->user->no_telp ?? 'N/A' }}</td>
                                                    <td class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input dosen-selector" 
                                                                   type="radio" 
                                                                   name="id_dosen" 
                                                                   id="dosen_{{ $item->id }}" 
                                                                   value="{{ $item->id }}"
                                                                   style="transform: scale(1.3); cursor: pointer;"
                                                                   {{ $timMonev->id_dosen == $item->id ? 'checked' : '' }}>
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

                {{-- TABEL PLOTTING UNIT --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Plotting Unit Bimbingan</h5>

                                <div id="loading-state" class="text-center py-5 d-none">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Memuat Data Unit...</p>
                                </div>

                                <div id="table-container">
                                    <div class="alert alert-warning">
                                        <i class="mdi mdi-check-circle-outline me-1"></i> 
                                        Unit yang <b>tercentang</b> adalah unit yang akan dipegang oleh dosen yang dipilih diatas.
                                    </div>
                                    <div class="table-responsive">
                                        <table id="datatable-unit" class="table table-bordered table-striped dt-responsive nowrap w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%" class="text-center">Pilih</th>
                                                    <th>Periode KKN</th>
                                                    <th>Nama Unit</th>
                                                    <th>Lokasi</th>
                                                    <th>DPL</th>
                                                    <th>Status Monev</th>
                                                </tr>
                                            </thead>
                                            <tbody id="unit-list-body">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-warning"><i class="bx bx-save me-1"></i> Update Data</button>
                                    <a href="{{ route('tim-monev.index') }}" class="btn btn-secondary">Batal</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <input type="hidden" id="current_monev_id" value="{{ $timMonev->id }}">
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
        
        $('#datatable-dosen').DataTable({
            responsive: true,
            lengthChange: true, 
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            language: { 
                emptyTable: "Tidak ada data dosen",
                search: "Cari Dosen:",
                lengthMenu: "Tampilkan _MENU_ dosen"
            }
        });

        let currentMonevId = $('#current_monev_id').val();

        function loadUnits() {
            let kknId = $('#id_kkn').val();
            let checkedDosen = $('input[name="id_dosen"]:checked');
            let dosenId = checkedDosen.val();

            let loadingState = $('#loading-state');
            let tableContainer = $('#table-container');
            let tbody = $('#unit-list-body');

            // Hapus DataTable lama
            if ($.fn.DataTable.isDataTable('#datatable-unit')) {
                $('#datatable-unit').DataTable().destroy();
            }

            tbody.empty();
            
            if (!dosenId) {
                tbody.html('<tr><td colspan="6" class="text-center text-muted py-4">Silahkan <b>Centang 1 Dosen</b> terlebih dahulu.</td></tr>');
                return;
            }

            tableContainer.addClass('d-none');
            loadingState.removeClass('d-none');

            let ajaxUrl = "/tim-monev/get-units/" + kknId + "?id_dosen=" + dosenId + "&current_monev_id=" + currentMonevId;

            $.ajax({
                url: ajaxUrl,
                type: "GET",
                success: function(response) {
                    loadingState.addClass('d-none');
                    tableContainer.removeClass('d-none'); 
                    
                    if (response.length === 0) {
                        tbody.html('<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada unit tersedia.</td></tr>');
                    } else {
                        $.each(response, function(index, unit) {
                            let statusBadge = '<span class="badge bg-light text-secondary">Belum Diplot</span>';
                            let rowClass = '';
                            let isChecked = false;
                            
                            let dplName = (unit.dpl && unit.dpl.dosen && unit.dpl.dosen.user) ? unit.dpl.dosen.user.nama : '<span class="text-danger fst-italic">Belum ada DPL</span>';
                            let kknName = (unit.kkn) ? unit.kkn.nama : '-';
                            let lokasiName = (unit.lokasi) ? unit.lokasi.nama : '-';

                            if (unit.id_tim_monev == currentMonevId) {
                                statusBadge = `<span class="badge bg-success">Milik Dosen Ini</span>`;
                                rowClass = 'table-success table-opacity-10'; 
                                isChecked = true;
                            }
                            else if (unit.tim_monev && unit.tim_monev.dosen && unit.tim_monev.dosen.user) {
                                let monevName = unit.tim_monev.dosen.user.nama;
                                statusBadge = `<span class="badge bg-warning text-dark">Milik: ${monevName}</span>`;
                                rowClass = 'table-warning'; 
                            }

                            let row = `
                                <tr class="${rowClass}">
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <input type="checkbox" name="units[]" value="${unit.id}" class="form-check-input" style="transform: scale(1.3); cursor: pointer;" ${isChecked ? 'checked' : ''}>
                                        </div>
                                    </td>
                                    <td>${kknName}</td>
                                    <td class="fw-bold">${unit.nama}</td>
                                    <td><small>${lokasiName}</small></td>
                                    <td><small class="text-primary fw-bold">${dplName}</small></td>
                                    <td>${statusBadge}</td>
                                </tr>
                            `;
                            tbody.append(row);
                        });

                        let tableUnit = $('#datatable-unit').DataTable({
                            responsive: true,
                            destroy: true,
                            lengthChange: true,
                            pageLength: 10, 
                            autoWidth: false,
                            language: {
                                search: "Cari Unit:",
                                emptyTable: "Tidak ada data unit",
                                zeroRecords: "Unit tidak ditemukan",
                                lengthMenu: "Tampilkan _MENU_ unit" // Labelnya
                            }
                        });

                        tableUnit.columns.adjust().draw();
                    }
                },
                error: function(xhr) {
                    loadingState.addClass('d-none');
                    alert("Gagal memuat data unit.");
                }
            });
        }

        loadUnits();

        $('#datatable-dosen tbody').on('change', '.dosen-selector', function() {
            if(this.checked) {
                $('.dosen-selector').not(this).prop('checked', false);
                $('#datatable-dosen tbody tr').removeClass('table-success');
                $(this).closest('tr').addClass('table-success');
                loadUnits();
            } else {
                $(this).closest('tr').removeClass('table-success');
                if ($.fn.DataTable.isDataTable('#datatable-unit')) {
                    $('#datatable-unit').DataTable().destroy();
                }
                $('#unit-list-body').empty(); 
            }
        });
    });
</script>
@endsection