$(document).ready(function () {
    var calendarEl = document.getElementById("calendar");
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: getInitialView(),
        timeZone: "local",
        locale: "id",
        themeSystem: "bootstrap",
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth",
        },
        buttonText: {
            today: "Hari ini",
            month: "Bulan",
            week: "Minggu",
            day: "Hari",
            list: "Daftar",
        },
        windowResize: function (view) {
            var newView = getInitialView();
            calendar.changeView(newView);
        },
        events: function (fetchInfo, successCallback, failureCallback) {
            // Get the selected unit ID from the hidden input
            var selectedUnitId = $('#id_unit').val();

            // Use the unit-specific endpoint to show activities for the selected unit
            $.ajax({
                url: "/unit/getKegiatanByUnit/" + selectedUnitId, // Endpoint untuk mengambil data unit yang dipilih
                type: "GET",
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        var events = response.data.map(function (event) {
                            return {
                                title: event.nama_kegiatan,
                                start: event.tanggal,
                                className: event.className,
                                id: event.id,
                            };
                        });
                        successCallback(events);
                    } else {
                        failureCallback();
                    }
                },
                error: function () {
                    failureCallback();
                },
            });
        },
        eventClick: function (info) {
            var kegiatanId = info.event.id;
            getDataKegiatan(kegiatanId);
        },
    });

    setTimeout(() => {
        calendar.render();
    }, 0);

    // Handle refresh button click
    $('#refreshCalendar').on('click', function() {
        calendar.refetchEvents();
    });
});

function getInitialView() {
    if (window.innerWidth >= 768 && window.innerWidth < 1200) {
        return "timeGridWeek";
    } else if (window.innerWidth <= 768) {
        return "listMonth";
    } else {
        return "dayGridMonth";
    }
}

function getDataKegiatan(id) {
    $.ajax({
        url: "/unit/getKegiatanInfo/" + id,
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                var kegiatan = response.data;
                $("#modalNamaKegiatan").text(kegiatan.nama_kegiatan);
                $("#modalNamaProker").text(kegiatan.nama_proker);
                $("#modalBidangProker").text(kegiatan.bidang_proker);

                $("#kegiatanDetailModal").modal("show");
            } else {
                alert("Data tidak ditemukan.");
            }
        },
        error: function () {
            alert("Terjadi kesalahan saat mengambil data.");
        },
    });
}
