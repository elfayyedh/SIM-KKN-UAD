$(document).ready(function () {
    // Datepicker
    flatpickr(".datepicker-basic", {
        //Buat tanggal format indonesia
        locale: "id",
        altInput: true,
        altFormat: "l, j F Y",
        dateFormat: "Y-m-d",
    });

    var id_bidang_proker;

    $("#deleteModal").on("shown.bs.modal", function (event) {
        var button = $(event.relatedTarget); // Button yang memicu modal
        var id = button.data("id"); // Ambil id dari data-id
        var count = button.data("count"); // Ambil count dari data-count
        var form = $("#deleteForm");
        id_bidang_proker = id;

        form.attr("action", "/bidang/" + id); // Set action ke delete route dengan id
        $("#total_proker").text(count); // Update total_proker di modal
    });

    $("#btn-delete-bidang-proker").on("click", function () {
        $.ajax({
            type: "DELETE",
            url: "/bidang/destroy/" + id_bidang_proker,
            data: {
                _token: $("meta[name='csrf-token']").attr("content"),
            },
            success: function (response) {
                if (response.status === "success") {
                    $("#deleteModal").modal("hide");
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
        });
    });
});
