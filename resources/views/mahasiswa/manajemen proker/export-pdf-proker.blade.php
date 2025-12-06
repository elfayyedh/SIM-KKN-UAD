<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @include('layouts.head-style')
</head>

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 11px;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }
    
    table, th, td {
        border: 1px solid #000;
    }
    
    th {
        background-color: #f0f0f0;
        font-weight: bold;
        padding: 6px;
        text-align: center;
    }
    
    td {
        padding: 5px;
    }
    
    .text-center {
        text-align: center;
    }
    
    h3 {
        text-align: center;
        margin-bottom: 5px;
        font-size: 16px;
    }
    
    h4 {
        text-align: center;
        margin-bottom: 15px;
        font-size: 13px;
    }
    
    .fw-bold {
        font-weight: bold;
    }
    
    .profil-table td {
        border: 1px solid #ccc;
        padding: 5px;
    }
    
    .bidang-title {
        font-weight: bold;
        margin: 15px 0 8px 0;
        font-size: 12px;
    }
    
    .proker-title {
        background-color: #f8f8f8;
        font-weight: bold;
        padding: 6px;
    }
</style>


<body>
    <h3 class="fw-bold">PROGRAM KERJA UNIT</h3>
    <h4>KULIAH KERJA NYATA UNIVERSITAS AHMAD DAHLAN</h4>
    
    <table class="profil-table" style="margin-bottom: 20px;">
        <tr>
            <td width="80"><strong>Unit</strong></td>
            <td>: {{ $unit->nama }}</td>
        </tr>
        <tr>
            <td><strong>DPL</strong></td>
            <td>: {{ $unit->dpl->dosen->user->nama ?? 'Nama DPL Tidak Ditemukan' }}</td>
        </tr>
        <tr>
            <td><strong>Lokasi</strong></td>
            <td>: {{ $unit->lokasi->nama }}, {{ $unit->lokasi->kecamatan->kabupaten->nama ?? '' }}</td>
        </tr>
        <tr>
            <td><strong>KKN</strong></td>
            <td>: {{ $unit->kkn->nama ?? '' }}</td>
        </tr>
    </table>

    @foreach ($prokers as $bidang)
        <p class="bidang-title">{{ $loop->iteration }}. {{ $bidang->nama }}</p>
        @foreach ($bidang->proker as $proker)
            <table>
                <tr>
                    <td colspan="7" class="proker-title">{{ $proker->nama }}</td>
                </tr>
                <tr>
                    <th width="5%">No</th>
                    <th width="30%">Kegiatan</th>
                    <th width="10%">Frekuensi</th>
                    <th width="8%">JKEM</th>
                    <th width="10%">Total JKEM</th>
                    <th width="17%">Tanggal Rencana</th>
                    <th width="20%">Penanggung Jawab</th>
                </tr>
                @foreach ($proker->kegiatan as $kegiatan)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $kegiatan->nama }}</td>
                        <td style="text-align: center;">{{ $kegiatan->frekuensi }}</td>
                        <td style="text-align: center;">{{ $kegiatan->jkem }}</td>
                        <td style="text-align: center;">{{ $kegiatan->total_jkem }}</td>
                        <td>
                            @foreach ($kegiatan->tanggalRencanaProker as $date)
                                {{ $date->tanggal }}@if(!$loop->last), @endif
                            @endforeach
                        </td>
                        <td>
                            {{ $kegiatan->mahasiswa->userRole->user->nama ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </table>
        @endforeach
    @endforeach

</body>


</html>
