<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanAbsenController;
use App\Http\Livewire\Absen;
use App\Http\Livewire\CrudGenerator;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Employee;
use App\Http\Livewire\Location;
use App\Http\Livewire\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});


Route::post('login', [AuthController::class, 'login'])->name('admin.login');
Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    // Crud Generator Route
    Route::get('crud-generator', CrudGenerator::class)->name('crud.generator');
    Route::post('laporan-absensi', [LaporanAbsenController::class, 'cetak_pdf'])->name('laporan.absensi');

    // App Route
    Route::get('dashboard', Dashboard::class)->name('dashboard');

    // Master data
    Route::get('data-lokasi', Location::class)->name('data.lokasi');
    Route::get('data-karyawan', Employee::class)->name('data.karyawan');
    Route::get('data-absen', Absen::class)->name('data.absen');
    Route::get('log', Log::class)->name('log');
});
