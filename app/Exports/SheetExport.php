<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Models\Proker;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\WithTitle;

class SheetExport implements FromView, WithTitle
{
    public $sheetName;
    public $id_unit;
    public $bidangProker;

    public function __construct($sheetName, $id_unit, $bidangProker)
    {
        $this->sheetName = $sheetName;
        $this->id_unit = $id_unit;
        $this->bidangProker = $bidangProker;
    }

    public function view(): View
    {
        // Ambil data proker terkait dengan id_unit dan bidang_proker yang diberikan
        $prokers = Proker::where('id_unit', $this->id_unit)
            ->where('id_bidang', $this->bidangProker->id)
            ->with('kegiatan')
            ->get();

        $unit = Unit::findOrFail($this->id_unit);

        return view('mahasiswa.manajemen proker.export-proker', [
            'prokers' => $prokers,
            'unit' => $unit
        ]);
    }

    public function title(): string
    {
        return $this->sheetName;
    }
}
