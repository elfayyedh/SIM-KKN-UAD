$(document).ready(function () {
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

    let index = 2;

    $("#addKegiatan").on("click", function () {
        // Dapatkan template HTML untuk row kegiatan dengan nomor indeks
        var kegiatanRowTemplate = `
        <div class="row border kegiatan-row pt-3 mb-3">
            <div class="col-12">
                <div class="row">
                    <div class="col">
                        <h6 class="mb-3 text-secondary">#Kegiatan
                            ke-${index}</h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="kegiatan" class="form-label">Nama kegiatan
                                <span class="text-danger" id="error_kegiatan">*</span></label>
                            <input type="text" required class="form-control"
                                name="kegiatan" id="kegiatan" placeholder="Nama kegiatan">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="frekuensi" class="form-label">Frekuensi
                                <span class="text-danger"
                                    id="error_frekuensi">*</span></label>
                            <input type="number" min="1"
                                class="form-control frekuensi" name="frekuensi" required
                                id="frekuensi">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="jkem" class="form-label">JKEM
                                <span class="text-danger" id="error_jkem">*</span></label>
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
                            <label for="totalJKEM" class="form-label">Total
                                JKEM</label>
                            <input type="text" min="1" readonly disabled
                                class="form-control totalJKEM" name="totalJKEM"
                                id="totalJKEM">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="tanggal_kegiatan" class="form-label">Tanggal Kegiatan
                                <span class="text-danger" id="error_tanggal_kegiatan">*</span> <span
                                    class="text-muted small">(Pilih tanggal
                                    tanggal sesuai jumlah
                                    frekuensi)</span></label>
                            <input type="text" required data-flatpickr
                                class="form-control tanggal_kegiatan" name="tanggal_kegiatan"
                                id="tanggal_kegiatan">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <button class="btn btn-soft-danger w-md deleteKegiatan">Hapus</button>
                        </div>
                    </div>
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
    $(document).on("click", ".deleteKegiatan", function () {
        alertify.success("Kegiatan dihapus!");
    });
    toggleKegiatanNextButton();
});
