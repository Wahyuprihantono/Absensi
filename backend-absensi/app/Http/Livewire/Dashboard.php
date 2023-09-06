<?php

namespace App\Http\Livewire;

use App\Models\Karyawan;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard', [
            'karyawan' => Karyawan::count(),
            'wfh' => Karyawan::where('wfh', '1')->count(),
            'wfo' => Karyawan::where('wfh', '0')->count(),
        ]);
    }
}
