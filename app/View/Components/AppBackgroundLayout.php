<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppBackgroundLayout extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(): View
    {
        return view('layouts.app-background');
    }
}
