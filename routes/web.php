<?php

use App\Http\Controllers\AgenController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BTTBController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\JadwalKapalController;
use App\Http\Controllers\KapalController;
use App\Http\Controllers\KondisiController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PelayaranController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\TrukController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('admin')->middleware('auth')->group( function(){
    Route::resource('user',UserController::class)->except(['create','edit']);
    Route::resource('customer',CustomerController::class)->except(['create','edit']);
    Route::resource('pelayaran',PelayaranController::class)->except(['create','edit']);
    Route::resource('agen',AgenController::class)->except(['create','edit']);
    Route::resource('truk',TrukController::class)->except(['create','edit']);
    Route::resource('kapal',KapalController::class)->except(['create','edit']);
    Route::resource('jadwalkapal',JadwalKapalController::class)->except(['create','edit']);
    Route::resource('shipment',ShipmentController::class)->except(['create','edit']);
    Route::resource('kondisi',KondisiController::class)->except(['create','edit']);
    Route::resource('satuan',SatuanController::class)->except(['create','edit']);
    Route::resource('lokasi',LokasiController::class)->except(['create','edit']);
    Route::resource('tarif',TarifController::class)->except(['create','edit']);
    Route::resource('barang',BarangController::class);
    Route::resource('order',OrderController::class);
    Route::resource('bttb',BTTBController::class);

    Route::get('cetak/surat-jalan',[CetakController::class,'suratJalan'])->name('cetak.suratJalan');
    Route::get('pdf/surat-jalan',[CetakController::class,'pdfSuratJalan'])->name('cetak.pdf.suratJalan');
    Route::get('cetak/pick-order',[CetakController::class,'pickOrder'])->name('cetak.pickOrder');
    Route::get('cetak/packing-list',[CetakController::class,'packingList'])->name('cetak.packingList');
    Route::get('cetak/packing-list-kubikasi',[CetakController::class,'packingListKubikasi'])->name('cetak.packingList.kubikasi');
    Route::get('cetak/bttb',[CetakController::class,'bttb'])->name('cetak.bttb');
    Route::get('cetak/bttb-kubikasi',[CetakController::class,'bttbKubikasi'])->name('cetak.bttb.kubikasi');
    Route::get('cetak/shipment',[CetakController::class,'shipment'])->name('cetak.shipment');
    Route::post('copy-order/{order}',[OrderController::class,'copy'])->name('order.copy');
});
