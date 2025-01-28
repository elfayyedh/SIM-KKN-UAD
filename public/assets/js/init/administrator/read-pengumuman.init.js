$(document).ready(function () {
    var editorInstance;

    ClassicEditor.create(document.querySelector("#ckeditor-classic"), {
        ckfinder: {
            uploadUrl: "/upload-image",
        },
        toolbar: [
            "heading",
            "|",
            "bold",
            "italic",
            "link",
            "bulletedList",
            "numberedList",
            "blockQuote",
            "insertTable",
            "mediaEmbed",
            "undo",
            "redo",
        ],
    })
        .then(function (editor) {
            editor.ui.view.editable.element.style.height = "200px";
            editorInstance = editor; // Simpan referensi ke instance editor
        })
        .catch(function (error) {
            console.error(error);
        });

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

        if (editorInstance) {
            editorInstance.setData(jawaban); // Set data CKEditor
        }
    });

    $(document).on("click", ".btn-delete", function () {
        var id = $(this).data("id");

        $("#deleteModal #id_delete").val(id);
    });
});
