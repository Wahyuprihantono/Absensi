<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;
    protected $table = 'karyawan';
    protected $primaryKey = 'npk';
    public $incrementing = false;
    protected $guarded = [];

    /**
     * Get the user that owns the Karyawan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the location that owns the Karyawan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id', 'id');
    }

    /**
     * Get the absen associated with the Karyawan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function absen()
    {
        return $this->hasOne(Absen::class, 'npk_karyawan', 'npk');
    }

    /**
     * Get the modelWajah associated with the Karyawan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function modelWajah()
    {
        return $this->hasOne(ModelWajah::class, 'npk_karyawan', 'npk');
    }
}
