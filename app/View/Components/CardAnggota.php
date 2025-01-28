<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CardAnggota extends Component
{
    /**
     * Create a new component instance.
     */
    public $anggota;

    public function __construct($anggota)
    {
        $this->anggota = $anggota;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.card-anggota');
    }
}