$(document).ready(async function () {
    const id_unit = $("#id_unit").val();
    const table_anggota = $(".table-anggota");
    table_anggota.html("Loading anggota...");

    try {
        // Mendapatkan data dari server
        const response = await fetch(`/unit/getAnggota/${id_unit}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
            },
        });

        // Mengecek jika response status adalah OK
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Mengambil data JSON dari response
        const data = await response.json();
        let html =
            "<table class='table table-bordered table-responsive text-nowrap nowrap w-100 mt-1'>";
        html += "<thead>";
        html += "<tr>";
        html += "<th>No</th>";
        html += "<th>NIM</th>";
        html += "<th>Nama</th>";
        html += "<th>Jenis Kelamin</th>";
        html += "<th>Nomor Telpon</th>";
        html += "<th>Jabatan</th>";
        html += "<th>Prodi</th>";
        html += "<th>Aksi</th>";
        html += "</tr>";
        html += "</thead>";
        html += "<tbody>";
        for (let i = 0; i < data.length; i++) {
            html += "<tr>";
            html += "<td>" + (i + 1) + "</td>";
            html += "<td>" + data[i].nim + "</td>";
            html += "<td>" + data[i].nama + "</td>";
            if (data[i].jenis_kelamin == "L") {
                html += "<td>Laki-laki</td>";
            } else {
                html += "<td>Perempuan</td>";
            }
            html +=
                "<td> <a href='https://wa.me/" +
                data[i].no_telp +
                "'>" +
                data[i].no_telp +
                "</td>";
            if (data[i].jabatan == null) {
                html += "<td>-</td>";
            } else {
                html += "<td>" + data[i].jabatan + "</td>";
            }
            html += "<td>" + data[i].prodi + "</td>";
            html +=
                "<td><a class='btn btn-primary btn-sm' href='/mahasiswa/detail/" +
                data[i].id +
                "'><i class='fas fa-eye'></i></a></td>";
            html += "</tr>";
        }
        html += "</tbody>";
        html += "</table>";
        table_anggota.html(html);
    } catch (error) {
        // Menangani error jika ada
        alert(error.message);
    }
});
