<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceHistoryResource extends JsonResource
{
    private function getStatusAbsen($status, $type)
    {
        $status_absen = 'TELAT';
        if ($type == 1) {
            if ($status == 1) {
                $status_absen = 'TEPAT WAKTU';
            } else {
                $status_absen = 'TELAT';
            }
        } else {
            if ($status == 0) {
                $status_absen = 'BELUM ABSEN PULANG';
            } else {
                $status_absen = 'OK';
            }
        }

        return $status_absen;
    }

    private function getTypeAbsen($type)
    {
        $type_absen = 'Absen Masuk';
        switch ($type) {
            case 1:
                $type_absen = 'Absen Masuk';
                break;
            case 2:
                $type_absen = 'Istirahat';
                break;
            case 3:
                $type_absen = 'Selesai Istirahat';
                break;
            case 4:
                $type_absen = 'Absen Pulang';
                break;

            default:
                $type_absen = 'Absen Masuk';
                break;
        }

        return $type_absen;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type_absen' => $this->getTypeAbsen($this->type_absen),
            'type' => $this->type_absen,
            'status' => $this->status_absen,
            'tanggal_absen' => $this->waktu_absen->isoFormat('dddd, D MMMM Y'),
            'jam_absen' => $this->status_absen == 0 && $this->type_absen == 4 ? '-' : date('H:i', strtotime($this->waktu_absen)),
            'status_absen' => $this->getStatusAbsen($this->status_absen, $this->type_absen),
        ];
    }
}
