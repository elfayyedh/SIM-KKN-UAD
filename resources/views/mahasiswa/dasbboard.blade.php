@extends('layouts.index')

@section('title', 'Dashboard | SIM KKN UAD')
@section('styles')
    @livewireStyles
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            {{-- Page title --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">DASHBOARD</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">SIM KKN UAD</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            {{-- End Page title --}}


            <div class="row">
                <div class="col-md-3">
                    <livewire:dashboard-card2 :id_unit="$id_unit" :title="'Total Proker'" :subContent="''" />
                </div>
                <div class="col-md-3">
                    <livewire:dashboard-card1 :id_unit="$id_unit" :title="'Pengeluaran Unit'" :subContent="''" />
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-8">
                    <livewire:table-content1 :id_unit="$id_unit" />
                </div>
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Kegiatan bulan ini</h4>
                        </div>
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="id_unit" value="{{ $id_unit }}">
    @livewireScripts
@endsection
@section('pageScript')
    <script>
        $(document).ready(function() {
            $('.formatRupiah').each(function() {
                let value = parseFloat($(this).text().replace(/[^0-9.-]+/g,
                    "")); // Menghapus simbol non-angka
                if (!isNaN(value)) {
                    $(this).text(value.toLocaleString("id-ID", {
                        style: 'currency',
                        currency: 'IDR'
                    }));
                }
            });
        });
    </script>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>
    <script src="{{ asset('assets/js/init/mahasiswa/dashboard-kalender.init.js') }}"></script>
@endsection
