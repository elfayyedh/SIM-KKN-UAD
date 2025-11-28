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
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title text-muted fw-bold mb-3">Form Data Tim Monev</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="id_kkn" class="form-label">Pilih Periode KKN <span class="text-danger">*</span></label>
                                            <select name="id_kkn" id="id_kkn" class="form-select" required>
                                                <option value="">-- Pilih KKN --</option>
                                                @foreach ($kkn as $item)
                                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
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
                                                    <option value="{{ $item->id }}">{{ $item->user->nama }} ({{ $item->nip }})</option>
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
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Plotting Unit Bimbingan</h5>

                                {{-- Belum Pilih KKN/Dosen --}}
                                <div id="empty-state" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bx bx-layer display-4 text-muted"></i>
                                    </div>
                                    <h5 class="text-muted">Lengkapi Form di Atas</h5>
                                    <p class="text-muted">Silakan pilih <b>Periode KKN</b> dan <b>Dosen</b> terlebih dahulu untuk memuat daftar unit.</p>
                                </div>

                                {{-- Loading --}}
                                <div id="loading-state" class="text-center py-5 d-none">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Memuat Data Unit...</p>
                                </div>

                                {{-- Tabel Data --}}
                                <div id="table-container" class="d-none">
                                    <div class="alert alert-info">
                                        <i class="mdi mdi-information me-1"></i> Centang unit yang ingin ditugaskan. Unit berwarna <b>kuning</b> sudah dimiliki dosen monev lain (akan ditimpa jika dicentang).
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
                                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan & Plotting</button>
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
<script>
    $(document).ready(function() {
        
        // Fungsi Load Data Unit
        function loadUnits() {
            let kknId = $('#id_kkn').val();
            let dosenId = $('#id_dosen').val();

            let emptyState = $('#empty-state');
            let loadingState = $('#loading-state');
            let tableContainer = $('#table-container');
            let tbody = $('#unit-list-body');

            // Reset View
            tableContainer.addClass('d-none');
            tbody.empty();

            // Jika salah satu belum dipilih, stop.
            if (!kknId || !dosenId) {
                emptyState.removeClass('d-none');
                return;
            }

            // Mulai Loading
            console.log("Requesting units for KKN:", kknId, " Dosen:", dosenId);
            emptyState.addClass('d-none');
            loadingState.removeClass('d-none');

            // Request AJAX
            $.ajax({
                url: "/tim-monev/get-units/" + kknId + "?id_dosen=" + dosenId,
                type: "GET",
                success: function(response) {
                    console.log("Data diterima:", response);
                    loadingState.addClass('d-none');
                    
                    if (response.length === 0) {
                        tbody.html(`
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-danger mb-2"><i class="bx bx-block display-4"></i></div>
                                    <h5 class="text-danger">Tidak ada unit tersedia</h5>
                                    <p class="text-muted">
                                        Semua unit di KKN ini mungkin dibimbing oleh dosen ini (sebagai DPL), <br>
                                        sehingga tidak bisa dipilih untuk dimonev.
                                    </p>
                                </td>
                            </tr>
                        `);
                    } else {
                        // KASUS: Ada unit yang bisa dipilih
                        $.each(response, function(index, unit) {
                            let statusBadge = '<span class="badge bg-light text-secondary">Belum Diplot</span>';
                            let rowClass = '';
                            let dplName = unit.dpl && unit.dpl.dosen && unit.dpl.dosen.user ? unit.dpl.dosen.user.nama : '<span class="text-danger">Belum ada DPL</span>';
                            
                            // Cek kepemilikan orang lain
                            if (unit.tim_monev && unit.tim_monev.dosen && unit.tim_monev.dosen.user) {
                                let monevName = unit.tim_monev.dosen.user.nama;
                                statusBadge = `<span class="badge bg-warning text-dark">Milik: ${monevName}</span>`;
                                rowClass = 'table-warning';
                            }

                            let row = `
                                <tr class="${rowClass}">
                                    <td class="text-center">
                                        <input type="checkbox" name="units[]" value="${unit.id}" class="form-check-input" style="transform: scale(1.3); cursor: pointer;">
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
                    
                    // Munculkan Tabel (Penting!)
                    tableContainer.removeClass('d-none');
                },
                error: function(xhr) {
                    loadingState.addClass('d-none');
                    console.error("Error:", xhr);
                    alert("Gagal memuat data unit. Cek Console untuk detail.");
                }
            });
        }

        // Trigger saat Dropdown berubah
        $('#id_kkn, #id_dosen').change(function() {
            loadUnits();
        });

    });
</script>
@endsection