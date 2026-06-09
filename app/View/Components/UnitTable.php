<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class UnitTable extends Component
{
    /**
     * Data unit yang akan ditampilkan.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    public $units;

    /**
     * Buat instance komponen baru.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $units
     * @return void
     */
    public function __construct($units)
    {
        $this->units = $units;
    }

    /**
     * Dapatkan view / konten yang mewakili komponen.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): View
    {
        return view('components.unit-table');
    }
}