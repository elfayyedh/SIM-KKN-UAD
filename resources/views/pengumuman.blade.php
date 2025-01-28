@extends('layouts.index')

@section('title', 'Pengumuman | SIM KKN UAD')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Page title --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">PENGUMUMAN</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Informasi</a></li>
                                <li class="breadcrumb-item active">Pengumuman</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            {{-- End Page title --}}

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mt-5">
                                @foreach ($data as $item)
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body overflow-hidden position-relative">
                                                <div>
                                                    <i class="bx bx-bell widget-box-1-icon text-primary"></i>
                                                </div>
                                                <h5 class="mt-3">{{ $item->judul }}</h5>
                                                <p class="text-muted mt-3 mb-0">{!! $item->isi !!}</p>
                                            </div>
                                            <!-- end card body -->
                                        </div>
                                        <!-- end card -->
                                    </div>
                                @endforeach
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end  card body -->
                    </div>
                    <!-- end card -->
                </div>
                <!-- end col -->
            </div>
        </div>
    </div>
@endsection
