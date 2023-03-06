<?php

use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\JadwalKapalController;
use App\Http\Controllers\Api\TarifController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('customer', [CustomerController::class,'getOne'])->name('api.customer.getOne');
Route::post('jadwal-kapal', [JadwalKapalController::class,'getOne'])->name('api.jadwal-kapal.getOne');
Route::post('tarif', [TarifController::class,'getOne'])->name('api.tarif.getOne');
Route::get('get-pengirim', [CustomerController::class,'getPengirim']);
Route::get('get-barang', [BarangController::class,'getBarang']);
Route::get('get-jadwal-kapal-pelayaran/{id}', [JadwalKapalController::class,'getByPelayaran']);
