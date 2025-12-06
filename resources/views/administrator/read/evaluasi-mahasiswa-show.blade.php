@extends('layouts.index')

@section('title', 'Hasil Evaluasi Mahasiswa')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Hasil Evaluasi - {{ $mahasiswa->user->nama ?? '-' }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('kkn.index') }}">Daftar KKN</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.evaluasi.index', ['kkn_id' => $kkn->id]) }}">Evaluasi Mahasiswa</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p><strong>Nama:</strong> {{ $mahasiswa->user->nama ?? '-' }}</p>
                        <p><strong>NIM:</strong> {{ $mahasiswa->nim ?? '-' }}</p>
                        <p><strong>Unit:</strong> {{ $mahasiswa->unit->nama ?? '-' }}</p>

                        

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>Detail Evaluasi</h5>
                            @if($evaluations->count() > 0)
                                <a href="{{ route('admin.evaluasi.mahasiswa.export', ['kkn_id' => $kkn->id, 'id' => $mahasiswa->id]) }}" class="btn btn-success">
                                    <i class="fas fa-download"></i> Export to Excel
                                </a>
                            @endif
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Evaluator</th>
                                        <th>Tanggal</th>
                                        @foreach($kriteriaList as $k)
                                            <th>{{ \\Illuminate\\Support\\Str::limit($k->judul,20) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($evaluations as $eval)
                                        @php
                                            $vals = [];
                                            foreach($eval->evaluasiMahasiswaDetail as $d) {
                                                $vals[$d->id_kriteria_monev] = $d->nilai;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $eval->timMonev->dosen->user->nama ?? 'Admin' }}</td>
                                            <td>{{ $eval->created_at->format('Y-m-d H:i') }}</td>
                                            @foreach($kriteriaList as $k)
                                                <td class="text-center">{{ $vals[$k->id] ?? '-' }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ 2 + $kriteriaList->count() }}" class="text-center">Belum ada penilaian untuk mahasiswa ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
