$(document).ready(function () {
    const table_container = $("#proker-container");
    $.ajax({
        url: "/proker/getProkerUnit",
        type: "GET",
        dataType: "json",
        success: function (data) {
            let prokerHtml = "";
            data.forEach((bidangData, index) => {
                prokerHtml += `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">${bidangData.nama}</h5>
                        </div>
                        <div class="card-body" style="overflow-x: auto;">
                            <table class="table-proker">
                                <thead class="align-middle">
                                    <tr>
                                        <th rowspan="2" class="p-1 text-center text-nowrap">No</th>
                                        <th rowspan="2" class="p-1 text-center text-nowrap">Program & Kegiatan</th>
                                        <th colspan="3" class="p-1 text-center text-nowrap">Ekuivalensi JKEM (Menit)</th>
                                        <th rowspan="2" class="p-1 text-center text-nowrap">Tanggal Rencana</th>
                                        <th rowspan="2" class="p-1 text-center text-nowrap">Peran Mahasiswa</th>
                                        <th rowspan="2" class="p-1 text-center text-nowrap">Aksi</th>
                                    </tr>
                                    <tr>
                                        <th class="p-1 text-center text-nowrap">Frekuensi</th>
                                        <th class="p-1 text-center text-nowrap">JKEM</th>
                                        <th class="p-1 text-center text-nowrap">Total JKEM</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                bidangData.proker.forEach((program, programIndex) => {
                    let tempat = program.tempat_dan_sasaran[0].tempat;
                    let sasaran = program.tempat_dan_sasaran[0].sasaran;
                    let idTempat = program.tempat_dan_sasaran[0].id;
                    prokerHtml += `
                        <tr class="program-row bg-light">
                            <td class="p-1 text-center fw-bold text-nowrap">${
                                programIndex + 1
                            }</td>
                            <td colspan="5" class="p-1 fw-bold text-nowrap">${
                                program.nama
                            }</td>
                            <td rowspan="${
                                program.kegiatan.length + 1
                            }" class="p-1  align-top">
                                <ul>`;

                    program.organizer.forEach((organizer) => {
                        prokerHtml += `<li><span class="fw-bold text-nowrap">${
                            organizer.nama
                        }: </span> ${
                            organizer.peran == null ? "-" : organizer.peran
                        }</li>`;
                    });

                    prokerHtml += `</ul></td>
                            <td class="p-2 text-nowrap align-top" rowspan="${
                                program.kegiatan.length + 1
                            }" >
                                <a href="/proker/unit/detail/${
                                    program.id
                                }" class="btn btn-primary btn-xl"><i class="mdi mdi-eye"></i></a>
                                <button class="btn btn-warning btn-xl edit-proker" data-bs-toggle="modal" data-id="${
                                    program.id
                                }" data.bidang="${
                        bidangData.id
                    }" data-tempat="${tempat}" data-id-tempat="${idTempat}" data-sasaran="${sasaran}" data-nama="${
                        program.nama
                    }" data-bs-target="#editProkerModal"><i class="bx bx-edit"></i></button>
                                <button class="btn btn-danger btn-xl" id="delete-proker-unit" data-bs-toggle="modal" data-id="${
                                    program.id
                                }" data-bs-target="#modal-delete-proker-unit"><i class="bx bx-trash"></i></button>
                            </td>
                        </tr>`;

                    program.kegiatan.forEach((kegiatan, index) => {
                        prokerHtml += `
                            <tr>
                            ${
                                index === 0
                                    ? "<td rowspan='" +
                                      program.kegiatan.length +
                                      "' class='p-1 text-center fw-bold text-nowrap vertical-text'>Kegiatan</td>"
                                    : ""
                            }
                                <td class="p-1 align-top text-nowrap">${
                                    kegiatan.nama
                                }</td>
                                <td class="p-1 align-top text-nowrap">${
                                    kegiatan.frekuensi
                                }</td>
                                <td class="p-1 align-top text-nowrap">${
                                    kegiatan.jkem
                                }</td>
                                <td class="p-1 align-top text-nowrap">${
                                    kegiatan.total_jkem
                                }</td>
                                <td class="p-1 align-top text-nowrap">
                                    <ul>
                                    ${kegiatan.tanggal_rencana_proker
                                        .map((date) => {
                                            return `<li>${moment(
                                                date.tanggal
                                            ).format(
                                                "dddd, D MMMM YYYY"
                                            )}</li>`;
                                        })
                                        .join("")}
                                    </ul>
                                </td>
                            </tr>`;
                    });
                });
                var total_jkem = 0;
                bidangData.proker.forEach((program) => {
                    total_jkem += program.total_jkem;
                });

                prokerHtml += `</tbody></table></div><div class="card-footer">
                <h5>Total JKEM : ${total_jkem}</h5>
                <h5>Minimal JKEM : ${bidangData.syarat_jkem}</h5></div></div>`;
            });

            table_container.empty();
            table_container.html(prokerHtml);
        },
        error: function () {
            alert("Failed to load data.");
        },
    });

    $(document).on("click", "#delete-proker-unit", function () {
        let id = $(this).data("id");
        $(".modal_delete").addClass("d-none");
        $.ajax({
            type: "GET",
            url: "/proker/checkProkerStatus",
            data: {
                id: id,
            },
            success: function (response) {
                if (response.status === "error") {
                    $("#modal-status").html(response.message);
                } else {
                    $("#modal-status").html(`
                        <div class="text-center" id="modal-status">
                        <p>Anda yakin ingin menghapus proker <span class="text-danger" id="nama_program"></span>?</p>
                        <p>Beberapa data kegiatan, tanggal rencana, peran mahasiswa, tempat & sasaran juga akan dihapus!</p>
                    </div>`);
                    $(".modal_delete").removeClass("d-none");
                }
            },
        });
    });
});
