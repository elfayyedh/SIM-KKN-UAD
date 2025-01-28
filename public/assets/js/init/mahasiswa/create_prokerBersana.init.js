$(document).ready(function () {
    moment.locale("id");
    $(".datatable").DataTable();

    $(".selectize").selectize({
        create: true,
        sortField: "text",
        placeholder: "Pilih atau tambah program baru",
        onChange: function (value) {
            toggleNextButton();
        },
    });

    // Fungsi untuk mengaktifkan/menonaktifkan tombol "Berikutnya"
    function toggleNextButton() {
        const program = $(".selectize").val();
        const bidangProker = $("#bidang_proker").val();
        const tempat = $("#tempat").val();
        const sasaran = $("#sasaran").val();

        if (
            !program ||
            bidangProker === "" ||
            tempat === "" ||
            sasaran === ""
        ) {
            $("#proker-pager").addClass("d-none");
        } else {
            $("#proker-pager").removeClass("d-none");
        }
    }

    // Event listener untuk perubahan pada bidang proker, tempat, dan sasaran
    $(document).on("change", "#bidang_proker, #tempat, #sasaran", function () {
        toggleNextButton();
    });

    // Initial call to toggleNextButton to set the initial state of the button
    toggleNextButton();

    // ! Peran

    let allRoles = [];

    $(".selectPeran").each(function () {
        let $select = $(this);

        // Initialize selectize
        $select.selectize({
            persist: false,
            createOnBlur: true,
            create: true,
            sortField: "text",
            placeholder: "Pilih atau tambah peran baru",
            options: allRoles.map((role) => ({ value: role, text: role })),
            onChange: function (value) {
                if (value && !allRoles.includes(value)) {
                    // Add new role to allRoles array if it doesn't already exist
                    allRoles.push(value);

                    // Update options for all selectPeran elements
                    $(".selectPeran").each(function () {
                        let $currentSelect = $(this);
                        let selectize = $currentSelect[0].selectize;

                        // Ensure selectize instance is available
                        if (selectize) {
                            // Check if the value is already an option
                            if (!selectize.options[value]) {
                                // Add the new role as an option
                                selectize.addOption({
                                    value: value,
                                    text: value,
                                });
                            }

                            // Refresh the selectize control to display the new option
                            selectize.refreshOptions(false);
                        }
                    });
                }
            },
        });
    });

    // Function to calculate total JKEM
    function calculateTotalJKEM(row) {
        var frekuensi = parseInt(row.find(".frekuensi").val()) || 0;
        var jkem = parseInt(row.find(".jkem").val()) || 0;
        var totalJKEM = frekuensi * jkem;
        row.find(".totalJKEM").val(totalJKEM);
    }

    // Calculate totalJKEM for all rows on page load
    $(".kegiatan-row").each(function () {
        calculateTotalJKEM($(this));
    });

    // Event listener for changes in frekuensi and jkem inputs
    $(document).on("change", ".frekuensi, .jkem", function () {
        var row = $(this).closest(".kegiatan-row");
        calculateTotalJKEM(row);
    });

    // Min date
    let minDate = $("#tanggal_penerjunan-unit").val();

    $(document).on("change", ".frekuensi", function () {
        var row = $(this).closest(".kegiatan-row");
        var frekuensi = parseInt(row.find(".frekuensi").val()) || 0;
        dates = row.find(".tanggal_kegiatan").val("");
        dates.flatpickr({
            locale: "id",
            mode: "multiple",
            altInput: true,
            minDate: minDate,
            altFormat: "l j F Y",
            dateFormat: "Y-m-d",
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length > frekuensi) {
                    selectedDates.splice(frekuensi);
                    instance.setDate(selectedDates);
                }
            },
        });
    });

    $(".tanggal_kegiatan").each(function () {
        var input = $(this);
        var maxDates =
            parseInt(input.closest(".kegiatan-row").find(".frekuensi").val()) ||
            0;

        input.flatpickr({
            locale: "id",
            mode: "multiple",
            minDate: minDate,
            altInput: true,
            altFormat: "l j F Y",
            dateFormat: "Y-m-d",
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length > maxDates) {
                    selectedDates.splice(maxDates);
                    instance.setDate(selectedDates);
                }
            },
        });
    });

    function toggleKegiatanNextButton() {
        let allValid = true;

        $(".kegiatan-row").each(function () {
            const kegiatan = $(this).find("input[name='kegiatan']").val();
            const frekuensi = $(this).find("input[name='frekuensi']").val();
            const jkem = $(this).find("select[name='jkem']").val();
            const tanggal = $(this).find(".tanggal_kegiatan").val().split(",");

            if (
                !kegiatan ||
                !frekuensi ||
                !jkem ||
                tanggal.length < 1 ||
                tanggal.length != frekuensi ||
                tanggal == ""
            ) {
                allValid = false;
            }
        });

        if (allValid) {
            $("#kegiatanNextButton").removeClass("d-none");
        } else {
            $("#kegiatanNextButton").addClass("d-none");
        }
    }

    var index = 2; // Mulai dari 1 untuk kegiatan pertama

    $("#addKegiatan").on("click", function () {
        // Dapatkan template HTML untuk row kegiatan dengan nomor indeks
        var kegiatanRowTemplate = `
        <div class="row border kegiatan-row pt-3">
            <div class="col-lg-4">
                <div class="mb-3">
                    <label for="kegiatan"
                        class="form-label label-kegiatan">Kegiatan ke-${index}
                        <span class="text-danger">*</span></label>
                    <input type="text" required
                        class="form-control" name="kegiatan"
                        id="kegiatan"
                        placeholder="Nama kegiatan">
                </div>
            </div>
            <div class="col-lg-3">
                <div class="mb-3">
                    <label for="frekuensi"
                        class="form-label">Frekuensi <span
                            class="text-danger">*</span></label>
                    <input type="number" min="1"
                        class="form-control frekuensi"
                        name="frekuensi" required id="frekuensi">
                </div>
            </div>
            <div class="col-lg-3">
                <div class="mb-3">
                    <label for="jkem" class="form-label">JKEM
                        <span class="text-danger">*</span></label>
                    <select name="jkem" required id="jkem"
                        class="form-select jkem">
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="150">150</option>
                        <option value="200">200</option>
                        <option value="250">250</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="mb-3">
                    <label for="totalJKEM"
                        class="form-label">Total
                        JKEM</label>
                    <input type="text" min="1" readonly
                        class="form-control totalJKEM"
                        name="totalJKEM" id="totalJKEM">
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <label for="tanggal_kegiatan"
                        class="form-label">Tanggal Kegiatan <span
                            class="text-danger">*</span> <span
                            class="text-muted small">(Pilih tanggal tanggal sesuai jumlah frekuensi)</span></label>
                    <input type="text" required data-flatpickr
                        class="form-control tanggal_kegiatan"
                        name="tanggal_kegiatan"
                        id="tanggal_kegiatan">
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <button class="btn btn-soft-danger w-md deleteKegiatan">Hapus</button>
                </div>
            </div>
        </div>
    `;

        // Tambahkan template kegiatan ke dalam DOM
        $("#listKegiatan").append(kegiatanRowTemplate);
        index++; // Tambahkan indeks untuk kegiatan berikutnya
        toggleKegiatanNextButton();
    });

    $(document).on("click", ".deleteKegiatan", function () {
        // Hapus row kegiatan saat tombol "Hapus" ditekan
        $(this).closest(".kegiatan-row").remove();

        // Perbarui indeks pada semua kegiatan yang tersisa
        $(".kegiatan-row").each(function (idx) {
            $(this)
                .find(".label-kegiatan")
                .text(`Kegiatan ke-${idx + 1} *`);
        });

        index--; // Kurangi indeks setelah penghapusan
        toggleKegiatanNextButton();
    });

    $(document).on(
        "input change",
        "input[name='kegiatan'], input[name='frekuensi'], select[name='jkem'], .tanggal_kegiatan",
        function () {
            toggleKegiatanNextButton();
        }
    );

    // Ketika button berikutnya pada peran mahasiswa di klik
    // Ketika button berikutnya pada peran mahasiswa di klik
    $("#peranNextButton").on("click", function () {
        let bidang = $("#bidang_program").text();
        let program = $("#program").val();
        let tempat = $("#tempat").val();
        let sasaran = $("#sasaran").val();
        var totalJkem = 0;
        var totalKegiatan = 0;

        // Hitung total JKEM dan total kegiatan
        $(".kegiatan-row").each(function () {
            totalJkem += parseInt($(this).find(".totalJKEM").val());
            totalKegiatan += 1;
        });

        // Set data review
        $("#data-review_bidang").text(bidang);
        $("#data-review_program").text(program);
        $("#data-review_totalJKEM").text(totalJkem);
        $("#data-review_tempat").text(tempat);
        $("#data-review_sasaran").text(sasaran);

        var tbody = $(".review_daftar-kegiatain").find("table tbody");
        tbody.empty();

        // Buat list peran mahasiswa
        let peran_Mahasiswa = "<ul>";
        $(".peran_Mahasiswa .row .col-lg-4").each(function () {
            let mhs = $(this).find(".anggota_peran").val();
            let prn = $(this).find(".selectPeran").val();
            peran_Mahasiswa += `<li><span class="fw-medium">${mhs}</span> : ${prn}</li>`;
        });
        peran_Mahasiswa += "</ul>";

        var indexKegiatan = 0;
        $(".kegiatan-row").each(function () {
            const kegiatan = $(this).find("input[name='kegiatan']").val();
            const frekuensi = $(this).find("input[name='frekuensi']").val();
            const jkem = $(this).find("select[name='jkem']").val();
            const totaljkem = $(this).find(".totalJKEM").val();
            const tanggal = $(this).find(".tanggal_kegiatan").val();

            const tanggalArray = tanggal.split(",").map((date) => date.trim());

            let tanggalListHTML = "<ul>";
            tanggalArray.forEach((date) => {
                tanggalListHTML += `<li>${moment(date).format(
                    "dddd, D MMMM YYYY"
                )}</li>`;
            });
            tanggalListHTML += "</ul>";

            // Tambahkan baris ke tabel
            tbody.append(`
            <tr>
                <td>${kegiatan}</td>
                <td>${frekuensi}</td>
                <td>${jkem}</td>
                <td>${totaljkem}</td>
                <td>${tanggalListHTML}</td>
                ${
                    indexKegiatan == 0
                        ? `<td rowspan="${totalKegiatan}">${peran_Mahasiswa}</td>`
                        : ""
                }
            </tr>
        `);
            indexKegiatan++;
        });
    });

    // Trigger change event untuk mengatur state awal
    $(".frekuensi").trigger("change");

    var loader = `
    <div class="spinner-border text-primary m-1" role="status">
        <span class="sr-only">Loading...</span>
    </div>`;

    // Save data
    $("#save-change").click(function () {
        $(".btnConfirm").empty();
        $(".btnConfirm").append(loader);

        // Collect program data
        let program = $("#program").val();
        let id_bidang = $("#bidang_program").val();
        let tempat = $("#tempat").val();
        let sasaran = $("#sasaran").val();

        // Collect organizer data
        let organizer = [];
        $(".anggota_peran").each(function () {
            let peran = $(this).siblings("select").val();
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

        // Send data via AJAX
        $.ajax({
            url: "/unit/proker/store", // Replace with your endpoint URL
            type: "POST",
            data: {
                _token: $("meta[name='csrf-token']").attr("content"),
                program: program,
                id_bidang: id_bidang,
                tempat: tempat,
                sasaran: sasaran,
                organizer: organizer,
                kegiatan: kegiatan,
                id_unit: 7,
                id_kkn: 1,
                id_mahasiswa: 1,
            },
            success: function (response) {
                // Reset the form values
                resetModal();
                // Close the modal
                $("#myModal").modal("hide");
                // Reset the wizard
                $("#smartwizard").smartWizard("reset"); // Assuming you use SmartWizard
            },
            error: function (error) {
                // Reset the form values
                resetModal();
                // Optionally handle the error
                // Reset the wizard
                $("#smartwizard").smartWizard("reset"); // Assuming you use SmartWizard
            },
        });
    });

    function resetModal() {
        // Reset input values
        $("#program").val("");
        $("#bidang_program").prop("selectedIndex", 0);
        $("#tempat").val("");
        $("#sasaran").val("");

        // Reset organizer data
        $(".anggota_peran").each(function () {
            $(this).val("");
            $(this).siblings("select").prop("selectedIndex", 0);
        });

        // Reset kegiatan data
        $("#listKegiatan .kegiatan-row").each(function () {
            $(this).find('input[name="kegiatan"]').val("");
            $(this).find('input[name="frekuensi"]').val("");
            $(this).find('select[name="jkem"]').prop("selectedIndex", 0);
            $(this).find('input[name="totalJKEM"]').val("");
            $(this).find(".tanggal_kegiatan").val("");
        });

        // Optionally remove dynamic rows
        $("#listKegiatan .kegiatan-row").not(":first").remove();
    }
});
