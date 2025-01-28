$(document).ready(function () {
    $(".datatable").DataTable();

    $(".datatable-buttons").each(function () {
        var table = $(this).DataTable({
            lengthChange: false,
            buttons: ["copy", "excel", "pdf", "colvis"],
            responsive: true,
        });

        table
            .buttons()
            .container()
            .appendTo(
                $(this).closest(".dataTables_wrapper").find(".col-md-6:eq(0)")
            );

        $(".dataTables_length select").addClass("form-select form-select-sm");
    });
});
