<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BidangProker extends Component
{
    /**
     * Create a new component instance.
     */

    public $bidangProker;

    public function __construct($bidangProker)
    {
        $this->bidangProker = $bidangProker;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.bidang-proker');
    }
}
