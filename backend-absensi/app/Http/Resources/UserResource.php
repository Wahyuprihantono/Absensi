<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            '_id' => $this->id,
            'name' => $this->karyawan->nama_karyawan,
            'telepon' => $this->karyawan->telepon_karyawan,
            'email' => $this->email,
            'jabatan' => $this->karyawan->jabatan_karyawan,
            'isWfh' => $this->karyawan->wfh,
            // 'fotoKaryawan' => $this->profile_photo_url,
            'haveFace' => $this->karyawan->modelWajah ? true : null,
            'haveLocation' => $this->karyawan->location ? true : null,
            'location' => [
                'latitude' => $this->karyawan->location ? floatval($this->karyawan->location->latitude) : null,
                'longitude' => $this->karyawan->location ? floatval($this->karyawan->location->longitude) : null
            ]
        ];
    }
}
