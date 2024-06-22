<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard')
            ->with(['pageTitle' => "Admin Dashboard"])
            ->layout('layouts/cms/lv-main', ['menuName' => 'admin_dashboard']);;
    }
}
