<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/login', [AuthController::class, 'login'])->name('client.login');


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('input-lokasi', [UserController::class, 'addLocation'])->name('lokasi');
    Route::get('get-user', [UserController::class, 'getUserInfo'])->name('get.user');
    Route::post('update-password', [UserController::class, 'updatePassword'])->name('update.password');
    Route::post('logout', [UserController::class, 'logout'])->name('logout.user');
    Route::post('update-profile', [UserController::class, 'updateProfile'])->name('update.profile');
    Route::post('cek-absen', [AttendanceController::class, 'checkAttendance'])->name('cek-absen');
    Route::post('absen', [AttendanceController::class, 'attendanceProccess'])->name('absen');
    Route::post('upload_face', [UserController::class, 'uploadFace'])->name('upload.face');
    Route::get('get_faces', [UserController::class, 'getFace'])->name('get.face');
    Route::get('riwayat-absen', [AttendanceController::class, 'historyAbsen'])->name('riwayat.absensi.api');
});
