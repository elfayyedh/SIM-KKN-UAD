@extends('layouts.index')

@section('title', 'Evaluasi Mahasiswa | SIM KKN UAD')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Page title --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">EVALUASI MAHASISWA</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">SIM KKN UAD</a></li>
                            <li class="breadcrumb-item active">Evaluasi Mahasiswa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="card shadow">

                    {{-- Header --}}
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title flex-grow-1 mb-0">Evaluasi Mahasiswa</h4>
                        <div class="flex-shrink-0">
                            <a href="{{ route('admin.evaluasi.export', $kkn->id ?? '') }}"
                               class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-download me-1"></i>Export Excel
                            </a>
                        </div>
                    </div>

                    {{-- Tabel --}}
                    <div class="card-body table-responsive p-0" data-simplebar style="max-height: 500px;">

                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="text-center">Mahasiswa</th>
                                    <th rowspan="2" class="text-center">Pencapaian JKEM<br>(1.0 - 3.0)</th>
                                    <th rowspan="2" class="text-center">Sholat<br>(1.0 - 3.0)</th>
                                    <th rowspan="2" class="text-center">Form 1<br>(1.0 - 3.0)</th>
                                    <th rowspan="2" class="text-center">Form 2<br>(1.0 - 3.0)</th>
                                    <th rowspan="2" class="text-center">Form 3<br>(1.0 - 3.0)</th>
                                    <th rowspan="2" class="text-center">Form 4<br>(1.0 - 3.0)</th>
                                    <th rowspan="2" class="text-center">Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($mahasiswa as $mhs)
                                    <tr>
                                        {{-- 1. Nama + Identitas --}}
                                        <td>
                                            <strong>{{ $mhs->userRole->user->nama ?? '-' }}</strong><br>
                                            <small>{{ $mhs->nim }}</small><br>
                                            <small>{{ $mhs->no_hp ?? '' }}</small>
                                        </td>

                                        {{-- 3. Nilai (LOOPING DINAMIS SESUAI CONTROLLER) --}}
                                        {{-- Kita ambil nilai berdasarkan ID Kriteria, bukan nama manual --}}
                                        @foreach($kriteriaList as $kriteria)
                                            @php
                                                // Ambil nilai dari array mappedNilai menggunakan ID Mahasiswa & ID Kriteria
                                                $skor = $mappedNilai[$mhs->id][$kriteria->id] ?? '-';
                                            @endphp
                                            <td class="text-center">{{ $skor }}</td>
                                        @endforeach

                                        {{-- 4. Status (Cek apakah ada data nilai untuk mahasiswa ini) --}}
                                        @php
                                            // Cek apakah mahasiswa ini punya entry di array mappedNilai
                                            $sudahDinilai = isset($mappedNilai[$mhs->id]) && count($mappedNilai[$mhs->id]) > 0;
                                        @endphp
                                        <td class="text-center">
                                            @if($sudahDinilai)
                                                <span class="badge bg-success">Sudah Dinilai</span>
                                            @else
                                                <span class="badge bg-warning">Belum Dinilai</span>
                                            @endif
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
</div>
@endsection
