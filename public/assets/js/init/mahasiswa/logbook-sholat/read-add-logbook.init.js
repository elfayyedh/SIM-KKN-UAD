$(document).ready(function () {
    const iconSholat = {
        "sholat berjamaah":
            '<i class="bx bx-check-circle font-size-20 text-success"></i>',
        "tidak sholat berjamaah":
            '<i class="bx bx-x-circle font-size-20 text-danger"></i>',
        "belum diisi":
            '<i class="bx bx-info-circle font-size-20 text-warning"></i>',
        "sedang halangan":
            '<i class="bx bx-minus-circle font-size-20 text-secondary"></i>',
    };

    const waktuList = ["Subuh", "Dzuhur", "Ashar", "Maghrib", "Isya"];
    const tanggal = $("#tanggal").val();
    const id_mahasiswa = $("#id_mahasiswa").val();
    const kota = $("#kota").val();
    const apiUrl = `https://api.aladhan.com/v1/timingsByCity?city=${kota}&country=Indonesia&method=5`;

    function capitalizeEachWord(str) {
        return str.replace(/\b\w/g, function (char) {
            return char.toUpperCase();
        });
    }

    function isDisableSelect(sholatTimeMs) {
        const now = new Date();
        const nowMs = now.getTime();
        return nowMs < sholatTimeMs + 30 * 60000; // 30 menit setelah waktu sholat
    }

    function saveLogbook(waktu, status, namaImam = "", jumlahJamaah = "") {
        $.ajax({
            url: "/logbook/sholat/saveSholat",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                tanggal: tanggal,
                waktu: waktu,
                status: status,
                nama_imam: namaImam,
                jumlah_jamaah: jumlahJamaah,
                id_mahasiswa: id_mahasiswa,
            },
            success: function (response) {
                if (response.status === "success") {
                    alertify.success("Data diperbarui");
                    editIconSholat();
                } else {
                    console.log("Failed");
                }
            },
            error: function (error) {
                console.error("Error saving logbook data", error);
            },
        });
    }

    function editIconSholat() {
        $.ajax({
            type: "GET",
            url: `/logbook/sholat/getLogbookByDate/${tanggal}`,
            success: function (response) {
                // Set default icons
                waktuList.forEach((waktu) => {
                    $(`.icons-${waktu}`).html(iconSholat["belum diisi"]);
                });

                if (response.length > 0) {
                    response.forEach((logbook) => {
                        const waktu = capitalizeEachWord(logbook.waktu);
                        if (waktuList.includes(waktu)) {
                            $(`.icons-${waktu}`).html(
                                iconSholat[logbook.status] ||
                                    iconSholat["belum diisi"]
                            );
                        }
                    });
                }
            },
            error: function (error) {
                console.error("Error fetching logbook data", error);
            },
        });
    }

    // Event handler for select change
    $(document).on("change", ".logbook-select", function () {
        const waktu = $(this).data("waktu");
        const status = $(this).val();

        if (status === "sholat berjamaah") {
            $(`#details-${waktu}`).removeClass("d-none");
        } else {
            $(`#details-${waktu}`).addClass("d-none");
        }
        saveLogbook(waktu, status);
    });

    // Event handler for detail inputs change
    $(document).on("change", ".detail-input", function () {
        const waktu = $(this).data("waktu");
        const status = $(`.logbook-select[data-waktu="${waktu}"]`).val();
        const namaImam = $(`#nama_imam_${waktu}`).val();
        const jumlahJamaah = $(`#jumlah_jamaah_${waktu}`).val();

        saveLogbook(waktu, status, namaImam, jumlahJamaah);
    });

    // Initial data load
    function loadData() {
        $.ajax({
            url: `/logbook/sholat/getLogbookByDate/${tanggal}`,
            method: "GET",
            success: function (response) {
                response.forEach(function (logbook) {
                    const waktu = capitalizeEachWord(logbook.waktu);
                    const statusClass = `.logbook-select[data-waktu="${waktu}"]`;
                    const detailsSelector = `#details-${waktu}`;

                    // Set status select option
                    $(statusClass).val(logbook.status);

                    // Show or hide details based on status
                    if (logbook.status === "sholat berjamaah") {
                        $(detailsSelector).removeClass("d-none");
                        $(detailsSelector)
                            .find(`#nama_imam_${waktu}`)
                            .val(logbook.imam);
                        $(detailsSelector)
                            .find(`#jumlah_jamaah_${waktu}`)
                            .val(logbook.jumlah_jamaah);
                    } else {
                        $(detailsSelector).addClass("d-none");
                        $(detailsSelector).find(`#nama_imam_${waktu}`).val(""); // Clear the input
                        $(detailsSelector)
                            .find(`#jumlah_jamaah_${waktu}`)
                            .val(""); // Clear the select
                    }
                });
            },
            error: function (error) {
                console.error("Error fetching logbook data", error);
            },
        });
    }

    // Check if the date is today and fetch prayer times from API
    const today = moment().format("YYYY-MM-DD");

    if (tanggal === today) {
        $.getJSON(apiUrl, function (data) {
            const timings = data.data.timings;
            const waktuSholatMap = {
                Subuh: "Fajr",
                Dzuhur: "Dhuhr",
                Ashar: "Asr",
                Maghrib: "Maghrib",
                Isya: "Isha",
            };

            $(".logbook-select").each(function () {
                const waktu = $(this).data("waktu");
                const apiWaktuSholat = waktuSholatMap[waktu];
                const sholatTimeStr = timings[apiWaktuSholat];
                const sholatTime = new Date(`${today}T${sholatTimeStr}:00`); // Format waktu sebagai objek Date
                const sholatTimeMs = sholatTime.getTime(); // Mendapatkan timestamp dalam milidetik

                // Periksa apakah waktu saat ini sudah melewati waktu sholat
                const disableSelect = isDisableSelect(sholatTimeMs); // Tambahkan 30 menit dalam milidetik

                $(this).prop("disabled", disableSelect);
            });
        });
    } else {
        $(".logbook-select").prop("disabled", false);
    }

    const loader = `
    <div class="spinner-border text-primary m-1" role="status">
        <span class="sr-only">Loading...</span>
    </div>`;

    // Button click handler for setting halangan status for the entire day
    $(document).on("click", "#btn-halangan-hari-ini", function () {
        $(".spinner").html(loader);
        $.ajax({
            url: "/logbook/sholat/halanganFullDay/",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                id_mahasiswa: id_mahasiswa,
                tanggal: tanggal,
            },
            success: function (response) {
                if (response.status == "success") {
                    $(".spinner").empty();
                    window.location = "/logbook/sholat";
                    alertify.success("Data diperbarui");
                }
            },
            error: function (error) {
                console.error("Error saving logbook data", error);
            },
        });
    });

    // Edit icons based on the logbook data
    editIconSholat();
    loadData();
});
