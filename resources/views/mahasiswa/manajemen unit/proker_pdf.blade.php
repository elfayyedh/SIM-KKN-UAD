<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit Data</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-4">
        <table class="mb-5">
            <thead>
                <tr>
                    <th rowspan="4"><img src="{{ asset('assets/images/logo.svg') }}" alt="logo" width="150">
                    </th>
                    <th>
                        <h4>PROGRAM KERJA KULIAH KERJA NYATA</h4>
                    </th>
                </tr>
                <tr>
                    <th>
                        <h4>KKN REGULER 119</h4>
                    </th>
                </tr>
                <tr>
                    <th>
                        <h4>TAHUN AJARAN 2024/2025</h4>
                    </th>
                </tr>
                <tr>
                    <th>
                        <h4>UNIT VII.A.1</h4>
                    </th>
                </tr>
            </thead>
        </table>

        <ol>
            @foreach ($data['proker'] as $bidang)
                <li class="fw-bold fs-5">{{ $bidang['bidang'] }}</li>
                <ol type="a">
                    @foreach ($bidang['proker'] as $proker)
                        <li class="fw-bold fs-6">{{ $proker['nama'] }}</li>
                        <p>#Daftar kegiatan</p>
                        <table class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle" rowspan="2">
                                        Nama Kegiatan
                                    </th>
                                    <th class="text-center align-middle" colspan="3">Ekuivalensi Kegiatan</th>
                                    <th class="text-center align-middle" rowspan="2">Tanggal Rencana</th>
                                </tr>
                                <tr>
                                    <th class="text-center align-middle">Frekuensi</th>
                                    <th class="text-center align-middle">JKEM</th>
                                    <th class="text-center align-middle">Total JKEM</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($proker['kegiatan'] as $kegiatan)
                                    <tr>
                                        <td class="text-center align-middle">{{ $kegiatan['nama'] }}</td>
                                        <td class="text-center align-middle">{{ $kegiatan['frekuensi'] }}</td>
                                        <td class="text-center align-middle">{{ $kegiatan['jkem'] }}</td>
                                        <td class="text-center align-middle">{{ $kegiatan['total_jkem'] }}</td>
                                        <td class="text-center align-middle">
                                            <ul>

                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                </ol>
            @endforeach
        </ol>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
