<?php

namespace App\Livewire;

use App\Models\Proker;
use Livewire\Component;

class DashboardCard2 extends Component
{
    public $id_unit;

    public $content;
    public $title;
    public $subContent;
    public $total_kegiatan = 0;

    public function mount()
    {
        $prokers = Proker::withCount('kegiatan')
            ->where('id_unit', $this->id_unit)
            ->get();

        $this->content = $prokers->count();
        $this->total_kegiatan = $prokers->sum('kegiatan_count');
    }

    public function render()
    {
        return view('livewire.dashboard-card2');
    }
}
