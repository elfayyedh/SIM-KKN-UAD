<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AlertWithButton extends Component
{
    public $sessionSuccess;
    public $sessionError;
    public function __construct($sessionSuccess, $sessionError)
    {
        $this->sessionSuccess = $sessionSuccess;
        $this->sessionError = $sessionError;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.alert-with-button');
    }
}