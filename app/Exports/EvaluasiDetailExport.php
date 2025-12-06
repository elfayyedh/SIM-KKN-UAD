<?php

namespace App\Exports;

use App\Models\EvaluasiMahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EvaluasiDetailExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    protected $kknId;
    protected $mahasiswaId;

    public function __construct($kknId, $mahasiswaId)
    {
        $this->kknId = $kknId;
        $this->mahasiswaId = $mahasiswaId;
    }

    public function collection()
    {
        return EvaluasiMahasiswa::with(['evaluasiMahasiswaDetail', 'timMonev.dosen.user'])
            ->where('id_mahasiswa', $this->mahasiswaId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        $kriteriaList = \App\Models\KriteriaMonev::where('id_kkn', $this->kknId)->orderBy('urutan', 'asc')->get();

        $headings = ['Evaluator', 'Tanggal'];
        foreach ($kriteriaList as $k) {
            $headings[] = $k->judul;
        }

        return $headings;
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function map($eval): array
    {
        $kriteriaList = \App\Models\KriteriaMonev::where('id_kkn', $this->kknId)->orderBy('urutan', 'asc')->get();

        $vals = [];
        foreach ($eval->evaluasiMahasiswaDetail as $d) {
            $vals[$d->id_kriteria_monev] = $d->nilai;
        }

        $row = [
            $eval->timMonev->dosen->user->nama ?? 'Admin',
            $eval->created_at->format('Y-m-d H:i')
        ];

        foreach ($kriteriaList as $k) {
            $row[] = $vals[$k->id] ?? '';
        }

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

        foreach (range('A', 'Z') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $sheet->freezePane('A3');
    }
}
