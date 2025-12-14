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
                <form action="{{ route('admin.evaluasi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="kkn_id" value="{{ $kkn->id ?? '' }}">
                    
                    <div class="card shadow">

                        {{-- Header --}}
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title flex-grow-1 mb-0">Evaluasi Mahasiswa</h4>
                            <div class="flex-shrink-0">
                                <a href="{{ route('admin.evaluasi.export', $kkn->id ?? '') }}"
                                   class="btn btn-outline-primary btn-sm me-2">
                                    <i class="bx bx-download me-1"></i>Export Excel
                                </a>
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bx bx-save me-1"></i>Simpan Perubahan
                                </button>
                            </div>
                        </div>

                        {{-- Tabel --}}
                        <div class="card-body table-responsive p-0" data-simplebar style="max-height: 500px;">

                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th rowspan="2" class="text-center" style="min-width: 180px;">Mahasiswa</th>
                                        @foreach($kriteriaList as $kriteria)
                                            <th rowspan="2" class="text-center" style="min-width: 100px;">
                                                {{ $kriteria->judul }}<br>
                                                <small class="text-muted">(1.0 - 3.0)</small>
                                            </th>
                                        @endforeach
                                        <th rowspan="2" class="text-center" style="min-width: 100px;">Nilai Akhir</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($mahasiswa as $mhs)
                                        <tr>
                                            {{-- 1. Nama + Identitas --}}
                                            <td>
                                                <strong>{{ $mhs->userRole->user->nama ?? '-' }}</strong><br>
                                                <small class="text-muted">{{ $mhs->nim }}</small><br>
                                                <small class="text-muted">{{ $mhs->no_hp ?? '' }}</small>
                                            </td>

                                            {{-- 2. Input Nilai per Kriteria --}}
                                            @php
                                                $totalNilai = 0;
                                                $jumlahKriteria = 0;
                                            @endphp
                                            @foreach($kriteriaList as $kriteria)
                                                @php
                                                    // Ambil nilai dari array mappedNilai (nilai dari Tim Monev)
                                                    $nilaiDefault = $mappedNilai[$mhs->id][$kriteria->id] ?? '';
                                                    
                                                    // Hitung untuk nilai akhir
                                                    if ($nilaiDefault !== '' && is_numeric($nilaiDefault)) {
                                                        $totalNilai += (float)$nilaiDefault;
                                                        $jumlahKriteria++;
                                                    }
                                                @endphp
                                                <td class="text-center">
                                                    <input 
                                                        type="number" 
                                                        name="evaluasi[{{ $mhs->id }}][{{ $kriteria->id }}]" 
                                                        class="form-control form-control-sm text-center nilai-input" 
                                                        data-mahasiswa="{{ $mhs->id }}"
                                                        value="{{ $nilaiDefault }}" 
                                                        min="1" 
                                                        max="3" 
                                                        step="0.1"
                                                        style="width: 80px; margin: 0 auto;"
                                                        placeholder="-">
                                                </td>
                                            @endforeach

                                            {{-- 3. Nilai Akhir (Auto Calculate) --}}
                                            @php
                                                $nilaiAkhir = $jumlahKriteria > 0 ? round($totalNilai / $jumlahKriteria, 2) : 0;
                                            @endphp
                                            <td class="text-center">
                                                <strong class="nilai-akhir" data-mahasiswa="{{ $mhs->id }}">
                                                    {{ $nilaiAkhir > 0 ? number_format($nilaiAkhir, 2) : '-' }}
                                                </strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-calculate Nilai Akhir when input changes
    $(document).on('input', '.nilai-input', function() {
        const mahasiswaId = $(this).data('mahasiswa');
        const inputs = $(`.nilai-input[data-mahasiswa="${mahasiswaId}"]`);
        
        let total = 0;
        let count = 0;
        
        inputs.each(function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value) && value > 0) {
                total += value;
                count++;
            }
        });
        
        const nilaiAkhir = count > 0 ? (total / count).toFixed(2) : '-';
        $(`.nilai-akhir[data-mahasiswa="${mahasiswaId}"]`).text(nilaiAkhir);
    });
</script>
@endpush

@endsection
