<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;
    protected $table = 'lokasi';

    protected $guarded = [];

    /**
     * Get the karyawan associated with the Lokasi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'lokasi_id');
    }
}
