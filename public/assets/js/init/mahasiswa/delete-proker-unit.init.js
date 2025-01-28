$(document).ready(function () {
    let programIdToDelete = null;

    // Event listener for delete button to open modal
    $(document).on("click", "#delete-proker-unit", function () {
        programIdToDelete = $(this).data("id");
        const programName = $(this)
            .closest("tr")
            .find("td:nth-child(2)")
            .text();

        $("#nama_program").text(programName);
    });

    $("#btn-confirm-delete").on("click", function () {
        if (programIdToDelete !== null) {
            // Perform delete action, e.g., send a delete request to the server
            $.ajax({
                url: `/proker/delete/${programIdToDelete}`,
                type: "DELETE",
                data: {
                    _token: $("meta[name='csrf-token']").attr("content"),
                },
                success: function (data) {
                    if (data.success) {
                        location.reload();
                    }
                },
                error: function (error) {
                    alert("Terjadi kesalahan");
                },
            });
        }
    });
});
