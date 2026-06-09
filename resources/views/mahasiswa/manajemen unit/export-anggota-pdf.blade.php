<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daftar Anggota Unit {{ $unit->nama }}</title>
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
        margin-bottom: 20px;
    }
    
    table, th, td {
        border: 1px solid #000;
    }
    
    th {
        background-color: #f0f0f0;
        font-weight: bold;
        padding: 8px;
        text-align: center;
    }
    
    td {
        padding: 6px;
    }
    
    .text-center {
        text-align: center;
    }
    
    h3, h4 {
        text-align: center;
        margin-bottom: 10px;
    }
    
    .fw-bold {
        font-weight: bold;
    }
    
    .profil-table td {
        border: 1px solid #ccc;
        padding: 5px;
    }
</style>

<body>
    <h3 class="fw-bold">DAFTAR ANGGOTA UNIT</h3>
    <h4>KULIAH KERJA NYATA UNIVERSITAS AHMAD DAHLAN</h4>
    
    <table class="profil-table" style="margin-bottom: 20px;">
        <tr>
            <td width="100"><strong>Unit</strong></td>
            <td>: {{ $unit->nama }}</td>
        </tr>
        <tr>
            <td><strong>DPL</strong></td>
            <td>: {{ $unit->dpl->dosen->user->nama ?? 'Nama DPL Tidak Ditemukan' }}</td>
        </tr>
        <tr>
            <td><strong>Lokasi</strong></td>
            <td>: {{ $unit->lokasi->nama }}, {{ $unit->lokasi->kecamatan->kabupaten->nama }}</td>
        </tr>
        <tr>
            <td><strong>KKN</strong></td>
            <td>: {{ $unit->kkn->nama }}</td>
        </tr>
    </table>
    
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">NIM</th>
                <th width="20%">Nama</th>
                <th width="10%">Jenis Kelamin</th>
                <th width="13%">Jabatan</th>
                <th width="25%">Program Studi</th>
                <th width="15%">Nomor Telepon</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($anggota as $index => $mhs)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $mhs->nim }}</td>
                    <td>{{ $mhs->userRole->user->nama }}</td>
                    <td class="text-center">{{ $mhs->userRole->user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td>{{ $mhs->jabatan ?? '-' }}</td>
                    <td>{{ $mhs->prodi->nama_prodi }}</td>
                    <td>{{ $mhs->userRole->user->no_telp }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 20px;"><strong>Total Anggota:</strong> {{ $anggota->count() }} Mahasiswa</p>
</body>

</html>
