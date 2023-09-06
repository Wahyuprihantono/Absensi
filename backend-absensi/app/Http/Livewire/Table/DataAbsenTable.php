<?php

namespace App\Http\Livewire\Table;

use App\Http\Livewire\LivewireDatatable;
use App\Models\Absen;
use Mediconesystems\LivewireDatatables\Column;

class DataAbsenTable extends LivewireDatatable
{
    public $sort = 'waktu_absen|desc';
    public $filters = [];
    protected $listeners = ['refreshTable', 'setFilter'];
    public function builder()
    {
        if (count($this->filters)) {
            $tgl_selesai = $this->filters['tanggal_selesai'];
            if ($this->filters['npk'] && $this->filters['tanggal_selesai'] && $this->filters['tanggal_mulai']) {
                return Absen::query()->where('npk_karyawan', $this->filters['npk'])->whereBetween('waktu_absen', [$this->filters['tanggal_mulai'], date('Y-m-d', strtotime($tgl_selesai . ' +1 days'))])->orderBy('waktu_absen', 'DESC');
            }

            if ($this->filters['npk']) {
                return Absen::query()->where('npk_karyawan', $this->filters['npk'])->orderBy('waktu_absen', 'DESC');
            }
        }
        return Absen::query();
    }


    public function columns()
    {
        $data_column = [
            Column::name('karyawan.npk')->label('NPK'),
            Column::name('karyawan.nama_karyawan')->label('Nama Karyawan'),
            Column::name('waktu_absen')->label('Waktu Absen'),
            Column::callback(['tbl_absen.status_absen', 'tbl_absen.type_absen'], function ($status_absen, $type_absen) {
                if ($status_absen == 1) {
                    if ($type_absen == 4) {
                        return '<p style="margin-bottom: 0; color: green">SUDAH ABSEN PULANG</p>';
                    } else {
                        return '<p style="margin-bottom: 0; color: green">TEPAT WAKTU</p>';
                    }
                } else {
                    if ($type_absen == 4) {
                        return '<p style="margin-bottom: 0; color: red">BELUM ABSEN PULANG</p>';
                    } else {
                        return '<p style="margin-bottom: 0; color: red">TELAT</p>';
                    }
                }
            })->label('Status Absen'),
            Column::callback('type_absen', function ($type_absen) {
                if ($type_absen == 1) {
                    return 'Absen masuk';
                }
                return 'Absen pulang';
            })->label('Jenis Absen'),
            Column::name('nama_lokasi')->label('Lokasi absen'),
            Column::name('keterangan')->label('Keterangan'),
        ];

        return $data_column;
    }

    public function setFilter($filters)
    {
        $this->filters = $filters;
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
