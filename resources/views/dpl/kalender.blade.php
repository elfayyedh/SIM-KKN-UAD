@extends('layouts.index')

@section('title', 'Kalender Kegiatan ')
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">KALENDER KEGIATAN</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Unit</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Kalender kegiatan</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row mb-4 p-3">
                <div class="col-12 col-md-5 mb-2 mb-md-0">
                    <div class="form-group">
                        <label for="periode_kkn" class="form-label fw-bold">Pilih Periode KKN</label>
                        <select id="periode_kkn" class="form-select border-primary">
                            <option value="">-- Pilih Periode KKN --</option>
                            @foreach($units->unique('id_kkn') as $unitItem)
                                <option value="{{ $unitItem->id_kkn }}">
                                    {{ $unitItem->kkn->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-12 col-md-5 mb-2 mb-md-0">
                    <div class="form-group">
                        <label for="unit" class="form-label fw-bold">Pilih Unit Bimbingan</label>
                        <select name="unit" id="unit" class="form-select">
                            <option value="">-- Semua Unit Bimbingan --</option>
                            @foreach($units as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-12 col-md-2 d-flex align-items-end justify-content-md-end">
                    <button type="button" id="refreshCalendar" class="btn btn-primary w-100" title="Refresh Kalender">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>


            <div class="row">
                <div class="col-12">

                    <div class="row">
                        <div class="col-xl-3 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div id="external-events" class="mt-2">
                                        <br>
                                        <p class="text-muted">Informasi warna : </p>
                                        <div class="external-event fc-event text-white bg-primary" data-class="bg-info">
                                            Tanggal Realisasi
                                        </div>
                                        <div class="external-event fc-event text-white bg-warning" data-class="bg-warning">
                                            Tanggal Rencana
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div> <!-- end col-->

                        <div class="col-xl-9 col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <div id="calendar"></div>
                                </div>
                            </div>
                        </div> <!-- end col -->

                    </div>

                    <div style='clear:both'></div>


                    <!-- Modal -->
                    <div class="modal fade" id="kegiatanDetailModal" tabindex="-1" role="dialog"
                        aria-labelledby="kegiatanDetailModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="kegiatanDetailModalLabel">Detail Kegiatan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-hidden="true"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Nama Kegiatan:</strong> <span id="modalNamaKegiatan"></span></p>
                                    <p><strong>Nama Proker:</strong> <span id="modalNamaProker"></span></p>
                                    <p><strong>Bidang Proker:</strong> <span id="modalBidangProker"></span></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="id_unit" value="">
@endsection
@section('pageScript')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>
    <script src="{{ asset('assets/js/init/dpl/unit/kalender.init.js') }}"></script>
    <script>
        $(document).ready(function() {
            
            // 1. Amankan daftar asli semua unit bawaan controller ke memori browser
            const originalUnitOptions = $('#unit').html();

            // 2. Logika ketika Dropdown Periode KKN Diubah
            $('#periode_kkn').on('change', function() {
                const kknId = $(this).val();
                const unitSelect = $('#unit');

                $('#id_unit').val('');
                
                try {
                    if (typeof calendar !== 'undefined' && typeof calendar.getEvents === 'function') { 
                        calendar.getEvents().forEach(event => event.remove()); 
                    }
                } catch (err) {
                    console.warn("FullCalendar belum siap atau method remove berbeda:", err);
                }

                if (kknId) {
                    unitSelect.empty().append('<option value="">-- Sedang memuat... --</option>');
                    $.ajax({
                        url: "{{ route('dpl.units.by.kkn', '') }}/" + kknId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            unitSelect.empty().append('<option value="">-- Pilih Unit Bimbingan --</option>');
                            
                            if (data.length > 0) {
                                $.each(data, function(key, value) {
                                    unitSelect.append(`<option value="${value.id}">${value.nama}</option>`);
                                });
                            } else {
                                unitSelect.append('<option value="">Tidak ada unit di periode ini</option>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Detail kegagalan AJAX:", xhr.responseText);
                            unitSelect.empty().append('<option value="">Gagal memuat data unit</option>');
                            alert('Terjadi kendala koneksi saat mengambil data unit.');
                        }
                    });
                } else {
                    unitSelect.html(originalUnitOptions);
                }
            });

            $('#unit').on('change', function() {
                const unitId = $(this).val();
                $('#id_unit').val(unitId);

                if (typeof calendar !== 'undefined' && unitId) {
                    calendar.refetchEvents();
                }
            });

            $('#refreshCalendar').on('click', function() {
                if (typeof calendar !== 'undefined' && $('#id_unit').val()) {
                    calendar.refetchEvents();
                }
            });
        });
    </script>
@endsection
