<?php

namespace App\Http\Livewire\Report;

use Livewire\Component;

class LvRpAchievement extends Component
{
    public function render()
    {
        return view('livewire.report.lv-rp-achievement')
        ->with(['pageTitle' => "Report Achievement"])
        ->layout('layouts.cms.lv-main', ['menuName' => 'report_achievement']);
    }
}
