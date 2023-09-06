<?php

namespace App\Http\Livewire\Table;

use App\Http\Livewire\LivewireDatatable;
use App\Models\Karyawan;
use Mediconesystems\LivewireDatatables\Column;

class KaryawanTable extends LivewireDatatable
{
    public $sort = 'nama_karyawan|asc';
    public function builder()
    {
        return Karyawan::query();
    }

    protected $listeners = ['refreshTable'];

    public function columns()
    {
        $data_column = [
            Column::name('npk')->label('NPK')->searchable(),
            Column::name('nama_karyawan')->label('Nama Lengkap')->searchable(),
            Column::name('telepon_karyawan')->label('Telepon'),
            Column::name('user.email')->label('Email'),
            Column::name('jabatan_karyawan')->label('Jabatan'),
            Column::callback('wfh', function ($wfh) {
                if ($wfh == 1) {
                    return 'WFH';
                }
                return 'WFO';
            })->label('Status Kerja'),
            Column::callback('location.nama_lokasi', function ($lokasi) {
                if ($lokasi) {
                    return $lokasi;
                }
                return '-';
            })->label('Lokasi Absen'),


            Column::callback(['npk'], function ($id) {
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
