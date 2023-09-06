<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceHistoryResource;
use App\Models\Absen;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class AttendanceController extends Controller
{
    public function checkAttendance(Request $request)
    {
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $current_date = strtotime(date('Y-m-d H:i:s'));
        $start_date_of_attendance = strtotime(date('Y-m-d 07:00:00'));
        $start_date_of_day = strtotime(date('Y-m-d 09:00:00'));
        $end_date_of_day = strtotime(date('Y-m-d 18:00:00'));
        $data = null;

        $absen_masuk = Absen::whereDate('waktu_absen', date('Y-m-d'))->where(['npk_karyawan' => $user->karyawan->npk, 'type_absen' => 1])->first();
        $absen_istirahat = Absen::whereDate('waktu_absen', date('Y-m-d'))->where(['npk_karyawan' => $user->karyawan->npk, 'type_absen' => 2])->first();
        $absen_selesai_istirahat = Absen::whereDate('waktu_absen', date('Y-m-d'))->where(['npk_karyawan' => $user->karyawan->npk, 'type_absen' => 3])->first();
        $absen_pulang = Absen::whereDate('waktu_absen', date('Y-m-d'))->where(['npk_karyawan' => $user->karyawan->npk, 'type_absen' => 4])->first();


        if ($user->karyawan->modelWajah && $user->karyawan->location) {
            if ($absen_masuk) {
                if ($request->type_absen == 1) {
                    if ($absen_masuk) {
                        $data = [
                            'message' => 'Anda Sudah melakukan absen masuk hari ini.',
                            'alert' => true,
                            'next' => false,
                        ];
                        return response()->json($data, 200);
                    }
                    return response()->json([
                        'message' => '',
                        'alert' => false,
                        'next' => true,
                        'coordinate' => [
                            'latitude' => $user->karyawan->location ? floatval($user->karyawan->location->latitude) : null,
                            'longitude' => $user->karyawan->location ? floatval($user->karyawan->location->longitude) : null
                        ]
                    ], 200);
                }
                // absen istirahat
                if ($request->type_absen == 2) {
                    if ($absen_masuk) {
                        if ($absen_istirahat) {
                            $data = [
                                'message' => 'Anda sudah melakukan absen istirahat.',
                                'alert' => true,
                                'next' => false,
                            ];
                            return response()->json($data, 200);
                        } else {
                            if ($absen_pulang->status_absen == 1) {
                                $data = [
                                    'message' => 'Anda sudah melakukan absen pulang.',
                                    'alert' => true,
                                    'next' => false,
                                ];
                                return response()->json($data, 200);
                            }
                            return response()->json([
                                'message' => '',
                                'alert' => false,
                                'next' => true,
                                'coordinate' => [
                                    'latitude' => $user->karyawan->location ? floatval($user->karyawan->location->latitude) : null,
                                    'longitude' => $user->karyawan->location ? floatval($user->karyawan->location->longitude) : null
                                ]
                            ], 200);
                        }
                    }
                    $data = [
                        'message' => 'Anda belum melakukan absen masuk hari ini.',
                        'alert' => true,
                        'next' => false,
                    ];
                    return response()->json($data, 200);
                }
                // absen selesai istirahat
                if ($request->type_absen == 3) {
                    if ($absen_istirahat) {
                        if ($absen_selesai_istirahat) {
                            $data = [
                                'message' => 'Anda sudah melakukan absen selesai istirahat.',
                                'alert' => true,
                                'next' => false,
                            ];
                            return response()->json($data, 200);
                        } else {
                            return response()->json([
                                'message' => '',
                                'alert' => false,
                                'next' => true,
                                'coordinate' => [
                                    'latitude' => $user->karyawan->location ? floatval($user->karyawan->location->latitude) : null,
                                    'longitude' => $user->karyawan->location ? floatval($user->karyawan->location->longitude) : null
                                ]
                            ], 200);
                        }
                    }

                    $data = [
                        'message' => 'Anda tidak melakukan absen istirahat.',
                        'alert' => true,
                        'next' => false,
                    ];
                    return response()->json($data, 200);
                }
                // absen pulang
                if ($request->type_absen == 4) {
                    if ($absen_pulang->status_absen == 1) {
                        $data = [
                            'message' => 'Anda sudah melakukan absen pulang.',
                            'alert' => true,
                            'next' => false,
                        ];
                        return response()->json($data, 200);
                    }
                    if ($absen_masuk) {
                        if ($absen_istirahat) {
                            if (!$absen_selesai_istirahat) {
                                $data = [
                                    'message' => 'Anda belum melakukan absen selesai istirahat.',
                                    'alert' => true,
                                    'next' => false,
                                ];
                                return response()->json($data, 200);
                            }
                        }
                        return response()->json([
                            'message' => '',
                            'alert' => false,
                            'next' => true,
                            'coordinate' => [
                                'latitude' => $user->karyawan->location ? floatval($user->karyawan->location->latitude) : null,
                                'longitude' => $user->karyawan->location ? floatval($user->karyawan->location->longitude) : null
                            ]
                        ], 200);
                    }

                    return response()->json([
                        'message' => '',
                        'alert' => false,
                        'next' => true,
                        'coordinate' => [
                            'latitude' => $user->karyawan->location ? floatval($user->karyawan->location->latitude) : null,
                            'longitude' => $user->karyawan->location ? floatval($user->karyawan->location->longitude) : null
                        ]
                    ], 200);
                }
            } else {
                if ($request->type_absen == 1) {
                    return response()->json([
                        'message' => '',
                        'alert' => false,
                        'next' => true,
                        'coordinate' => [
                            'latitude' => $user->karyawan->location ? floatval($user->karyawan->location->latitude) : null,
                            'longitude' => $user->karyawan->location ? floatval($user->karyawan->location->longitude) : null
                        ]
                    ], 200);
                }
                $data = [
                    'message' => 'Anda belum melakukan absen masuk',
                    'alert' => true,
                    'next' => false,
                    'coordinate' => null
                ];
                return response()->json($data, 200);
            }
        } else {
            $data = [
                'message' => 'Anda Tidak Dijinkan Absen.',
                'alert' => true,
                'next' => false,
            ];
            return response()->json($data, 200);
        }
    }

    public function attendanceProccess(Request $request)
    {
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $tanggal = date('Y-m-d 08:15:00');
        $current_date = Carbon::parse($tanggal);
        $waktu_absen = Carbon::now();
        $keterangan = null;
        $status_absen = $current_date->greaterThan($waktu_absen) ? '1' : '0';

        $data = [];
        if ($request->type_absen == 1) {
            $diff = $current_date->diffInMinutes($waktu_absen);
            if (strtotime($waktu_absen) > strtotime($tanggal)) {
                $keterangan = $diff > 15 ? "Terlambat $diff menit" : '';
            }
            $absen_masuk = Absen::whereDate('waktu_absen', date('Y-m-d'))->where(['npk_karyawan' => $user->karyawan->npk, 'type_absen' => 1])->first();

            if (!$absen_masuk) {
                $data = [
                    [
                        'waktu_absen' => $waktu_absen,
                        'type_absen' => $request->type_absen,
                        'status_absen' => $status_absen,
                        'keterangan' => $keterangan,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'nama_lokasi' => $request->nama_lokasi,
                        'npk_karyawan' => $user->karyawan->npk,
                    ],
                    [
                        'waktu_absen' => $waktu_absen,
                        'type_absen' => 4,
                        'status_absen' => '0',
                        'keterangan' => null,
                        'latitude' => 0,
                        'longitude' => 0,
                        'nama_lokasi' => null,
                        'npk_karyawan' => $user->karyawan->npk,
                    ]
                ];
                Absen::insert($data);
            }
        }

        if ($request->type_absen == 4) {
            $absen_pulang = Absen::whereDate('waktu_absen', date('Y-m-d'))->where(['npk_karyawan' => $user->karyawan->npk, 'type_absen' => 4])->orderBy('waktu_absen', 'DESC')->first();
            if ($absen_pulang->status_absen == 0) {
                $data = [
                    'waktu_absen' => $waktu_absen,
                    'type_absen' => $request->type_absen,
                    'status_absen' => '1',
                    'keterangan' => $keterangan,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'nama_lokasi' => $request->nama_lokasi,
                    'npk_karyawan' => $user->karyawan->npk,
                ];
                $absen_pulang->update($data);
            }
        }

        if ($request->type_absen == 2 || $request->type_absen == 3) {
            $uuid = Uuid::uuid4()->toString();
            $break_in_out = Absen::whereDate('waktu_absen', date('Y-m-d'))->where(['npk_karyawan' => $user->karyawan->npk])->whereIn('type_absen', [2, 3])->orderBy('waktu_absen', 'DESC')->first();
            if (!$break_in_out) {
                $data = [
                    'waktu_absen' => $waktu_absen,
                    'type_absen' => $request->type_absen,
                    'status_absen' => '1',
                    'keterangan' => null,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'nama_lokasi' => $request->nama_lokasi,
                    'npk_karyawan' => $user->karyawan->npk,
                ];

                Absen::updateOrCreate(['id' => $uuid], $data);
            }
        }

        return response()->json([
            'error' => false,
            'message' => 'Absen ' . $this->getStatus($request->type_absen) . ' Berhasil dilakukan, ' . $keterangan,
            'data' => $data
        ], 200);
    }

    public function historyAbsen()
    {
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $attendance = Absen::where('npk_karyawan', $user->karyawan->npk)->get();

        if (count($attendance) > 0) {
            return response()->json([
                'error' => false,
                'data' => AttendanceHistoryResource::collection($attendance)
            ], 200);
        }

        return response()->json([
            'error' => false,
            'data' => [],
            'message' => 'Tidak ada absen'
        ], 200);
    }

    public function getStatus($type = 1)
    {
        switch ($type) {
            case 1:
                return 'Masuk';
            case 2:
                return 'Istirahat';
            case 3:
                return 'Selesai Istirahat';
            case 4:
                return 'Pulang';
            case 5:
                return 'Lembur';
            case 6:
                return 'Selesai Lembur';

            default:
                return 'Masuk';
        }
    }
}
