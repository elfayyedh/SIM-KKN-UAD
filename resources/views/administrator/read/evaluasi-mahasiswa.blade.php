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
                                    <th colspan="2" class="text-center">Statistik</th>
                                    <th rowspan="2" class="text-center">Pencapaian JKEM<br>(1.0 - 3.0)</th>
                                    <th rowspan="2" class="text-center">Sholat<br>(1.0 - 3.0)</th>
                                    <th rowspan="2" class="text-center">Form 1<br>(1.0 - 3.0)</th>
                                    <th rowspan="2" class="text-center">Form 2<br>(1.0 - 3.0)</th>
                                    <th rowspan="2" class="text-center">Form 3<br>(1.0 - 3.0)</th>
                                    <th rowspan="2" class="text-center">Form 4<br>(1.0 - 3.0)</th>
                                    <th rowspan="2" class="text-center">Status</th>
                                </tr>
                                <tr>
                                    <th class="text-center">JKEM</th>
                                    <th class="text-center">Sholat</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($mahasiswa as $mhs)

                                    @php
                                        $nilai = $mappedNilai[$mhs->id] ?? [];

                                        $statJkem   = $nilai['stat_jkem']   ?? '0%';
                                        $statSholat = $nilai['stat_sholat'] ?? '0%';

                                        $n_jkem   = $nilai['jkem']   ?? '';
                                        $n_sholat = $nilai['sholat'] ?? '';
                                        $f1       = $nilai['form1']  ?? '';
                                        $f2       = $nilai['form2']  ?? '';
                                        $f3       = $nilai['form3']  ?? '';
                                        $f4       = $nilai['form4']  ?? '';

                                        $statusNilai = ($n_jkem || $n_sholat || $f1 || $f2 || $f3 || $f4);
                                    @endphp

                                    <tr>
                                        {{-- Nama + Identitas --}}
                                        <td>
                                            <strong>{{ $mhs->userRole->user->nama ?? '-' }}</strong><br>
                                            <small>{{ $mhs->nim }}</small><br>
                                            <small>{{ $mhs->no_hp ?? '' }}</small>
                                        </td>

                                        {{-- Statistik JKEM & Sholat --}}
                                        <td class="text-center">{{ $statJkem }}</td>
                                        <td class="text-center">{{ $statSholat }}</td>

                                        {{-- Nilai 1.0 - 3.0 --}}
                                        <td class="text-center">{{ $n_jkem ?: '-' }}</td>
                                        <td class="text-center">{{ $n_sholat ?: '-' }}</td>
                                        <td class="text-center">{{ $f1 ?: '-' }}</td>
                                        <td class="text-center">{{ $f2 ?: '-' }}</td>
                                        <td class="text-center">{{ $f3 ?: '-' }}</td>
                                        <td class="text-center">{{ $f4 ?: '-' }}</td>

                                        {{-- Status --}}
                                        <td class="text-center">
                                            @if($statusNilai)
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
