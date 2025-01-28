$(document).ready(async function () {
    const id_unit = $("#id_unit").val();
    const id_kkn = $("#id_kkn").val();
    const minDate = $("#tanggal_penerjunan-unit").val();
    const maxDate = $("#tanggal_penarikan-unit").val();
    const startDate = new Date(minDate);
    const endDate = new Date(maxDate);
    const numDays =
        Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 20;

    // Membuat kolom tanggal dan hari
    for (let i = 0; i < numDays; i++) {
        const currentDate = new Date(startDate);
        currentDate.setDate(currentDate.getDate() + i);
        const day = i + 1;
        const date = currentDate.toLocaleDateString("id-ID", {
            day: "2-digit",
            month: "2-digit",
        });

        $(".align-middle.text-center:first").append(
            `<th colspan="2">${day}</th>`
        );
        $(".align-middle.text-center:last").append(
            `<th colspan="2">${date}</th>`
        );
    }

    try {
        // Mengambil data dari server
        const response = await fetch(`/unit/getMatriks/${id_unit}/${id_kkn}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
            },
        });

        // Mengecek jika response status adalah OK
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Mengambil data JSON dari response
        const data = await response.json();

        data.forEach((bidang) => {
            let bidangRow = `<tr><td class="bidang_row" colspan="${
                numDays * 2 + 2
            }">Bidang ${bidang.nama}</td></tr>`;
            $("#program-body").append(bidangRow);

            bidang.proker.forEach((proker) => {
                let prokerRow = `<tr><td class="p-1 text-nowrap">${proker.nama}</td>`;
                prokerRow += `<td class="p-1 text-nowrap"></td>`;

                const planDates = proker.kegiatan.flatMap((kegiatan) =>
                    kegiatan.tanggal_rencana_proker.map((t) => t.tanggal)
                );
                const realisasi = proker.kegiatan.flatMap((kegiatan) =>
                    kegiatan.logbook_kegiatan.map(
                        (logbookKegiatan) =>
                            logbookKegiatan.logbook_harian.tanggal
                    )
                );

                for (let i = 0; i < numDays; i++) {
                    const currentDate = new Date(startDate);
                    currentDate.setDate(currentDate.getDate() + i);
                    const date = currentDate.toISOString().split("T")[0]; // format YYYY-MM-DD

                    // Tandai apakah tanggal ini adalah tanggal rencana
                    const isPlanned = planDates.includes(date);
                    const isRealized = realisasi.includes(date);

                    // Temukan kegiatan yang memiliki tanggal rencana ini
                    const kegiatanPadaTanggalIni = proker.kegiatan.filter(
                        (kegiatan) =>
                            kegiatan.tanggal_rencana_proker.some(
                                (t) => t.tanggal === date
                            )
                    );

                    // Ambil nama kegiatan (jika ada)
                    const kegiatanNama = kegiatanPadaTanggalIni
                        .map((k) => k.nama)
                        .join(", ");

                    // Ambil nama kegiatan realisasi (jika ada)
                    const kegiatanRealisasiNama = proker.kegiatan
                        .flatMap((kegiatan) =>
                            kegiatan.logbook_kegiatan
                                .filter(
                                    (logbookKegiatan) =>
                                        logbookKegiatan.logbook_harian
                                            .tanggal === date
                                )
                                .map((logbookKegiatan) => kegiatan.nama)
                        )
                        .join(", ");

                    // Tambahkan 2 kolom untuk setiap tanggal
                    prokerRow += `<td data-bs-toggle="tooltip" data-bs-placement="top" title="${kegiatanNama}" class="${
                        isPlanned ? "bg-warning" : ""
                    }"></td><td data-bs-toggle="tooltip" data-bs-placement="top" title="${kegiatanRealisasiNama}" class="${
                        isRealized ? "bg-primary" : ""
                    }"></td>`;
                }
                prokerRow += `</tr>`;
                $("#program-body").append(prokerRow);
            });
        });
    } catch (error) {
        // Menangani error jika ada
        alert(error.message);
    }
});
