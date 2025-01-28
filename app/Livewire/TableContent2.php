<?php

namespace App\Livewire;

use App\Models\Proker;
use Livewire\Component;

class TableContent2 extends Component
{

    public $id_unit;
    public $content;

    public function mount()
    {
        $this->content = Proker::with(['kegiatan.tanggalRencanaProker', 'kegiatan.mahasiswa'])->where('id_unit', $this->id_unit)->get();
    }
    public function render()
    {
        return view('livewire.table-content2');
    }
}
