<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AgenController;
use App\Http\Controllers\AsuransiController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BTTBController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\COAController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerTruckingController;
use App\Http\Controllers\EstimasiController;
use App\Http\Controllers\JadwalKapalController;
use App\Http\Controllers\JasaKirimController;
use App\Http\Controllers\KapalController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\KondisiController;
use App\Http\Controllers\LainController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\LSSController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NeracaController;
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
use App\Http\Controllers\SubaccountController;
use App\Http\Controllers\SubMenuController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\TagihanTruckingController;
use App\Http\Controllers\TarifAgenController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\TarifPelayaranController;
use App\Http\Controllers\TarifTruckingController;
use App\Http\Controllers\TarifTrukController;
use App\Http\Controllers\THCController;
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
Route::get('/logs', function () {
    $logPath = storage_path('/logs/laravel.log');
    $logs = fopen($logPath , "r") or die("Unable to open file!");
    return response(stream_get_contents($logs));
});
Route::get('test', function () {
    $num = round(17288100.49);
    dd($num);
});
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('admin')->middleware(['auth','protect'])->group( function(){
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
    Route::resource('menu',MenuController::class);
    Route::resource('submenu',SubMenuController::class);
    Route::resource('tariftrucking',TarifTruckingController::class);
    Route::resource('tagihantrucking',TagihanTruckingController::class);
    Route::resource('thc',THCController::class);
    Route::resource('lss',LSSController::class);
    Route::resource('lain',LainController::class);
    Route::resource('jasakirim',JasaKirimController::class);
    Route::resource('neraca',NeracaController::class);
    Route::resource('account',AccountController::class);
    Route::resource('subaccount',SubaccountController::class);
    Route::resource('coa',COAController::class);


    Route::get('marketing/{marketing}', [OrderController::class,'index'])->name('order.index.marketing');
    Route::get('laporan/pelayaran', [LaporanController::class,'pelayaran'])->name('laporan.pelayaran');
    Route::get('laporan/tujuan', [LaporanController::class,'tujuan'])->name('laporan.tujuan');
    Route::get('laporan/customer', [LaporanController::class,'customer'])->name('laporan.customer');
    Route::get('laporan/marketing', [LaporanController::class,'marketing'])->name('laporan.marketing');
    Route::get('laporan/cs', [LaporanController::class,'cs'])->name('laporan.cs');
    Route::get('laporan/trucking', [LaporanController::class,'trucking'])->name('laporan.trucking');
    Route::get('laporan/sopir', [LaporanController::class,'sopir'])->name('laporan.sopir');

    Route::get('customer-tarif', [CustomerController::class,'tarif'])->name('customer.tarif');
    Route::get('nsfp-cancel', [NSFPController::class,'cancel'])->name('nsfp.cancel');
    Route::post('revisi-nsfp', [NSFPController::class,'revisi'])->name('nsfp.revisi');
    Route::post('tarik-nsfp', [NSFPController::class,'tarik'])->name('nsfp.tarik');
    Route::post('delete-all', [NSFPController::class,'deleteAll'])->name('nsfp.delete.all');
    Route::get('trucking/order',[TruckingController::class,'order'])->name('trucking.order');
    Route::get('trucking/monitoring',[TruckingController::class,'monitoring'])->name('trucking.monitoring');
    Route::get('trucking/pre-invoice',[TruckingController::class,'preInvoice'])->name('trucking.pre-invoice');
    Route::get('trucking/totalan-sopir',[TruckingController::class,'totalan_sopir'])->name('trucking.totalan_sopir');
    Route::get('trucking/totalan-sopir/invoice',[TruckingController::class,'cetak_invoice_sopir'])->name('trucking.cetak_invoice.totalan_sopir');
    Route::post('trucking/invoice/totalan-sopir',[TruckingController::class,'totalan_sopir_invoice'])->name('trucking.invoice.total_sopir');
    Route::post('trucking/totalan-sopir',[TruckingController::class,'generate_totalan_sopir'])->name('trucking.generate.total_sopir');
    Route::get('trucking/cetak-invoice/get',[TruckingController::class,'cetak_invoice_get'])->name('trucking.cetak_get.invoice');
    Route::get('trucking/invoice',[TruckingController::class,'invoice'])->name('trucking.invoice');
    Route::get('trucking/invoice-sopir',[TruckingController::class,'invoice_sopir'])->name('trucking.invoice_sopir');
    Route::post('trucking/cetak-invoice',[TruckingController::class,'cetak_invoice'])->name('trucking.cetak.invoice');
    Route::post('trucking/generate-invoice',[TruckingController::class,'generate_invoice'])->name('trucking.generate.invoice');
    Route::get('keuangan/fee-cust',[KeuanganController::class,'fee_cust'])->name('keuangan.fee_cust');
    Route::post('keuangan/fee-cust-bayar',[KeuanganController::class,'fee_cust_bayar'])->name('keuangan.fee_cust.bayar');
    Route::get('keuangan/customer',[KeuanganController::class,'customer'])->name('keuangan.customer');
    Route::get('keuangan/order',[KeuanganController::class,'order'])->name('keuangan.order');
    Route::get('keuangan/ba_kembali',[KeuanganController::class,'ba_kembali'])->name('keuangan.ba_kembali');
    Route::get('keuangan/pre-invoice',[KeuanganController::class,'pre_invoice'])->name('keuangan.pre_invoice');
    Route::get('keuangan/pre-invoic1',[KeuanganController::class,'pre_invoice1'])->name('keuangan.pre_invoice1');
    Route::get('keuangan/laporan-ppn',[KeuanganController::class,'laporanPpn'])->name('keuangan.laporan.ppn');
    Route::post('generate-invoice/{order}',[KeuanganController::class,'generateInvoice'])->name('keuangan.generateInvoice');
    Route::post('import-invoice',[KeuanganController::class,'import'])->name('invoice.import');
    Route::post('export-laporan-ppn',[KeuanganController::class,'PPNExport'])->name('keuangan.ppn.export');
    Route::post('export-order',[OrderController::class,'export'])->name('order.export');
    Route::post('export-order/ba_kembali',[OrderController::class,'export_ba_kembali'])->name('order.export.ba_kembali');
    Route::post('export-order-trucking',[OrderTruckingController::class,'export'])->name('ordertrucking.export');
    Route::post('export-asuransi',[AsuransiController::class,'export'])->name('asuransi.export');
    Route::post('tarik-asuransi',[AsuransiController::class,'tarik'])->name('asuransi.tarik');
    Route::get('ba-kembali',[OrderController::class,'baKembali'])->name('order.ba-kembali');
    Route::get('closing',[OrderController::class,'closing'])->name('order.closing');
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
    Route::get('cetak/dooring',[CetakController::class,'dooring'])->name('cetak.dooring');
    Route::get('cetak/invoice',[CetakController::class,'invoice'])->name('cetak.invoice');
    Route::get('cetak/invoice-cont',[CetakController::class,'invoiceCont'])->name('cetak.invoice.cont');
    Route::post('copy-orders/{order}',[OrderController::class,'copy'])->name('order.copy');
    Route::post('si-export',[OrderController::class,'SIExport'])->name('order.export.si');
    Route::post('customer-import',[CustomerController::class,'import'])->name('customer.import');
    Route::post('customer-import-update',[CustomerController::class,'importUpdate'])->name('customer.import.update');
    Route::post('order-import',[OrderController::class,'import'])->name('order.import');
    Route::get('estimasi-biaya',[EstimasiController::class,'biaya'])->name('estimasi.biaya');

    Route::get('sync-kuli',[SyncController::class,'kuli']);
    Route::get('sync-import',[SyncController::class,'import']);
    Route::get('sync-kapal',[SyncController::class,'kapal']);
    Route::get('sync-sync',[SyncController::class,'sync']);
    Route::get('sync-invoice',[SyncController::class,'invoice']);
    Route::get('sync-customer',[SyncController::class,'customerTrucking']);
    Route::get('sync-data',[SyncController::class,'data']);
    Route::get('sync-agen',[SyncController::class,'agen']);
    Route::get('sync-pph',[SyncController::class,'pph']);
    Route::get('sync-menu',[SyncController::class,'menu_link']);
    Route::get('sync-menu-backup',[SyncController::class,'menu_link_backup']);
    Route::get('sync-menu-ras',[SyncController::class,'menu_link_ras']);
    Route::get('sync-order-menu',[SyncController::class,'orderMenu']);
    Route::get('sync-transaksi',[SyncController::class,'transaksi']);
    Route::get('sync-trucking',[SyncController::class,'trucking']);
    Route::get('sync-same',[SyncController::class,'sameData']);
    Route::get('sync-pull',[SyncController::class,'pull']);
});
// Route::view('test','test');
