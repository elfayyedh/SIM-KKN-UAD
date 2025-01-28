$(document).ready(function () {
    var id_mahasiswa = $("#id_mahasiswa").val(); // Pastikan ID mahasiswa tersedia
    var tanggal_penerjunan = $("#tanggal_penerjunan").val(); // Tanggal penerjunan (contoh)
    var tanggal_penarikan = $("#tanggal_penarikan").val(); // Tanggal penarikan (contoh)
    const container_logbook = $("#logbook-container");

    $.ajax({
        url: "/logbook/sholat/check",
        method: "GET",
        data: {
            id_mahasiswa: id_mahasiswa,
            tanggal_penerjunan: tanggal_penerjunan,
            tanggal_penarikan: tanggal_penarikan,
        },
        success: function (response) {
            container_logbook.empty();
            var logbookCards = "";
            var penerjunanDate = new Date(tanggal_penerjunan);
            var penarikanDate = new Date(tanggal_penarikan);
            var today = new Date();

            // Reset hours to avoid issues with time zones
            today.setHours(0, 0, 0, 0);
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
                var d = new Date(today);
                d >= new Date(penerjunanDate) && d <= new Date(penarikanDate);
                d.setDate(d.getDate() - 1)
            ) {
                var dateString = new Date(d).toLocaleDateString("id-ID", {
                    weekday: "long",
                    day: "numeric",
                    month: "long",
                    year: "numeric",
                });
                var dayIndex = Math.ceil(
                    (d - penerjunanDate) / (1000 * 60 * 60 * 24) + 1
                );

                logbookCards += `
                    <a href="/logbook/sholat/add/${id_mahasiswa}/${formatDate(
                    d
                )}" class="text-decoration-none text-dark clickable-card">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div>
                                    <h3 class="mb-0">Hari Ke-${dayIndex}</h3>
                                    <p>${dateString}</p>
                                    <div class="d-flex gap-3 flex-wrap">
                `;

                sholatTimes.forEach(function (waktu, index) {
                    var logbook = response.find(
                        (l) =>
                            l.waktu === dataSholat[index] &&
                            new Date(l.tanggal).toDateString() ===
                                d.toDateString()
                    );
                    var iconClass = logbook
                        ? statusIcons[logbook.status]
                        : "bx-info-circle text-warning";
                    var status = logbook ? logbook.status : "belum diisi";

                    logbookCards += `
                        <div class="d-flex flex-column align-items-center" title="${status}">
                            <i class="bx ${iconClass} font-size-24"></i>
                            <p>${waktu}</p>
                        </div>
                    `;
                });

                logbookCards += `
                                    </div>
                                    <p class="font-size-10 mb-0 text-muted" style="opacity: 0.6;">Klik untuk mengisi logbook sholat berjamaah</p>
                                </div>
                            </div>
                        </div>
                    </a>
                `;
            }

            container_logbook.html(logbookCards);
        },
        error: function (error) {
            console.error("Error fetching logbook data", error);
        },
    });
});

function formatDate(date) {
    var d = new Date(date);
    var month = "" + (d.getMonth() + 1);
    var day = "" + d.getDate();
    var year = d.getFullYear();

    if (month.length < 2) month = "0" + month;
    if (day.length < 2) day = "0" + day;

    return [year, month, day].join("-");
}
