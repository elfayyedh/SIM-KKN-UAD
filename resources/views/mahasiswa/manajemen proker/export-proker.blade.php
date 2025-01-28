<table>
    <tr>
        <td rowspan="4" colspan="3">
            <img src="{{ public_path('assets/images/favicon.ico') }}" alt="Header Image" width="100">
        </td>
        <td>PROGRAM KERJA UNIT</td>
    </tr>
    <tr>
        <td>{{ $unit->kkn->nama }}</td>
    </tr>
    <tr>
        <td>UNIT {{ $unit->nama }}</td>
    </tr>
    <tr>
        <td>{{ $unit->lokasi->nama }}</td>
    </tr>

    @foreach ($prokers as $proker)
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <td colspan="5">{{ $proker->nama }}</td>
        </tr>
        <tr style="border: solid 2px;">
            <th>No</th>
            <th>Kegiatan</th>
            <th>Frekuensi</th>
            <th>JKEM</th>
            <th>Total JKEM</th>
            <th>Tanggal Rencana</th>
            <th>Penanggung Jawab</th>
        </tr>
        @foreach ($proker->kegiatan as $kegiatan)
            <tr style="border: solid 2px;">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $kegiatan->nama }}</td>
                <td>{{ $kegiatan->frekuensi }}</td>
                <td>{{ $kegiatan->jkem }}</td>
                <td>{{ $kegiatan->total_jkem }}</td>
                <td>
                    <ul>
                        @foreach ($kegiatan->tanggalRencanaProker as $date)
                            <li>{{ $date->tanggal }}</li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    {{ $kegiatan->mahasiswa->userRole->user->nama }}
                </td>
            </tr>
        @endforeach
    @endforeach
</table>
