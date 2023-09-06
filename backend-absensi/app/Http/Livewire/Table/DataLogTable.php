<?php

namespace App\Http\Livewire\Table;

use App\Models\Log;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class DataLogTable extends LivewireDatatable
{
    public $sort = 'tanggal_log|asc';
    public function builder()
    {
        return Log::query();
    }

    protected $listeners = ['refreshTable'];

    public function columns()
    {
        $data_column = [
            Column::name('user.karyawan.nama_karyawan')->label('Nama')->searchable(),
            Column::name('keterangan')->label('Keterangan')->searchable()->width(600),
            Column::name('aksi')->label('Aksi'),
            Column::name('tanggal_log')->label('Tanggal'),
        ];

        return $data_column;
    }

    public function refreshTable()
    {
        $this->emit('refreshLivewireDatatable');
    }


    public function getDataById($id)
    {
        $this->emit('getDataById', $id);
    }

    public function getId($id)
    {
        $this->emit('getId', $id);
    }
}
