<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\SheetExport;
use App\Models\BidangProker;
use App\Models\Unit;

class ProkerExport implements WithMultipleSheets
{
    protected $id_unit;

    public function __construct($id_unit)
    {
        $this->id_unit = $id_unit;
    }

    public function sheets(): array
    {
        // Ambil unit berdasarkan id_unit
        $unit = Unit::findOrFail($this->id_unit);

        // Ambil bidang_proker berdasarkan id_kkn yang ada di unit
        $bidangProkers = BidangProker::where('id_kkn', $unit->id_kkn)->get();

        $sheets = [];

        // Iterasi setiap bidang_proker dan buat sheet dengan nama bidang_proker
        foreach ($bidangProkers as $bidangProker) {
            // Tambahkan sheet untuk setiap bidang_proker
            $sheets[] = new SheetExport($bidangProker->nama, $this->id_unit, $bidangProker);
        }

        return $sheets;
    }
}
