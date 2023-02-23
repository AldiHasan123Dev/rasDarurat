<?php

use App\Http\Controllers\AgenController;
use App\Http\Controllers\JadwalKapalController;
use App\Http\Controllers\KapalController;
use App\Http\Controllers\KondisiController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\PelayaranController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TrukController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('pelayaran',[PelayaranController::class,'datatable'])->name('pelayaran.data');
Route::post('user',[UserController::class,'datatable'])->name('user.data');
Route::post('pelayaran',[PelayaranController::class,'datatable'])->name('pelayaran.data');
Route::post('agen',[AgenController::class,'datatable'])->name('agen.data');
Route::post('truk',[TrukController::class,'datatable'])->name('truk.data');
Route::post('kapal',[KapalController::class,'datatable'])->name('kapal.data');
Route::post('jadwalkapal',[JadwalKapalController::class,'datatable'])->name('jadwalkapal.data');
Route::post('shipment',[ShipmentController::class,'datatable'])->name('shipment.data');
Route::post('kondisi',[KondisiController::class,'datatable'])->name('kondisi.data');
Route::post('satuan',[SatuanController::class,'datatable'])->name('satuan.data');
Route::post('lokasi',[LokasiController::class,'datatable'])->name('lokasi.data');
Route::post('tarif',[App\Http\Controllers\TarifController::class,'datatable'])->name('tarif.data');Route::post('barang',[App\Http\Controllers\BarangController::class,'datatable'])->name('barang.data');Route::post('order',[App\Http\Controllers\OrderController::class,'datatable'])->name('order.data');