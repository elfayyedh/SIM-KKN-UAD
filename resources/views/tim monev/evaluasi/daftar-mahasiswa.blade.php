@extends('layouts.index')

@section('title', 'Penilaian Unit | ' . ($unit->nama ?? '-'))

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Header Halaman -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Penilaian Unit: {{ $unit->nama }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('monev.evaluasi.index') }}">Evaluasi Unit</a></li>
                            <li class="breadcrumb-item active">Input Nilai</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM PEMBUNGKUS TABEL -->
        <form action="{{ route('monev.evaluasi.bulk-store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            
                            <!-- Header Card & Tombol Simpan -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h5 class="card-title mb-1">Form Penilaian Mahasiswa</h5>
                                    <p class="text-muted mb-0">
                                        Isi nilai <strong>(1.00 - 3.00)</strong> pada kolom kriteria yang tersedia.
                                    </p>
                                </div>
                            </div>

                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    <i class="mdi mdi-check-all me-2"></i> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle table-nowrap mb-0">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <!-- Kolom Statis -->
                                            <th rowspan="2" class="align-middle text-start">Mahasiswa</th>
                                            <th colspan="2" class="align-middle">Statistik</th>
                                            
                                            <!-- KOLOM DINAMIS: Loop Header Kriteria -->
                                            @foreach($kriteriaList as $kriteria)
                                                <th class="align-middle" style="min-width: 120px;">
                                                    <span data-bs-toggle="tooltip" title="{{ $kriteria->judul }}">
                                                        {{ \Illuminate\Support\Str::limit($kriteria->judul, 15) }}
                                                    </span>
                                                </th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <!-- Sub Header Statistik -->
                                            <th style="font-size: 11px;">JKEM</th>
                                            <th style="font-size: 11px;">Sholat</th>
                                            
                                            <!-- Sub Header Range Nilai -->
                                            @foreach($kriteriaList as $kriteria)
                                                <th style="font-size: 10px;" class="text-muted">(1.0 - 3.0)</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($unit->mahasiswa as $mhs)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $mhs->userRole->user->nama ?? '-' }}</div>
                                                    <small class="text-muted">{{ $mhs->nim }}</small><br>
                                                    <small class="text-muted">
                                                        <a href="https://wa.me/{{ $mhs->userRole->user->no_telp }}" target="_blank">{{ $mhs->userRole->user->no_telp ?? '-' }}</a>
                                                    </small>
                                                </td>
                                                
                                                <!-- Data Statistik -->
                                                <td class="text-center">
                                                    <span class="badge badge-soft-info">{{ number_format($mhs->hitung_jkem) }} m</span>
                                                </td>
                                                <td class="text-center">
                                                    @php $s = $mhs->hitung_sholat; @endphp
                                                    <span class="badge badge-soft-{{ $s >= 50 ? 'success' : 'warning' }}">
                                                        {{ $s }}%
                                                    </span>
                                                </td>

                                                <!-- KOLOM DINAMIS: Loop Input Kriteria -->
                                                @foreach($kriteriaList as $kriteria)
                                                    <td>
                                                        @php
                                                            // Ambil nilai dari array mapping yang dikirim controller
                                                            // Syntax: $mappedNilai[ID_MHS][ID_KRITERIA]
                                                            $val = $mappedNilai[$mhs->id][$kriteria->id] ?? '';
                                                        @endphp
                                                        
                                                        <input type="number" 
                                                               step="0.01" 
                                                               min="1" 
                                                               max="3"
                                                               class="form-control form-control-sm text-center"
                                                               name="evaluasi[{{ $mhs->id }}][{{ $kriteria->id }}]" 
                                                               value="{{ $val }}"
                                                               placeholder="-">
                                                        @if(!empty($kriteria->link_url))
                                                            <div class="d-grid">
                                                                <a href="{{ route('mahasiswa.show', $mhs->id) }}{{ $kriteria->link_url }}" 
                                                                target="_blank" 
                                                                class="btn btn-outline-info btn-sm font-size-10 py-0"
                                                                style="border-style: dashed;">
                                                                    <i class="mdi mdi-open-in-new me-1"></i>
                                                                    {{ $kriteria->link_text ?? 'Cek Data' }}
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ 5 + count($kriteriaList) }}" class="text-center py-4">
                                                    Tidak ada data mahasiswa.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- TABEL MATRIKS / PEDOMAN PENILAIAN (FULL DINAMIS) -->
                            <div class="mt-5 mb-3">
                                <h5 class="card-title mb-1"> Pedoman Penilaian</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm table-striped w-100 font-size-13 align-middle">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th style="width: 25%;">Kriteria / Aspek</th>
                                                <th style="width: 25%;">Nilai 1 (Kurang)</th>
                                                <th style="width: 25%;">Nilai 2 (Cukup)</th>
                                                <th style="width: 25%;">Nilai 3 (Baik)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($kriteriaList as $kriteria)
                                                @php
                                                    $ket = $kriteria->keterangan;
                                                    $d1 = '-'; $d2 = '-'; $d3 = '-';

                                                    // Logic Pemecah String (Regex)
                                                    // Mengambil teks setelah "1:", "2:", "3:" dan membuang koma sisa
                                                    if (preg_match('/1:\s*(.*?)(?=\s*2:|$)/i', $ket, $match)) $d1 = trim($match[1], ", \t\n\r\0\x0B");
                                                    if (preg_match('/2:\s*(.*?)(?=\s*3:|$)/i', $ket, $match)) $d2 = trim($match[1], ", \t\n\r\0\x0B");
                                                    if (preg_match('/3:\s*(.*)/i', $ket, $match)) $d3 = trim($match[1], ", \t\n\r\0\x0B");

                                                    // Fallback: Jika admin nulisnya gak pake angka "1:", tampilin semua di kolom 1 biar info gak ilang
                                                    if ($d1 == '-' && $d2 == '-' && $d3 == '-') {
                                                        $d1 = $ket;
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="fw-bold ps-3">{{ $kriteria->judul }}</td>
                                                    
                                                    <!-- KOLOM 1: MERAH -->
                                                    <td class="text-center text-danger fw-bold">
                                                        {{ $d1 }}
                                                    </td>
                                                    
                                                    <!-- KOLOM 2: KUNING -->
                                                    <td class="text-center text-warning fw-bold">
                                                        {{ $d2 }}
                                                    </td>
                                                    
                                                    <!-- KOLOM 3: HIJAU -->
                                                    <td class="text-center text-success fw-bold">
                                                        {{ $d3 }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Tombol Simpan Bawah -->
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-content-save-all me-1"></i> Simpan Nilai
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection