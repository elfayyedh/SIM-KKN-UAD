importScripts(
    "https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
);
self.importScripts(
    "https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"
);

self.onmessage = function (event) {
    const file = event.data;
    const reader = new FileReader();

    reader.onload = function (event) {
        try {
            const data = new Uint8Array(event.target.result);
            const workbook = XLSX.read(data, { type: "array" });
            const sheetName = workbook.SheetNames[0];
            const sheet = workbook.Sheets[sheetName];
            const jsonData = XLSX.utils.sheet_to_json(sheet, { raw: true });

            const transformedData = transformData(jsonData);

            self.postMessage(transformedData);
        } catch (error) {
            self.postMessage({ error: error.message });
        }
    };

    reader.onerror = function (error) {
        self.postMessage({ error: error.message });
    };

    reader.readAsArrayBuffer(file);
};

function transformData(jsonData) {
    const transformedData = [];
    jsonData.forEach((row) => {
        let existingDPL = transformedData.find((dpl) => dpl.DPL === row.DPL);
        if (existingDPL) {
            let existingUnit = existingDPL.unit.find(
                (unit) => unit.nama === row.UNIT
            );
            if (existingUnit) {
                existingUnit.anggota.push(...getAnggota(row));
            } else {
                existingDPL.unit.push({
                    nama: row.UNIT,
                    lokasi: row.lokasi,
                    kabupaten: row.Kabupaten,
                    kecamatan: row.Kecamatan,
                    tanggal_penerjunan: formatTanggall(
                        row[`TANGGAL PENERJUNAN KKN`]
                    ),
                    anggota: getAnggota(row),
                });
            }
        } else {
            transformedData.push({
                DPL: row.DPL,
                email: row["Email DPL"],
                password: row["NIP"],
                unit: [
                    {
                        nama: row.UNIT,
                        lokasi: row.lokasi,
                        kabupaten: row.Kabupaten,
                        kecamatan: row.Kecamatan,
                        tanggal_penerjunan: formatTanggall(
                            row[`TANGGAL PENERJUNAN KKN`]
                        ),
                        anggota: getAnggota(row),
                    },
                ],
            });
        }
    });
    return transformedData;
}

function getAnggota(row) {
    const anggota = [];
    for (let i = 1; i <= 12; i++) {
        const nama = row[`Nama${i}`];
        const nim = row[`NIM${i}`];
        const email = row[`Email ${i}`];
        const prodi = row[`Prodi${i}`];
        const jenisKelamin = row[`L/P ${i}`];
        const nomorHP = row[`HP ${i}`];

        if (nama && nim && email && prodi && jenisKelamin && nomorHP) {
            anggota.push({
                nama,
                nim: nim.toString(),
                prodi,
                email,
                jenisKelamin,
                nomorHP,
            });
        }
    }
    return anggota;
}

function formatTanggall(value) {
    let formattedDate = moment("1899-12-30")
        .add(value, "days")
        .format("YYYY-MM-DD");
    return formattedDate;
}
