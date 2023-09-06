<?php

namespace App\Http\Livewire;

use App\Models\Absen as ModelsAbsen;
use App\Models\Karyawan;
use App\Models\Log as LogModel;
use Livewire\Component;

class Absen extends Component
{
  public $npk;
  public $tanggal_mulai;
  public $tanggal_selesai;
  public $show = false;
  public $items = [];

  public function render()
  {
    return view('livewire.absen', [
      'employess' => Karyawan::whereHas('user', function ($query) {
        return $query->where('level', 'karyawan');
      })->get(),
    ]);
  }

  public function filter()
  {
    $this->validate([
      'npk' => 'required',
      'tanggal_mulai' => 'required',
      'tanggal_selesai' => 'required',
    ], [
      'npk.required' => 'Mohon pilih karyawan terlebih dahulu',
      'tanggal_mulai.required' => 'Masukkan tanggal mulai',
      'tanggal_selesai.required' => 'Masukkan tanggal selesai',
    ]);

    if (strtotime($this->tanggal_mulai) > strtotime($this->tanggal_selesai)) {
      return $this->emit('showAlertError', ['msg' => 'Tanggal mulai tidak boleh lebih dari tanggal selesai']);
    }

    $this->emit('setFilter', [
      'npk' => $this->npk,
      'tanggal_mulai' => $this->tanggal_mulai,
      'tanggal_selesai' => $this->tanggal_selesai,
    ]);
    $this->show = true;
    // $this->items = ModelsAbsen::where('npk_karyawan', $this->npk)->whereBetween('waktu_absen', [$this->tanggal_mulai, $this->tanggal_selesai])->orderBy('waktu_absen', 'DESC')->get();
  }

  public function handleChange($value)
  {
    $this->emit('setFilter', [
      'npk' => $value,
      'tanggal_mulai' => null,
      'tanggal_selesai' => null,
    ]);
  }

  public function _reset()
  {
    $this->npk = null;
    $this->tanggal_mulai = null;
    $this->tanggal_selesai = null;
    $this->items = [];
  }
}
