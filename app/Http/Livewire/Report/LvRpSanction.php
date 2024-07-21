<?php

namespace App\Http\Livewire\Report;

use Livewire\Component;

class LvRpSanction extends Component
{
    public function render()
    {
        return view('livewire.report.lv-rp-sanction')
            ->with(['pageTitle' => "Report Sanction"])
            ->layout('layouts.cms.lv-main', ['menuName' => 'report_sanction']);
    }
}
