<?php

use App\Http\Controllers\AgenController;
use App\Http\Controllers\AsuransiController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BTTBController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerTruckingController;
use App\Http\Controllers\JadwalKapalController;
use App\Http\Controllers\KapalController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\KondisiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\NSFPController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderTruckingController;
use App\Http\Controllers\PelayaranController;
use App\Http\Controllers\PengirimController;
use App\Http\Controllers\RoleAccessController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SanguSopirController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\SopirController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\TarifAgenController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\TarifPelayaranController;
use App\Http\Controllers\TarifTrukController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\TruckingController;
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
Route::get('test', function () {
    $role = Auth::user()->role_id;
    $access = App\Models\RoleAccess::join('sub_menu','sub_menu.id','=','role_access.sub_menu_id')
    ->join('menu','menu.id','=','sub_menu.menu_id')
    ->where('role_access.role_id',$role)
    ->select('menu.title as label','menu.id','sub_menu.url','sub_menu.title','role_access.role_id','role_access.sub_menu_id')
    ->get()
    ->groupBy('id');
    dd($access);
});
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('admin')->middleware('auth')->group( function(){
    Route::resource('user',UserController::class)->except(['create']);
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
    Route::resource('tarifagen',TarifAgenController::class);
    Route::resource('tarifpelayaran',TarifPelayaranController::class);
    Route::resource('tariftruk',TarifTrukController::class);
    Route::resource('nsfp',NSFPController::class);
    Route::resource('asuransi',AsuransiController::class);
    Route::resource('tagihan',TagihanController::class);
    Route::resource('transaksi',TransaksiController::class);
    Route::resource('role',RoleController::class);
    Route::resource('customertrucking',CustomerTruckingController::class);
    Route::resource('kendaraan',KendaraanController::class);
    Route::resource('sopir',SopirController::class);
    Route::resource('role-access',RoleAccessController::class);
    Route::resource('sangusopir',SanguSopirController::class);
    Route::resource('ordertrucking',OrderTruckingController::class);

    Route::get('laporan/pelayaran', [LaporanController::class,'pelayaran'])->name('laporan.pelayaran');
    Route::get('laporan/tujuan', [LaporanController::class,'tujuan'])->name('laporan.tujuan');
    Route::get('laporan/customer', [LaporanController::class,'customer'])->name('laporan.customer');
    Route::get('laporan/marketing', [LaporanController::class,'marketing'])->name('laporan.marketing');
    Route::get('laporan/cs', [LaporanController::class,'cs'])->name('laporan.cs');

    Route::get('customer-tarif', [CustomerController::class,'tarif'])->name('customer.tarif');
    Route::get('nsfp-cancel', [NSFPController::class,'cancel'])->name('nsfp.cancel');
    Route::post('revisi-nsfp', [NSFPController::class,'revisi'])->name('nsfp.revisi');
    Route::post('tarik-nsfp', [NSFPController::class,'tarik'])->name('nsfp.tarik');
    Route::get('trucking/order',[TruckingController::class,'order'])->name('trucking.order');
    Route::get('keuangan/customer',[KeuanganController::class,'customer'])->name('keuangan.customer');
    Route::get('keuangan/order',[KeuanganController::class,'order'])->name('keuangan.order');
    Route::get('keuangan/ba_kembali',[KeuanganController::class,'ba_kembali'])->name('keuangan.ba_kembali');
    Route::get('keuangan/pre-invoice',[KeuanganController::class,'pre_invoice'])->name('keuangan.pre_invoice');
    Route::get('keuangan/pre-invoic1',[KeuanganController::class,'pre_invoice1'])->name('keuangan.pre_invoice1');
    Route::get('keuangan/laporan-ppn',[KeuanganController::class,'laporanPpn'])->name('keuangan.laporan.ppn');
    Route::post('generate-invoice/{order}',[KeuanganController::class,'generateInvoice'])->name('keuangan.generateInvoice');
    Route::post('import-invoice',[KeuanganController::class,'import'])->name('invoice.import');
    Route::post('export-laporan-ppn',[KeuanganController::class,'PPNExport'])->name('keuangan.ppn.export');
    Route::post('export-asuransi',[AsuransiController::class,'export'])->name('asuransi.export');
    Route::get('ba-kembali',[OrderController::class,'baKembali'])->name('order.ba-kembali');
    Route::get('sj-kembali',[OrderController::class,'sj_kembali'])->name('order.sj-kembali');
    Route::get('order-asuransi',[OrderController::class,'asuransi'])->name('order.asuransi');
    Route::get('invoice',[KeuanganController::class,'invoice'])->name('order.invoice');
    Route::get('cetak/surat-jalan',[CetakController::class,'suratJalan'])->name('cetak.suratJalan');
    Route::get('pdf/surat-jalan',[CetakController::class,'pdfSuratJalan'])->name('cetak.pdf.suratJalan');
    Route::get('cetak/pick-order',[CetakController::class,'pickOrder'])->name('cetak.pickOrder');
    Route::get('cetak/packing-list',[CetakController::class,'packingList'])->name('cetak.packingList');
    Route::get('cetak/packing-list-kubikasi',[CetakController::class,'packingListKubikasi'])->name('cetak.packingList.kubikasi');
    Route::get('cetak/bttb',[CetakController::class,'bttb'])->name('cetak.bttb');
    Route::get('cetak/bttb-kubikasi',[CetakController::class,'bttbKubikasi'])->name('cetak.bttb.kubikasi');
    Route::get('cetak/shipment',[CetakController::class,'shipment'])->name('cetak.shipment');
    Route::get('cetak/invoice',[CetakController::class,'invoice'])->name('cetak.invoice');
    Route::get('cetak/invoice-cont',[CetakController::class,'invoiceCont'])->name('cetak.invoice.cont');
    Route::post('copy-orders/{order}',[OrderController::class,'copy'])->name('order.copy');
    Route::post('si-export',[OrderController::class,'SIExport'])->name('order.export.si');
    Route::post('customer-import',[CustomerController::class,'import'])->name('customer.import');
    Route::post('customer-import-update',[CustomerController::class,'importUpdate'])->name('customer.import.update');
    Route::post('order-import',[OrderController::class,'import'])->name('order.import');
    Route::view('static-invoice', 'admin.print.invoice');

    Route::get('sync-kuli',[SyncController::class,'kuli']);
    Route::get('sync-import',[SyncController::class,'import']);
    Route::get('sync-sync',[SyncController::class,'sync']);
    Route::get('sync-invoice',[SyncController::class,'invoice']);
    Route::get('sync-customer',[SyncController::class,'customerTrucking']);
    Route::get('sync-data',[SyncController::class,'data']);
    Route::get('sync-agen',[SyncController::class,'agen']);
    Route::get('sync-pph',[SyncController::class,'pph']);
    Route::get('sync-menu',[SyncController::class,'menu_link']);
});
// Route::view('test','test');
Route::resource('menu',App\Http\Controllers\MenuController::class);Route::resource('submenu',App\Http\Controllers\SubMenuController::class);
Route::resource('tariftrucking',App\Http\Controllers\TarifTruckingController::class);
