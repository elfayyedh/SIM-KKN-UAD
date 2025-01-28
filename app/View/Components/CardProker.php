<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CardProker extends Component
{
    public $proker;

    public function __construct($proker)
    {
        $this->proker = $proker;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.card-proker');
    }
}