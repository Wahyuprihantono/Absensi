<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Location;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        $validate = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);


        if ($validate->fails()) {
            $respon = [
                'error' => true,
                'status_code' => 401,
                'message' => 'Silahkan isi semua form yang tersedia',
                'messages' => $validate->errors(),
            ];
            return response()->json($respon, 401);
        }

        if (!$user) {
            return response()->json([
                'error' => true,
                'status_code' => 401,
                'message' => 'NPK yang kamu masukkan tidak terdaftar'
            ], 401);
        }

        $credentials = request(['username', 'password']);
        if (!Auth::attempt($credentials)) {
            $respon = [
                'error' => true,
                'status_code' => 401,
                'message' => 'Unathorized, Password Tidak Sesuai',
            ];
            return response()->json($respon, 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            $respon = [
                'error' => true,
                'status_code' => 401,
                'message' => 'Unathorized, password yang kamu masukkan tidak sesuai',
            ];
            return response()->json($respon, 401);
        }

        $tokenResult = $user->createToken('token-auth')->plainTextToken;
        $respon = [
            'error' => false,
            'status_code' => 200,
            'message' => 'Login successfully',
            'data' => [
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => new UserResource($user)
            ]
        ];
        return response()->json($respon, 200);
    }
}
