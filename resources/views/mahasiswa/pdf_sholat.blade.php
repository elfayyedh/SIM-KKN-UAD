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
                <h3 class="fw-bold">BUKU FORM SHOLAT BERJAMA’AH</h3>
                <h4 class="fw-bold">KULIAH KERJA NYATA</h4>
                <h4 class="fw-bold">UNIVERSITAS AHMAD DAHLAN</h4> <!-- Perbaiki dari </h5> menjadi </h4> -->
            </div>

            <div class="border text-start border-dark rounded py-3 px-5 mb-5" style="max-width: fit-content;">
                <h5>Nama : {{ $mahasiswa->userRole->user->nama }}</h5>
                <h5>NIM : {{ $mahasiswa->nim }}</h5>
                <h5>UNIT : {{ $mahasiswa->unit->nama }}</h5>
            </div>

            <div class="mt-auto">
                <h5 class="mb-1">Bidang Pengabdian kepada Masyarakat dan Kuliah Kerja Nyata</h5>
                <h5 class="mb-1">Lembaga Penelitian dan Pengabdian kepada Masyarakat</h5>
                <h5 class="mb-1">Universitas Ahmad Dahlan</h5>
            </div>
        </div>
    </div>

    <section class="m-5">

        <table class="table table-borderless mb-3">
            <tr class="border-bottom">
                <td class="p-0">Nama</td>
                <td class="p-0">:</td>
                <td class="p-0">{{ $mahasiswa->userRole->user->nama }}</td>
            </tr>
            <tr class="border-bottom">
                <td class="p-0">NIM</td>
                <td class="p-0">:</td>
                <td class="p-0">{{ $mahasiswa->nim }}</td>
            </tr>
            <tr class="border-bottom">
                <td class="p-0">PRODI</td>
                <td class="p-0">:</td>
                <td class="p-0">{{ $mahasiswa->prodi->nama_prodi }}</td>
            </tr>
            <tr class="border-bottom">
                <td class="p-0">Lokasi</td>
                <td class="p-0">:</td>
                <td class="p-0">{{ $mahasiswa->unit->lokasi->nama }}</td>
            </tr>
            <tr class="border-bottom">
                <td class="p-0">Unit</td>
                <td class="p-0">:</td>
                <td class="p-0">{{ $mahasiswa->unit->nama }}</td>
            </tr>
            <tr class="border-bottom">
                <td class="p-0">DPL</td>
                <td class="p-0">:</td>
                <td class="p-0">{{ $mahasiswa->unit->dpl->userRole->user->nama }}</td>
            </tr>
        </table>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Keterangan</th>
                    <th>Jumlah Jamaah</th>
                    <th>Nama Imam</th>
                </tr>
            </thead>
            <tbody id="logbook-sholat-container">

            </tbody>
        </table>
    </section>

    <input type="hidden" id="id_mahasiswa" value="{{ $mahasiswa->id }}">
    <input type="hidden" id="id_unit" value="{{ $mahasiswa->id_unit }}">
    <input type="hidden" id="id_kkn" value="{{ $mahasiswa->id_kkn }}">
    <input type="hidden" id="tanggal_penerjunan" value="{{ $mahasiswa->unit->tanggal_penerjunan }}">
    <input type="hidden" id="tanggal_penarikan"
        value="{{ $mahasiswa->unit->tanggal_penarikan != null ? $mahasiswa->unit->tanggal_penarikan : $mahasiswa->kkn->tanggal_selesai }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('assets/js/init/mahasiswa/profil-mahasiswa/read-logbook-sholat.init.js') }}"></script>
</body>


</html>
