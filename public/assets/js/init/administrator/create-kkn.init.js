// Datepicker

$(document).ready(function () {
    flatpickr(".datepicker-basic", {
        //Buat tanggal format indonesia
        locale: "id",
        altInput: true,
        altFormat: "l, j F Y",
        dateFormat: "Y-m-d",
    });

    //? Start of Bidang

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

    // ? Simpan data

    function handleError() {
        var status_error = false;

        // List of fields to check
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
                isFile: true, // Add a flag to indicate this is a file input
            },
        ];

        // Function to check each field
        function checkField(field) {
            const element = $(field.id);
            const errorElement = $(field.errorId);
            let value;

            if (field.isFile) {
                const files = element[0].files;
                if (files.length === 0) {
                    // Tidak ada file yang dipilih
                    errorElement.text(field.message);
                    status_error = true;
                    return;
                } else {
                    value = files[0]; // Ambil file pertama yang diunggah
                    const allowedTypes = [
                        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                    ]; // MIME type untuk .xlsx
                    if (!allowedTypes.includes(value.type)) {
                        // File bukan xlsx
                        errorElement.text("File harus berupa .xlsx");
                        status_error = true;
                        return;
                    }
                }
            } else {
                value = element.val(); // Ambil nilai dari input biasa
                if (value == 0) {
                    // Nilai kosong
                    errorElement.text(field.message);
                    status_error = true;
                    return;
                }
            }

            errorElement.text("*"); // Kosongkan pesan error jika ada
        }

        $("#fieldsContainer .row").each(function () {
            var bidang = $(this).find("#bidang").val();
            var tipe_bidang = $(this).find("#tipe_bidang").val();
            var syarat_jkem = $(this).find("#syarat_jkem").val();

            if (bidang === "") {
                $(this).find("#bidang").next(".error-message").remove(); // Remove previous error message if any
                $(this)
                    .find("#bidang")
                    .after(
                        '<div class="error-message text-danger">* Wajib diisi!</div>'
                    );
                status_error = true;
            } else {
                $(this).find("#bidang").next(".error-message").remove(); // Clear error message if any
            }

            if (syarat_jkem === "") {
                $(this).find("#syarat_jkem").next(".error-message").remove(); // Remove previous error message if any
                $(this)
                    .find("#syarat_jkem")
                    .after(
                        '<div class="error-message text-danger">* Wajib diisi!</div>'
                    );
                status_error = true;
            } else {
                $(this).find("#syarat_jkem").next(".error-message").remove(); // Clear error message if any
            }
        });

        // Iterate over all fields and check them
        fields.forEach(checkField);
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

    // Klik dan cek error
    $("#save-change").click(function (e) {
        e.preventDefault();
        const file = $("#file_excel").prop("files")[0];
        const file_excel = convertToJson(file);

        if (!isOnProgress) {
            if (handleError()) {
                modal_statusContainer.html(errorModal);
                $("#btn-confirm").addClass("d-none");
            } else {
                modal_statusContainer.html(defaultModal);
                $("#btn-confirm").removeClass("d-none"); // Ensure the confirm button is shown if no error
            }
        }
    });

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

    $("#btn-confirm").click(async function (e) {
        e.preventDefault();
        const file = $("#file_excel").prop("files")[0];
        const file_excel = await convertToJson(file);
        modal_statusContainer.html(progressBar);

        // Ambil semua value form
        const nama = $("#nama").val();
        const thn_ajaran = $("#thn_ajaran").val();
        const tanggal_mulai = $("#tanggal_mulai").val();
        const tanggal_selesai = $("#tanggal_selesai").val();

        // Ambil nilai dari setiap bidang
        let fields = [];
        $("#fieldsContainer .row").each(function () {
            let field = {
                bidang: $(this).find("#bidang").val(),
                tipe_bidang: $(this).find("#tipe_bidang").val(),
                syarat_jkem: $(this).find("#syarat_jkem").val(),
            };
            fields.push(field);
        });

        // Kirim data ke endpoint dengan AJAX
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
            },
            success: function (response) {
                const id_progress = response.id_progress; // Pastikan respons dari server sesuai dengan key id_progress
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
        // Ambil progress dengan ajax
        $.ajax({
            url: `/queue-progress/${id}`, // Pastikan template literal Anda digunakan dengan benar
            type: "GET",
            success: function (response) {
                const progress = response.progress;
                const step = response.step;
                const total = response.total;
                const status = response.status;
                const message = response.message;

                // Sesuaikan logika berdasarkan respons progress yang diterima
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
                    //Jika sudah 100 maka setTimeout berhen
                    setTimeout(() => updateProgress(id), 300); // Cek lagi setelah 0.3 detik
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

    //? End of bidang
});
