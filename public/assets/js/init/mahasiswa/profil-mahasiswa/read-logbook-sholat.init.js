$(document).ready(function () {
    const container = $("#logbook-sholat-container");
    var id_mahasiswa = $("#id_mahasiswa").val();
    var tanggal_penerjunan = $("#tanggal_penerjunan").val();
    var tanggal_penarikan = $("#tanggal_penarikan").val();
    console.log("Ready");

    $.ajax({
        url: "/logbook/sholat/check",
        method: "GET",
        data: {
            id_mahasiswa: id_mahasiswa,
            tanggal_penerjunan: tanggal_penerjunan,
            tanggal_penarikan: tanggal_penarikan,
        },
        success: function (data) {
            var row = "";
            var penerjunanDate = new Date(tanggal_penerjunan);
            var penarikanDate = new Date(tanggal_penarikan);

            // Reset hours to avoid issues with time zones
            penerjunanDate.setHours(0, 0, 0, 0);
            penarikanDate.setHours(0, 0, 0, 0);

            var sholatTimes = ["Subuh", "Dzuhur", "Ashar", "Maghrib", "Isya"];
            var dataSholat = ["subuh", "dzuhur", "ashar", "maghrib", "isya"];
            var statusIcons = {
                "sholat berjamaah": "bx-check-circle text-success",
                "sedang halangan": "bx-minus-circle text-secondary",
                "tidak sholat berjamaah": "bx-x-circle text-danger",
                "belum diisi": "bx-info-circle text-warning",
            };

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
                var dayIndex = Math.ceil(
                    (d - penerjunanDate) / (1000 * 60 * 60 * 24)
                );

                row += `
                    <tr>
                        <td colspan="4" class="p-1 table-secondary">${dateString} (Hari ke-${
                    dayIndex + 1
                })
                        </td>
                    </tr>
                `;

                sholatTimes.forEach(function (waktu, index) {
                    var logbook = data.find(
                        (l) =>
                            l.waktu === dataSholat[index] &&
                            new Date(l.tanggal).toDateString() ===
                                d.toDateString()
                    );
                    var status = logbook ? logbook.status : "-";
                    var imam = logbook && logbook.imam ? logbook.imam : "-";
                    var jumlah_jamaah =
                        logbook && logbook.jumlah_jamaah
                            ? logbook.jumlah_jamaah + " jamaah"
                            : "-";

                    row += `
                        <tr>
                            <td>${waktu}</td>
                            <td>${capitalizeEachWord(status)}</td>
                            <td>${jumlah_jamaah}</td>
                            <td>${imam}</td>
                        </tr>`;
                });
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
