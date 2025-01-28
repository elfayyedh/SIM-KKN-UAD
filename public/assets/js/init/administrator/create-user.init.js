$(document).ready(function () {
    // When role is change to Admin, make select id="kkn" is disabled
    $("#role").on("change", function () {
        if ($(this).val() == "Admin") {
            $("#kkn").prop("disabled", true);
        } else {
            $("#kkn").prop("disabled", false);
        }
    });
});
