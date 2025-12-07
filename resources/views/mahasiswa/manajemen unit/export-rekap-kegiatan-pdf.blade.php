<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Kegiatan Unit {{ $unit->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            padding: 15px;
        }

        @page {
            size: landscape;
            margin: 15mm;
        }

        h3 {
            text-align: center;
            margin-bottom: 5px;
            font-size: 14px;
            font-weight: bold;
        }

        h5 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 11px;
            font-weight: normal;
        }

        h6 {
            margin-top: 15px;
            margin-bottom: 8px;
            font-size: 11px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            padding: 5px;
            font-size: 8px;
        }

        td {
            padding: 4px;
            vertical-align: top;
            font-size: 8px;
        }

        .info-table {
            margin-bottom: 15px;
            border: 1px solid black;
        }

        .info-table td {
            border: 1px solid black;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h3>REKAP KEGIATAN UNIT</h3>
    <h5>KULIAH KERJA NYATA UNIVERSITAS AHMAD DAHLAN</h5>
    
    <table class="info-table">
        <tr>
            <td style="width: 100px;"><strong>Unit</strong></td>
            <td>: {{ $unit->nama }}</td>
            <td style="width: 100px;"><strong>DPL</strong></td>
                <td>: {{ $unit->dpl->dosen->user->nama ?? 'Nama DPL Tidak Ditemukan' }}</td>
            </tr>
            <tr>
                <td><strong>Lokasi</strong></td>
                <td>: {{ $unit->lokasi->nama }}, {{ $unit->lokasi->kecamatan->kabupaten->nama }}</td>
                <td><strong>KKN</strong></td>
                <td>: {{ $unit->kkn->nama }}</td>
            </tr>
        </table>

        @foreach ($kegiatan as $bidang)
            @if ($bidang->proker->count() > 0)
                <h6>Bidang {{ $bidang->nama }}</h6>
                
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 150px;">Nama Program</th>
                            <th rowspan="2" style="width: 60px;">Total JKEM</th>
                            <th rowspan="2" style="width: 60px;">Frekuensi Kegiatan</th>
                            <th colspan="4">Pelaksanaan Kegiatan</th>
                            <th colspan="5">Dana</th>
                        </tr>
                        <tr>
                            <th style="width: 80px;">Tempat</th>
                            <th style="width: 80px;">Sasaran</th>
                            <th style="width: 50px;">Frekuensi</th>
                            <th style="width: 50px;">JKEM</th>
                            <th style="width: 50px;">Mhs</th>
                            <th style="width: 50px;">Mas</th>
                            <th style="width: 50px;">Pem</th>
                            <th style="width: 50px;">PT</th>
                            <th style="width: 60px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bidang->proker as $proker)
                            @php
                                $totalJkem = $proker->kegiatan->sum('total_jkem');
                                $frekuensiTotal = 0;
                                $jkemTotal = 0;
                                $danaMhs = 0;
                                $danaMas = 0;
                                $danaPem = 0;
                                $danaPT = 0;
                                
                                $tempatList = [];
                                $sasaranList = [];
                                
                                // Ambil tempat dan sasaran dari relasi proker
                                if (isset($proker->tempatDanSasaran) && is_iterable($proker->tempatDanSasaran)) {
                                    foreach ($proker->tempatDanSasaran as $ts) {
                                        if ($ts->tempat) {
                                            $tempatList[] = $ts->tempat;
                                        }
                                        if ($ts->sasaran) {
                                            $sasaranList[] = $ts->sasaran;
                                        }
                                    }
                                }
                                
                                if ($proker->kegiatan && is_iterable($proker->kegiatan)) {
                                    foreach ($proker->kegiatan as $keg) {
                                        if ($keg->logbookKegiatan && is_iterable($keg->logbookKegiatan)) {
                                            foreach ($keg->logbookKegiatan as $logbook) {
                                                if ($logbook->logbookHarian) {
                                                    $frekuensiTotal += is_countable($logbook->logbookHarian) ? count($logbook->logbookHarian) : 1;
                                                    
                                                    if (is_object($logbook->logbookHarian) && method_exists($logbook->logbookHarian, 'sum')) {
                                                        $jkemTotal += $logbook->logbookHarian->sum('jkem');
                                                    } elseif (isset($logbook->logbookHarian->jkem)) {
                                                        $jkemTotal += $logbook->logbookHarian->jkem;
                                                    }
                                                }
                                                
                                                if ($logbook->dana) {
                                                    $danaMhs += $logbook->dana->dana_mahasiswa ?? 0;
                                                    $danaMas += $logbook->dana->dana_masyarakat ?? 0;
                                                    $danaPem += $logbook->dana->dana_pemerintah ?? 0;
                                                    $danaPT += $logbook->dana->dana_pt ?? 0;
                                                }
                                            }
                                        }
                                    }
                                }
                                
                                $tempat = !empty($tempatList) ? implode(', ', array_unique($tempatList)) : '-';
                                $sasaran = !empty($sasaranList) ? implode(', ', array_unique($sasaranList)) : '-';
                                $totalDana = $danaMhs + $danaMas + $danaPem + $danaPT;
                            @endphp
                            <tr>
                                <td>{{ $proker->nama ?? '-' }}</td>
                                <td style="text-align: center;">{{ $totalJkem }}</td>
                                <td style="text-align: center;">{{ $proker->kegiatan ? (is_countable($proker->kegiatan) ? count($proker->kegiatan) : 0) : 0 }}</td>
                                <td>{{ $tempat }}</td>
                                <td>{{ $sasaran }}</td>
                                <td style="text-align: center;">{{ $frekuensiTotal }}</td>
                                <td style="text-align: center;">{{ $jkemTotal }}</td>
                                <td style="text-align: right;">{{ number_format($danaMhs, 0, ',', '.') }}</td>
                                <td style="text-align: right;">{{ number_format($danaMas, 0, ',', '.') }}</td>
                                <td style="text-align: right;">{{ number_format($danaPem, 0, ',', '.') }}</td>
                                <td style="text-align: right;">{{ number_format($danaPT, 0, ',', '.') }}</td>
                                <td style="text-align: right;">{{ number_format($totalDana, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        
                        @php
                            $grandTotalJkem = 0;
                            if ($bidang->proker && is_iterable($bidang->proker)) {
                                foreach ($bidang->proker as $p) {
                                    if ($p->kegiatan && is_iterable($p->kegiatan)) {
                                        foreach ($p->kegiatan as $k) {
                                            $grandTotalJkem += $k->total_jkem ?? 0;
                                        }
                                    }
                                }
                            }
                            
                            $grandFrekuensi = 0;
                            $grandJkem = 0;
                            $grandDanaMhs = 0;
                            $grandDanaMas = 0;
                            $grandDanaPem = 0;
                            $grandDanaPT = 0;
                            
                            if ($bidang->proker && is_iterable($bidang->proker)) {
                                foreach ($bidang->proker as $p) {
                                    if ($p->kegiatan && is_iterable($p->kegiatan)) {
                                        foreach ($p->kegiatan as $k) {
                                            if ($k->logbookKegiatan && is_iterable($k->logbookKegiatan)) {
                                                foreach ($k->logbookKegiatan as $l) {
                                                    if ($l->logbookHarian) {
                                                        $grandFrekuensi += is_countable($l->logbookHarian) ? count($l->logbookHarian) : 1;
                                                        
                                                        if (is_object($l->logbookHarian) && method_exists($l->logbookHarian, 'sum')) {
                                                            $grandJkem += $l->logbookHarian->sum('jkem');
                                                        } elseif (isset($l->logbookHarian->jkem)) {
                                                            $grandJkem += $l->logbookHarian->jkem;
                                                        }
                                                    }
                                                    
                                                    if ($l->dana) {
                                                        $grandDanaMhs += $l->dana->dana_mahasiswa ?? 0;
                                                        $grandDanaMas += $l->dana->dana_masyarakat ?? 0;
                                                        $grandDanaPem += $l->dana->dana_pemerintah ?? 0;
                                                        $grandDanaPT += $l->dana->dana_pt ?? 0;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            
                            $grandTotalDana = $grandDanaMhs + $grandDanaMas + $grandDanaPem + $grandDanaPT;
                        @endphp
                        
                        <tr style="background-color: #f0f0f0; font-weight: bold;">
                            <td colspan="3" style="text-align: center;">TOTAL BIDANG {{ strtoupper($bidang->nama) }}</td>
                            <td colspan="2"></td>
                            <td style="text-align: center;">{{ $grandFrekuensi }}</td>
                            <td style="text-align: center;">{{ $grandJkem }}</td>
                            <td style="text-align: right;">{{ number_format($grandDanaMhs, 0, ',', '.') }}</td>
                            <td style="text-align: right;">{{ number_format($grandDanaMas, 0, ',', '.') }}</td>
                            <td style="text-align: right;">{{ number_format($grandDanaPem, 0, ',', '.') }}</td>
                            <td style="text-align: right;">{{ number_format($grandDanaPT, 0, ',', '.') }}</td>
                            <td style="text-align: right;">{{ number_format($grandTotalDana, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        @endforeach
</body>

</html>
