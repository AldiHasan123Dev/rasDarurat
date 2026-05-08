<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AgenController;
use App\Http\Controllers\Api\OmsetController;
use App\Http\Controllers\AsuransiController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BTTBController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\COAController;
use App\Models\Agen;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerTruckingController;
use App\Http\Controllers\EstimasiController;
use App\Http\Controllers\HutangAgenController;
use App\Http\Controllers\HutangPelayaranController;
use App\Http\Controllers\JadwalKapalController;
use App\Http\Controllers\JasaKirimController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\KapalController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\KondisiController;
use App\Http\Controllers\LainController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\LSSController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MutasiTotalanSopirController;
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
use App\Http\Controllers\TemplateJurnalController;
use App\Http\Controllers\TemplateJurnalItemController;
use App\Http\Controllers\THCController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\TruckingController;
use App\Models\Customer;
use App\Models\TarifAgen;
use App\Http\Controllers\TrukController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderBiayaController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\UpdateDataController;
use App\Http\Controllers\LapPelayaranController;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use Google\Client;
use Google\Service\Drive;

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
    $setting = Setting::first();
    if ($setting->short_name == 'ALB') {
        return redirect('login');
    }
    return redirect('login');
});
Route::get('/logs', function () {
    $logPath = storage_path('/logs/laravel.log');
    $logs = fopen($logPath, "r") or die("Unable to open file!");
    return response(stream_get_contents($logs));
});
Route::get('/upload', function () {
    try {
        $client = new Client();
        $client->setAuthConfig(public_path('credentials.json'));
        $client->addScope(Drive::DRIVE);
        $driveService = new Drive($client);
        $file = public_path('logo.png');
        $fileName = basename($file);
        $mimeType = mime_content_type($file);

        $fileMetadata = new Drive\DriveFile(
            array('name' => $fileName, 'parents' => ['11CjKzIs8ndfv_V6jhIDFy4y99jsUuYYN'])
        );
        $content = file_get_contents($file);
        $file = $driveService->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart',
            'fields' => 'id'
        ));
        printf("File ID: %s\n", $file->id);
        return $file->id;
    } catch (Exception $e) {
        echo "Error Message: " . $e;
    }
});

Route::get('test', function () {
    $data = Storage::allFiles('public/RAS');
    $input = date('Y-m-d');
    $result = array_filter($data, function ($item) use ($input) {
        if (stripos($item, $input) !== false) {
            return true;
        }
        return false;
    });
    $file = $result[0];
    $file = str_replace('public/', '', $file);
});

Auth::routes(['register' => false]);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('bulk-update', function () {
    return view('bulk-update');
});
Route::get('buku-besar', function () {
    $file = Storage::path('public/buku-besar.xlsx');
    return response()->download($file);
})->name('download.buku-besar');

Route::post('update-jurnal', [UpdateDataController::class, 'jurnal'])->name('update.jurnal');

