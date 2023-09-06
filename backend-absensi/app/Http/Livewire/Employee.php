<?php

namespace App\Http\Livewire;

use App\Models\Karyawan;
use App\Models\Location;
use App\Models\Log;
use App\Models\Lokasi;
use App\Models\User;
use Carbon\Carbon;
use Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Employee extends Component
{
  public $karyawan_id;
  public $user_id;
  public $npk;
  public $nama_karyawan;
  public $telepon_karyawan;
  public $email_karyawan;
  public $jabatan_karyawan;
  public $is_whf;
  public $lokasi_id;


  public $form_active = false;
  public $form = false;
  public $update_mode = false;
  public $modal = true;

  protected $listeners = ['getDataById', 'getId'];

  public function render()
  {
    $locations = Lokasi::where('lokasi_perusahaan', 1)->get();
    return view('livewire.employee', [
      'items' => Karyawan::whereHas('user', function ($query) {
        return $query->where('level', 'karyawan');
      })->get(),
      'locations' => $locations
    ]);
  }

  public function store()
  {
    $this->_validate();
    try {
      DB::beginTransaction();

      $user = User::create([
        'username' => $this->npk,
        'email' => $this->email_karyawan,
        'level' => 'karyawan',
        'password' => Hash::make('Karyawan123'),
      ]);

      $user->karyawan()->create([
        'npk'  => $this->npk,
        'nama_karyawan'  => $this->nama_karyawan,
        'telepon_karyawan'  => $this->telepon_karyawan,
        'jabatan_karyawan'  => $this->jabatan_karyawan,
        'wfh'  => $this->is_whf,
        'lokasi_id'  => $this->lokasi_id
      ]);

      Log::create([
        'user_id' => Auth::user()->id,
        'aksi' => 'create',
        'keterangan' => 'Menambah data karyawan (' . $this->nama_karyawan . ')',
        'tanggal_log' => Carbon::now(),
      ]);

      DB::commit();

      $this->_reset();
      return $this->emit('showAlert', ['msg' => 'Data Berhasil Disimpan']);
    } catch (Error $error) {
      DB::rollback();
      return $this->emit('showAlertError', ['msg' => 'Data Gagal Disimpan']);
    }
  }

  public function update()
  {
    $this->_validate();

    $user = User::find($this->user_id);

    try {
      DB::beginTransaction();

      $user->update([
        'email' => $this->email_karyawan,
      ]);

      $user->karyawan()->update([
        'npk'  => $this->npk,
        'nama_karyawan'  => $this->nama_karyawan,
        'telepon_karyawan'  => $this->telepon_karyawan,
        'jabatan_karyawan'  => $this->jabatan_karyawan,
        'wfh'  => $this->is_whf,
        'lokasi_id'  => $this->lokasi_id
      ]);

      Log::create([
        'user_id' => Auth::user()->id,
        'aksi' => 'update',
        'keterangan' => 'Mengubah data karyawan (' . $this->nama_karyawan . ')',
        'tanggal_log' => Carbon::now(),
      ]);

      DB::commit();
      $this->_reset();
      return $this->emit('showAlert', ['msg' => 'Data Berhasil Diupdate']);
    } catch (Error $error) {
      DB::rollback();
      return $this->emit('showAlertError', ['msg' => 'Data Gagal Diupdate']);
    }
  }

  public function delete()
  {
    try {
      DB::beginTransaction();
      Karyawan::find($this->karyawan_id)->delete();

      Log::create([
        'user_id' => Auth::user()->id,
        'aksi' => 'delete',
        'keterangan' => 'Menghapus data karyawan (' . $this->nama_karyawan . ')',
        'tanggal_log' => Carbon::now(),
      ]);

      DB::commit();
      $this->_reset();
      return $this->emit('showAlert', ['msg' => 'Data Berhasil Dihapus']);
    } catch (Error $error) {
      DB::rollback();
      return $this->emit('showAlertError', ['msg' => 'Data Gagal Dihapus']);
    }
  }

  public function _validate()
  {
    $rule = [
      'npk'  => 'required',
      'nama_karyawan'  => 'required|regex:/^[a-zA-Z]+$/u',
      'telepon_karyawan'  => 'required|numeric',
      'email_karyawan'  => 'required|email',
      'jabatan_karyawan'  => 'required',
      'is_whf'  => 'required',
    ];

    if (!$this->update_mode) {
      $rule['npk'] = 'required|unique:karyawan,npk';
      $rule['telepon_karyawan'] = 'required|numeric|unique:karyawan,telepon_karyawan';
      $rule['email_karyawan'] = 'required|email|unique:users,email';
    }

    $message = [
      'npk.required' => 'npk tidak boleh kosong',
      'npk.unique' => 'npk sudah terdaftar',
      'nama_karyawan.required' => 'nama tidak boleh kosong',
      'nama_karyawan.regex' => 'nama harus berupa huruf',
      'telepon_karyawan.required' => 'telepon karyawan tidak boleh kosong',
      'telepon_karyawan.unique' => 'telepon karyawan sudah terdaftar',
      'telepon_karyawan.numeric' => 'telepon karyawan harus berupa angka',
      'email_karyawan.required' => 'email karyawan tidak boleh kosong',
      'email_karyawan.unique' => 'email karyawan sudah terdaftar',
      'email_karyawan.email' => 'email karyawan tidak valid',
      'jabatan_karyawan.required' => 'jabatan karyawan tidak boleh kosong',
      'is_whf.required' => 'data tidak boleh kosong',
    ];

    if ($this->is_whf == 0) {
      $rule['lokasi_id'] = 'required';
    }

    return $this->validate($rule, $message);
  }

  public function getDataById($karyawan_id)
  {
    $karyawan = Karyawan::find($karyawan_id);
    $this->karyawan_id = $karyawan->npk;
    $this->npk = $karyawan->npk;
    $this->nama_karyawan = $karyawan->nama_karyawan;
    $this->telepon_karyawan = $karyawan->telepon_karyawan;
    $this->email_karyawan = $karyawan->user->email;
    $this->jabatan_karyawan = $karyawan->jabatan_karyawan;
    $this->is_whf = $karyawan->wfh;
    $this->lokasi_id = $karyawan->lokasi_id;
    $this->user_id = $karyawan->user_id;
    if ($this->form) {
      $this->form_active = true;
      $this->emit('loadForm');
    }
    if ($this->modal) {
      $this->emit('showModal');
    }
    $this->update_mode = true;
  }

  public function getId($karyawan_id)
  {
    $karyawan = Karyawan::find($karyawan_id);
    $this->karyawan_id = $karyawan->npk;
    $this->nama_karyawan = $karyawan->nama_karyawan;
  }

  public function toggleForm($form)
  {
    $this->form_active = $form;
    $this->emit('loadForm');
  }

  public function showModal()
  {
    $this->emit('showModal');
  }

  public function _reset()
  {
    $this->emit('refreshTable');
    $this->emit('closeModal');
    $this->karyawan_id = null;
    $this->npk = null;
    $this->nama_karyawan = null;
    $this->telepon_karyawan = null;
    $this->email_karyawan = null;
    $this->jabatan_karyawan = null;
    $this->is_whf = null;
    $this->lokasi_id = null;
    $this->user_id = null;
    $this->form = false;
    $this->form_active = false;
    $this->update_mode = false;
    $this->modal = true;
  }
}
