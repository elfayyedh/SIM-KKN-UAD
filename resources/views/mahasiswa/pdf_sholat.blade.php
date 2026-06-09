@php
    $penerjunanDate = \Carbon\Carbon::parse($tanggal_penerjunan);
    $penarikanDate = \Carbon\Carbon::parse($tanggal_penarikan);
    $sholatTimes = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];
    $dataSholat = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];
    $statusIcons = [
        'sholat berjamaah' => 'bx-check-circle text-success',
        'sedang halangan' => 'bx-minus-circle text-secondary',
        'tidak sholat berjamaah' => 'bx-x-circle text-danger',
        'belum diisi' => 'bx-info-circle text-warning',
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logbook Sholat Mahasiswa - {{ $mahasiswa->userRole->user->nama }}</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>LOGBOOK SHOLAT MAHASISWA</h1>
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
        <h3>Riwayat Sholat Berjamaah</h3>

        @if($data && $data->count() > 0)
            <table class="logbook-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Tanggal</th>
                        <th width="15%">Waktu</th>
                        <th width="25%">Keterangan</th>
                        <th width="15%">Jumlah Jamaah</th>
                        <th width="25%">Nama Imam</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($data as $entry)
                        <tr>
                            <td style="text-align: center;">{{ $no++ }}</td>
                            <td>{{ \Carbon\Carbon::parse($entry->tanggal)->format('d-m-Y') }}</td>
                            <td>{{ ucfirst($entry->waktu) }}</td>
                            <td>{{ ucwords($entry->status) }}</td>
                            <td style="text-align: center;">{{ $entry->jumlah_jamaah ? $entry->jumlah_jamaah . ' jamaah' : '-' }}</td>
                            <td>{{ $entry->imam ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Tidak ada data logbook sholat untuk periode ini.</p>
        @endif
    </div>


</body>
</html>
