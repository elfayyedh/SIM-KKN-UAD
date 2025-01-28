<?php

namespace App\Livewire;

use App\Models\DanaKegiatan;
use Livewire\Component;

class DashboardCard1 extends Component
{
    public $id_unit;

    public $content;
    public $title;
    public $subContent;

    public function mount()
    {
        $this->content = DanaKegiatan::where('id_unit', $this->id_unit)->sum('jumlah');
    }
    public function render()
    {
        return view('livewire.dashboard-card1');
    }
}
