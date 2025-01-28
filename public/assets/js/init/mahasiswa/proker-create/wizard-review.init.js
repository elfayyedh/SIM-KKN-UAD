$(document).ready(function () {
    $("#peranNextButton, .next-berikutnya").on("click", function () {
        let bidang = $("#bidang_proker option:selected").text();
        let program;
        if (window.location.pathname.includes("edit")) {
            program = $("#program").val();
        } else {
            program = $("#program").text();
        }
        let tempat = $("#tempat").val();
        let sasaran = $("#sasaran").val();
        var totalJkem = 0;
        var totalKegiatan = 0;

        // Hitung total JKEM dan total kegiatan
        $(".kegiatan-row").each(function () {
            totalJkem += parseInt($(this).find(".totalJKEM").val());
            totalKegiatan += 1;
        });

        // Set data review
        $("#data-review_bidang").text(bidang);
        $("#data-review_program").text(program);
        $("#data-review_totalJKEM").text(totalJkem);
        $("#data-review_tempat").text(tempat);
        $("#data-review_sasaran").text(sasaran);

        var tbody = $(".review_daftar-kegiatain").find("table tbody");
        tbody.empty();

        // Buat list peran mahasiswa
        let peran_Mahasiswa = "<ul>";
        $(".peran_Mahasiswa .row .col-lg-4").each(function () {
            let mhs = $(this).find(".nama_anggota").val();
            let prn = $(this).find(".nama_peran").val();
            peran_Mahasiswa += `<li><span class="fw-medium">${mhs}</span> : ${prn}</li>`;
        });
        peran_Mahasiswa += "</ul>";

        var indexKegiatan = 0;
        $(".kegiatan-row").each(function () {
            const kegiatan = $(this).find("input[name='kegiatan']").val();
            const frekuensi = $(this).find("input[name='frekuensi']").val();
            const jkem = $(this).find("select[name='jkem']").val();
            const totaljkem = $(this).find(".totalJKEM").val();
            const tanggal = $(this).find(".tanggal_kegiatan").val();

            const tanggalArray = tanggal.split(",").map((date) => date.trim());

            let tanggalListHTML = "<ul>";
            tanggalArray.forEach((date) => {
                tanggalListHTML += `<li>${moment(date).format(
                    "dddd, D MMMM YYYY"
                )}</li>`;
            });
            tanggalListHTML += "</ul>";

            // Tambahkan baris ke tabel
            if (kegiatan != "") {
                tbody.append(`
            <tr>
                <td>${kegiatan}</td>
                <td>${frekuensi}</td>
                <td>${jkem}</td>
                <td>${totaljkem}</td>
                <td>${tanggalListHTML}</td>
                ${
                    (indexKegiatan == 0 &&
                        window.location.pathname.includes("unit")) ||
                    window.location.pathname.includes("edit")
                        ? `<td rowspan="${totalKegiatan}">${peran_Mahasiswa}</td>`
                        : ""
                }
            </tr>
        `);
            }
            indexKegiatan++;
        });
    });
});
