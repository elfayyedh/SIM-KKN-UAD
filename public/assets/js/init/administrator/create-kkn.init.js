$(document).ready(function () {
    // Datepicker
    flatpickr(".datepicker-basic", {
        locale: "id",
        altInput: true,
        altFormat: "l, j F Y",
        dateFormat: "Y-m-d",
    });

    var fieldTemplate = `
    <div class="row border mb-3">
        <div class="col-lg-4">
            <div class="mb-3">
                <label for="bidang" class="form-label">Nama bidang</label>
                <input type="text" class="form-control" id="bidang" placeholder="Masukkan nama bidang" value="">
            </div>
        </div>
        <div class="col-lg-3">
            <div class="mb-3">
                <label for="tipe_bidang" class="form-label">Tipe</label>
                <select id="tipe_bidang" class="form-select">
                    <option value="individu">Individu</option>
                    <option value="unit">Bersama</option>
                </select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="mb-3">
                <label for="syarat_jkem" class="form-label">Minimal JKEM</label>
                <input type="number" class="form-control" id="syarat_jkem" placeholder="Masukkan minimal JKEM" value="">
            </div>
        </div>
        <div class="col-lg-2 d-flex align-items-end">
            <div class="mb-3">
                <button class="btn btn-soft-danger removeFieldButton"><i class="bx bx-trash"></i> Hapus</button>
            </div>
        </div>
    </div>
    `;

    // Bidang default
    var defaultFields = [
        { name: "Keilmuan", tipe: "individu", jkem: 600 },
        { name: "Keagamaan", tipe: "individu", jkem: 1000 },
        { name: "Seni & Olahraga", tipe: "individu", jkem: 600 },
        { name: "Tematik/Non-Tematik", tipe: "unit", jkem: 6000 },
    ];

    // Tambahkan bidang default
    defaultFields.forEach(function (field) {
        var newField = $(fieldTemplate);
        newField.find("#bidang").val(field.name);
        newField.find("#tipe_bidang").val(field.tipe);
        newField.find("#syarat_jkem").val(field.jkem);
        $("#fieldsContainer").append(newField);
    });

    $("#tambah-bidang").click(function (e) {
        e.preventDefault();
        $("#fieldsContainer").append(fieldTemplate);
    });

    $("#fieldsContainer").on("click", ".removeFieldButton", function (e) {
        e.preventDefault();
        $(this).closest(".row").remove();
    });

    const KRITERIA_PRESETS = {
        jkem: {
            judul: "Pencapaian JKEM",
            ket: "1: <30% (JKEM <2460), 2: 30-50% (JKEM 2460-4100), 3: >50% (JKEM >4100)",
            var_key: "total_jkem",
            url: "",
            text: "",
        },
        sholat: {
            judul: "Sholat",
            ket: "1: <=50%, 2: 51%-75%, 3: >75%",
            var_key: "persen_sholat",
            url: "#logbook_sholat",
            text: "Logbook Sholat",
        },
        form1: {
            judul: "Form 1",
            ket: "1: Tidak Sesuai, 2: Cukup Sesuai, 3: Sesuai",
            var_key: "",
            url: "#program_kerja",
            text: "Cek Form 1",
        },
        form2: {
            judul: "Form 2",
            ket: "1: Tidak Rutin, 2: Cukup Rutin, 3: Rutin",
            var_key: "",
            url: "#logbook_harian",
            text: "Cek Form 2",
        },
        form3: {
            judul: "Form 3",
            ket: "1: Tidak Sesuai, 2: Cukup Sesuai, 3: Sesuai",
            var_key: "",
            url: "#matriks",
            text: "Cek Form 3",
        },
        form4: {
            judul: "Form 4",
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

    $("#btn-tambah-kriteria").click(function () {
        let container = $("#container-kriteria");
        let rowCount = container.find(".row-kriteria").length;

        // Template HTML Baris Baru
        let newRow = `
            <div class="row border mb-3 row-kriteria">
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label class="form-label">Tipe Kriteria</label>
                        <select class="form-select select-template">
                            <option value="" selected disabled>-- Pilih Tipe --</option>
                            <option value="jkem">Penilaian JKEM</option>
                            <option value="sholat">Penilaian Sholat</option>
                            <option value="form1">Penilaian Form 1</option>
                            <option value="form2">Penilaian Form 2</option>
                            <option value="form3">Penilaian Form 3</option>
                            <option value="form4">Penilaian Form 4</option>
                            <option value="custom" class="fw-bold text-primary">-- Custom / Manual --</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="mb-3">
                        <label class="form-label">Judul Kriteria <span class="text-danger">*</span></label>
                        <input type="text" class="form-control input-judul" name="kriteria[${rowCount}][judul]" placeholder="Masukkan Judul" required>
                        
                        <input type="hidden" class="input-var" name="kriteria[${rowCount}][variable_key]">
                        <input type="hidden" class="input-url" name="kriteria[${rowCount}][link_url]">
                        <input type="hidden" class="input-text" name="kriteria[${rowCount}][link_text]">
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="mb-3">
                        <label class="form-label">Keterangan (Skala) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control input-ket" name="kriteria[${rowCount}][keterangan]" placeholder="1:..., 2:..., 3:..." required>
                    </div>
                </div>

                <div class="col-lg-1 d-flex align-items-center justify-content-center">
                    <button type="button" class="btn btn-soft-danger btn-hapus-kriteria mt-3">
                        <i class="bx bx-trash font-size-18"></i>
                    </button>
                </div>
            </div>
        `;
        container.append(newRow);
        updateKriteriaStatus();
    });

    // Tombol Hapus Baris
    $(document).on("click", ".btn-hapus-kriteria", function () {
        $(this).closest(".row-kriteria").remove();
        reIndexKriteria();
        updateKriteriaStatus();
    });

    // Logic Change Dropdown
    $(document).on("change", ".select-template", function () {
        let val = $(this).val();
        let row = $(this).closest(".row-kriteria");

        // Reset semua input
        row.find(".input-judul").val("");
        row.find(".input-ket").val("");
        row.find(".input-var").val("");
        row.find(".input-url").val("");
        row.find(".input-text").val("");

        if (val !== "custom") {
            // Isi otomatis Presets
            let data = KRITERIA_PRESETS[val];
            if (data) {
                row.find(".input-judul").val(data.judul);
                row.find(".input-ket").val(data.ket);

                // Isi otomatis Hidden Input
                row.find(".input-var").val(data.var_key);
                row.find(".input-url").val(data.url);
                row.find(".input-text").val(data.text);
            }
        } else {
            row.find(".input-judul").focus();
        }
    });

    function reIndexKriteria() {
        $("#container-kriteria .row-kriteria").each(function (index) {
            $(this)
                .find(".number")
                .text(index + 1);
            $(this)
                .find("input")
                .each(function () {
                    let oldName = $(this).attr("name");
                    if (oldName) {
                        let newName = oldName.replace(
                            /kriteria\[\d+\]/,
                            `kriteria[${index}]`
                        );
                        $(this).attr("name", newName);
                    }
                });
        });
    }

    function updateKriteriaStatus() {
        let rowCount = $("#container-kriteria .row-kriteria").length;
        if (rowCount <= 1) {
            $(".btn-hapus-kriteria").prop("disabled", true);
        } else {
            $(".btn-hapus-kriteria").prop("disabled", false);
        }
    }

    updateKriteriaStatus();

    function handleError() {
        var status_error = false;

        const fields = [
            { id: "#nama", errorId: "#text-nama", message: "* Wajib diisi!" },
            {
                id: "#thn_ajaran",
                errorId: "#text-thn_ajaran",
                message: "* Wajib diisi!",
            },
            {
                id: "#tanggal_mulai",
                errorId: "#text-tanggal_mulai",
                message: "* Wajib diisi!",
            },
            {
                id: "#tanggal_selesai",
                errorId: "#text-tanggal_selesai",
                message: "* Wajib diisi!",
            },
            {
                id: "#file_excel",
                errorId: "#text-file",
                message: "* Wajib diisi!",
                isFile: true,
            },
        ];

        function checkField(field) {
            const element = $(field.id);
            const errorElement = $(field.errorId);
            let value;

            if (field.isFile) {
                const files = element[0].files;
                if (files.length === 0) {
                    errorElement.text(field.message);
                    status_error = true;
                    return;
                } else {
                    value = files[0];
                    const allowedTypes = [
                        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                    ];
                    if (!allowedTypes.includes(value.type)) {
                        errorElement.text("File harus berupa .xlsx");
                        status_error = true;
                        return;
                    }
                }
            } else {
                value = element.val();
                if (value == 0) {
                    errorElement.text(field.message);
                    status_error = true;
                    return;
                }
            }
            errorElement.text("*");
        }

        // Cek Bidang Proker
        $("#fieldsContainer .row").each(function () {
            var bidang = $(this).find("#bidang").val();
            var tipe_bidang = $(this).find("#tipe_bidang").val();
            var syarat_jkem = $(this).find("#syarat_jkem").val();

            if (bidang === "") {
                $(this).find("#bidang").next(".error-message").remove();
                $(this)
                    .find("#bidang")
                    .after(
                        '<div class="error-message text-danger">* Wajib diisi!</div>'
                    );
                status_error = true;
            } else {
                $(this).find("#bidang").next(".error-message").remove();
            }

            if (syarat_jkem === "") {
                $(this).find("#syarat_jkem").next(".error-message").remove();
                $(this)
                    .find("#syarat_jkem")
                    .after(
                        '<div class="error-message text-danger">* Wajib diisi!</div>'
                    );
                status_error = true;
            } else {
                $(this).find("#syarat_jkem").next(".error-message").remove();
            }
        });

        // Cek Field Utama & Excel
        fields.forEach(checkField);

        // Cek Kriteria Monev
        $("#container-kriteria .row-kriteria").each(function () {
            var judulInput = $(this).find(".input-judul");
            var judulVal = judulInput.val();

            if (judulVal === "") {
                judulInput.next(".error-message").remove();
                judulInput.after(
                    '<div class="error-message text-danger">* Wajib diisi!</div>'
                );
                status_error = true;
            } else {
                judulInput.next(".error-message").remove();
            }
        });

        return status_error;
    }

    const modal_statusContainer = $("#modal-status");
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
    <h5>Yeay, semua data telah disimpan!</h5>
    `;

    var errorModal = `
    <div class="mb-3">
        <i class="bx bx-error-circle display-4 text-danger"></i>
    </div>
    <h5>Harap lengkapi data terlebih dahulu</h5>
    `;

    var progressBar = `
    <div class="progress-container">
    <p>Progress input data</p>
    <div class="progress">
        <div class="progress-bar" role="progressbar" id="progressBar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">0%</div>
    </div>
    <div class="progress-status">
        <p><span>Data inserted: </span><span id="step">0</span>/<span id="total">0</span>(<span id="percent">0%</span>)</p>
    </div>
</div>`;

    var isOnProgress = false;

    // Klik Simpan
    $("#save-change").click(function (e) {
        e.preventDefault();
        if (!isOnProgress) {
            if (handleError()) {
                modal_statusContainer.html(errorModal);
                $("#btn-confirm").addClass("d-none");
            } else {
                modal_statusContainer.html(defaultModal);
                $("#btn-confirm").removeClass("d-none");
            }
        }
    });

    // Worker Excel
    function convertToJson(file) {
        return new Promise((resolve, reject) => {
            const worker = new Worker("/js/worker.js");
            worker.postMessage(file);

            worker.onmessage = function (event) {
                if (event.data.error) {
                    reject(event.data.error);
                } else {
                    resolve(event.data);
                }
            };

            worker.onerror = function (error) {
                reject(error.message);
            };
        });
    }

    // Klik Konfirmasi Simpan
    $("#btn-confirm").click(async function (e) {
        e.preventDefault();
        const file = $("#file_excel").prop("files")[0];
        const file_excel = await convertToJson(file);
        modal_statusContainer.html(progressBar);

        const nama = $("#nama").val();
        const thn_ajaran = $("#thn_ajaran").val();
        const tanggal_mulai = $("#tanggal_mulai").val();
        const tanggal_selesai = $("#tanggal_selesai").val();

        // Collect Bidang Proker
        let fields = [];
        $("#fieldsContainer .row").each(function () {
            let field = {
                bidang: $(this).find("#bidang").val(),
                tipe_bidang: $(this).find("#tipe_bidang").val(),
                syarat_jkem: $(this).find("#syarat_jkem").val(),
            };
            fields.push(field);
        });

        // Collect Kriteria Monev
        let kriteria_monev = [];
        $("#container-kriteria .row-kriteria").each(function () {
            let item = {
                judul: $(this).find("input[name*='[judul]']").val(),
                keterangan: $(this).find("input[name*='[keterangan]']").val(),
                variable_key: $(this)
                    .find("input[name*='[variable_key]']")
                    .val(),
                link_url: $(this).find("input[name*='[link_url]']").val(),
                link_text: $(this).find("input[name*='[link_text]']").val(),
            };
            kriteria_monev.push(item);
        });

        $.ajax({
            url: "/kkn/store",
            type: "POST",
            data: {
                _token: $("meta[name='csrf-token']").attr("content"),
                nama: nama,
                thn_ajaran: thn_ajaran,
                tanggal_mulai: tanggal_mulai,
                tanggal_selesai: tanggal_selesai,
                file_excel: file_excel,
                fields: fields,
                kriteria: kriteria_monev,
            },
            success: function (response) {
                const id_progress = response.id_progress;
                updateProgress(id_progress);
            },
            error: function (xhr, status, error) {
                console.log("Error");
            },
        });
    });

    function updateProgress(id) {
        isOnProgress = true;
        var progressBar = $("#progressBar");
        var stepBar = $("#step");
        var totalBar = $("#total");
        var percent = $("#percent");
        $("#btn-confirm").addClass("d-none");

        $.ajax({
            url: `/queue-progress/${id}`,
            type: "GET",
            success: function (response) {
                const progress = response.progress;
                const step = response.step;
                const total = response.total;
                const status = response.status;

                if (status === "in_progress") {
                    progressBar.css("width", `${progress}%`);
                    progressBar.text(`${progress}%`);
                    if (step > 0) {
                        stepBar.text(step);
                        totalBar.text(total);
                    } else {
                        stepBar.text(0);
                        totalBar.text(0);
                    }
                    percent.text(`${progress}%`);
                    setTimeout(() => updateProgress(id), 300);
                } else if (progress == 100 || status === "completed") {
                    isOnProgress = false;
                    modal_statusContainer.html(successFinish);
                } else if (status === "failed") {
                    isOnProgress = false;
                    var failedModal = `
                        <div class="mb-3">
                            <i class="bx bx-error-circle display-4 text-danger"></i>
                        </div>
                        <h5>Terjadi masalah saat memasukkan data, periksa kembali file excel anda</h5>
                    `;
                    modal_statusContainer.html(failedModal);
                }
            },
            error: function (xhr, status, error) {
                console.log("Error");
            },
        });
    }
});
