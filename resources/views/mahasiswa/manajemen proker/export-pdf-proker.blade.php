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
    @media print {

        /* Menyembunyikan elemen header dan footer */
        .header,
        .footer {
            display: none;
        }

        /* Jika Anda ingin mengatur margin dan padding */
        body {
            margin: 0;
            padding: 0;
        }

        /* Mengatur ukuran halaman jika perlu */
        @page {
            size: auto;
            /* Ukuran otomatis */
            margin: 30px;
            padding: 30px 0;
            /* Menghapus margin default */
        }
    }
</style>


<body>
    <div style="page-break-after: always">
        <div class="d-flex flex-column justify-content-between align-items-center text-center"
            style="height: 100vh; padding: 120px 0">
            <div style="margin-bottom: 130px;">
                <img src="{{ url('assets/images/logo UAD hitam.jpg') }}" class="mb-3" height="250" alt="Logo">
                <h3 class="fw-bold">PROGRAM KERJA UNIT</h3>
                <h4 class="fw-bold">KULIAH KERJA NYATA</h4>
                <h4 class="fw-bold">UNIVERSITAS AHMAD DAHLAN</h4> <!-- Perbaiki dari </h5> menjadi </h4> -->
            </div>

            <div class="border text-start border-dark rounded py-3 px-5 mb-5" style="max-width: fit-content;">
                <h5>Unit : {{ $unit->nama }}</h5>
                <h5>DPL : {{ $unit->dpl->userRole->user->nama }}</h5>
                <h5>Lokasi : {{ $unit->lokasi->nama }}</h5>
            </div>

            <div class="mt-auto">
                <h5 class="mb-1">Bidang Pengabdian kepada Masyarakat dan Kuliah Kerja Nyata</h5>
                <h5 class="mb-1">Lembaga Penelitian dan Pengabdian kepada Masyarakat</h5>
                <h5 class="mb-1">Universitas Ahmad Dahlan</h5>
            </div>
        </div>
    </div>

    <div class="container mx-auto">
        @foreach ($prokers as $bidang)
            <p>{{ $loop->iteration }}. {{ $bidang->nama }}</p>
            @foreach ($bidang->proker as $proker)
                <table class="table table-bordered nowrap w-100 mb-3">
                    <tr>
                        <td colspan="7">{{ $proker->nama }}</td>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Kegiatan</th>
                        <th>Frekuensi</th>
                        <th>JKEM</th>
                        <th>Total JKEM</th>
                        <th>Tanggal Rencana</th>
                        <th>Penanggung Jawab</th>
                    </tr>
                    @foreach ($proker->kegiatan as $kegiatan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $kegiatan->nama }}</td>
                            <td>{{ $kegiatan->frekuensi }}</td>
                            <td>{{ $kegiatan->jkem }}</td>
                            <td>{{ $kegiatan->total_jkem }}</td>
                            <td>
                                <ul class="list-unstyled text-nowrap">
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
                </table>
            @endforeach
        @endforeach
    </div>

</body>


</html>
