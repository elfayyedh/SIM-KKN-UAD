var donutChart;
$(document).ready(function () {
    $(document).on("change", "#periode", function () {
        var periode = $(this).val();
        chageCardValue(periode);
        setDonutChart(periode);
        fetchUnitData(periode);
        getProdiData(periode);
    });

    chageCardValue($("#periode").val());
    setDonutChart($("#periode").val());
    getProdiData($("#periode").val());
    fetchUnitData($("#periode").val());

    function chageCardValue(periode) {
        const mahasiswa = $(".total-mahasiswa");
        const unit = $(".total-unit");
        const dpl = $(".total-dpl");
        const tim_monev = $(".total-tim_monev");
        const placeholder = `<div class="placeholder-glow"><span class="placeholder col-12"></span></div>`;
        mahasiswa.html(placeholder);
        unit.html(placeholder);
        dpl.html(placeholder);
        tim_monev.html(placeholder);
        $.ajax({
            type: "GET",
            url: "/card-value",
            data: {
                periode: periode,
            },
            success: function (response) {
                if (response.status === "success") {
                    mahasiswa.empty();
                    mahasiswa.html(response.total_mahasiswa);
                    unit.empty();
                    unit.html(response.total_unit);
                    dpl.empty();
                    dpl.html(response.total_dpl);
                    tim_monev.empty();
                    tim_monev.html(response.total_tim_monev);
                }
            },
            error: function () {},
        });
    }

    setChartValue();
});

// Variabel global untuk menyimpan referensi chart

function setDonutChart(periode) {
    $("#donut_chart").empty();
    // Mengambil data dari backend menggunakan AJAX
    $.ajax({
        url: "/get-donut-chart", // Sesuaikan URL dengan endpoint backend Anda
        type: "GET",
        data: { periode: periode },
        success: function (response) {
            // Memproses response dari backend
            var labels = [];
            var series = [];

            response.forEach(function (item) {
                labels.push(item.nama);
                series.push(item.proker_count);
            });

            // Warna donut chart
            var donutColors = getChartColorsArray("#donut_chart");

            // Membuat chart
            var options = {
                chart: {
                    height: 320,
                    type: "donut",
                },
                series: series, // data dinamis untuk series
                labels: labels, // data dinamis untuk labels
                colors: donutColors,
                legend: {
                    show: true,
                    position: "bottom",
                    horizontalAlign: "center",
                    verticalAlign: "middle",
                    floating: false,
                    fontSize: "14px",
                    offsetX: 0,
                },
                responsive: [
                    {
                        breakpoint: 600,
                        options: {
                            chart: {
                                height: 240,
                            },
                            legend: {
                                show: false,
                            },
                        },
                    },
                ],
            };

            // Hapus chart lama jika ada sebelum merender chart baru
            if (donutChart) {
                donutChart.destroy();
            }
            if (periode !== "semua") {
                // Merender chart baru dengan data baru
                donutChart = new ApexCharts(
                    document.querySelector("#donut_chart"),
                    options
                );
                donutChart.render();
            } else {
                $("#donut_chart").html(
                    `<center>Tidak menampilkan data</center>`
                );
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching data:", error);
        },
    });
}

function setChartValue() {
    $.ajax({
        url: "/chart-data",
        method: "GET",
        success: function (response) {
            if (typeof response === "object" && response !== null) {
                var years = Object.keys(response); // Mengambil tahun dari objek
                var mahasiswaData = [];
                var dplData = [];
                var timMonevData = [];

                years.forEach(function (year) {
                    var data = response[year];
                    mahasiswaData.push(data.total_mahasiswa);
                    dplData.push(data.total_dpl);
                    timMonevData.push(data.total_tim_monev);
                });

                var columnColors = getChartColorsArray("#column_chart");

                var options = {
                    chart: {
                        height: 370,
                        type: "bar",
                        toolbar: {
                            show: false,
                        },
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: "45%",
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ["transparent"],
                    },
                    series: [
                        {
                            name: "Mahasiswa",
                            data: mahasiswaData,
                        },
                        {
                            name: "DPL",
                            data: dplData,
                        },
                        {
                            name: "Tim Monev",
                            data: timMonevData,
                        },
                    ],
                    colors: columnColors,
                    xaxis: {
                        categories: years,
                    },
                    yaxis: {
                        title: {
                            text: "Total",
                            style: {
                                fontWeight: "500",
                            },
                        },
                    },
                    grid: {
                        borderColor: "#f1f1f1",
                    },
                    fill: {
                        opacity: 1,
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val;
                            },
                        },
                    },
                };

                var chart = new ApexCharts(
                    document.querySelector("#column_chart"),
                    options
                );

                chart.render();
            } else {
                console.error(
                    "Data yang diterima tidak sesuai format yang diharapkan"
                );
            }
        },
        error: function () {
            console.log("Failed to fetch data");
        },
    });
}

function getChartColorsArray(chartId) {
    var colors = $(chartId).attr("data-colors");
    var colors = JSON.parse(colors);
    return colors.map(function (value) {
        var newValue = value.replace(" ", "");
        if (newValue.indexOf("--") != -1) {
            var color = getComputedStyle(
                document.documentElement
            ).getPropertyValue(newValue);
            if (color) return color;
        } else {
            return newValue;
        }
    });
}

function getProdiData(id_kkn) {
    $.ajax({
        url: "/get-prodi-data", // Endpoint untuk mendapatkan data
        type: "GET",
        data: {
            periode: id_kkn,
        },
        success: function (response) {
            var tbody = $("#data-prodi");
            tbody.empty(); // Kosongkan isi tabel sebelumnya

            // Looping melalui data yang diterima dari backend
            response.forEach(function (item) {
                var row = "<tr>";
                row += "<td>" + item.nama_prodi + "</td>";
                row += "<td>" + item.total_unit + "</td>";
                row += "<td>" + item.total_mahasiswa + "</td>";
                row += "</tr>";
                tbody.append(row); // Tambahkan data ke tabel
            });
        },
        error: function (xhr, status, error) {
            console.error("Error fetching data:", error);
        },
    });
}

function fetchUnitData(periode) {
    $.ajax({
        type: "GET",
        url: "/get-unit-data",
        data: { periode: periode },
        success: function (response) {
            var tbody = $("#data-lokasi");
            tbody.empty(); // Hapus isi tabel sebelumnya

            response.forEach(function (item) {
                var row = `
                    <tr>
                        <td>${item.total_unit}</td>
                        <td>${item.kecamatan}</td>
                        <td>${item.kabupaten}</td>
                    </tr>
                `;
                tbody.append(row);
            });
        },
        error: function () {
            console.error("Error fetching data");
        },
    });
}
