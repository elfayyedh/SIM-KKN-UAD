<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EvaluasiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    protected $kknId;

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

    // HEADER BARIS 2 (A2–K2)
    public function headings(): array
    {
        return [
            'Nama Mahasiswa', // A
            'NIM',            // B
            'Unit',           // C
            'Lokasi',         // D
            'Capaian JKEM',   // E
            'Sholat',         // F
            'Form 1',         // G
            'Form 2',         // H
            'Form 3',         // I
            'Form 4',         // J
            'Jumlah Nilai'    // K (now as subheader under the 'Nilai' group)
        ];
    }

    // HEADINGS mulai dari A2 agar data mulai A3
    public function startCell(): string
    {
        return 'A2';
    }

    public function map($mhs): array
    {
        $nama = $mhs->userRole->user->nama ?? '';
        $nim  = $mhs->nim ?? '';
        $unit = $mhs->unit->nama ?? '';

        // lokasi aman
        $lokasi = '-';
        if (
            $mhs->unit &&
            $mhs->unit->lokasi &&
            $mhs->unit->lokasi->kecamatan &&
            $mhs->unit->lokasi->kecamatan->kabupaten
        ) {
            $lokasi = $mhs->unit->lokasi->kecamatan->kabupaten->nama;
        }

        // nilai evaluasi
        $jkem = 0;
        $sholat = 0;
        $form1 = 0;
        $form2 = 0;
        $form3 = 0;
        $form4 = 0;

        if ($mhs->evaluasiMahasiswa) {
            foreach ($mhs->evaluasiMahasiswa as $e) {
                $jkem   += $e->eval_jkem ?? 0;
                $sholat += $e->eval_sholat ?? 0;
                $form1  += $e->form_1 ?? ($e->form1 ?? 0);
                $form2  += $e->form_2 ?? ($e->form2 ?? 0);
                $form3  += $e->form_3 ?? ($e->form3 ?? 0);
                $form4  += $e->form_4 ?? ($e->form4 ?? 0);
            }
        }

        // Hitung jumlah nilai (tetapi kolom Jumlah Nilai akan dikosongkan sesuai permintaan)
        $jumlahNilai = $jkem + $sholat + $form1 + $form2 + $form3 + $form4;

        return [
            $nama,
            $nim,
            $unit,
            $lokasi,
            $jkem ?: '',
            $sholat ?: '',
            $form1 ?: '',
            $form2 ?: '',
            $form3 ?: '',
            $form4 ?: '',
            ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Baris 1: Judul "Nilai" — sekarang mencakup Form 1..4 dan Jumlah Nilai (E1:K1)
        $sheet->setCellValue('E1', 'Nilai');
        $sheet->mergeCells('E1:K1');

        // Styling header baris 1–2
        $sheet->getStyle('A1:K2')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Auto-size kolom
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Tinggi baris
        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Freeze panes baris atas
        $sheet->freezePane('A3');
    }
}
