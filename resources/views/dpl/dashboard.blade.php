@extends('layouts.index') {{-- Pastikan nama layout utama benar --}}

@section('title', 'Dashboard DPL') {{-- Judul tab browser --}}

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Dashboard DPL</h4>
                    {{-- Breadcrumb minimal --}}
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Konten Utama --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{-- Tulisan Utama --}}
                        <h1 class="text-center">Hello DPL!</h1>
                        <p class="text-center">Dashboard Anda berhasil dimuat.</p>

                        <hr> {{-- Pemisah --}}

                        {{-- Menampilkan data NIP dan ID KKN jika ada --}}
                        <div class="text-center mt-3">
                            @isset($nip)
                                <p class="text-muted mb-1">NIP Anda: <strong>{{ $nip }}</strong></p>
                            @else
                                <p class="text-danger mb-1"><em>(Data NIP tidak ditemukan)</em></p>
                            @endisset

                            @isset($id_kkn)
                                <p class="text-muted mb-0">ID KKN Anda: <strong>{{ $id_kkn }}</strong></p>
                            @else
                                <p class="text-danger mb-0"><em>(Data ID KKN tidak ditemukan)</em></p>
                            @endisset
                        </div>

                    </div> {{-- Akhir card-body --}}
                </div> {{-- Akhir card --}}
            </div> {{-- Akhir col-lg-12 --}}
        </div> {{-- Akhir row --}}

    </div> {{-- Akhir container-fluid --}}
</div> {{-- Akhir page-content --}}
@endsection

@section('pageScript')
    {{-- Tidak perlu script tambahan untuk halaman minimal ini --}}
@endsection