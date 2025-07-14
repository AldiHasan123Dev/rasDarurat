<?php

use App\Http\Controllers\AgenController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\JadwalKapalController;
use App\Http\Controllers\KapalController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\KondisiController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\PelayaranController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TrukController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('pelayaran',[PelayaranController::class,'datatable'])->name('pelayaran.data');
Route::post('customer',[CustomerController::class,'datatable'])->name('customer.data');
Route::post('uservaleg55',[UserController::class,'datatable'])->name('uservaleg55.data');
Route::post('pelayaran',[PelayaranController::class,'datatable'])->name('pelayaran.data');
Route::post('agen',[AgenController::class,'datatable'])->name('agen.data');
Route::post('truk',[TrukController::class,'datatable'])->name('truk.data');
Route::post('kapal',[KapalController::class,'datatable'])->name('kapal.data');
Route::post('jadwalkapal',[JadwalKapalController::class,'datatable'])->name('jadwalkapal.data');
Route::post('shipment',[ShipmentController::class,'datatable'])->name('shipment.data');
Route::post('kondisi',[KondisiController::class,'datatable'])->name('kondisi.data');
Route::post('satuan',[SatuanController::class,'datatable'])->name('satuan.data');
Route::post('lokasi',[LokasiController::class,'datatable'])->name('lokasi.data');
Route::get('invoice',[KeuanganController::class,'invoiceTable'])->name('invoice.data');
Route::post('tarif',[App\Http\Controllers\TarifController::class,'datatable'])->name('tarif.data');Route::post('barang',[App\Http\Controllers\BarangController::class,'datatable'])->name('barang.data');Route::post('order',[App\Http\Controllers\OrderController::class,'datatable'])->name('order.data');Route::post('bttb',[App\Http\Controllers\BTTBController::class,'datatable'])->name('bttb.data');
Route::post('pengirim',[App\Http\Controllers\PengirimController::class,'datatable'])->name('pengirim.data');Route::post('tarifagen',[App\Http\Controllers\TarifAgenController::class,'datatable'])->name('tarifagen.data');Route::post('tarifpelayaran',[App\Http\Controllers\TarifPelayaranController::class,'datatable'])->name('tarifpelayaran.data');Route::post('tariftruk',[App\Http\Controllers\TarifTrukController::class,'datatable'])->name('tariftruk.data');Route::post('nsfp',[App\Http\Controllers\NSFPController::class,'datatable'])->name('nsfp.data');Route::post('asuransi',[App\Http\Controllers\AsuransiController::class,'datatable'])->name('asuransi.data');Route::post('tagihan',[App\Http\Controllers\tagihanController::class,'datatable'])->name('tagihan.data');Route::post('transaksi',[App\Http\Controllers\TransaksiController::class,'datatable'])->name('transaksi.data');Route::post('role',[App\Http\Controllers\RoleController::class,'datatable'])->name('role.data');
Route::post('customertrucking',[App\Http\Controllers\CustomerTruckingController::class,'datatable'])->name('customertrucking.data');Route::post('kendaraan',[App\Http\Controllers\KendaraanController::class,'datatable'])->name('kendaraan.data');Route::post('sopir',[App\Http\Controllers\SopirController::class,'datatable'])->name('sopir.data');Route::post('sangusopir',[App\Http\Controllers\SanguSopirController::class,'datatable'])->name('sangusopir.data');Route::post('ordertrucking',[App\Http\Controllers\OrderTruckingController::class,'datatable'])->name('ordertrucking.data');Route::post('menu',[App\Http\Controllers\MenuController::class,'datatable'])->name('menu.data');Route::post('submenu',[App\Http\Controllers\SubMenuController::class,'datatable'])->name('submenu.data');Route::post('tariftrucking',[App\Http\Controllers\TarifTruckingController::class,'datatable'])->name('tariftrucking.data');Route::post('tagihantrucking',[App\Http\Controllers\TagihanTruckingController::class,'datatable'])->name('tagihantrucking.data');Route::post('thc',[App\Http\Controllers\THCController::class,'datatable'])->name('thc.data');Route::post('lss',[App\Http\Controllers\LSSController::class,'datatable'])->name('lss.data');Route::post('lain',[App\Http\Controllers\LainController::class,'datatable'])->name('lain.data');Route::post('jasakirim',[App\Http\Controllers\JasaKirimController::class,'datatable'])->name('jasakirim.data');Route::post('neraca',[App\Http\Controllers\NeracaController::class,'datatable'])->name('neraca.data');Route::post('account',[App\Http\Controllers\AccountController::class,'datatable'])->name('account.data');Route::post('subaccount',[App\Http\Controllers\SubaccountController::class,'datatable'])->name('subaccount.data');Route::post('coa',[App\Http\Controllers\COAController::class,'datatable'])->name('coa.data');Route::post('templatejurnal',[App\Http\Controllers\TemplateJurnalController::class,'datatable'])->name('templatejurnal.data');Route::post('templatejurnalitem',[App\Http\Controllers\TemplateJurnalItemController::class,'datatable'])->name('templatejurnalitem.data');Route::post('jurnal',[App\Http\Controllers\JurnalController::class,'datatable'])->name('jurnal.data');Route::post('hutangagen',[App\Http\Controllers\HutangAgenController::class,'datatable'])->name('hutangagen.data');Route::post('hutangpelayaran',[App\Http\Controllers\HutangPelayaranController::class,'datatable'])->name('hutangpelayaran.data');Route::post('port',[App\Http\Controllers\PortController::class,'datatable'])->name('port.data');Route::post('setting',[App\Http\Controllers\SettingController::class,'datatable'])->name('setting.data');