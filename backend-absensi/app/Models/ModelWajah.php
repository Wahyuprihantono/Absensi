<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelWajah extends Model
{
    use HasFactory;
    protected $table = 'model_wajah';
    protected $guarded = [];

    /**
     * Get the karyawan that owns the ModelWajah
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'npk_karyawan');
    }
}
