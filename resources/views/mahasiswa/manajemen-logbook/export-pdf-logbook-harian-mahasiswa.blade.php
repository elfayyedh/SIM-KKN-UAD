<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logbook Harian Mahasiswa - {{ $mahasiswa->userRole->user->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-section th, .info-section td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .logbook-section {
            margin-bottom: 30px;
        }
        .logbook-section h3 {
            margin-bottom: 10px;
            font-size: 18px;
        }
        .logbook-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .logbook-table th, .logbook-table td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 12px;
        }
        .logbook-table th {
            background-color: #f0f0f0;
        }
        .date-header {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f8f8f8;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LOGBOOK HARIAN MAHASISWA</h1>
        <p>KULIAH KERJA NYATA (KKN)</p>
        <p>Periode: {{ \Carbon\Carbon::parse($tanggal_penerjunan)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($tanggal_penarikan)->format('d-m-Y') }}</p>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <th width="20%">Nama Mahasiswa</th>
                <td>{{ $mahasiswa->userRole->user->nama }}</td>
            </tr>
            <tr>
                <th>NIM</th>
                <td>{{ $mahasiswa->nim }}</td>
            </tr>
            <tr>
                <th>Program Studi</th>
                <td>{{ $mahasiswa->prodi->nama_prodi }}</td>
            </tr>
            <tr>
                <th>Unit</th>
                <td>{{ $mahasiswa->unit->nama }}</td>
            </tr>
            <tr>
                <th>Lokasi</th>
                <td>{{ $mahasiswa->unit->lokasi->nama }}, {{ $mahasiswa->unit->lokasi->kecamatan->kabupaten->nama }}</td>
            </tr>
        </table>
    </div>

    <div class="logbook-section">
        <h3>Riwayat Kegiatan Harian</h3>

        @if($logbookData && $logbookData->count() > 0)
            <table class="logbook-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Tanggal</th>
                        <th width="20%">Kegiatan</th>
                        <th width="10%">Bidang</th>
                        <th width="10%">Jenis</th>
                        <th width="8%">Jam Mulai</th>
                        <th width="8%">Jam Selesai</th>
                        <th width="8%">Total JKEM</th>
                        <th width="21%">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; $totalJKEM = 0; @endphp
                    @foreach($logbookData as $logbook)
                        @if($logbook->logbookKegiatan && $logbook->logbookKegiatan->count() > 0)
                            @foreach($logbook->logbookKegiatan as $kegiatan)
                                @php $totalJKEM += $kegiatan->total_jkem; @endphp
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ \Carbon\Carbon::parse($logbook->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ $kegiatan->kegiatan ? $kegiatan->kegiatan->nama : '-' }}</td>
                                    <td>{{ $kegiatan->kegiatan && $kegiatan->kegiatan->proker && $kegiatan->kegiatan->proker->bidang ? $kegiatan->kegiatan->proker->bidang->nama : '-' }}</td>
                                    <td>{{ $kegiatan->jenis }}</td>
                                    <td>{{ $kegiatan->jam_mulai }}</td>
                                    <td>{{ $kegiatan->jam_selesai }}</td>
                                    <td>{{ $kegiatan->total_jkem }}</td>
                                    <td>{{ $kegiatan->deskripsi ?: '-' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                    <tr class="total-row">
                        <td colspan="7" style="text-align: right;"><strong>Total JKEM:</strong></td>
                        <td><strong>{{ $totalJKEM }}</strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        @else
            <p>Tidak ada data logbook harian untuk periode ini.</p>
        @endif
    </div>

    <div style="margin-top: 50px; text-align: center; font-size: 12px;">
        <p>Dokumen ini dibuat secara otomatis pada {{ date('d-m-Y H:i:s') }}</p>
    </div>
</body>
</html>