Route::prefix('admin')->middleware(['auth', 'protect'])->group(function () {
    Route::resource('uservaleg55', UserController::class)->except(['create']);
    Route::resource('customer', CustomerController::class);
    Route::resource('pelayaran', PelayaranController::class)->except(['create', 'edit']);
    Route::resource('agen', AgenController::class)->except(['create', 'edit']);
    Route::resource('truk', TrukController::class)->except(['create', 'edit']);
    Route::resource('kapal', KapalController::class)->except(['create', 'edit']);
    Route::resource('jadwalkapal', JadwalKapalController::class)->except(['create', 'edit']);
    Route::resource('shipment', ShipmentController::class)->except(['create', 'edit']);
    Route::resource('kondisi', KondisiController::class)->except(['create', 'edit']);
    Route::resource('satuan', SatuanController::class)->except(['create', 'edit']);
    Route::resource('lokasi', LokasiController::class)->except(['create', 'edit']);
    Route::resource('tarif', TarifController::class)->except(['create']);
    Route::resource('barang', BarangController::class);
    Route::resource('order', OrderController::class);
    Route::resource('bttb', BTTBController::class);
    Route::resource('pengirim', PengirimController::class);
    Route::resource('tarifagen', TarifAgenController::class);
    Route::resource('tarifpelayaran', TarifPelayaranController::class);
    Route::resource('tariftruk', TarifTrukController::class);
    Route::resource('nsfp', NSFPController::class);
    Route::resource('asuransi', AsuransiController::class);
    Route::resource('tagihan', TagihanController::class);
    Route::resource('transaksi', TransaksiController::class);
    Route::resource('role', RoleController::class);
    Route::resource('customertrucking', CustomerTruckingController::class);
    Route::resource('kendaraan', KendaraanController::class);
    Route::resource('sopir', SopirController::class);
    Route::resource('role-access', RoleAccessController::class);
    Route::resource('sangusopir', SanguSopirController::class);
    Route::resource('ordertrucking', OrderTruckingController::class);
    Route::put('/admin/ordertrucking/mass-update-sj', [OrderTruckingController::class, 'massUpdateSJ'])
    ->name('ordertrucking.massUpdateSJ');
    Route::resource('menu', MenuController::class);
    Route::post('/kendaraan/mass-update', [KendaraanController::class, 'massUpdate'])->name('kendaraan.mass-update');
    Route::resource('submenu', SubMenuController::class);
    Route::resource('tariftrucking', TarifTruckingController::class);
    Route::resource('tagihantrucking', TagihanTruckingController::class);
    Route::resource('thc', THCController::class);
    Route::resource('lss', LSSController::class);
    Route::resource('lain', LainController::class);
    Route::resource('jasakirim', JasaKirimController::class);
    Route::resource('neraca', NeracaController::class);
    Route::resource('account', AccountController::class);
    Route::resource('subaccount', SubaccountController::class);
    Route::resource('coa', COAController::class);
    Route::resource('templatejurnal', TemplateJurnalController::class);
    Route::resource('templatejurnalitem', TemplateJurnalItemController::class);
    Route::resource('jurnal', JurnalController::class)->except('edit');
    Route::resource('hutang-agen', HutangAgenController::class);
    Route::resource('hutang-pelayaran', HutangPelayaranController::class)->except(['show']);
    Route::resource('mutasi-totalan-sopir', MutasiTotalanSopirController::class);
    Route::resource('port', PortController::class);
    Route::post('/tarif/datatable1', [TarifController::class, 'datatable1'])->name('tarif.datatable1');
    Route::get('order-marketing',[OrderController::class,'orderMarketing'])->name('order.order_marketing');
    Route::get('data-customer', [CustomerController::class, 'data_customer'])->name('data-customer.list');
    Route::get('tarif/edit-marketing/{tarif}', [TarifController::class, 'editMarketing'])->name('tarif.edit_marketing');
    Route::get('data-harga-of', [LapPelayaranController::class, 'data'])->name('data-lap-pelayaran.list');
    Route::post('harga-of/store', [LapPelayaranController::class, 'store'])->name('lap-pelayaran.store');
    Route::get('/harga-of/show', [LapPelayaranController::class, 'show'])->name('lap-pelayaran.show');
    Route::post('/harga-of/update/{id}', [LapPelayaranController::class, 'update'])->name('lap-pelayaran.update');
    Route::get('hutang-agen-list', [HutangAgenController::class, 'list'])->name('hutang-agen.list');
    Route::get('hutang-agen-print', [HutangAgenController::class, 'print'])->name('hutang-agen.print');
    Route::post('hutang-agen/draf', [HutangAgenController::class, 'draf'])->name('hutang-agen.draf');
    Route::post('hutang-agen/jurnal', [HutangAgenController::class, 'generate_jurnal'])->name('hutang-agen.jurnal');
    Route::post('hutang-pelayaran/cetak-voucher-post', [HutangPelayaranController::class, 'cetak_invoice'])->name('hutang-pelayaran.cetak.voucher');
    Route::get('hutang-pelayaran/cetak-voucher-get', [HutangPelayaranController::class, 'cetak_invoice_get'])->name('hutang-pelayaran.cetak.voucher.get');

    Route::post('hutang-pelayaran/delete', [HutangPelayaranController::class, 'delete'])->name('hutang-pelayaran.delete');
    Route::post('hutang-pelayaran/tarik', [HutangPelayaranController::class, 'tarik'])->name('hutang-pelayaran.tarik');
    Route::get('hutang-pelayaran-print', [HutangPelayaranController::class, 'print'])->name('hutang-pelayaran.print');
    Route::get('hutang-pelayaran-cetak', [HutangPelayaranController::class, 'cetak'])->name('hutang-pelayaran.cetak');
    Route::get('get-data',[JadwalKapalController::class,'jqgrid'])->name('jqgrid.jadwalkapal');

    Route::get('marketing/{marketing}', [OrderController::class, 'index'])->name('order.index.marketing');
    Route::get('trucking/monitor_biaya_truck', [OrderTruckingController::class, 'monitoring_biaya_truck'])->name('monitoringBiayaTruck');
    Route::post('trucking/monitor_biaya_truck/update-sangu-kuli', [OrderTruckingController::class, 'updateSangu'])->name('monitoringBiayaTruck.update');
    Route::post('trucking/monitor_biaya_truck/update-tb-tl', [OrderTruckingController::class, 'updateTbtl'])->name('monitoringBiayaTruck.update1');
    Route::post('trucking/monitor_biaya_truck/update-stappel', [OrderTruckingController::class, 'updateStappel '])->name('monitoringBiayaTruck.update2');
    Route::get('laporan/pelayaran', [LaporanController::class, 'pelayaran'])->name('laporan.pelayaran');
    Route::get('laporan/tujuan', [LaporanController::class, 'tujuan'])->name('laporan.tujuan');
    Route::get('laporan/customer', [LaporanController::class, 'customer'])->name('laporan.customer');
    Route::get('laporan/marketing', [LaporanController::class, 'marketing'])->name('laporan.marketing');
    Route::get('laporan/cs', [LaporanController::class, 'cs'])->name('laporan.cs');
    Route::get('laporan/trucking', [LaporanController::class, 'trucking'])->name('laporan.trucking');
    Route::get('laporan/sopir', [LaporanController::class, 'sopir'])->name('laporan.sopir');
    Route::get('laporan/invoice', [LaporanController::class, 'invoice'])->name('laporan.invoice');
    Route::get('laporan/dashmonitor', [LaporanController::class, 'dashmonitor'])->name('laporan.dashmonitor');
    Route::get('laporan/omset', [LaporanController::class, 'omset'])->name('laporan.omset');
    Route::get('laporan/omset-customer', [LaporanController::class, 'omset_customer'])->name('laporan.omset_customer');
    Route::get('laporan/pra-omset', [LaporanController::class, 'praomset'])->name('laporan.praomset');
    Route::get('laporan/omset-marketing', [LaporanController::class, 'omsetMarketing'])->name('laporan.omset_marketing');
    Route::get('lap-outstanding-trucking', [LaporanController::class, 'lapOutstandingTrucking'])->name('lap_outstanding.trucking');
    Route::get('laporan/omset-trucking', [LaporanController::class, 'omset_trucking'])->name('laporan.omset_trucking');
    Route::post('/kunci-jurnal/toggle', [JurnalController::class, 'toggle'])->name('kunci-jurnal.toggle');
    Route::get('laporan/tujuan/ajax', [LaporanController::class, 'tujuanAjax'])->name('laporan.tujuan.ajax');
    Route::post('/jurnal/export=jurnal-code-excel', [JurnalController::class, 'exportExcel'])->name('jurnal.exportExcel');

    Route::get('data-outstanding-trucking', [LaporanController::class, 'data_outstanding_trucking'])->name('data-outstanding.trucking');
    Route::get('customer-tarif', [CustomerController::class, 'tarif'])->name('customer.tarif');
    Route::get('customer-tarif-marketing', [CustomerController::class, 'tarifMarketing'])->name('customer.tarif_marketing');
    Route::get('harga-of', [LapPelayaranController::class, 'index'])->name('lap.pelayaran');
    Route::get('kunci-jurnal', [JurnalController::class, 'kunci_jurnal'])->name('kunci.jurnal');
    Route::get('order-blum-invoice', [OrderController::class,'order_blum_inv'])->name('order_blum_inv');
    Route::get('nsfp-cancel', [NSFPController::class, 'cancel'])->name('nsfp.cancel');
    Route::post('revisi-nsfp', [NSFPController::class, 'revisi'])->name('nsfp.revisi');
    Route::post('revisi-non-nsfp', [NSFPController::class, 'revisi_non_faktur'])->name('nsfp.revisi.non');
    Route::post('tarik-nsfp', [NSFPController::class, 'tarik'])->name('nsfp.tarik');
    Route::post('delete-all', [NSFPController::class, 'deleteAll'])->name('nsfp.delete.all');
    Route::get('trucking/order', [TruckingController::class, 'order'])->name('trucking.order');
    Route::post('jasa-kirim-sync-jurnal', [JasaKirimController::class, 'syncJurnal'])->name('jasakirim.sync.jurnal');
    Route::post('jasa-kirim-sync', [JasaKirimController::class, 'syncNominal'])->name('jasakirim.sync');
    Route::post('jasa-kirim-sync-data', [JasaKirimController::class, 'syncData'])->name('jasakirim.sync.data');
    Route::get('draf-jurnal-jasa-kirim', [JasaKirimController::class, 'jurnal'])->name('jasakirim.draf.jurnal');
    Route::post('draf-jurnal-jasa-kirim', [JasaKirimController::class, 'generateJurnal'])->name('jasakirim.generate.jurnal');
    Route::get('jurnal-order', [JurnalController::class, 'order'])->name('jurnal.order');
    Route::get('monitoring-kasir', [JurnalController::class, 'moniOps'])->name('jurnal.MoniOps');
    Route::get('jurnal-code', [JurnalController::class, 'code'])->name('jurnal.code');
    Route::get('jurnal-order-trucking', [JurnalController::class, 'order_trucking'])->name('jurnal.order_trucking');
    Route::get('rekap-data-blum-bayar', [LaporanController::class, 'exportRekapData'])->name('rekap_piutang.blum');
    Route::get('rekap-blum-bayar', [LaporanController::class, 'lapOutstandingBlumInv'])->name('rekap_piutang.blum_inv');
    Route::get('trucking/monitoring', [TruckingController::class, 'monitoring'])->name('trucking.monitoring');
    Route::get('trucking/monitoring-invoice', [TruckingController::class, 'monitoring_invoice'])->name('trucking.monitoring.invoice');
    Route::get('trucking/pre-invoice', [TruckingController::class, 'preInvoice'])->name('trucking.pre-invoice');
    Route::get('trucking/invoice-yansen', [TruckingController::class, 'invoice_yansen'])->name('trucking.invoice.yansen');
    Route::get('trucking/totalan-sopir', [TruckingController::class, 'totalan_sopir'])->name('trucking.totalan_sopir');
    Route::get('trucking/totalan-sopir/invoice', [TruckingController::class, 'cetak_invoice_sopir'])->name('trucking.cetak_invoice.totalan_sopir');
    Route::post('trucking/invoice/totalan-sopir', [TruckingController::class, 'totalan_sopir_invoice'])->name('trucking.invoice.total_sopir');
    Route::post('export/slip-sopir', [TruckingController::class, 'export_slip_sopir'])->name('trucking.export.slip_sopir');
    Route::post('trucking/totalan-sopir', [TruckingController::class, 'generate_totalan_sopir'])->name('trucking.generate.total_sopir');
    Route::get('trucking/cetak-invoice/get', [TruckingController::class, 'cetak_invoice_get'])->name('trucking.cetak_get.invoice');
    Route::get('trucking/invoice', [TruckingController::class, 'invoice'])->name('trucking.invoice');
    Route::get('trucking/invoice-sopir', [TruckingController::class, 'invoice_sopir'])->name('trucking.invoice_sopir');
    Route::post('trucking/cetak-invoice', [TruckingController::class, 'cetak_invoice'])->name('trucking.cetak.invoice');
    Route::post('trucking/generate-invoice', [TruckingController::class, 'generate_invoice'])->name('trucking.generate.invoice');
    Route::get('keuangan/fee-cust', [KeuanganController::class, 'fee_cust'])->name('keuangan.fee_cust');
    Route::post('keuangan/fee-cust-bayar', [KeuanganController::class, 'fee_cust_bayar'])->name('keuangan.fee_cust.bayar');
    Route::get('keuangan/customer', [KeuanganController::class, 'customer'])->name('keuangan.customer');
    Route::get('keuangan/order', [KeuanganController::class, 'order'])->name('keuangan.order');
    Route::get('keuangan/draft-invoice', [KeuanganController::class, 'draft_invoice'])->name('keuangan.draft_invoice');
    Route::get('keuangan/draft-invoice1', [KeuanganController::class, 'draft_invoice1'])->name('keuangan.draft_invoice1');
    Route::get('keuangan/data-draft-invoice', [KeuanganController::class, 'draftInvoiceData'])->name('draft.invoice.data');
    Route::get('keuangan/data-draft-invoice1', [KeuanganController::class, 'draftInvoiceData1'])->name('draft.invoice.data1');
    Route::get('keuangan/ba_kembali', [KeuanganController::class, 'ba_kembali'])->name('keuangan.ba_kembali');
    Route::get('keuangan/pre-invoice', [KeuanganController::class, 'pre_invoice'])->name('keuangan.pre_invoice');
    Route::get('keuangan/pre-invoic1', [KeuanganController::class, 'pre_invoice1'])->name('keuangan.pre_invoice1');
    Route::get('keuangan/laporan-ppn', [KeuanganController::class, 'laporanPpn'])->name('keuangan.laporan.ppn');
    Route::post('generate-invoice/{order}', [KeuanganController::class, 'generateInvoice'])->name('keuangan.generateInvoice');
    Route::post('import-invoice', [KeuanganController::class, 'import'])->name('invoice.import');
    Route::post('export-laporan-ppn', [KeuanganController::class, 'PPNExport'])->name('keuangan.ppn.export');
    Route::post('export-laporan-pajak', [KeuanganController::class, 'PajakExport'])->name('keuangan.pajak.export');
    Route::post('export-laporan-xml', [KeuanganController::class, 'XmlExport'])->name('keuangan.xml.export');
    Route::post('export-order', [OrderController::class, 'export'])->name('order.export');
    Route::post('export-order-malindo', [OrderController::class, 'exportMalindo'])->name('order.export.malindo');
    Route::post('export-order-sinar-balado', [OrderController::class, 'exportSinarBalado'])->name('order.export.sinar-balado');
    Route::post('export-order-logisted', [OrderController::class, 'exportLogisted'])->name('order.export.logisted');
    Route::post('export-order-fortuna', [OrderController::class, 'exportFortuna'])->name('order.export.fortuna');
    Route::post('export-order-cheiljedang', [OrderController::class, 'exportCheiljedang'])->name('order.export.cheiljedang');
    Route::post('export-order/ba_kembali', [OrderController::class, 'export_ba_kembali'])->name('order.export.ba_kembali');
    Route::post('export-order-trucking', [OrderTruckingController::class, 'export'])->name('ordertrucking.export');
    Route::post('export-asuransi', [AsuransiController::class, 'export'])->name('asuransi.export');
    Route::post('tarik-asuransi', [AsuransiController::class, 'tarik'])->name('asuransi.tarik');
    Route::post('cetak-asuransi', [AsuransiController::class, 'cetak'])->name('asuransi.cetak');
    Route::get('ba-kembali', [OrderController::class, 'baKembali'])->name('order.ba-kembali');
    Route::get('barang-diantar', [OrderController::class, 'barangDiantar'])->name('order.barang_diantar');
    Route::get('ba-diantar-sby', [OrderController::class, 'baDiantarSBY'])->name('order.ba_diantar_sby');
    Route::get('ba-diantar-sby-makassar', [OrderController::class, 'baDiantarSBYMakassar'])->name('order.ba_diantar_sby_makassar');
    Route::get('closing', [OrderController::class, 'closing'])->name('order.closing');
    Route::get('sj-kembali', [OrderController::class, 'sj_kembali'])->name('order.sj-kembali');
    Route::get('order-asuransi', [OrderController::class, 'asuransi'])->name('order.asuransi');
    Route::get('invoice', [KeuanganController::class, 'invoice'])->name('order.invoice');
    Route::get('cetak/surat-jalan', [CetakController::class, 'suratJalan'])->name('cetak.suratJalan');
    Route::get('pdf/surat-jalan', [CetakController::class, 'pdfSuratJalan'])->name('cetak.pdf.suratJalan');
    Route::get('cetak/pick-order', [CetakController::class, 'pickOrder'])->name('cetak.pickOrder');
    Route::get('cetak/packing-list', [CetakController::class, 'packingList'])->name('cetak.packingList');
    Route::get('cetak/packing-list-kubikasi', [CetakController::class, 'packingListKubikasi'])->name('cetak.packingList.kubikasi');
    Route::get('cetak/bttb', [CetakController::class, 'bttb'])->name('cetak.bttb');
    Route::get('cetak/bttb-kubikasi', [CetakController::class, 'bttbKubikasi'])->name('cetak.bttb.kubikasi');
    Route::get('cetak/shipment', [CetakController::class, 'shipment'])->name('cetak.shipment');
    Route::get('cetak/dooring', [CetakController::class, 'dooring'])->name('cetak.dooring');
    Route::get('cetak/invoice', [CetakController::class, 'invoice'])->name('cetak.invoice');
    Route::get('cetak/draft_invoice', [CetakController::class, 'draftinvoice'])->name('cetak.draft_invoice');
    Route::get('cetak/invoice-cont', [CetakController::class, 'invoiceCont'])->name('cetak.invoice.cont');
    Route::post('copy-orders/{order}', [OrderController::class, 'copy'])->name('order.copy');
    Route::post('si-export', [OrderController::class, 'SIExport'])->name('order.export.si');
    Route::post('rekap-invoice', [OrderController::class, 'rekap_invoice'])->name('order.rekap_invoice');
    Route::post('jurnal-import', [JurnalController::class, 'import'])->name('jurnal.import');
    Route::post('customer-import', [CustomerController::class, 'import'])->name('customer.import');
    Route::post('customer-import-update', [CustomerController::class, 'importUpdate'])->name('customer.import.update');
    Route::post('pindah-kapal', [OrderController::class, 'pindah_kapal'])->name('order.pindah_kapal');
    Route::post('order-import', [OrderController::class, 'import'])->name('order.import');
    Route::get('estimasi-biaya', [EstimasiController::class, 'biaya'])->name('estimasi.biaya');
    Route::get('estimasi-biaya-hpp', [EstimasiController::class, 'hpp'])->name('estimasi.hpp');
    Route::post('hitung-estimasi-hpp', [EstimasiController::class, 'hitung'])->name('estimasi.hpp.hitung');
    Route::get('/get-agens', function (Request $request) {
        return Agen::where('kota', $request->input('lokasi_pelayaran'))
            ->orderBy('nama')
            ->get(['id', 'nama']);
    })->name('get.agens');

    Route::get('/get-penerima-agens', function (Request $request) {
        // Ambil semua penerima_id dari agen di kota tertentu
        $agenIds = TarifAgen::where('agen_id', $request->input('penerima'))
            ->whereNotNull('penerima_id')
            ->where('is_active', 1)
            ->pluck('penerima_id');


        // Ambil data penerima dari tabel customers
        $penerima = Customer::whereIn('id', $agenIds)
            ->get(['id', 'nama']);

        return $penerima;
    })->name('get.penerima');

    Route::get('rekap-piutang', [LaporanController::class, 'rekap_piutang'])->name('rekap.piutang');
    Route::get('monitoring-subjek-bb', [LaporanController::class, 'MonitorSubjekBB'])->name('monitoring-subjek-bb');
    Route::get('jurnal/cek-coa', [JurnalController::class, 'j_cekcoa'])->name('jurnal.cekcoa');
    Route::get('data-rekap-piutang', [LaporanController::class, 'data_rekap_piutang'])->name('data-rekap.piutang');
    Route::get('data-rekap-piutang-addcost', [LaporanController::class, 'data_rekap_piutang_addcost'])->name('data-rekap-addcost.piutang');
    Route::get('data-total-rekap-piutang', [LaporanController::class, 'data_total_rekap_piutang'])->name('data-rekap-total.piutang');

    Route::get('jurnal-edit', [JurnalController::class, 'edit'])->name('jurnal.edit');
    Route::post('/jurnal/simpan-kode', [JurnalController::class, 'simpanKode'])->name('jurnal.simpanKode');
    Route::get('jurnal-kolektif', [JurnalController::class, 'kolektif'])->name('jurnal.kolektif.create');
    Route::get('jurnal-tampungan', [JurnalController::class, 'tampungan'])->name('jurnal.tampungan');
    Route::delete('jurnal-tampungan', [JurnalController::class, 'tampungan_destroy'])->name('jurnal.tampungan.destroy');
    Route::post('jurnal-tampungan', [JurnalController::class, 'tampungan_store'])->name('jurnal.tampungan.store');
    Route::get('jurnal-edit-coa', [JurnalController::class, 'editCoa'])->name('jurnal.edit.coa');
    Route::get('jurnal-buat-code', [JurnalController::class, 'buatCode'])->name('jurnal.buat.code');
    Route::get('jurnal-161-no-job', [JurnalController::class, 'jNoJob'])->name('jurnal.noJob');
    Route::put('jurnal-edit-coa-{jurnal}', [JurnalController::class, 'updateCoa'])->name('jurnal.update.coa');

    Route::get('jurnal-edit-{jurnal}', [JurnalController::class, 'editOne'])->name('jurnal.edit.one');
    Route::put('jurnal-edit-{jurnal}', [JurnalController::class, 'updateOne'])->name('jurnal.update.one');
    Route::get('jurnal-balik', [JurnalController::class, 'balik'])->name('jurnal.balik.create');
    Route::get('jurnal-neraca', [JurnalController::class, 'neraca'])->name('jurnal.neraca');
    Route::get('jurnal-lr', [JurnalController::class, 'laba_rugi'])->name('jurnal.laba_rugi');
    Route::get('jurnal-lr-thn', [JurnalController::class, 'laba_rugiThn'])->name('jurnal.laba_rugiThn');
    Route::get('jurnal-buku-besar', [JurnalController::class, 'buku_besar'])->name('jurnal.buku_besar');
    Route::get('jurnal-buku-besar-pembantu', [JurnalController::class, 'buku_besar_pembantu'])->name('jurnal.buku_besar_pembantu');
    Route::get('jurnal-buku-besar-pembantu1', [JurnalController::class, 'bb_pembantu'])->name('jurnal.buku_besar_pembantu1');
    Route::get('jurnal-buku-besar-pembantu/{year}/{month}/{coa_id}/{customer}/{subjek}', [JurnalController::class, 'buku_besar_pembantu_rincian'])->name('jurnal.buku_besar_pembantu_rincian');
    Route::get('jurnal-trucking', [JurnalController::class, 'trucking'])->name('jurnal.trucking');
    Route::get('jurnal-bupot-trucking', [JurnalController::class, 'jurnal_bupot_trucking'])->name('jurnal.trucking.bupot');
    Route::post('jurnal-bupot-trucking', [JurnalController::class, 'jurnal_bupot_trucking_store'])->name('jurnal.bupot.trucking.store');
    Route::get('jurnal-totalan-sopir', [JurnalController::class, 'totalan_sopir'])->name('jurnal.totalan_sopir');
    Route::post('jurnal-totalan-sopir', [JurnalController::class, 'slip_totalan_sopir'])->name('jurnal.slip_totalan_sopir');
    Route::post('jurnal-sync-job', [JurnalController::class, 'syncJob'])->name('jurnal.sync.job');
    Route::post('submit-jurnal-totalan-sopir', [JurnalController::class, 'submit_slip_totalan_sopir'])->name('jurnal.submit_slip_totalan_sopir');
    Route::get('jurnal-merge', [JurnalController::class, 'merge'])->name('jurnal.merge');
    Route::post('jurnal-merge', [JurnalController::class, 'store_merge'])->name('jurnal.merge.store');
    Route::post('jurnal-kolektif', [JurnalController::class, 'store_kolektif'])->name('jurnal.kolektif.store');
    Route::post('jurnal-balik', [JurnalController::class, 'store_balik'])->name('jurnal.balik.store');
    Route::post('jurnal-trucking', [JurnalController::class, 'store_trucking'])->name('jurnal.trucking.store');

    Route::get('jurnal-kolektif', [JurnalController::class, 'kolektif'])->name('jurnal.kolektif.create');
    Route::get('jurnal-balik', [JurnalController::class, 'balik'])->name('jurnal.balik.create');
    Route::get('jurnal-neraca', [JurnalController::class, 'neraca'])->name('jurnal.neraca');
    Route::get('jurnal-lr', [JurnalController::class, 'laba_rugi'])->name('jurnal.laba_rugi');
    Route::get('jurnal-buku-besar', [JurnalController::class, 'buku_besar'])->name('jurnal.buku_besar');
    Route::get('jurnal-trucking', [JurnalController::class, 'trucking'])->name('jurnal.trucking');
    Route::get('jurnal-merge', [JurnalController::class, 'merge'])->name('jurnal.merge');
    Route::get('jurnal-manual', [JurnalController::class, 'manual'])->name('jurnal.manual');
    Route::get('jurnal-filter', [JurnalController::class, 'filter'])->name('jurnal.filter');
    Route::post('jurnal-merge', [JurnalController::class, 'store_merge'])->name('jurnal.merge.store');
    Route::post('jurnal-manual', [JurnalController::class, 'store_manual'])->name('jurnal.manual.store');
    Route::post('jurnal-kolektif', [JurnalController::class, 'store_kolektif'])->name('jurnal.kolektif.store');
    Route::post('jurnal-balik', [JurnalController::class, 'store_balik'])->name('jurnal.balik.store');
    Route::post('jurnal-trucking', [JurnalController::class, 'store_trucking'])->name('jurnal.trucking.store');
    Route::post('jurnal-export-batch', [JurnalController::class, 'exportJurnalBatch'])->name('jurnal.exportJurnalBatch');
    Route::post('jurnal-export-month', [JurnalController::class, 'exportMonth'])->name('jurnal.exportMonth');
    Route::post('jurnal-balik-trucking', [OmsetController::class, 'jurnalBalikTrucking'])->name('jurnal.balik.trucking');
    Route::post('jurnal-balik-trucking-ext', [OmsetController::class, 'jurnalBalikTruckingExt'])->name('jurnal.balik.trucking.ext');

    Route::get('jqgrid-tarif-agent', [TarifAgenController::class, 'jqgrid'])->name('jqgrid.tarif.agent');
    Route::get('jqgrid-tarif-pelayaran', [TarifPelayaranController::class, 'jqgrid'])->name('jqgrid.tarif.pelayaran');
    Route::get('jqgrid-order-biaya', [OrderBiayaController::class, 'jqgrid'])->name('order_biaya.jqgrid');
    Route::get('monitoring-pembayar', [OrderController::class, 'monitoring_pembayar'])->name('order.monitoring_pembayar');
    Route::get('monitoring-shipment', [OrderBiayaController::class, 'index'])->name('order_biaya.index');
    Route::get('monitoring-shipment-jayapura', [OrderBiayaController::class, 'jayapura'])->name('order_biaya.jayapura');
    Route::get('monitoring-shipment/{order}', [OrderBiayaController::class, 'edit'])->name('order_biaya.edit');
    Route::put('monitoring-shipment/{order}', [OrderBiayaController::class, 'update'])->name('order_biaya.update');

    Route::get('sync-reset-tbtl', [SyncController::class, 'resetTBTL']);
    Route::get('sync-kuli', [SyncController::class, 'kuli']);
    Route::get('sync-import', [SyncController::class, 'import']);
    Route::get('sync-kapal', [SyncController::class, 'kapal']);
    Route::get('sync-sync', [SyncController::class, 'sync']);
    Route::get('sync-invoice', [SyncController::class, 'invoice']);
    Route::get('sync-customer', [SyncController::class, 'customerTrucking']);
    Route::get('sync-data', [SyncController::class, 'data']);
    Route::get('sync-agen', [SyncController::class, 'agen']);
    Route::get('sync-pph', [SyncController::class, 'pph']);
    Route::get('sync-menu', [SyncController::class, 'menu_link']);
    Route::get('sync-menu-backup', [SyncController::class, 'menu_link_backup']);
    Route::get('sync-menu-ras', [SyncController::class, 'menu_link_ras']);
    Route::get('sync-menu-alb', [SyncController::class, 'menu_link_alb']);
    Route::get('sync-order-menu', [SyncController::class, 'orderMenu']);
    Route::get('sync-transaksi', [SyncController::class, 'transaksi']);
    Route::get('sync-trucking', [SyncController::class, 'trucking']);
    Route::get('sync-same', [SyncController::class, 'sameData']);
    Route::get('sync-pull', [SyncController::class, 'pull']);
    Route::get('sync-coa', [SyncController::class, 'coa']);
    Route::get('sync-jurnal', [SyncController::class, 'jurnal']);
    Route::get('sync-penjurnal', [SyncController::class, 'penjurnal']);
    Route::get('sync-jurnal-asuransi', [SyncController::class, 'jurnalAsuransi']);
    Route::get('sync-hutang-pelayaran', [SyncController::class, 'hutang_pelayaran']);
    Route::get('sync-lock', [SyncController::class, 'lock']);
    Route::get('sync-penerimabl', [SyncController::class, 'penerimabl']);
    Route::get('sync-port', [SyncController::class, 'port']);
    Route::get('sync-lokasi-agen', [SyncController::class, 'lokasi_agen']);
    Route::get('sync-tarif-trucking', [SyncController::class, 'tarif_trucking']);
    Route::get('sync-coa-name', [SyncController::class, 'coa_name']);
    Route::get('sync-jasa-kirim', [SyncController::class, 'jasa_kirim']);
    Route::get('sync-jurnal-invoice', [SyncController::class, 'jurnal_invoice']);

    Route::get('sync-jurnal-hutang-trucking/{trx_id}/{no}', [TruckingController::class, 'jurnal_hutang_trucking']);
    Route::resource('setting', App\Http\Controllers\SettingController::class);
});
// Route::view('test','test');
