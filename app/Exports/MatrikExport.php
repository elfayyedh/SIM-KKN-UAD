<?php

namespace App\Exports;

use App\Models\BidangProker;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class MatrikExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $idUnit;
    protected $idKkn;

    public function __construct($idUnit, $idKkn)
    {
        $this->idUnit = $idUnit;
        $this->idKkn = $idKkn;
    }

    public function collection()
    {
        // Ambil data dari database sesuai dengan idUnit dan idKkn
        return BidangProker::with(['proker' => function ($query) {
            $query->where('id_unit', $this->idUnit);
        }, 'proker.kegiatan.tanggalRencanaProker', 'proker.kegiatan.logbookKegiatan.logbookHarian'])
            ->where('id_kkn', $this->idKkn)
            ->get();
    }

    public function headings(): array
    {
        // Header tabel yang diinginkan
        $headers = [
            'Bidang',
            'Program',
        ];

        // Tambahkan kolom untuk setiap tanggal dalam range yang ditentukan
        $startDate = Carbon::parse(request()->input('minDate'));
        $endDate = Carbon::parse(request()->input('maxDate'));
        $numDays = $endDate->diffInDays($startDate) + 1;

        for ($i = 0; $i < $numDays; $i++) {
            $date = $startDate->copy()->addDays($i)->format('d/m/Y');
            $headers[] = $date;
        }

        return $headers;
    }

    public function map($bidangProker): array
    {
        $mappedData = [];
        $startDate = Carbon::parse(request()->input('minDate'));
        $endDate = Carbon::parse(request()->input('maxDate'));
        $numDays = $endDate->diffInDays($startDate) + 1;

        foreach ($bidangProker->proker as $proker) {
            $row = [
                $bidangProker->nama,
                $proker->nama,
            ];

            $planDates = $proker->kegiatan->flatMap(function ($kegiatan) {
                return $kegiatan->tanggalRencanaProker->pluck('tanggal');
            });

            $realisasiDates = $proker->kegiatan->flatMap(function ($kegiatan) {
                return $kegiatan->logbookKegiatan->map(function ($logbookKegiatan) {
                    return $logbookKegiatan->logbookHarian->tanggal;
                });
            });

            for ($i = 0; $i < $numDays; $i++) {
                $currentDate = $startDate->copy()->addDays($i)->format('Y-m-d');
                $isPlanned = $planDates->contains($currentDate);
                $isRealized = $realisasiDates->contains($currentDate);

                // Determine cell content
                $content = '';
                if ($isPlanned) {
                    $content .= 'Rencana ';
                }
                if ($isRealized) {
                    $content .= 'Realisasi';
                }

                $row[] = $content;
            }

            $mappedData[] = $row;
        }

        return $mappedData;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:Z1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Additional styling can be added here
    }
}
