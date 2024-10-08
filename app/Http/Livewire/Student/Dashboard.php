<?php

namespace App\Http\Livewire\Student;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.student.dashboard')
            ->with(['pageTitle' => "Student Dashboard"])
            ->layout('layouts/student/lv-main', ['menuName' => 'dashboard']);
    }
}
