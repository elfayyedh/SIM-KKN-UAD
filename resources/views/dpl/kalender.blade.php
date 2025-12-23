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

            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <p class="fw-bold mb-1">Halo, User DPL</p>
                    <p>Selamat datang di Kalender Kegiatan</p>
                </div>
                <div class="col-12 col-md-6 d-flex justify-content-md-end align-items-end">
                    <div class="form-group w-75 me-2">
                        <label for="unit" class="form-label">Pilih Unit</label>
                        <select name="unit" id="unit" class="form-select">
                            @foreach ($units as $item)
                                <option value="{{ $item->id }}" {{ $item->id == $unit->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" id="refreshCalendar" class="btn btn-primary" title="Refresh Kalender">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
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

    <input type="hidden" id="id_unit" value="{{ $unit->id }}">
@endsection
@section('pageScript')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>
    <script src="{{ asset('assets/js/init/dpl/unit/kalender.init.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Reload calendar when unit changes
            $('#unit').on('change', function() {
                loadCalendarData();
            });
        });

        function loadCalendarData() {
            const unitId = $('#unit').val();
            // Update the hidden input with selected unit
            $('#id_unit').val(unitId);

            // Reload calendar events
            if (typeof calendar !== 'undefined') {
                calendar.refetchEvents();
            }
        }
    </script>
@endsection
