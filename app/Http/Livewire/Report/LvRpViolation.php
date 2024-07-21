<?php

namespace App\Http\Livewire\Report;

use Livewire\Component;

class LvRpViolation extends Component
{
    public function render()
    {
        return view('livewire.report.lv-rp-violation')
        ->with(['pageTitle' => "Report Violation"])
        ->layout('layouts.cms.lv-main', ['menuName' => 'report_violation']);
    }

    public function dtRpViolation(Request $request)
    {
        
    }
}
