<?php

namespace App\Http\Livewire;

use App\Models\Log;
use Livewire\Component;
use GeoLocation;
use App\Models\Lokasi;
use Carbon\Carbon;
use Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Location extends Component
{
  public $locations_id;
  public $nama_lokasi;
  public $lokasi_perusahaan = 1;
  public $latitude = 0;
  public $longitude = 0;


  public $form_active = false;
  public $form = true;
  public $update_mode = false;
  public $modal = false;

  protected $listeners = ['getDataById', 'getId'];

  public function mount()
  {
    $this->_getLatitude();
  }

  public function render()
  {
    return view('livewire.location', [
      'items' => Lokasi::all(),
    ]);
  }

  public function store()
  {
    $this->_validate();
    try {
      DB::beginTransaction();
      Lokasi::create([
        'nama_lokasi'  => $this->nama_lokasi,
        'latitude'  => $this->latitude,
        'longitude'  => $this->longitude,
        'lokasi_perusahaan'  => $this->lokasi_perusahaan,
      ]);

      Log::create([
        'user_id' => Auth::user()->id,
        'aksi' => 'create',
        'keterangan' => 'Menambah data lokasi (' . $this->nama_lokasi . ')',
        'tanggal_log' => Carbon::now(),
      ]);

      DB::commit();
      $this->_reset();
      return $this->emit('showAlert', ['msg' => 'Data Lokasi Berhasil Disimpan']);
    } catch (Error $error) {
      DB::rollback();
      return $this->emit('showAlertError', ['msg' => 'Data Lokasi Gagal Disimpan']);
    }
  }

  public function update()
  {
    $this->_validate();
    $lokasi = Lokasi::find($this->locations_id);

    try {
      DB::beginTransaction();
      $lokasi->update([
        'nama_lokasi'  => $this->nama_lokasi,
        'latitude'  => $this->latitude,
        'longitude'  => $this->longitude,
        'lokasi_perusahaan'  => $this->lokasi_perusahaan,
      ]);

      Log::create([
        'user_id' => Auth::user()->id,
        'aksi' => 'update',
        'keterangan' => 'Mengubah data lokasi (' . $this->nama_lokasi . ')',
        'tanggal_log' => Carbon::now(),
      ]);

      DB::commit();
      $this->_reset();
      return $this->emit('showAlert', ['msg' => 'Data Lokasi Berhasil Diupdate']);
    } catch (Error $error) {
      DB::rollback();
      return $this->emit('showAlertError', ['msg' => 'Data Lokasi Gagal Diupdate']);
    }
  }

  public function delete()
  {
    try {
      DB::beginTransaction();
      Lokasi::find($this->locations_id)->delete();

      Log::create([
        'user_id' => Auth::user()->id,
        'aksi' => 'delete',
        'keterangan' => 'Menghapus data lokasi (' . $this->nama_lokasi . ')',
        'tanggal_log' => Carbon::now(),
      ]);

      DB::commit();
      $this->_reset();
      return $this->emit('showAlert', ['msg' => 'Data Lokasi Berhasil Dihapus']);
    } catch (Error $error) {
      DB::rollback();
      return $this->emit('showAlertError', ['msg' => 'Data Lokasi Gagal Dihapus']);
    }
  }

  public function _validate()
  {
    $rule = [
      // 'npk_karyawan'  => 'required',
      'latitude'  => 'required',
      'longitude'  => 'required'
    ];

    return $this->validate($rule);
  }

  public function getDataById($locations_id)
  {
    $locations = Lokasi::find($locations_id);
    $this->locations_id = $locations->id;
    $this->nama_lokasi = $locations->nama_lokasi;
    $this->latitude = $locations->latitude;
    $this->longitude = $locations->longitude;

    if ($this->form) {
      $this->form_active = true;
      $this->emit('loadForm', ['latitude' => $this->latitude, 'longitude' => $this->longitude]);
    }
    if ($this->modal) {
      $this->emit('showModalEdit');
    }
    $this->update_mode = true;
  }

  public function getId($locations_id)
  {
    $locations = Lokasi::find($locations_id);
    $this->locations_id = $locations->id;
    $this->nama_lokasi = $locations->nama_lokasi;
  }

  public function toggleForm($form)
  {
    $this->form_active = $form;
    if (!$form) {
      $this->_reset();
      return 0;
    }
    $this->_getLatitude();
    $this->emit('loadForm', ['latitude' => $this->latitude, 'longitude' => $this->longitude]);
  }

  public function showModal()
  {
    $this->_reset();
    $this->emit('showModal');
  }

  public function _getLatitude()
  {
    $ip = request()->ip();
    $data = GeoLocation::get('120.188.93.174');
    $this->latitude = $data->latitude;
    $this->longitude = $data->longitude;
  }

  public function _reset()
  {
    $this->emit('refreshTable');
    $this->emit('closeModal');
    $this->locations_id = null;
    $this->nama_lokasi = null;
    $this->latitude = null;
    $this->longitude = null;
    $this->form = true;
    $this->form_active = false;
    $this->update_mode = false;
    $this->modal = false;
  }
}
