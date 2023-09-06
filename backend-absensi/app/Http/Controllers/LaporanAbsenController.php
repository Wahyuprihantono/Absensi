<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use PDF;

class LaporanAbsenController extends Controller
{
    public function cetak_pdf(Request $request)
    {
        $tanggal_selesai = $request->tanggal_selesai;
        $data = Absen::where('npk_karyawan', $request->npk)->whereBetween('waktu_absen', [$request->tanggal_mulai, date('Y-m-d', strtotime($tanggal_selesai . ' +1 days'))])->orderBy('waktu_absen', 'DESC')->get();
        $karyawan = Karyawan::find($request->npk);
        $user = $karyawan->user;

        $pdf = PDF::loadview('laporan-absen', [
            'items' => $data,
            'user' => $user,
            'mulai' => date('d-m-Y', strtotime($request->tanggal_mulai)),
            'selesai' => date('d-m-Y', strtotime($request->tanggal_selesai))
        ]);
        return $pdf->stream('laporan-pegawai-pdf');
    }
}
