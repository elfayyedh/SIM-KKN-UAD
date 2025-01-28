$(document).ready(function () {
    var calendarEl = document.getElementById("calendar");
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: getInitialView(),
        timeZone: "local",
        locale: "id",
        themeSystem: "bootstrap",
        headerToolbar: {
            left: "",
            center: "",
            right: "",
        },
        windowResize: function (view) {
            var newView = getInitialView();
            calendar.changeView(newView);
        },
        events: function (fetchInfo, successCallback, failureCallback) {
            var id_unit = $("#id_unit").val(); // Ambil id_unit dari input atau variabel lain
            $.ajax({
                url: "/unit/getKegiatanByUnit/" + id_unit, // Endpoint untuk mengambil data
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
    });

    setTimeout(() => {
        calendar.render();
    }, 0);
});

function getInitialView() {
    return "listMonth";
}
