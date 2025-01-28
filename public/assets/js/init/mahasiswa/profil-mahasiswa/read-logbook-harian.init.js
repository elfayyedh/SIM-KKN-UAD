$(document).ready(function () {
    const container = $("#logbook_harian-container");
    var id_mahasiswa = $("#id_mahasiswa").val();
    var tanggal_penerjunan = $("#tanggal_penerjunan").val();
    var tanggal_penarikan = $("#tanggal_penarikan").val();

    $.ajax({
        url: "/logbook/getLogbookKegiatan",
        method: "GET",
        data: {
            id_mahasiswa: id_mahasiswa,
            tanggal_penerjunan: tanggal_penerjunan,
            tanggal_penarikan: tanggal_penarikan,
        },
        success: function (data) {
            console.log(data);
            var row = "";
            var penerjunanDate = new Date(tanggal_penerjunan);
            var penarikanDate = new Date(tanggal_penarikan);

            // Reset hours to avoid issues with time zones
            penerjunanDate.setHours(0, 0, 0, 0);
            penarikanDate.setHours(0, 0, 0, 0);

            // Convert data tanggal to comparable format (YYYY-MM-DD)
            var logbookDataMap = {};
            data.forEach((item) => {
                let date = new Date(item.tanggal);
                date.setHours(0, 0, 0, 0); // Reset hours to avoid issues with time zones
                logbookDataMap[date.toISOString().split("T")[0]] =
                    item.logbook_kegiatan || [];
            });

            for (
                var d = new Date(penerjunanDate);
                d <= penarikanDate;
                d.setDate(d.getDate() + 1)
            ) {
                var dateString = new Date(d).toLocaleDateString("id-ID", {
                    weekday: "long",
                    day: "numeric",
                    month: "long",
                    year: "numeric",
                });

                var currentDateString = d.toISOString().split("T")[0]; // Format: YYYY-MM-DD

                row += `
                    <tr>
                        <td colspan="11" class="p-1 table-secondary">${dateString} (Hari ke-${Math.ceil(
                    (d - penerjunanDate) / (1000 * 60 * 60 * 24) + 1
                )})
                        </td>
                    </tr>
                `;

                var logbookData =
                    logbookDataMap[d.toISOString().split("T")[0]] || [];

                if (logbookData.length > 0) {
                    logbookData.forEach((kegiatan) => {
                        if (kegiatan.jenis != "bersama") {
                            if (kegiatan.id_mahasiswa != id_mahasiswa) {
                                return;
                            }
                        }
                        // Hitung total dana per sumber
                        let danaPT = kegiatan.dana
                            .filter((dana) => dana.sumber === "PT")
                            .reduce((sum, dana) => sum + dana.jumlah, 0);
                        let danaMhs = kegiatan.dana
                            .filter((dana) => dana.sumber === "Mhs")
                            .reduce((sum, dana) => sum + dana.jumlah, 0);
                        let danaMas = kegiatan.dana
                            .filter((dana) => dana.sumber === "Mas")
                            .reduce((sum, dana) => sum + dana.jumlah, 0);
                        let danaPem = kegiatan.dana
                            .filter((dana) => dana.sumber === "Pem")
                            .reduce((sum, dana) => sum + dana.jumlah, 0);

                        // Hitung total dana keseluruhan
                        let total = danaPT + danaMhs + danaMas + danaPem;

                        row += `
                        <tr>
                            <td>${kegiatan.kegiatan.nama}</td>
                            <td>${kegiatan.jam_mulai} - ${
                            kegiatan.jam_selesai
                        }</td>
                            <td>${kegiatan.kegiatan.proker.bidang.nama}</td>
                            <td>${kegiatan.jenis}</td>
                            <td>${kegiatan.total_jkem}</td>
                            <td>${
                                kegiatan.deskripsi ? kegiatan.deskripsi : "-"
                            }</td>
                            <td>${danaMas.toLocaleString("id-ID")}</td>
                            <td>${danaMhs.toLocaleString("id-ID")}</td>
                            <td>${danaPT.toLocaleString("id-ID")}</td>
                            <td>${danaPem.toLocaleString("id-ID")}</td>
                            <td>${total.toLocaleString("id-ID")}</td>
                        </tr>
                    `;
                    });
                }
            }

            container.html(row);
        },
    });
});

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, function (char) {
        return char.toUpperCase();
    });
}
