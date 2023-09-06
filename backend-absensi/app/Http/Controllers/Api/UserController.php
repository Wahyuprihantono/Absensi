<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Lokasi;
use App\Models\ModelWajah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function addLocation(Request $request)
    {
        $user_id = auth()->user()->id;

        $user = User::find($user_id);
        if (!$user->karyawan->location) {
            $location = Lokasi::create([
                'nama_lokasi' => $request->nama_lokasi,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'lokasi_perusahaan' => 0,
            ]);

            $user->karyawan->update(['lokasi_id' => $location->id]);

            $respon = [
                'error' => false,
                'status_code' => 200,
                'message' => 'Lokasi berhasil ditambah',
                'redirect' => 'HomeScreen'
            ];
            return response()->json($respon, 200);
        }

        $user->karyawan->location->update([
            'nama_lokasi' => $request->nama_lokasi,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'lokasi_perusahaan' => 0,
        ]);

        $respon = [
            'error' => false,
            'status_code' => 200,
            'message' => 'Lokasi Berhasil Diupdate',
            'redirect' => 'HomeScreen'
        ];
        return response()->json($respon, 200);
    }

    public function getUserInfo(Request $request)
    {
        $user_id = auth()->user()->id;
        $user = User::find($user_id);

        $respon = [
            'error' => false,
            'status_code' => 200,
            'data' => new UserResource($user)
        ];
        return response()->json($respon, 200);
    }

    public function uploadFace(Request $request)
    {
        $user_id = auth()->user()->id;
        $user = User::find($user_id);


        ModelWajah::create([
            'key' => $request->key,
            'value' => $request->value,
            'npk_karyawan' => $user->karyawan->npk
        ]);

        $respon = [
            'error' => false,
            'status_code' => 200,
        ];
        return response()->json($respon, 200);
    }

    public function getFace()
    {
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $faces = ModelWajah::where('npk_karyawan', $user->karyawan->npk)->first();

        if ($faces) {
            $respon = [
                'key' =>  $faces->key,
                'value' =>  $faces->value,
            ];
            return response()->json($respon, 200);
        }

        $respon = [
            'key' =>  "",
            'value' =>  "{}",
        ];
        return response()->json($respon, 200);
    }

    public function updatePassword(Request $request)
    {
        $user_id = auth()->user()->id;
        $user = User::find($user_id);

        if (Hash::check($request->oldPassword, $user->password)) {
            $user->update(['password' => Hash::make($request->newPassword)]);
            $respon = [
                'error' => false,
                'status_code' => 200,
                'message' => 'Kata sandi berhasil diperbarui'
            ];
            return response()->json($respon, 200);
        }

        $respon = [
            'error' => false,
            'status_code' => 401,
            'message' => 'Kata sandi sebelumnya tidak sesuai'
        ];
        return response()->json($respon, 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        $respon = [
            'error' => false,
            'status_code' => 200,
            'message' => 'Logout Berhasil'
        ];
        return response()->json($respon, 200);
    }

    public function updateProfile(Request $request)
    {
        $user_id = auth()->user()->id;

        $user = User::find($user_id);
        if (!$request->hasFile('foto_karyawan')) {
            return response()->json(['upload_file_not_found'], 400);
        }
        $file = $request->file('foto_karyawan');
        if (!$file->isValid()) {
            return response()->json(['invalid_file_upload'], 400);
        }
        $file = $request->foto_karyawan->store('foto_karyawan', 'public');

        $user->update(['profile_photo_path' => $file]);
        $respon = [
            'error' => false,
            'status_code' => 200,
            'message' => 'Profile berhasil diubah',
            'data' => [
                'foto_karyawan' => asset('storage/' . $file),
            ]

        ];
        return response()->json($respon, 200);
    }
}
