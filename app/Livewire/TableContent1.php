<?php

namespace App\Livewire;

use App\Models\Mahasiswa;
use Livewire\Component;

class TableContent1 extends Component
{
    public $id_unit;
    public $content;

    public function mount()
    {
        $this->content = Mahasiswa::with(['kegiatan.proker.bidang', 'logbookHarian'])
            ->where('id_unit', $this->id_unit)
            ->get()
            ->map(function ($mhs) {
                // Menghitung total jkem yang sudah tercapai
                $mhs->jkem_tercapai = $mhs->logbookHarian->sum('total_jkem');
                $mhs->jkem_belum_tercapai = $mhs->total_jkem - $mhs->jkem_tercapai;
                $mhs->jkem_belum_tercapai < 0 ? $mhs->jkem_belum_tercapai = 0 : $mhs->jkem_belum_tercapai;
                return $mhs;
            });
    }
    public function render()
    {
        return view('livewire.table-content1');
    }
}