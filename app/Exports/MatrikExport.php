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
        
        // Header Row 1: PROGRAM dan Hari
        $headerRow1 = ['PROGRAM', 'Hari'];
        for ($i = 0; $i < $numDays; $i++) {
            $day = $i + 1;
            $headerRow1[] = $day;
        }
        $data[] = $headerRow1;

        // Header Row 2: (kosong) dan Tanggal/Bulan
        $headerRow2 = ['', 'Tanggal/Bulan'];
        for ($i = 0; $i < $numDays; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $date = $currentDate->format('d/m');
            $headerRow2[] = $date;
        }
        $data[] = $headerRow2;

        // Data rows per bidang dan proker
        foreach ($bidangProkers as $bidang) {
            // Baris Bidang - tampilkan semua bidang
            $bidangRow = ['Bidang ' . $bidang->nama];
            for ($i = 0; $i < $numDays + 1; $i++) {
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

                    // Isi kolom untuk setiap hari
                    for ($i = 0; $i < $numDays; $i++) {
                        $currentDate = $startDate->copy()->addDays($i)->format('Y-m-d');
                        $isPlanned = in_array($currentDate, $planDates);
                        $isRealized = in_array($currentDate, $realisasiDates);

                        $content = '';
                        if ($isPlanned && $isRealized) {
                            $content = 'R+P'; // Rencana + Realisasi
                        } elseif ($isPlanned) {
                            $content = 'R'; // Rencana
                        } elseif ($isRealized) {
                            $content = 'P'; // Realisasi (Pelaksanaan)
                        }

                        $prokerRow[] = $content;
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

        // Style untuk semua sel - border
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Merge cell untuk header row 1 kolom PROGRAM
        $sheet->mergeCells('A1:A2');
        
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
