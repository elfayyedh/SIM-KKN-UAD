@extends('layouts.index')

@section('title', 'Evaluasi DPL')

@section('pageStyle')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Pilih DPL untuk Dievaluasi</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Evaluasi</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <x-alert-component />

        @php
            $jumlahDipilih = $dpl_dipilih->count();
            $jumlahTersedia = $dpl_tersedia->count();
            
            $showBoxTersedia = ($jumlahTersedia > 0 && $jumlahDipilih < 3); 
            $showBoxDipilih = ($jumlahDipilih > 0);

            $colClass = ($showBoxTersedia && $showBoxDipilih) ? 'col-lg-6' : 'col-lg-12';
        @endphp

        <div class="row">
            @if ($showBoxTersedia)
            <div class="{{ $colClass }}">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">DPL Tersedia (Maks. 3 Pilihan)</h4>

                        <div class="table-responsive">
                            <table id="table-tersedia" class="table table-bordered table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>Nama DPL</th>
                                        <th>NIP</th>
                                        <th>Jml. Unit</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dpl_tersedia as $dpl)
                                        <tr>
                                            <td>{{ $dpl->dosen->user->nama ?? 'N/A' }}</td>
                                            <td>{{ $dpl->dosen->nip ?? 'N/A' }}</td>
                                            <td>{{ $dpl->units->count() }} Unit</td>
                                            <td>
                                                <form action="{{ route('evaluasi.pilih', $dpl->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        Pilih
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if ($showBoxDipilih)
            <div class="{{ $colClass }}">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">DPL Pilihan Anda ({{ $jumlahDipilih }}/3)</h4>

                        <div class="table-responsive">
                            <table id="table-dipilih" class="table table-bordered table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>Nama DPL</th>
                                        <th>Jml. Unit</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dpl_dipilih as $penugasan)
                                        <tr>
                                            <td>{{ $penugasan->dpl->dosen->user->nama ?? 'N/A' }}</td>
                                            <td>{{ $penugasan->dpl->units->count() }} Unit</td>
                                            <td>
                                                @if($penugasan->status == 'pending')
                                                    <span class="badge bg-warning">Belum Dievaluasi</span>
                                                @else
                                                    <span class="badge bg-success">Selesai</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('evaluasi.form', $penugasan->id) }}" class="btn btn-success btn-sm">
                                                    Evaluasi
                                                </a>
                                                <form action="{{ route('evaluasi.hapus', $penugasan->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            @endif

            @if (!$showBoxTersedia && !$showBoxDipilih)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            @if ($jumlahTersedia == 0 && $jumlahDipilih == 0)
                                <h5 class="text-muted">Tidak ada DPL yang tersedia untuk dievaluasi di KKN ini.</h5>
                            @elseif($jumlahDipilih >= 3)
                                <h5 class="text-muted">Anda sudah memilih jumlah maksimal DPL (3).</h5>
                                <p>Silakan hapus salah satu DPL dari "DPL Pilihan Anda" untuk memilih DPL lain.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div> 
</div>
@endsection

@section('pageScript')
    {{-- (Opsional, tapi disarankan) --}}
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#table-tersedia').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                }
            });
            $('#table-dipilih').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                }
            });
        });
    </script>
@endsection