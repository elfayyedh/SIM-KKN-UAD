$(document).ready(function () {
    const container = $("#rekap_kegiatan");
    var id_unit = $("#id_unit").val();
    var id_kkn = $("#id_kkn").val();

    $.ajax({
        url: "/unit/getRekapKegiatan/",
        method: "GET",
        data: {
            id: id_unit,
            id_kkn: id_kkn,
        },
        success: function (data) {
            var row = `<div class="row mb-3">`;

            data.forEach((bidang) => {
                row += `<h5>Bidang ${bidang.nama}</h5>`;
                row += `
                <table class="table w-100 table-bordered table-hover nowrap">
                    <thead class="table-secondary">
                        <tr>
                            <th class="p-1 text-center" rowspan="2">Nama Program</th>
                            <th class="p-1 text-center" rowspan="2">Total JKEM</th>
                            <th class="p-1 text-center" rowspan="2">Frekuensi Kegiatan</th>
                            <th class="p-1 text-center" colspan="4">Pelaksanaan
                                Kegiatan</th>
                            <th class="p-1 text-center" colspan="5">Dana</th>
                        </tr>
                        <tr>
                            <th class="p-1 text-center">Tempat</th>
                            <th class="p-1 text-center">Sasaran</th>
                            <th class="p-1 text-center">Frekuensi</th>
                            <th class="p-1 text-center">JKEM</th>
                            <th class="p-1 text-center">Mhs</th>
                            <th class="p-1 text-center">Mas</th>
                            <th class="p-1 text-center">Pem</th>
                            <th class="p-1 text-center">PT</th>
                            <th class="p-1 text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                `;
                bidang.proker.forEach((proker) => {
                    let danaMhs = 0,
                        danaMas = 0,
                        danaPem = 0,
                        danaPT = 0;
                    let frekuensi = 0;
                    let jkem = 0;
                    let tempatArray = [];
                    let sasaranArray = [];
                    let total_jkem_proker = 0;
                    let totalDana = 0;
                    let frekuensi_kegiatan = 0;
                    proker.kegiatan.forEach((kegiatan) => {
                        total_jkem_proker += kegiatan.total_jkem;
                        frekuensi_kegiatan += kegiatan.frekuensi;
                        kegiatan.logbook_kegiatan.forEach((logbook) => {
                            logbook.dana.forEach((d) => {
                                if (d.sumber === "Mhs") danaMhs += d.jumlah;
                                if (d.sumber === "Mas") danaMas += d.jumlah;
                                if (d.sumber === "Pem") danaPem += d.jumlah;
                                if (d.sumber === "PT") danaPT += d.jumlah;
                            });
                        });

                        totalDana = danaMhs + danaMas + danaPem + danaPT;
                        kegiatan.logbook_kegiatan.forEach((logbook) => {
                            frekuensi += 1;
                            jkem += logbook.total_jkem;
                        });
                    });
                    proker.tempat_dan_sasaran.forEach((ts) => {
                        if (ts.tempat != "-") {
                            tempatArray.push(ts.tempat);
                        }
                        if (ts.sasaran != "-") {
                            sasaranArray.push(ts.sasaran);
                        }
                    });
                    let tempat = tempatArray.join(", ");
                    let sasaran = sasaranArray.join(", ");

                    row += `
                        <tr>
                            <td class="p-1 text-center">${proker.nama}</td>
                            <td class="p-1 text-center">${total_jkem_proker}</td>
                            <td class="p-1 text-center">${frekuensi_kegiatan}</td>
                            <td class="p-1 text-center">${tempat}</td>
                            <td class="p-1 text-center">${sasaran}</td>
                            <td class="p-1 text-center">${frekuensi}</td>
                            <td class="p-1 text-center">${jkem}</td>
                            <td class="p-1 text-center">${formatRupiah(
                                danaMhs
                            )}</td>
                            <td class="p-1 text-center">${formatRupiah(
                                danaMas
                            )}</td>
                            <td class="p-1 text-center">${formatRupiah(
                                danaPem
                            )}</td>
                            <td class="p-1 text-center">${formatRupiah(
                                danaPT
                            )}</td>
                            <td class="p-1 text-center">${formatRupiah(
                                totalDana
                            )}</td>
                        </tr>
                    `;
                });
                row += `</tbody></table>`;
            });

            row += `</div>`;
            container.html(row);
        },
    });

    function formatRupiah(value) {
        if (value != 0) {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
            }).format(value);
        } else {
            return "";
        }
    }
});

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, function (char) {
        return char.toUpperCase();
    });
}
