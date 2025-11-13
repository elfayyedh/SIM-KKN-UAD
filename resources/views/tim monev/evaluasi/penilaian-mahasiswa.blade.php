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
                                <a href="{{ route('monev.evaluasi.dpl-units', $mahasiswa->unit->dpl->id) }}">Daftar Unit</a>
                            </li>
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
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kriteria Penilaian</th>
                                        <th class="text-center" style="width: 150px;">Skor (1 / 2 / 3)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- 1. JKEM (Tidak ada link) --}}
                                    <tr>
                                        <td>
                                            <label for="eval_jkem" class="form-label mb-0">
                                                JKEM Kegiatan KKN (Total: {{ $totalJkem }})
                                            </label>
                                            <small class="d-block text-muted">1: &lt;30%, 2: 30%-50%, 3: &gt;50%</small>
                                        </td>
                                        <td>
                                            <select class="form-select" name="eval_jkem" id="eval_jkem" required>
                                                <option value="">Pilih...</option>
                                                <option value="1" @if(old('eval_jkem', $evaluasi->eval_jkem ?? '') == 1) selected @endif>1</option>
                                                <option value="2" @if(old('eval_jkem', $evaluasi->eval_jkem ?? '') == 2) selected @endif>2</option>
                                                <option value="3" @if(old('eval_jkem', $evaluasi->eval_jkem ?? '') == 3) selected @endif>3</option>
                                            </select>
                                        </td>
                                    </tr>

                                    {{-- 2. Form 1 (Link ke #program_kerja) --}}
                                    <tr>
                                        <td>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <label for="eval_form1" class="form-label mb-0">Pengisian Form 1</label>
                                                    <small class="d-block text-muted">1: Tdk Sesuai, 2: Cukup, 3: Sesuai</small>
                                                </div>
                                                <a href="{{ route('mahasiswa.show', $mahasiswa->id) }}#program_kerja" class="btn btn-link btn-sm flex-shrink-0">
                                                    Lihat Form 1
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <select class="form-select" name="eval_form1" id="eval_form1" required>
                                                <option value="">Pilih...</option>
                                                <option value="1" @if(old('eval_form1', $evaluasi->eval_form1 ?? '') == 1) selected @endif>1</option>
                                                <option value="2" @if(old('eval_form1', $evaluasi->eval_form1 ?? '') == 2) selected @endif>2</option>
                                                <option value="3" @if(old('eval_form1', $evaluasi->eval_form1 ?? '') == 3) selected @endif>3</option>
                                            </select>
                                        </td>
                                    </tr>

                                    {{-- 3. Form 2 (Link ke #logbook_harian) --}}
                                    <tr>
                                        <td>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <label for="eval_form2" class="form-label mb-0">Pengisian Form 2</label>
                                                    <small class="d-block text-muted">1: Tdk Rutin, 2: Cukup, 3: Rutin</small>
                                                </div>
                                                <a href="{{ route('mahasiswa.show', $mahasiswa->id) }}#logbook_harian" class="btn btn-link btn-sm flex-shrink-0">
                                                    Lihat Form 2
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <select class="form-select" name="eval_form2" id="eval_form2" required>
                                                <option value="">Pilih...</option>
                                                <option value="1" @if(old('eval_form2', $evaluasi->eval_form2 ?? '') == 1) selected @endif>1</option>
                                                <option value="2" @if(old('eval_form2', $evaluasi->eval_form2 ?? '') == 2) selected @endif>2</option>
                                                <option value="3" @if(old('eval_form2', $evaluasi->eval_form2 ?? '') == 3) selected @endif>3</option>
                                            </select>
                                        </td>
                                    </tr>
                                    
                                    {{-- 4. Form 3 (Link ke #matriks) --}}
                                    <tr>
                                        <td>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <label for="eval_form3" class="form-label mb-0">Pengisian Form 3</label>
                                                    <small class="d-block text-muted">1: Tdk Sesuai, 2: Cukup, 3: Sesuai</small>
                                                </div>
                                                <a href="{{ route('mahasiswa.show', $mahasiswa->id) }}#matriks" class="btn btn-link btn-sm flex-shrink-0">
                                                    Lihat Matriks
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <select class="form-select" name="eval_form3" id="eval_form3" required>
                                                <option value="">Pilih...</option>
                                                <option value="1" @if(old('eval_form3', $evaluasi->eval_form3 ?? '') == 1) selected @endif>1</option>
                                                <option value="2" @if(old('eval_form3', $evaluasi->eval_form3 ?? '') == 2) selected @endif>2</option>
                                                <option value="3" @if(old('eval_form3', $evaluasi->eval_form3 ?? '') == 3) selected @endif>3</option>
                                            </select>
                                        </td>
                                    </tr>

                                    {{-- 5. Form 4 (Link ke #rekap) --}}
                                    <tr>
                                        <td>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <label for="eval_form4" class="form-label mb-0">Pengisian Form 4</label>
                                                    <small class="d-block text-muted">1: Tdk Lengkap, 2: Cukup, 3: Lengkap</small>
                                                </div>
                                                <a href="{{ route('mahasiswa.show', $mahasiswa->id) }}#rekap" class="btn btn-link btn-sm flex-shrink-0">
                                                    Lihat Form 4
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <select class="form-select" name="eval_form4" id="eval_form4" required>
                                                <option value="">Pilih...</option>
                                                <option value="1" @if(old('eval_form4', $evaluasi->eval_form4 ?? '') == 1) selected @endif>1</option>
                                                <option value="2" @if(old('eval_form4', $evaluasi->eval_form4 ?? '') == 2) selected @endif>2</option>
                                                <option value="3" @if(old('eval_form4', $evaluasi->eval_form4 ?? '') == 3) selected @endif>3</option>
                                            </select>
                                        </td>
                                    </tr>

                                    {{-- 6. Sholat (Link ke #logbook_sholat) --}}
                                    <tr>
                                        <td>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <label for="eval_sholat" class="form-label mb-0">
                                                        Sholat Jamaah ({{ $persenSholat }}%)
                                                    </label>
                                                    <small class="d-block text-muted">1: &lt;50%, 2: 51%-75%, 3: &gt;75%</small>
                                                </div>
                                                <a href="{{ route('mahasiswa.show', $mahasiswa->id) }}#logbook_sholat" class="btn btn-link btn-sm flex-shrink-0">
                                                    Lihat Logbook Sholat
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <select class="form-select" name="eval_sholat" id="eval_sholat" required>
                                                <option value="">Pilih...</option>
                                                <option value="1" @if(old('eval_sholat', $evaluasi->eval_sholat ?? '') == 1) selected @endif>1</option>
                                                <option value="2" @if(old('eval_sholat', $evaluasi->eval_sholat ?? '') == 2) selected @endif>2</option>
                                                <option value="3" @if(old('eval_sholat', $evaluasi->eval_sholat ?? '') == 3) selected @endif>3</option>
                                            </select>
                                        </td>
                                    </tr>
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
                                <a href="{{ route('mahasiswa.show', $mahasiswa->id) }}" class="btn btn-secondary w-md">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection