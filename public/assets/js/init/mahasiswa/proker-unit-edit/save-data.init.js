$(document).ready(function () {
    var defaultModal = `
    <div class="mb-3">
        <i class="bx bx-check-circle display-4 text-success"></i>
    </div>
    <h5>Konfirmasi penyimpanan data</h5>
    `;

    var successFinish = `
    <div class="mb-3">
        <i class="bx bx-check-circle display-4 text-success"></i>
    </div>
    <h5>Data proker berhasil di ubah</h5>
    `;

    var loader = `
    <div class="spinner-border text-primary m-1" role="status">
        <span class="sr-only">Loading...</span>
    </div>`;

    var errorModal = `
    <div class="mb-3">
        <i class="bx bx-error-circle display-4 text-danger"></i>
    </div>
    <h5>Harap lengkapi data terlebih dahulu</h5>
    `;
    var errorFinish = `
    <div class="mb-3">
        <i class="bx bx-error-circle display-4 text-danger"></i>
    </div>
    <h5>Terjadi kesalahan!</h5>
    `;

    const modal = $("#modal-status");
    const modal_footer = $(".modal-footer");

    $("#save-change").click(function (e) {
        e.preventDefault();

        if (handleError()) {
            modal.html(defaultModal);
            $("#btn-confirm").removeClass("d-none");
        } else {
            modal.html(errorModal);
            $("#btn-confirm").addClass("d-none");
        }
    });

    function handleError() {
        let valid = true;
        var bidang_proker = $("#bidang_proker").val();
        var program = $("#program").val();

        if (bidang_proker == "") {
            $("#error_bidang").text("*Bidang proker harus diisi");
            valid = false;
        } else {
            $("#error_bidang").text("*");
        }
        if (program == "") {
            $("#error_program").text("*Program harus diisi");
            valid = false;
        } else {
            $("#error_program").text("*");
        }

        $(".kegiatan-row").each(function () {
            var kegiatan = $(this).find("#kegiatan").val();
            var frekuensi = $(this).find("#frekuensi").val();
            var jkem = $(this).find("#jkem").val();
            var totalJkem = $(this).find("#totalJkem").val();
            var tanggal = $(this).find("#tanggal_kegiatan").val();
            if (kegiatan == "") {
                $(this).find("#error_kegiatan").text("*Kegiatan harus diisi");
                valid = false;
            }
            if (frekuensi == "") {
                $(this).find("#error_frekuensi").text("*Frekuensi harus diisi");
                valid = false;
            }
            if (jkem == "") {
                $(this).find("#error_jkem").text("*Jenis Kegiatan harus diisi");
                valid = false;
            }
            if (totalJkem == "") {
                $(this)
                    .find("#error_totalJkem")
                    .text("*Total Jenis Kegiatan harus diisi");
                valid = false;
            }
            if (tanggal == "") {
                $(this)
                    .find("#error_tanggal_kegiatan")
                    .text("*Tanggal Kegiatan harus diisi");
                valid = false;
            }
        });

        return valid;
    }

    $("#btn-confirm").on("click", function () {
        modal.html(loader);
        $("#btn-confirm").addClass("d-none");
        let program = $("#program").val();
        let id_bidang = $("#bidang_proker").val();
        let tempat = $("#tempat").val();
        let sasaran = $("#sasaran").val();
        let id_kkn = $("#id_kkn").val();
        let id_unit = $("#id_unit").val();
        let id_mahasiswa = $("#id_mahasiswa").val();

        // Collect organizer data
        let organizer = [];
        $(".nama_anggota").each(function () {
            let peran = $(this).siblings(".select_peran").val();
            let nama_mahasiswa = $(this).val();
            organizer.push({
                nama: nama_mahasiswa,
                peran: peran,
            });
        });

        // Collect kegiatan data
        let kegiatan = [];
        $("#listKegiatan .kegiatan-row").each(function () {
            let nama = $(this).find('input[name="kegiatan"]').val();
            let frekuensi = $(this).find('input[name="frekuensi"]').val();
            let jkem = $(this).find('select[name="jkem"]').val();
            let totalJkem = $(this).find('input[name="totalJKEM"]').val();

            // Collect tanggal data
            let tanggal = [];
            let dateValues = $(this).find(".tanggal_kegiatan").val();

            if (dateValues) {
                dateValues.split(",").forEach((date) => {
                    tanggal.push({
                        tanggal: date.trim(),
                    });
                });
            }

            kegiatan.push({
                nama: nama,
                frekuensi: frekuensi,
                jkem: jkem,
                totalJkem: totalJkem,
                tanggal: tanggal,
            });
        });

        $.ajax({
            url: "/proker/unit/update", // Replace with your endpoint URL
            type: "PUT",
            data: {
                _token: $("meta[name='csrf-token']").attr("content"),
                program: program,
                id_bidang: id_bidang,
                organizer: organizer,
                kegiatan: kegiatan,
                tempat: tempat,
                sasaran: sasaran,
                id_proker: $("#id_proker").val(),
                id_unit: id_unit, //!!! TODO: get this from the authenticated user
                id_kkn: id_kkn, //!!! TODO: get this from the authenticated user
                id_mahasiswa: id_mahasiswa, //!!! TODO: get this from the authenticated user
            },
            success: function (response) {
                modal_footer.empty();
                modal.html(successFinish);
                modal_footer.html(
                    `<a href="/proker/unit" class="btn btn-primary">Kembali</a>`
                );
            },
            error: function (error) {
                modal.html(errorFinish);
            },
        });
    });
});
