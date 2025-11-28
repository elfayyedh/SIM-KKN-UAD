$(document).ready(function () {
    flatpickr(".datepicker-basic", {
        locale: "id",
        altInput: true,
        altFormat: "l, j F Y",
        dateFormat: "Y-m-d",
    });

    // Kamus Data Template
    const KRITERIA_PRESETS = {
        jkem: {
            judul: "Pencapaian JKEM",
            ket: "1: <30%, 2: 30-50%, 3: >50%",
            var_key: "total_jkem",
            url: "",
            text: "",
        },
        sholat: {
            judul: "Kedisiplinan Sholat Berjamaah",
            ket: "1: <=50%, 2: 51%-75%, 3: >75%",
            var_key: "persen_sholat",
            url: "#logbook_sholat",
            text: "Logbook Sholat",
        },
        form1: {
            judul: "Kelengkapan Form 1 (Program Kerja)",
            ket: "1: Tidak Sesuai, 2: Cukup Sesuai, 3: Sesuai",
            var_key: "",
            url: "#program_kerja",
            text: "Cek Form 1",
        },
        form2: {
            judul: "Kelengkapan Form 2 (Logbook Kegiatan)",
            ket: "1: Tidak Rutin, 2: Cukup Rutin, 3: Rutin",
            var_key: "",
            url: "#logbook_harian",
            text: "Cek Form 2",
        },
        form3: {
            judul: "Kelengkapan Form 3 (Matriks Kegiatan)",
            ket: "1: Tidak Sesuai, 2: Cukup Sesuai, 3: Sesuai",
            var_key: "",
            url: "#matriks",
            text: "Cek Form 3",
        },
        form4: {
            judul: "Kelengkapan Form 4 (Rekap Kegiatan)",
            ket: "1: Tidak Lengkap, 2: Cukup Lengkap, 3: Lengkap",
            var_key: "",
            url: "#rekap",
            text: "Cek Form 4",
        },
        custom: {
            judul: "",
            ket: "",
            var_key: "",
            url: "",
            text: "",
        },
    };

    // Logic untuk MODAL TAMBAH
    $("#add_template").change(function () {
        let val = $(this).val();
        let data = KRITERIA_PRESETS[val];

        if (data) {
            $("#add_judul").val(data.judul);
            $("#add_keterangan").val(data.ket);
            $("#add_variable").val(data.var_key);
            $("#add_url").val(data.url);
            $("#add_text").val(data.text);
        }
    });

    // Logic untuk MODAL EDIT
    // Saat dropdown edit berubah
    $("#edit_template").change(function () {
        let val = $(this).val();
        let data = KRITERIA_PRESETS[val];

        if (data) {
            $("#edit_judul").val(data.judul);
            $("#edit_keterangan").val(data.ket);
            $("#edit_variable").val(data.var_key);
            $("#edit_url").val(data.url);
            $("#edit_text").val(data.text);
        }
    });

    // Saat tombol Edit (Pensil) diklik
    $(document).on("change", ".edit-template-inline", function () {
        let val = $(this).val();
        // Cari baris (row) tempat dropdown ini berada
        let row = $(this).closest(".row-kriteria-inline");
        let customInputs = row.find(".custom-inputs");

        if (val === "custom") {
            // Tampilkan input custom jika dipilih custom
            customInputs.removeClass("d-none");
        } else {
            // Sembunyikan input custom
            customInputs.addClass("d-none");

            // Isi otomatis inputan di baris tersebut
            let data = KRITERIA_PRESETS[val];
            if (data) {
                row.find(".input-judul").val(data.judul);
                row.find(".input-ket").val(data.ket);
                row.find(".input-var").val(data.var_key);
                row.find(".input-url").val(data.url);
                row.find(".input-text").val(data.text);
            }
        }
    });

    // Handle Tombol HAPUS Kriteria
    $(".btn-delete-kriteria").click(function () {
        let id = $(this).data("id");
        $("#formDeleteKriteria").attr("action", "/kriteria/destroy/" + id);
    });

    var id_bidang_proker;

    $("#deleteModal").on("shown.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var id = button.data("id");
        var count = button.data("count");
        var form = $("#deleteForm");
        id_bidang_proker = id;

        form.attr("action", "/bidang/" + id);
        $("#total_proker").text(count);
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
