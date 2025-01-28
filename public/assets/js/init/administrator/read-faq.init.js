$(document).ready(function () {
    $("#datatable").DataTable({
        paging: false,
        info: false,
    });

    $(document).on("click", ".edit", function () {
        var id = $(this).data("id");
        var pertanyaan = $(this).data("pertanyaan");
        var jawaban = $(this).data("jawaban");

        $("#faq_form #id_faq").val(id);
        $("#faq_form #pertanyaan").val(pertanyaan);
        $("#faq_form #jawaban").val(jawaban);
    });
    $(document).on("click", ".btn-delete", function () {
        var id = $(this).data("id");

        $("#deleteModal #id_delete").val(id);
    });
});
