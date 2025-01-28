<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DplTable extends Component
{
    /**
     * Create a new component instance.
     */

    public $dpl;

    public function __construct($dpl)
    {
        $this->dpl = $dpl;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dpl-table');
    }
}