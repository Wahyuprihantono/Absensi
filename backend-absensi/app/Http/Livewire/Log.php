<?php

namespace App\Http\Livewire;

use App\Models\Log as LogModel;
use Livewire\Component;

class Log extends Component
{
  public function render()
  {
    return view('livewire.log', [
      'items' => LogModel::all()
    ]);
  }
}
