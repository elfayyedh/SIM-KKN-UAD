$(document).ready(function () {
    $("#downloadPdf").click(function () {
        const id_unit = $("#id_unit").val();
        const id_kkn = $("#id_kkn").val();
        const url = `/unit/generateProkerUnitPdf/${id_unit}/${id_kkn}`;

        $.ajax({
            url: url,
            method: "GET",
            success: function (data) {
                generatePdf(data);
            },
            error: function (error) {
                console.error("Error fetching data:", error);
            },
        });
    });

    function generatePdf(data) {
        const element = document.createElement("div");
        element.innerHTML = generateHtmlContent(data);
        const opt = {
            margin: [10, 10],
            filename: "proker_unit.pdf",
            image: { type: "jpeg", quality: 0.98 },
            html2canvas: { scale: 2 }, // meningkatkan skala untuk resolusi lebih baik
            jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
        };

        html2pdf().set(opt).from(element).save("proker_unit.pdf");
    }

    function generateHtmlContent(data) {
        let content = `
        `;

        data.forEach((bidangData, index) => {
            content += `
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
                                    ${
                                        bidangData.tipe === "unit"
                                            ? '<th rowspan="2" class="p-1 text-center text-nowrap">Peran Mahasiswa</th>'
                                            : '<th rowspan="2" class="p-1 text-center text-nowrap">Penanggung jawab</th>'
                                    }
                                </tr>
                                <tr>
                                    <th class="p-1 text-center text-nowrap">Frekuensi</th>
                                    <th class="p-1 text-center text-nowrap">JKEM</th>
                                    <th class="p-1 text-center text-nowrap">Total JKEM</th>
                                </tr>
                            </thead>
                            <tbody>`;

            bidangData.proker.forEach((program, programIndex) => {
                content += `
                    <tr class="program-row bg-light">
                        <td class="p-1 text-center fw-bold text-nowrap">${
                            programIndex + 1
                        }</td>
                        <td colspan="${
                            bidangData.tipe === "unit" ? 5 : 6
                        }" class="p-1 fw-bold text-nowrap">${program.nama}</td>
                        ${
                            bidangData.tipe === "unit"
                                ? `<td rowspan="${
                                      program.kegiatan.length + 1
                                  }" class="p-1 align-top">
                                      <ul>
                                      ${program.organizer
                                          .map((organizer) => {
                                              return `<li><span class="fw-bold text-nowrap">${
                                                  organizer.nama
                                              }: </span> ${
                                                  organizer.peran == null
                                                      ? "-"
                                                      : organizer.peran
                                              }</li>`;
                                          })
                                          .join("")}
                                      </ul>
                                  </td>`
                                : ""
                        }
                    </tr>`;

                program.kegiatan.forEach((kegiatan, index) => {
                    content += `
                        <tr>
                        ${
                            index === 0
                                ? "<td rowspan='" +
                                  program.kegiatan.length +
                                  "' class='p-1 text-center fw-bold text-nowrap vertical-text'>Kegiatan</td>"
                                : ""
                        }
                            <td class="p-1 text-wrap align-top">${
                                kegiatan.nama
                            }</td>
                            <td class="p-1 text-wrap align-top">${
                                kegiatan.frekuensi
                            }</td>
                            <td class="p-1 text-wrap align-top">${
                                kegiatan.jkem
                            }</td>
                            <td class="p-1 text-wrap align-top">${
                                kegiatan.total_jkem
                            }</td>
                            <td class="p-1 text-wrap align-top">
                                <ul>
                                ${kegiatan.tanggal_rencana_proker
                                    .map((date) => {
                                        return `<li>${moment(
                                            date.tanggal
                                        ).format("dddd, D MMMM YYYY")}</li>`;
                                    })
                                    .join("")}
                                </ul>
                            </td>`;
                    if (bidangData.tipe === "individu") {
                        const namaMahasiswa =
                            kegiatan.mahasiswa.user_role.user.nama;
                        content += `<td class="p-1 align-top"><span class="fw-bold text-nowrap">${namaMahasiswa}</span>`;
                    }
                    content += `</tr>`;
                });
            });

            var total_jkem = 0;
            bidangData.proker.forEach((program) => {
                total_jkem += program.total_jkem;
            });

            content += `</tbody></table></div><div class="card-footer">
                <h5>Total JKEM : ${total_jkem}</h5>`;
            if (bidangData.tipe === "unit") {
                content += `<h5>Minimal JKEM : ${bidangData.syarat_jkem}</h5>`;
            }
            content += `</div></div>`;
        });

        return content;
    }
});
