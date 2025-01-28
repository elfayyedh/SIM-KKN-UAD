$(document).ready(function () {
    var minDate = $("#tanggal_penerjunan-unit").val();

    function initializeFlatpickr(element, maxDates) {
        element.flatpickr({
            locale: "id",
            mode: "multiple",
            minDate: minDate,
            altInput: true,
            altFormat: "l j F Y",
            dateFormat: "Y-m-d",
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length > maxDates) {
                    selectedDates.splice(maxDates);
                    instance.setDate(selectedDates);
                }
            },
        });
    }

    function calculateTotalJKEM(row) {
        var frekuensi = parseInt(row.find(".frekuensi").val()) || 0;
        var jkem = parseInt(row.find(".jkem").val()) || 0;
        var totalJKEM = frekuensi * jkem;
        row.find(".totalJKEM").val(totalJKEM);
    }

    function disableSubmit(row) {
        const tanggal = row.find(".tanggal_kegiatan").val().split(",");
        const kegiatan = row.find("input[name='nama']").val();
        const frekuensi = row.find("input[name='frekuensi']").val();
        const jkem = row.find("select[name='jkem']").val();

        const isDisabled =
            tanggal.length < frekuensi || !kegiatan || !frekuensi || !jkem;
        row.find(".modal_submit").prop("disabled", isDisabled);
    }

    // Handle edit kegiatan modal
    $(document).on("click", ".edit-kegiatan", function () {
        var modal = $("#editKegiatanModal"); // Assuming the edit modal has this ID
        var id = $(this).data("id");
        var nama = $(this).data("nama");
        var frekuensi = $(this).data("frekuensi");
        var jkem = $(this).data("jkem");
        var total_jkem = $(this).data("total_jkem");
        var tanggal = $(this).data("tanggal");

        modal.find("#id_kegiatan").val(id);
        modal.find("#kegiatan").val(nama);
        modal.find("#frekuensi").val(frekuensi);
        modal.find('#jkem option[value="' + jkem + '"]').prop("selected", true);
        modal.find("#totalJKEM").val(total_jkem);
        modal.find("#tanggal_kegiatan").val(tanggal);

        var maxDates = parseInt(frekuensi) || 0;
        initializeFlatpickr(modal.find("#tanggal_kegiatan"), maxDates);
    });

    // Handle tambah kegiatan modal
    $(document).on("click", ".tambah-kegiatan", function () {
        var modal = $("#addKegiatan"); // Assuming the tambah modal has this ID

        modal.find("#kegiatan").val("");
        modal.find("#frekuensi").val("");
        modal.find("#jkem").val("");
        modal.find("#totalJKEM").val("");
        modal.find("#tanggal_kegiatan").val("");

        initializeFlatpickr(modal.find("#tanggal_kegiatan"), 0);
    });

    // Handle frekuensi change in both modals
    $(document).on("change", ".frekuensi", function () {
        var row = $(this).closest(".modal-content");
        var frekuensi = parseInt($(this).val()) || 0;
        var dates = row.find(".tanggal_kegiatan");
        dates.val("");
        initializeFlatpickr(dates, frekuensi);
    });

    // Handle changes to jkem, frekuensi, and tanggal_kegiatan
    $(document).on(
        "change",
        ".jkem, .frekuensi, .tanggal_kegiatan, .kegiatan",
        function () {
            var row = $(this).closest(".modal-content");
            calculateTotalJKEM(row);
            disableSubmit(row);
        }
    );

    // Disable submit button on document load for both modals
    $(".modal-content").each(function () {
        disableSubmit($(this));
    });
});
