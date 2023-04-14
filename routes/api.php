<?php

use App\Http\Controllers\Api\AsuransiController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\BTTBController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\JadwalKapalController;
use App\Http\Controllers\Api\NSFPController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderTruckingController;
use App\Http\Controllers\Api\SanguSopirController;
use App\Http\Controllers\Api\TagihanController;
use App\Http\Controllers\Api\TagihanTruckingController;
use App\Http\Controllers\Api\TarifController;
use App\Http\Controllers\Api\TarifTruckingController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\PelayaranController;
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

Route::post('generate-nsfp', [NSFPController::class,'generate'])->name('api.nsfp.generate');
Route::post('nsfp', [NSFPController::class,'store'])->name('api.nsfp.store');
Route::post('customer', [CustomerController::class,'getOne'])->name('api.customer.getOne');
Route::post('customer-update', [CustomerController::class,'update'])->name('api.customer.update');
Route::post('check-customer', [CustomerController::class,'getCustomer'])->name('api.customer.getCustomer');
Route::post('jadwal-kapal', [JadwalKapalController::class,'getOne'])->name('api.jadwal-kapal.getOne');
Route::post('tarif', [TarifController::class,'getOne'])->name('api.tarif.getOne');
Route::post('sangu-sopir', [SanguSopirController::class,'getSangu'])->name('api.sangusopir.getSangu');
Route::post('tarif-trucking', [TarifTruckingController::class,'createOrUpdate'])->name('api.tariftrucking.createorupdate');
Route::post('tarif-trucking-delete', [TarifTruckingController::class,'delete'])->name('api.tariftrucking.delete');
Route::post('transaksi-update', [TransaksiController::class,'update'])->name('api.transaksi.update');
Route::post('tagihan-trucking', [TagihanTruckingController::class,'store'])->name('api.tagihan-trucking.store');
Route::delete('tagihan-trucking/{tagihan}', [TagihanTruckingController::class,'destroy'])->name('api.tagihan-trucking.destroy');
Route::get('tagihan-trucking/{id}', [TagihanTruckingController::class,'getOne'])->name('api.tagihan-trucking.getOne');
Route::post('tagihan', [TagihanController::class,'store'])->name('api.tagihan.store');
Route::delete('tagihan/{tagihan}', [TagihanController::class,'destroy'])->name('api.tagihan.destroy');
Route::get('tagihan/{id}', [TagihanController::class,'getOne'])->name('api.tagihan.getOne');
Route::get('get-asuransi-pelayaran/{pelayaran_id}', [AsuransiController::class,'getAsuransiByPelayaran'])->name('api.asuransi.getByPelayaran');
Route::get('get-pengirim', [CustomerController::class,'getPengirim']);
Route::get('get-barang', [BarangController::class,'getBarang']);
Route::get('get-nama-barang', [BarangController::class,'getNama']);
Route::get('get-nama-satuan', [BarangController::class,'getNamaSatuan']);
Route::get('get-order-nopol/{nopol}', [OrderController::class,'getOrderwithNopol']);
Route::post('update-order', [OrderController::class,'update']);
Route::get('get-order', [OrderController::class,'index']);
Route::get('get-transaksi', [TransaksiController::class,'index']);
Route::get('get-order-ba-kembali', [OrderController::class,'ba_kembali']);
Route::get('get-order-pre-invoice', [OrderController::class,'pre_invoice']);
Route::get('get-jadwal-kapal-pelayaran/{id}', [JadwalKapalController::class,'getByPelayaran']);
Route::resource('api-bttb',BTTBController::class);
Route::delete('api-bttb-delete',[BTTBController::class,'delete']);
Route::delete('delete-order-trucking',[OrderTruckingController::class,'delete'])->name('order-trucking.delete');
Route::resource('api-tarif',TarifController::class);
Route::get('pelayaran-data',[PelayaranController::class,'data'])->name('api.pelayaran.data');

Route::get('jqgrid-order',[OrderController::class,'jqgrid'])->name('jqgrid.order');
