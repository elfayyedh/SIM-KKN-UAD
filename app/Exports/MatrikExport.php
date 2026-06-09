<?php

namespace App\Exports;

use App\Models\BidangProker;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;

class MatrikExport implements FromArray, WithStyles, WithColumnWidths
{
    protected $idUnit;
    protected $idKkn;

    public function __construct($idUnit, $idKkn)
    {
        $this->idUnit = $idUnit;
        $this->idKkn = $idKkn;
    }

    public function array(): array
    {
        // Ambil data unit untuk mendapatkan tanggal penerjunan dan penarikan
        $unit = Unit::with('kkn')->find($this->idUnit);
        $startDate = Carbon::parse($unit->tanggal_penerjunan);
        $endDate = $unit->tanggal_penarikan 
            ? Carbon::parse($unit->tanggal_penarikan) 
            : Carbon::parse($unit->kkn->tanggal_selesai);
        
        // Tambah buffer 20 hari
        $endDate->addDays(20);
        $numDays = $startDate->diffInDays($endDate) + 1;

        // Ambil data dari database
        $bidangProkers = BidangProker::with([
            'proker' => function ($query) {
                $query->where('id_unit', $this->idUnit)
                      ->with([
                          'kegiatan.tanggalRencanaProker',
                          'kegiatan.logbookKegiatan.logbookHarian'
                      ]);
            }
        ])
        ->where('id_kkn', $this->idKkn)
        ->get();

        $data = [];
        
        // Header Row 1: PROGRAM, Hari, dan setiap hari memiliki 2 kolom (R dan P)
        $headerRow1 = ['PROGRAM', 'Hari'];
        for ($i = 0; $i < $numDays; $i++) {
            $day = $i + 1;
            $headerRow1[] = $day; // Hari
            $headerRow1[] = '';   // Merge dengan kolom sebelah
        }
        $data[] = $headerRow1;

        // Header Row 2: (kosong), Tanggal/Bulan, dan setiap tanggal di-merge
        $headerRow2 = ['', 'Tanggal/Bulan'];
        for ($i = 0; $i < $numDays; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $date = $currentDate->format('d/m');
            $headerRow2[] = $date; // Tanggal
            $headerRow2[] = '';    // Merge dengan kolom sebelah
        }
        $data[] = $headerRow2;

        // Data rows per bidang dan proker
        foreach ($bidangProkers as $bidang) {
            // Baris Bidang - tampilkan semua bidang
            $bidangRow = ['Bidang ' . $bidang->nama];
            for ($i = 0; $i < ($numDays * 2) + 1; $i++) {
                $bidangRow[] = '';
            }
            $data[] = $bidangRow;

            // Baris per Proker jika ada
            if ($bidang->proker && $bidang->proker->count() > 0) {
                foreach ($bidang->proker as $proker) {
                    $prokerRow = [$proker->nama, ''];

                    // Kumpulkan tanggal rencana dan realisasi
                    $planDates = [];
                    $realisasiDates = [];
                    
                    if ($proker->kegiatan) {
                        foreach ($proker->kegiatan as $kegiatan) {
                            if ($kegiatan->tanggalRencanaProker) {
                                foreach ($kegiatan->tanggalRencanaProker as $tanggal) {
                                    $planDates[] = $tanggal->tanggal;
                                }
                            }
                            if ($kegiatan->logbookKegiatan) {
                                foreach ($kegiatan->logbookKegiatan as $logbook) {
                                    if ($logbook->logbookHarian) {
                                        $realisasiDates[] = $logbook->logbookHarian->tanggal;
                                    }
                                }
                            }
                        }
                    }

                    // Isi kolom untuk setiap hari (2 kolom per hari: R dan P)
                    for ($i = 0; $i < $numDays; $i++) {
                        $currentDate = $startDate->copy()->addDays($i)->format('Y-m-d');
                        $isPlanned = in_array($currentDate, $planDates);
                        $isRealized = in_array($currentDate, $realisasiDates);

                        // Kolom R (Rencana) - hanya tandai dengan value untuk warna
                        $prokerRow[] = $isPlanned ? 'PLANNED' : '';
                        
                        // Kolom P (Pelaksanaan) - hanya tandai dengan value untuk warna
                        $prokerRow[] = $isRealized ? 'REALIZED' : '';
                    }
                    $data[] = $prokerRow;
                }
            }
        }

        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40, // Kolom PROGRAM lebih lebar
            'B' => 15, // Kolom Hari/Tanggal
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        // Style untuk semua sel - border
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Merge cell untuk header row 1 kolom PROGRAM (A1:A2)
        $sheet->mergeCells('A1:A2');
        
        // Merge cell untuk header row 1 kolom Hari (B1:B2)
        $sheet->mergeCells('B1:B2');
        
        // Merge cells untuk setiap hari di row 1 dan row 2 (gabungkan R dan P)
        $colIndex = 3; // Mulai dari kolom C
        while ($colIndex <= $highestColumnIndex) {
            $col1 = Coordinate::stringFromColumnIndex($colIndex);
            $col2 = Coordinate::stringFromColumnIndex($colIndex + 1);
            // Merge hari (row 1)
            $sheet->mergeCells($col1 . '1:' . $col2 . '1');
            // Merge tanggal (row 2)
            $sheet->mergeCells($col1 . '2:' . $col2 . '2');
            $colIndex += 2;
        }
        
        // Style untuk header rows (row 1 dan 2)
        $sheet->getStyle('A1:' . $highestColumn . '2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9D9D9'],
            ],
        ]);

        // Warna KUNING untuk kolom R (Rencana) - kolom ganjil mulai dari C
        $colIndex = 3;
        while ($colIndex <= $highestColumnIndex) {
            $col = Coordinate::stringFromColumnIndex($colIndex);
            
            // Warna kuning untuk semua cell yang ada rencana
            for ($row = 3; $row <= $highestRow; $row++) {
                $cellValue = $sheet->getCell($col . $row)->getValue();
                if ($cellValue === 'PLANNED') {
                    // Hapus tulisan, hanya warna
                    $sheet->setCellValue($col . $row, '');
                    $sheet->getStyle($col . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFFFFF00'], // Kuning
                        ],
                    ]);
                }
            }
            
            $colIndex += 2; // Loncat 2 kolom (skip P)
        }

        // Warna BIRU untuk kolom P (Pelaksanaan) - kolom genap mulai dari D
        $colIndex = 4;
        while ($colIndex <= $highestColumnIndex) {
            $col = Coordinate::stringFromColumnIndex($colIndex);
            
            // Warna biru untuk semua cell yang ada pelaksanaan
            for ($row = 3; $row <= $highestRow; $row++) {
                $cellValue = $sheet->getCell($col . $row)->getValue();
                if ($cellValue === 'REALIZED') {
                    // Hapus tulisan, hanya warna
                    $sheet->setCellValue($col . $row, '');
                    $sheet->getStyle($col . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FF0000FF'], // Biru
                        ],
                    ]);
                }
            }
            
            $colIndex += 2; // Loncat 2 kolom (skip R)
        }

        // Style untuk baris bidang
        for ($row = 3; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('A' . $row)->getValue();
            if (strpos($cellValue, 'Bidang') === 0) {
                // Merge semua kolom untuk baris bidang
                $sheet->mergeCells('A' . $row . ':' . $highestColumn . $row);
                
                // Style untuk baris bidang
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF2F2F2'],
                    ],
                ]);
            }
        }

        // Center alignment untuk kolom tanggal (C onwards)
        $sheet->getStyle('C1:' . $highestColumn . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Set row height untuk header
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(25);

        return [];
    }
}
