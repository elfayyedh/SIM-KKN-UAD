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
    const transformedData = jsonData.map((row) => {
        // Normalisasi keys
        const normalizedRow = {};
        Object.keys(row).forEach((key) => {
            if (key) {
                const cleanKey = key
                    .toString()
                    .toLowerCase()
                    .replace(/\s/g, "")
                    .replace(/[^a-z0-9]/g, "");
                normalizedRow[cleanKey] = row[key];
            }
        });

        // Ambil data Unit
        const unitName = normalizedRow["unit"];
        const lokasi = normalizedRow["lokasi"];
        const kabupaten = normalizedRow["kabupaten"];
        const kecamatan = normalizedRow["kecamatan"];
        const tglPenerjunanRaw = normalizedRow["tanggalpenerjunankkn"];

        return {
            nama: unitName,
            lokasi: lokasi,
            kabupaten: kabupaten,
            kecamatan: kecamatan,
            tanggal_penerjunan: formatTanggall(tglPenerjunanRaw),
            anggota: getAnggotaRobust(normalizedRow),
        };
    });

    // Filter baris yang tidak ada nama unitnya
    return transformedData.filter((item) => item.nama);
}

function getAnggotaRobust(normalizedRow) {
    const anggota = [];

    // Loop cari kolom anggota
    for (let i = 1; i <= 15; i++) {
        const nama = normalizedRow[`nama${i}`];
        const nim = normalizedRow[`nim${i}`];
        const email = normalizedRow[`email${i}`];
        const prodi = normalizedRow[`prodi${i}`];
        const jenisKelamin =
            normalizedRow[`l/p${i}`] ||
            normalizedRow[`lp${i}`] ||
            normalizedRow[`jeniskelamin${i}`];
        const nomorHP =
            normalizedRow[`hp${i}`] ||
            normalizedRow[`nohp${i}`] ||
            normalizedRow[`nomorhp${i}`];

        // Validasi
        if (nama && nim) {
            anggota.push({
                nama: String(nama).trim(),
                nim: String(nim).trim(),
                prodi: prodi ? String(prodi).trim() : "-",
                email: email ? String(email).trim() : "-",
                jenisKelamin: jenisKelamin ? String(jenisKelamin).trim() : "L",
                nomorHP: nomorHP ? String(nomorHP).trim() : "-",
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
