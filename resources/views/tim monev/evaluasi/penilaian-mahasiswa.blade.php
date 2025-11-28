@extends('layouts.index')

@section('title', 'Penilaian Mahasiswa | ' . $mahasiswa->userRole->user->nama)

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Form Penilaian: {{ $mahasiswa->userRole->user->nama }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('monev.evaluasi.index') }}">Evaluasi Unit</a></li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('monev.evaluasi.daftar-mahasiswa', $mahasiswa->unit->id) }}">Daftar Mahasiswa</a>
                            </li>
                            <li class="breadcrumb-item active">Penilaian</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Form Penilaian Mahasiswa</h5>
                        <p class="card-title-desc">
                            Berikan penilaian (skor 1, 2, atau 3) untuk mahasiswa <strong>{{ $mahasiswa->userRole->user->nama }}</strong>
                            (Unit: {{ $mahasiswa->unit->nama }}) berdasarkan rubrik yang ada.
                        </p>

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Ada kesalahan input:</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('monev.evaluasi.penilaian.store', $mahasiswa->id) }}" method="POST">
                            @csrf
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kriteria Penilaian</th>
                                        <th class="text-center" style="width: 150px;">Skor (1 / 2 / 3)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kriteriaList as $kriteria)
                                        <tr>
                                            <td>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        @php
                                                            $judulTampil = $kriteria->judul;
                                                            if ($kriteria->variable_key && isset($dynamicData[$kriteria->variable_key])) {
                                                                $judulTampil .= ' (' . $dynamicData[$kriteria->variable_key] . ')';
                                                            }
                                                        @endphp

                                                        <label class="form-label mb-0 fw-bold">{{ $judulTampil }}</label>
                                                        
                                                        @if($kriteria->keterangan)
                                                            <small class="d-block text-muted fst-italic mt-1">{{ $kriteria->keterangan }}</small>
                                                        @endif
                                                    </div>

                                                    @if($kriteria->link_url && $kriteria->link_text)
                                                        <a href="{{ route('mahasiswa.show', $mahasiswa->id) }}{{ $kriteria->link_url }}" 
                                                        target="_blank"
                                                        class="btn btn-link btn-sm flex-shrink-0 text-decoration-none">
                                                            {{ $kriteria->link_text }} <i class="mdi mdi-open-in-new ms-1"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <select class="form-select" name="nilai[{{ $kriteria->id }}]" required>
                                                    <option value="">Pilih...</option>
                                                    
                                                    @php 
                                                        $val = $existingAnswers[$kriteria->id] ?? ''; 
                                                    @endphp
                                                    
                                                    <option value="1" {{ $val == 1 ? 'selected' : '' }}>1</option>
                                                    <option value="2" {{ $val == 2 ? 'selected' : '' }}>2</option>
                                                    <option value="3" {{ $val == 3 ? 'selected' : '' }}>3</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-5">
                                                <i class="mdi mdi-alert-circle-outline fs-1 d-block mb-2 text-secondary"></i>
                                                <h5 class="text-secondary">Belum ada Kriteria Penilaian</h5>
                                                <p class="mb-0">Admin belum mengatur form penilaian untuk periode KKN ini.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="mb-3 mt-4">
                                <label for="catatan_monev" class="form-label">Catatan Tambahan (Opsional)</label>
                                <textarea id="catatan_monev" 
                                          name="catatan_monev" 
                                          class="form-control" 
                                          rows="5">{{ old('catatan_monev', $evaluasi->catatan_monev ?? '') }}</textarea>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary w-md">
                                    {{ $evaluasi ? 'Update Penilaian' : 'Simpan Penilaian' }}
                                </button>
                                <a href="{{ route('monev.evaluasi.daftar-mahasiswa', $mahasiswa->unit->id) }}" class="btn btn-secondary w-md">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection