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
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title text-muted fw-bold mb-3">Form Edit Tim Monev</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="id_kkn" class="form-label">Pilih Periode KKN <span class="text-danger">*</span></label>
                                            <select name="id_kkn" id="id_kkn" class="form-select" required>
                                                <option value="">-- Pilih KKN --</option>
                                                @foreach ($kkn as $item)
                                                    <option value="{{ $item->id }}" {{ $timMonev->id_kkn == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="id_dosen" class="form-label">Pilih Dosen <span class="text-danger">*</span></label>
                                            <select name="id_dosen" id="id_dosen" class="form-select" required>
                                                <option value="">-- Pilih Dosen --</option>
                                                @foreach ($dosen as $item)
                                                    <option value="{{ $item->id }}" {{ $timMonev->id_dosen == $item->id ? 'selected' : '' }}>
                                                        {{ $item->user->nama }} ({{ $item->nip }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">* Unit bimbingan dosen ini (sebagai DPL) tidak akan muncul di daftar plotting.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card border-info border-top border-3">
                            <div class="card-body">
                                <h5 class="card-title mb-3 text-info">Plotting Unit Bimbingan</h5>

                                {{-- State Loading --}}
                                <div id="loading-state" class="text-center py-5 d-none">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Memuat Data Unit...</p>
                                </div>

                                {{-- State Table --}}
                                <div id="table-container">
                                    <div class="alert alert-info">
                                        <i class="mdi mdi-information me-1"></i> 
                                        Unit yang <b>tercentang</b> adalah unit yang saat ini dipegang oleh dosen ini.
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%" class="text-center">Pilih</th>
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
            <input type="hidden" id="owned_units" value="{{ json_encode($units->where('id_tim_monev', $timMonev->id)->pluck('id')) }}">
        </div>
    </div>
@endsection

@section('pageScript')
<script>
    $(document).ready(function() {
        
        // Ambil data unit yang sudah dimiliki saat load halaman
        let ownedUnits = JSON.parse($('#owned_units').val() || '[]');
        let currentMonevId = $('#current_monev_id').val();

        // Fungsi Load Data Unit
        function loadUnits() {
            let kknId = $('#id_kkn').val();
            let dosenId = $('#id_dosen').val();

            let loadingState = $('#loading-state');
            let tableContainer = $('#table-container');
            let tbody = $('#unit-list-body');

            // Reset View
            tbody.empty();
            
            if (!kknId || !dosenId) return;

            // Show Loading
            tableContainer.addClass('d-none');
            loadingState.removeClass('d-none');

            // Request AJAX
            $.ajax({
                url: "/tim-monev/get-units/" + kknId + "?id_dosen=" + dosenId,
                type: "GET",
                success: function(response) {
                    loadingState.addClass('d-none');
                    
                    if (response.length === 0) {
                        tbody.html('<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada unit tersedia.</td></tr>');
                    } else {
                        $.each(response, function(index, unit) {
                            let statusBadge = '<span class="badge bg-light text-secondary">Belum Diplot</span>';
                            let rowClass = '';
                            let dplName = unit.dpl && unit.dpl.dosen && unit.dpl.dosen.user ? unit.dpl.dosen.user.nama : '<span class="text-danger">Belum ada DPL</span>';
                            
                            // Cek Status Kepemilikan
                            let isChecked = false;
                            if (unit.id_tim_monev == currentMonevId) {
                                statusBadge = `<span class="badge bg-success">Milik Dosen Ini</span>`;
                                rowClass = 'table-success table-opacity-10'; // Highlight Hijau
                                isChecked = true;
                            }
                            else if (unit.tim_monev && unit.tim_monev.dosen && unit.tim_monev.dosen.user) {
                                let monevName = unit.tim_monev.dosen.user.nama;
                                statusBadge = `<span class="badge bg-warning text-dark">Milik: ${monevName}</span>`;
                                rowClass = 'table-warning'; // Highlight Kuning
                            }

                            let row = `
                                <tr class="${rowClass}">
                                    <td class="text-center">
                                        <input type="checkbox" 
                                               name="units[]" 
                                               value="${unit.id}" 
                                               class="form-check-input" 
                                               style="transform: scale(1.3); cursor: pointer;"
                                               ${isChecked ? 'checked' : ''}>
                                    </td>
                                    <td class="fw-bold">${unit.nama}</td>
                                    <td><small>${unit.lokasi ? unit.lokasi.nama : '-'}</small></td>
                                    <td><small class="text-primary">${dplName}</small></td>
                                    <td>${statusBadge}</td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    }
                    tableContainer.removeClass('d-none');
                },
                error: function(xhr) {
                    loadingState.addClass('d-none');
                    alert("Gagal memuat data unit.");
                }
            });
        }

        loadUnits();

        // Load ulang saat Dropdown berubah
        $('#id_kkn, #id_dosen').change(function() {
            ownedUnits = []; 
            loadUnits();
        });

    });
</script>
@endsection