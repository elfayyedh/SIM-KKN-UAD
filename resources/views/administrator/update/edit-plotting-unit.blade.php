@extends('layouts.index')
@section('title', 'Plotting Unit Tim Monev')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Header Info --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-start">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Plotting Tim Monev</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1">Dosen Monev: <strong>{{ $timMonev->dosen->user->nama }}</strong></p>
                                <p class="mb-0">Periode KKN: <strong>{{ $timMonev->kkn->nama }}</strong></p>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('tim-monev.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="mdi mdi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Plotting --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <i class="mdi mdi-information-outline"></i> 
                            Centang unit yang akan ditugaskan kepada Tim Monev ini.
                        </div>

                        <form action="{{ route('tim-monev.updatePlotting', $timMonev->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%" class="text-center">Pilih</th>
                                            <th>Nama Unit</th>
                                            <th>Lokasi KKN</th>
                                            <th>DPL Pengampu</th>
                                            <th>Status Saat Ini</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($units as $unit)
                                            @php
                                                // Cek apakah unit ini MILIK SAYA (Monev yg sedang diedit)
                                                $isMyUnit = $unit->id_tim_monev == $timMonev->id;
                                                
                                                // Cek apakah unit ini MILIK ORANG LAIN
                                                $isTaken = $unit->id_tim_monev != null && !$isMyUnit;
                                                
                                                // Nama DPL (Safe check takutnya null)
                                                $namaDpl = $unit->dpl->dosen->nama ?? '<span class="text-danger fst-italic">Belum ada DPL</span>';
                                            @endphp

                                            <tr class="{{ $isMyUnit ? 'bg-success bg-opacity-10' : '' }}">
                                                <td class="text-center">
                                                    <input type="checkbox" 
                                                           name="units[]" 
                                                           value="{{ $unit->id }}" 
                                                           class="form-check-input"
                                                           style="cursor: pointer; transform: scale(1.3);"
                                                           {{ $isMyUnit ? 'checked' : '' }}
                                                    >
                                                </td>
                                                <td class="fw-bold">{{ $unit->nama }}</td>
                                                <td>
                                                    <small class="text-muted"><i class="mdi mdi-map-marker"></i> {{ $unit->lokasi->nama ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    <small class="text-primary"><i class="mdi mdi-account-tie"></i> {{ $unit->dpl->dosen->user->nama }}</small>
                                                </td>
                                                <td>
                                                    @if($isMyUnit)
                                                        <span class="badge bg-success">Terpilih</span>
                                                    @elseif($isTaken)
                                                        <span class="badge bg-warning text-dark" title="Diambil alih dari Monev Lain">Milik Dosen Lain</span>
                                                    @else
                                                        <span class="badge bg-light text-secondary">Belum Diplot</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="mdi mdi-alert-circle-outline fs-4 d-block mb-2"></i>
                                                        Belum ada Unit terdaftar di KKN periode ini.
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary w-md">
                                    <i class="mdi mdi-content-save"></i> Simpan Plotting
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection