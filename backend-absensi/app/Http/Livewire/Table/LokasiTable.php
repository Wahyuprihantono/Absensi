<?php

namespace App\Http\Livewire\Table;

use App\Http\Livewire\LivewireDatatable;
use App\Models\Lokasi;
use Mediconesystems\LivewireDatatables\Column;

class LokasiTable extends LivewireDatatable
{
    public $sort = 'nama_lokasi|asc';
    public function builder()
    {
        return Lokasi::query();
    }

    protected $listeners = ['refreshTable'];

    public function columns()
    {
        $data_column = [
            Column::name('nama_lokasi')->label('Alamat')->searchable(),
            Column::name('latitude')->label('Latitude'),
            Column::name('longitude')->label('Longitude'),
            // Column::callback(['is_office'], function ($is_office) {
            //     return $is_office ? 'Ya' : 'Tidak';
            // })->label(__('Lokasi Perusahaan')),

            Column::callback(['id'], function ($id) {
                return view('livewire.components.action-button', [
                    'id' => $id,
                    'segment' => request()->segment(1)
                ]);
            })->label(__('Aksi')),
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
