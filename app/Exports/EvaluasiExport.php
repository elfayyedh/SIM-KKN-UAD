<?php

namespace App\Exports;

use App\Models\EvaluasiMahasiswa;
use App\Models\KriteriaMonev;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EvaluasiExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $kknId;

    public function __construct($kknId)
    {
        $this->kknId = $kknId;
    }

    public function collection()
    {
        return EvaluasiMahasiswa::with([
            'mahasiswa.userRole.user',
            'mahasiswa.unit.lokasi.kecamatan.kabupaten',
            'mahasiswa.unit.dpl.dosen.user',
            'timMonev.dosen.user',
            'details.kriteriaMonev'
        ])
        ->whereHas('mahasiswa.unit', function($query) {
            $query->where('id_kkn', $this->kknId);
        })
        ->get();
    }

    public function headings(): array
    {
        $kriteria = KriteriaMonev::where('id_kkn', $this->kknId)->orderBy('urutan')->get();

        $headings = [
            'NIM',
            'Nama Mahasiswa',
            'Unit',
            'Lokasi',
            'DPL',
            'Tim Monev',
            'Catatan Monev'
        ];

        foreach ($kriteria as $k) {
            $headings[] = $k->judul;
        }

        $headings[] = 'Rata-rata';

        return $headings;
    }

    public function map($evaluasi): array
    {
        $mahasiswa = $evaluasi->mahasiswa;
        $unit = $mahasiswa->unit;
        $lokasi = $unit->lokasi;
        $dpl = $unit->dpl;
        $timMonev = $evaluasi->timMonev;

        $row = [
            $mahasiswa->userRole->user->username ?? '',
            $mahasiswa->userRole->user->nama ?? '',
            $unit->nama ?? '',
            $lokasi ? ($lokasi->kecamatan->kabupaten->nama . ', ' . $lokasi->kecamatan->nama) : '',
            $dpl ? $dpl->dosen->user->nama : '',
            $timMonev ? $timMonev->dosen->user->nama : '',
            $evaluasi->catatan_monev ?? ''
        ];

        $kriteria = KriteriaMonev::where('id_kkn', $this->kknId)->orderBy('urutan')->get();
        $totalScore = 0;
        $count = 0;

        foreach ($kriteria as $k) {
            $detail = $evaluasi->details->where('id_kriteria_monev', $k->id)->first();
            $score = $detail ? $detail->nilai : '';
            $row[] = $score;

            if (is_numeric($score)) {
                $totalScore += $score;
                $count++;
            }
        }

        $average = $count > 0 ? round($totalScore / $count, 2) : '';
        $row[] = $average;

        return $row;
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

        // Auto-size columns
        foreach (range('A', 'Z') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $sheet;
    }
}
