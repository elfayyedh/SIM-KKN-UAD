<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EvaluasiExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $kknId;
    protected $row = 2; // Start from row 2 (after heading)

    public function __construct($kknId)
    {
        $this->kknId = $kknId;
    }

    public function collection()
    {
        return Mahasiswa::with([
            'userRole.user',
            'unit.lokasi.kecamatan.kabupaten',
            'evaluasiMahasiswa'
        ])
        ->where('id_kkn', $this->kknId)
        ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Mahasiswa',
            'NIM',
            'Unit',
            'Lokasi',
            'Capaian JKEM',
            'Sholat',
            'Jumlah Nilai',
            'Status'
        ];
    }

    public function map($mahasiswa): array
    {
        $nama = $mahasiswa->userRole->user->nama ?? '';
        $nim = $mahasiswa->nim ?? '';
        $unit = $mahasiswa->unit->nama ?? '';

        // Safe-check untuk lokasi dengan nested relationships
        $lokasi = '-';
        if ($mahasiswa->unit && $mahasiswa->unit->lokasi && $mahasiswa->unit->lokasi->kecamatan && $mahasiswa->unit->lokasi->kecamatan->kabupaten) {
            $lokasi = $mahasiswa->unit->lokasi->kecamatan->kabupaten->nama;
        }

        // Hitung sum dari eval fields dari semua evaluasi mahasiswa
        $jkem = 0;
        $sholat = 0;

        if ($mahasiswa->evaluasiMahasiswa && $mahasiswa->evaluasiMahasiswa->count() > 0) {
            foreach ($mahasiswa->evaluasiMahasiswa as $evaluasi) {
                $jkem += $evaluasi->eval_jkem ?? 0;
                $sholat += $evaluasi->eval_sholat ?? 0;
            }
        }

        // Formula untuk jumlah nilai agar bisa diisi langsung di Excel
        $jumlahNilaiFormula = '=SUM(E' . $this->row . ':F' . $this->row . ')';
        $statusFormula = '=IF(G' . $this->row . '>=70,"Lulus","Tidak Lulus")';
        $this->row++;

        return [
            $nama,
            $nim,
            $unit,
            $lokasi,
            $jkem ?: '',
            $sholat ?: '',
            $jumlahNilaiFormula,
            $statusFormula
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }
}
