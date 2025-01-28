$(document).ready(async function () {
    let id_unit = $("#id_unit").val();
    let id_kkn = $("#id_kkn").val();
    const table_container = $("#program_kerja_unit");
    table_container.html("Loading proker...");

    try {
        const response = await fetch(`/unit/getProker/${id_unit}/${id_kkn}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
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
                                    <th rowspan="2" class="p-1 text-center text-nowrap">Tanggal Realisasi</th>
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
                prokerHtml += `
                    <tr class="program-row bg-light">
                        <td class="p-1 text-center fw-bold text-nowrap">${
                            programIndex + 1
                        }</td>
                        <td colspan="${
                            bidangData.tipe === "unit" ? 6 : 7
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
                                        ).format("dddd, D MMMM YYYY")}</li>`;
                                    })
                                    .join("")}
                                </ul>
                            </td>
                            <td class="p-1 align-top text-nowrap">
                                <ul>
                                ${kegiatan.logbook_kegiatan
                                    .map((logbook) => {
                                        return `<li>${moment(
                                            logbook.logbook_harian.tanggal
                                        ).format("dddd, D MMMM YYYY")}</li>`;
                                    })
                                    .join("")}
                                </ul>
                            </td>`;
                    if (bidangData.tipe === "individu") {
                        const namaMahasiswa =
                            kegiatan.mahasiswa.user_role.user.nama;
                        prokerHtml += `<td class="p-1 align-top"><span class="fw-bold text-nowrap">${namaMahasiswa}</span>`;
                    }
                    prokerHtml += `</tr>`;
                });
            });

            var total_jkem = 0;
            bidangData.proker.forEach((program) => {
                total_jkem += program.total_jkem;
            });

            prokerHtml += `</tbody></table></div><div class="card-footer">
            <h5>Total JKEM : ${total_jkem}</h5>
            `;
            if (bidangData.tipe === "unit") {
                prokerHtml += `<h5>Minimal JKEM : ${bidangData.syarat_jkem}</h5>`;
            }
            prokerHtml += `</div></div>`;
        });

        table_container.html(prokerHtml);
    } catch (error) {
        console.error("Error fetching data:", error);
        table_container.html("Failed to load data.");
    }
});
