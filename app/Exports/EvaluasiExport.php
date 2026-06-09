<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use App\Models\KriteriaMonev;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class EvaluasiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    protected $kknId;
    protected $kriteriaList;

    public function __construct($kknId)
    {
        $this->kknId = $kknId;
        $this->kriteriaList = KriteriaMonev::where('id_kkn', $kknId)
            ->orderBy('urutan', 'asc')
            ->get();
    }

    public function collection()
    {
        return Mahasiswa::with([
            'userRole.user',
            'unit.lokasi.kecamatan.kabupaten',
            'evaluasiMahasiswa.evaluasiMahasiswaDetail'
        ])
        ->where('id_kkn', $this->kknId)
        ->get();
    }

    public function headings(): array
    {
        $headers = [
            'Nama Mahasiswa',
            'NIM',
            'Unit',
        ];

        foreach ($this->kriteriaList as $kriteria) {
            $headers[] = $kriteria->judul ?? $kriteria->nama_kriteria;
        }
        $headers[] = 'Nilai Akhir';
        return $headers;
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function map($mhs): array
    {
        $nama = $mhs->userRole->user->nama ?? '';
        $nim  = $mhs->nim ?? '';
        $unit = $mhs->unit->nama ?? '';

        $nilaiMap = [];
        
        if ($mhs->evaluasiMahasiswa) {
            $evaluations = $mhs->evaluasiMahasiswa->sortByDesc('created_at');
            foreach ($evaluations as $eval) {
                if ($eval->evaluasiMahasiswaDetail) {
                    foreach ($eval->evaluasiMahasiswaDetail as $detail) {
                        if (!isset($nilaiMap[$detail->id_kriteria_monev])) {
                            $nilaiMap[$detail->id_kriteria_monev] = $detail->nilai;
                        }
                    }
                }
            }
        }

        $row = [
            $nama,
            $nim,
            $unit,
        ];

        $totalScore = 0;
        $countScore = 0;

        // Loop sesuai urutan header Kriteria
        foreach ($this->kriteriaList as $kriteria) {
            $val = $nilaiMap[$kriteria->id] ?? '';
            $row[] = $val;

            // Hitung untuk rata-rata
            if ($val !== '' && is_numeric($val)) {
                $totalScore += $val;
                $countScore++;
            }
        }
        
        // Nilai Akhir = rata-rata
        $nilaiAkhir = $countScore > 0 ? round($totalScore / $countScore, 2) : '';
        $row[] = $nilaiAkhir;
        
        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        $totalColumns = 3 + $this->kriteriaList->count() + 1;
        
        // Konversi angka ke huruf kolom Excel
        $lastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns);

        // Header Utama
        $sheet->setCellValue('A1', 'REKAP NILAI EVALUASI MAHASISWA');
        $sheet->mergeCells('A1:' . $lastColumnLetter . '1');
        
        // Styling Header
        $sheet->getStyle('A1:' . $lastColumnLetter . '2')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // Auto Size semua kolom
        for ($i = 1; $i <= $totalColumns; $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
    }
}