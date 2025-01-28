$(document).ready(function () {
    var id_mahasiswa = $("#id_mahasiswa").val(); // Pastikan ID mahasiswa tersedia
    var tanggal_penerjunan = $("#tanggal_penerjunan").val(); // Tanggal penerjunan (contoh)
    var tanggal_penarikan = $("#tanggal_penarikan").val(); // Tanggal penarikan (contoh)
    const container_logbook = $("#logbook-container");

    $.ajax({
        url: "/logbook/check",
        method: "GET",
        data: {
            id_mahasiswa: id_mahasiswa,
            tanggal_penerjunan: tanggal_penerjunan,
            tanggal_penarikan: tanggal_penarikan,
        },
        success: function (response) {
            let htmlTag = ``;
            container_logbook.empty();
            var penerjunanDate = new Date(tanggal_penerjunan);
            var penarikanDate = new Date(tanggal_penarikan);
            console.log(
                "Penerjunan : " +
                    penerjunanDate +
                    " Penarikan : " +
                    penarikanDate
            );
            var statusIcons = {
                "sudah diisi": "bx-check-circle text-success",
                "belum diisi": "bx-x-circle text-danger",
                revisi: "bx-info-circle text-warning",
            };
            var today = new Date();

            today.setHours(0, 0, 0, 0);
            penerjunanDate.setHours(0, 0, 0, 0);
            penarikanDate.setHours(0, 0, 0, 0);
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

                var logbook = response.data
                    ? response.data.find(
                          (l) =>
                              new Date(l.tanggal).toDateString() ===
                              d.toDateString()
                      )
                    : null;

                var status = logbook ? logbook.status : "belum diisi";
                var icon = statusIcons[status] || "bx-x-circle text-danger";
                var total_jkem = logbook ? logbook.total_jkem : 0;

                htmlTag += cardLogbook(
                    status,
                    icon,
                    dayIndex,
                    dateString,
                    d,
                    total_jkem
                );
            }

            container_logbook.html(htmlTag);
        },
        error: function (error) {
            console.error("Error fetching logbook data", error);
        },
    });

    function cardLogbook(status, icon, index, tanggal, date, total_jkem) {
        return `
        <a href="/logbook/kegiatan/add/${id_mahasiswa}/${formatDate(date)}" class="text-decoration-none text-dark clickable-card">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="d-flex">
                        <p class="${
                            status === "sudah diisi"
                                ? "text-success"
                                : status === "revisi"
                                ? "text-warning"
                                : "text-danger"
                        } fw-bold mb-0 d-flex align-items-center">
                            <i class="bx ${icon} me-1"></i>${capitalizeEachWord(status)}
                        </p>
                    </div>
                    <div class="mt-2">
                        <h3 class="mb-0">Hari Ke-${index}</h3>
                        <p class="formatTanggal">${tanggal}</p>
                        <p class="text-muted">Anda mencapai <strong>${
                            total_jkem != null ? total_jkem : 0
                        }</strong> JKEM hari ini</p>
                        <p class="font-size-10 mb-0 text-muted" style="opacity: 0.6;">Klik untuk mengisi
                            kegiatan</p>
                    </div>
                </div>
            </div>
        </a>`;
    }

    function formatDate(date) {
        var d = new Date(date);
        var month = "" + (d.getMonth() + 1);
        var day = "" + d.getDate();
        var year = d.getFullYear();

        if (month.length < 2) month = "0" + month;
        if (day.length < 2) day = "0" + day;

        return [year, month, day].join("-");
    }
});

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, function (char) {
        return char.toUpperCase();
    });
}
