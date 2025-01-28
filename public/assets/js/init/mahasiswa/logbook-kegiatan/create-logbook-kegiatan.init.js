$(document).ready(function () {
    $(".datatable").DataTable({
        searching: false,
        // Hidden showing 0 to 10 of 10 entries
        info: false,
        paging: false,
        responsive: false,
    });

    var startTime = flatpickr("#start-time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        locale: "id",
        onChange: function (selectedDates, dateStr, instance) {
            endTime.set("minTime", dateStr);
        },
    });

    var endTime = flatpickr("#end-time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        locale: "id",
    });

    $(document).on("change", "#start-time", function () {
        $("#end-time").prop("disabled", false);
        getJkemVal();
    });

    // Hitung menit selisih start-time dan end-time
    $(document).on("change", "#end-time", function () {
        getJkemVal();
    });
    getJkemVal();

    getKegiatan();

    var currencyMask = IMask(document.getElementById("currency-mask"), {
        mask: "Rp.num",
        blocks: {
            num: {
                // nested masks are available!
                mask: Number,
                thousandsSeparator: ".",
                min: 100,
            },
        },
    });

    currencyMask.on("accept", function () {
        // Update hidden field with numeric value
        document.getElementById("hidden-amount").value =
            currencyMask.unmaskedValue;
    });

    $(".btn-edit").click(function () {
        // Ambil data dari baris yang diklik
        var id = $(this).data("id");
        var jamMulai = $(this).data("jam_mulai");
        var jamSelesai = $(this).data("jam_selesai");
        var idKegiatan = $(this).data("id_kegiatan");
        var kegiatan = $(this).data("kegiatan");
        var bidang = $(this).data("bidang");
        var totalJkem = $(this).data("total_jkem");
        var deskripsi = $(this).data("deskripsi");

        // Isi form di dalam modal dengan data yang diambil
        $("#dataModal #id").val(id);
        $("#dataModal #start-time").val(jamMulai);
        $("#dataModal #end-time").val(jamSelesai);
        $("#dataModal #kegiatan_select").val(idKegiatan);
        $("#dataModal #bidang").val(bidang);
        $("#dataModal #jkem").val(totalJkem);
        $("#dataModal #des").val(deskripsi);
        $("#dataModal #status").val("ubah");

        // Tampilkan modal
        $("#dataModal").modal("show");
    });

    $(".btn-delete").click(function () {
        // Ambil data dari baris yang diklik
        var id = $(this).data("id");
        $("#deleteModal #id_delete").val(id);
    });

    $(document).on("click", "#table-dana", function () {
        const id = $(this).data("id");
        $("#id_logbook_kegiatan_modal").val(id);

        getPendanaanByKegiatan(id);
    });
});

function setBidangValue(selectedValue) {
    let selectedOption =
        $("#kegiatan_select")[0].selectize.options[selectedValue];
    let bidang = selectedOption ? selectedOption.bidang : "";
    $("#bidang").val(bidang);
}

function getKegiatan() {
    let id_unit = $("#id_unit").val();
    const kegiatan_container = $("#kegiatan_select");

    // Destroy existing selectize instance if any
    if (kegiatan_container.hasClass("selectized")) {
        kegiatan_container[0].selectize.destroy();
    }

    kegiatan_container.selectize({
        create: true,
        sortField: "text",
        placeholder: "Pilih atau tambah program baru",
        onChange: function (value) {
            setBidangValue(value);
        },
    });

    $.ajax({
        type: "GET",
        url: "/logbook/getKegiatan",
        data: {
            id_unit: id_unit,
        },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                let selectize = kegiatan_container[0].selectize;
                response.data.forEach((data) => {
                    selectize.addOption({
                        value: data.id,
                        text: `${data.nama} (PJ: ${data.mahasiswa.user_role.user.nama})`,
                        bidang: data.proker.bidang.nama,
                    });
                });

                // Refresh options list
                selectize.refreshOptions(false);
            }
        },
    });
}

function getPendanaanByKegiatan(id) {
    $.ajax({
        type: "GET",
        url: "/logbook/getPendanaan",
        data: {
            id: id,
        },
        success: function (response) {
            if (response.status === "success") {
                $("#table_pendanaan_modal").empty();
                $("#table_pendanaan_modal").html(tablePendanaan(response.data));
            }
        },
    });
}
function parseTime(timeStr) {
    // Mendapatkan tanggal hari ini dalam format YYYY-MM-DD
    let today = new Date().toISOString().split("T")[0];
    // Menggabungkan tanggal dengan waktu
    return new Date(`${today}T${timeStr}`);
}
function getJkemVal() {
    let start = parseTime($("#start-time").val());
    let end = parseTime($("#end-time").val());
    if (isNaN(start) || isNaN(end)) {
        return;
    }
    let diff = end - start;
    let minutes = Math.floor(diff / 60000);
    $("#jkem").val(minutes);
}
function tablePendanaan(data) {
    let total = 0;

    let table = ``;

    data.forEach((item, index) => {
        total += item.jumlah;
        table += `
            <tr>
                <td>${index + 1}</td>
                <td>Rp. ${item.jumlah.toLocaleString("id-ID")}</td>
                <td>${item.sumber}</td>
            </tr>
        `;
    });

    table += `
            <tr>
                <th colspan="3">Total : Rp. ${total.toLocaleString(
                    "id-ID"
                )}</th>
            </tr>`;

    return table;
}

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, function (char) {
        return char.toUpperCase();
    });
}
