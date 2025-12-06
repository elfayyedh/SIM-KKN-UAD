@extends('layouts.index')
@section('pageStyle')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('title', 'Daftar Unit Bimbingan')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">DAFTAR UNIT BIMBINGAN</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Unit</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('unit.index') }}">Unit Bimbingan</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <x-unit-table :units="$units" />
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>
@endsection
@section('pageScript')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.datatable-buttons').DataTable({
                "responsive": true, 
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/English.json"
                }
            });
        });
    </script>
@endSection