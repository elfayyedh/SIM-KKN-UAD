<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UnitTable extends Component
{
    /**
     * Create a new component instance.
     */

    public $unit;

    public function __construct($unit)
    {
        $this->unit = $unit;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.unit-table');
    }
}