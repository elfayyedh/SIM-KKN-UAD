$(document).ready(function () {
    // Inisialisasi selectize
    const $program = $("#program").selectize({
        create: true,
        sortField: "text",
        placeholder: "Pilih atau tambah program baru",
    });

    const selectizeProgram = $program[0].selectize;

    function fetchDataProker(id_unit, id_bidang) {
        $.ajax({
            type: "GET",
            url:
                "/proker/unit/getDataProkerByIdBidang/" +
                id_unit +
                "/" +
                id_bidang,
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    selectizeProgram.clearOptions(); // Kosongkan opsi sebelumnya
                    const formattedData = response.data.map(function (item) {
                        return { value: item.id, text: item.nama };
                    });

                    selectizeProgram.addOption(formattedData); // Tambahkan opsi baru
                    selectizeProgram.refreshOptions(); // Refresh opsi
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching data:", error);
                selectizeProgram.clearOptions(); // Kosongkan opsi jika terjadi kesalahan
            },
        });
    }

    // Ambil data saat halaman dimuat
    const id_unit = $("#id_unit").val();
    let id_bidang = $("#bidang_proker").val();
    fetchDataProker(id_unit, id_bidang);

    // Update data saat #bidang_proker berubah
    $(document).on("change", "#bidang_proker", function () {
        id_bidang = $(this).val();
        fetchDataProker(id_unit, id_bidang);
    });
});
