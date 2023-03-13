<?php

use App\Http\Controllers\AgenController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BTTBController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\JadwalKapalController;
use App\Http\Controllers\KapalController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\KondisiController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PelayaranController;
use App\Http\Controllers\PengirimController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\SyncController;
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
    Route::resource('customer',CustomerController::class);
    Route::resource('pelayaran',PelayaranController::class)->except(['create','edit']);
    Route::resource('agen',AgenController::class)->except(['create','edit']);
    Route::resource('truk',TrukController::class)->except(['create','edit']);
    Route::resource('kapal',KapalController::class)->except(['create','edit']);
    Route::resource('jadwalkapal',JadwalKapalController::class)->except(['create','edit']);
    Route::resource('shipment',ShipmentController::class)->except(['create','edit']);
    Route::resource('kondisi',KondisiController::class)->except(['create','edit']);
    Route::resource('satuan',SatuanController::class)->except(['create','edit']);
    Route::resource('lokasi',LokasiController::class)->except(['create','edit']);
    Route::resource('tarif',TarifController::class)->except(['create']);
    Route::resource('barang',BarangController::class);
    Route::resource('order',OrderController::class);
    Route::resource('bttb',BTTBController::class);
    Route::resource('pengirim',PengirimController::class);

    Route::get('keuangan/order',[KeuanganController::class,'order'])->name('keuangan.order');
    Route::get('ba-kembali',[OrderController::class,'baKembali'])->name('order.ba-kembali');
    Route::get('invoice',[OrderController::class,'invoice'])->name('order.invoice');
    Route::get('cetak/surat-jalan',[CetakController::class,'suratJalan'])->name('cetak.suratJalan');
    Route::get('pdf/surat-jalan',[CetakController::class,'pdfSuratJalan'])->name('cetak.pdf.suratJalan');
    Route::get('cetak/pick-order',[CetakController::class,'pickOrder'])->name('cetak.pickOrder');
    Route::get('cetak/packing-list',[CetakController::class,'packingList'])->name('cetak.packingList');
    Route::get('cetak/packing-list-kubikasi',[CetakController::class,'packingListKubikasi'])->name('cetak.packingList.kubikasi');
    Route::get('cetak/bttb',[CetakController::class,'bttb'])->name('cetak.bttb');
    Route::get('cetak/bttb-kubikasi',[CetakController::class,'bttbKubikasi'])->name('cetak.bttb.kubikasi');
    Route::get('cetak/shipment',[CetakController::class,'shipment'])->name('cetak.shipment');
    Route::get('cetak/invoice',[CetakController::class,'invoice'])->name('cetak.invoice');
    Route::post('copy-orders/{order}',[OrderController::class,'copy'])->name('order.copy');
    Route::post('customer-import',[CustomerController::class,'import'])->name('customer.import');
    Route::post('order-import',[OrderController::class,'import'])->name('order.import');
    Route::view('static-invoice', 'admin.print.invoice');

    Route::get('sync-import',[SyncController::class,'import']);
    Route::get('sync-sync',[SyncController::class,'sync']);
});
Route::view('test','test');
Route::resource('tarifagen',App\Http\Controllers\TarifAgenController::class);Route::resource('tarifpelayaran',App\Http\Controllers\TarifPelayaranController::class);Route::resource('tariftruk',App\Http\Controllers\TarifTrukController::class);
Route::resource('nsfp',App\Http\Controllers\NSFPController::class);
