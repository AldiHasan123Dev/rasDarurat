<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SlipSopirExport;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderTruckingResource;
use App\Http\Resources\TransaksiSopirResource;
use App\Http\Resources\TransaksiTruckingResource;
use App\Services\SyncService;
use App\Models\CustomerTrucking;
use App\Models\Jurnal;
use App\Models\JurnalSample;
use App\Models\Kendaraan;
use App\Models\MutasiTotalanSopir;
use App\Models\Order;
use App\Models\OrderTrucking;
use App\Models\Pengirim;
use App\Models\SanguSopir;
use App\Models\Sopir;
use App\Models\Setting;
use App\Models\TemplateJurnal;
use App\Models\TransaksiSopir;
use App\Models\TransaksiTrucking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TruckingController extends Controller
{
    public function order()
    {
        return view('admin.trucking.order');
    }

    public function invoice_yansen()
    {
        $carbon = new Carbon();
        $start = request('start') ?? date('Y-m-d');
        $end = request('end') ?? $carbon->addMonths(1)->format('Y-m-d');
        $data1 = OrderTrucking::where('customer_id', 23)->whereBetween('tgl_muat', [$start, $end])->orderBy('tgl_muat')->get();
        $data2 = OrderTrucking::where('customer_id', 24)->whereBetween('tgl_muat', [$start, $end])->orderBy('tgl_muat')->get();
        return view('admin.trucking.yansen', compact('data1', 'data2', 'start', 'end'));
    }

    public function totalan_sopir()
    {
        $data = OrderTrucking::join('sopir', 'sopir.id', '=', 'order_trucking.sopir_id')
            ->join('kendaraan', 'kendaraan.id', '=', 'order_trucking.kendaraan_id')
            ->select('order_trucking.*', 'sopir.nama')
            ->where('kendaraan.milik', '!=', 'vendor')
            ->whereNull('order_trucking.tgl_total')
            ->whereNotNull('order_trucking.sj_kembali_fa')
            ->orderBy('sopir.nama')
            ->orderBy('order_trucking.tgl_muat')
            ->get()
            ->groupBy('sopir.nama');
        return view('admin.trucking.totalan_sopir', compact('data'));
    }

    public function totalan_sopir_invoice(Request $request)
    {
        $order_id = explode(',', $request->order_id);
        if (count($order_id) <= 1 && $order_id[0] == "") {
            return back()->with('danger', 'Harap checklist order!');
        }
        $orders = OrderTrucking::whereIn('id', $order_id)->get();
        $cek = OrderTrucking::whereIn('id', $order_id)->get()->groupBy('sopir_id');
        $order = $orders[0];
        if ($cek->count() > 1) {
            return back()->with('danger', 'Anda tidak bisa memilih ' . $cek->count() . ' Sopir sekaligus!, Harap untuk pilih satu sopir');
        }
        $service = new SyncService();
        foreach ($orders as $item) {
            $service->trucking($item->id);
        }
        return view('admin.trucking.totalan_sopir_invoice', compact('orders', 'order', 'order_id'));
    }

    public function cetak_invoice_sopir()
    {
        $invoice = request('invoice');
        $order = OrderTrucking::where('invoice_sopir', $invoice)->first();
        if (!$order) {
            return back()->with('danger', 'Invoice tidak ditemukan!');
        }
        $orders = OrderTrucking::where('invoice_sopir', $invoice)->get();
        return view('admin.trucking.totalan_sopir_invoice', compact('orders', 'order', 'invoice'));
    }

    public function generate_totalan_sopir(Request $request)
    {
        $order_id = explode(',', $request->order_id);
        if (count($order_id) <= 1 && $order_id[0] == "") {
            return back()->with('danger', 'Harap checklist order!');
        }
        $orders = OrderTrucking::whereIn('id', $order_id)->get()->groupBy('sopir_id');
        if ($orders->count() > 1) {
            return back()->with('danger', 'Anda tidak bisa memilih ' . $orders->count() . ' Sopir sekaligus!, Harap untuk pilih satu sopir');
        }
        $no = TransaksiSopir::whereYear('created_at', date('Y'))->max('order') + 1;
        $invoice = 'RIT/' . date('ymd') . '/' . sprintf('%03d', $no);
        OrderTrucking::whereIn('id', $order_id)->update([
            'tgl_total' => date('Y-m-d'),
            'order_sopir' => $no,
            'invoice_sopir' => $invoice
        ]);
        TransaksiSopir::create([
            'tgl_invoice' => date('Y-m-d'),
            'jurnal_status' => 0,
            'invoice' => $invoice,
            'sopir_id' => $request->sopir_id,
            'order_id' => '[' . $request->order_id . ']',
            'order_trucking_id' => $request->order_trucking_id,
            'total' => $request->total,
            'order' => $no,
            'submited_by' => Auth::id(),
        ]);
        MutasiTotalanSopir::create([
            'tgl_invoice' => date('Y-m-d'),
            'invoice' => $invoice,
            'sopir_id' => $request->sopir_id,
            'order_id' => '[' . $request->order_id . ']',
            'order_trucking_id' => $request->order_trucking_id,
            'total' => $request->total,
            'order' => $no,
            'submited_by' => Auth::id(),
        ]);
        return redirect()->route('trucking.cetak_invoice.totalan_sopir', ['invoice' => $invoice]);
    }

    public function invoice()
    {
        $data = TransaksiTrucking::all();
        $data = TransaksiTruckingResource::collection($data);
        return view('admin.trucking.invoice_list', compact('data'));
    }

    public function invoice_sopir()
    {
        $data = TransaksiSopir::all();
        $data = TransaksiSopirResource::collection($data);
        return view('admin.trucking.invoice_sopir_list', compact('data'));
    }

    public function preInvoice()
    {
        // $data1 = OrderTrucking::join('customer_trucking','customer_trucking.id','=','order_trucking.customer_id')
        //     ->join('kendaraan','kendaraan.id','=','order_trucking.kendaraan_id')
        //     ->select('order_trucking.*','customer_trucking.nama as customer','customer_trucking.id as id_customer')
        //     ->where('kendaraan.milik','R1')
        //     ->where('customer_trucking.r2',0)
        //     ->whereNull('order_trucking.invoice')
        //     ->whereNotNull('order_trucking.tgl_total')
        //     ->whereNotNull('order_trucking.sj_kembali_fa')
        //     ->orderBy('customer')
        //     ->orderBy('tgl_muat')
        //     ->get()
        //     ->groupBy('customer');

       $data1 = OrderTrucking::join('customer_trucking', 'customer_trucking.id', '=', 'order_trucking.customer_id')
    ->join('kendaraan', 'kendaraan.id', '=', 'order_trucking.kendaraan_id')
    ->select(
        'order_trucking.*',
        'customer_trucking.nama as customer',
        'customer_trucking.id as id_customer'
    )

    // =========================
    // INVOICE WAJIB UNTUK SEMUA
    // =========================
    ->whereNull('order_trucking.invoice')

    // =========================
    // ATURAN PER JENIS KENDARAAN
    // =========================
    ->where(function ($q) {

        // R1 & R2 → WAJIB TOTAL & SJ
        $q->where(function ($r) {
            $r->whereIn('kendaraan.milik', ['R1', 'R2'])
              ->whereNotNull('order_trucking.tgl_total')
              ->whereNotNull('order_trucking.sj_kembali_fa');
        })

        // VENDOR → TANPA TOTAL & SJ
        ->orWhere(function ($v) {
            $v->where('kendaraan.milik', 'vendor')
                ->whereNotNull('order_trucking.sj_kembali_fa');
        });
    })

    // =========================
    // ATURAN CUSTOMER
    // =========================
    ->where(function ($q) {

        // r1=0 r2=0 → hanya R1
        $q->where(function ($c) {
            $c->where('customer_trucking.r1', 0)
              ->where('customer_trucking.r2', 0)
              ->where('kendaraan.milik', 'R1');
        })

        // r1=1 r2=0 → R1, R2, vendor
        ->orWhere(function ($c) {
            $c->where('customer_trucking.r1', 1)
              ->where('customer_trucking.r2', 0)
              ->whereIn('kendaraan.milik', ['R1', 'R2', 'vendor']);
        })

        // r1=1 r2=1 → bebas
        ->orWhere(function ($c) {
            $c->where('customer_trucking.r1', 1)
              ->where('customer_trucking.r2', 1);
        });
    })

    ->orderBy('customer')
    ->orderBy('tgl_muat')
    ->get()
    ->groupBy('customer');



        $data2 = OrderTrucking::join('customer_trucking', 'customer_trucking.id', '=', 'order_trucking.customer_id')
            ->join('kendaraan', 'kendaraan.id', '=', 'order_trucking.kendaraan_id')
            ->select('order_trucking.*', 'customer_trucking.nama as customer', 'customer_trucking.id as id_customer')
            ->where('kendaraan.milik', 'R2')
            ->where('order_trucking.customer_id', '!=', 2)
            ->whereNull('order_trucking.invoice')
            ->whereNotNull('order_trucking.tgl_total')
            ->whereNotNull('order_trucking.sj_kembali_fa')
            ->where('customer_trucking.r1', 0)
            ->where('customer_trucking.r2', 0)
            ->orWhere('customer_trucking.r2', 1)
            ->where('order_trucking.customer_id', '!=', 2)
            ->whereNull('order_trucking.invoice')
            ->whereNotNull('order_trucking.tgl_total')
            ->whereNotNull('order_trucking.sj_kembali_fa')
            ->orWhere('kendaraan.milik', 'vendor')
            ->where('order_trucking.customer_id', '!=', 2)
            ->whereNull('order_trucking.invoice')
            ->whereNotNull('order_trucking.sj_kembali_fa')
            ->where('customer_trucking.r1', 0)
            ->where('customer_trucking.r2', 0)
            ->orderBy('customer')
            ->orderBy('tgl_muat')
            ->get()
            ->groupBy('customer');

        // $data3 = OrderTrucking::join('customer_trucking', 'customer_trucking.id', '=', 'order_trucking.customer_id')
        //     ->join('kendaraan', 'kendaraan.id', '=', 'order_trucking.kendaraan_id')
        //     ->select('order_trucking.*', 'customer_trucking.nama as customer', 'customer_trucking.id as id_customer')
        //     ->where('kendaraan.milik', 'vendor')
        //     ->where('order_trucking.customer_id', '!=', 2)
        //     ->whereNull('order_trucking.invoice')
        //     ->whereNotNull('order_trucking.sj_kembali_fa')
        //     ->orderBy('customer')
        //     ->orderBy('tgl_muat')
        //     ->get()
        //     ->groupBy('customer');
        return view('admin.trucking.pre_invoice', compact('data1', 'data2'));
    }

    public function cetak_invoice_get()
    {
        $invoice = request('invoice');
        $order = OrderTrucking::where('invoice', $invoice)->first();
        if (!$order) {
            return back()->with('danger', 'Invoice Tidak ditemukan!');
        }
        $transaksi = TransaksiTrucking::where('invoice', request('invoice'))->first();
        $tipe = $transaksi->tipe;
        $data = OrderTrucking::where('invoice', $invoice)->orderBy('tgl_muat')->get()->groupBy('tarif_id');
        return view('admin.trucking.invoice', compact('order', 'data', 'tipe', 'invoice'));
    }

    public function cetak_invoice(Request $request)
    {
        $order_id = explode(',', $request->order_id);
        if (count($order_id) <= 1 && $order_id[0] == "") {
            return back()->with('danger', 'Harap checklist terlebih dahulu!');
        }
        $orders = OrderTrucking::whereIn('id', $order_id)->get()->groupBy('customer_id');
        if ($orders->count() > 1) {
            return back()->with('danger', 'Anda tidak bisa memilih ' . $orders->count() . ' Customer sekaligus!, Harap untuk pilih satu Customer');
        }
        $order_sync = OrderTrucking::whereIn('id', $order_id)->get();
        $service = new SyncService();
        foreach ($order_sync as $item) {
            $service->trucking($item->id);
        }
        $order = OrderTrucking::whereIn('id', $order_id)->first();
        $null_job = OrderTrucking::whereIn('id', $order_id)->whereNull('order_id')->count();

        $tipe = $request->tipe;
        $data = OrderTrucking::whereIn('id', $order_id)->orderBy('tgl_muat')->get()->groupBy('tarif_id');
        // if(count($r1s)>0&&count($r2s)>0){
        //     return back()->with('danger','Anda tidak bisa memilih 2 Tipe invoice(R1 & R2) sekaligus!');
        // }

        $pengirim = Pengirim::orderBy('nama')->get();
        return view('admin.trucking.invoice', compact('orders', 'order', 'data', 'order_id', 'tipe', 'null_job', 'pengirim'));
    }

    public function generate_invoice(Request $request)
    {
        $lock = Cache::lock('invoice-trucking', 10);
        $setting = Setting::first();
        $tahunLompat = $setting->tahun_lompat; // contoh: 2025
        $tahunLompaty = substr($tahunLompat, -2); // hasil: 25
        if ($lock->get()) {
            try {
                $invoice = DB::transaction(function () use ($request, $tahunLompat, $tahunLompaty) {
                    $roman_numerals = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"); // daftar angka Romawi
                    $order_id = explode(',', $request->order_id);
                    $tgl_muat = OrderTrucking::whereIn('id', $order_id)->min('tgl_muat');
                   $month_number = (int) date('m', strtotime($tgl_muat));
$month_roman  = $roman_numerals[$month_number];
                    $no1 = 0;
                    $no2 = 0;
                    $no3 = 0;
                    if ($request->tipe == 'R1') {
                        $no1 = TransaksiTrucking::whereYear('tgl_invoice', $tahunLompat)->max('order_r1') + 1;
                        $invoice = sprintf('%03d', $no1) . '/' . $month_roman . '/' . $tahunLompaty;
                    } else if ($request->tipe == 'R2') {
                        $no2 = TransaksiTrucking::whereYear('tgl_invoice', $tahunLompat)->max('order_r2') + 1;
                        $invoice = sprintf('%03d', $no2) . '/RAS-LT/' . $month_roman . '/' . $tahunLompaty;
                    } else {
                        $no3 = TransaksiTrucking::whereYear('tgl_invoice', $tahunLompat)->max('order_vendor') + 1;
                        $invoice = sprintf('%03d', $no3) . '/VENDOR-' . $month_roman . '/' . $tahunLompaty;
                    }

                    $trx = TransaksiTrucking::create([
                        'order_trucking_id' => $request->order,
                        'order_id' => '[' . $request->order_id . ']',
                        'rit' => $request->rit,
                        'customer_id' => $request->customer_id,
                        'tipe' => $request->tipe,
                        'pph' => $request->pph,
                        'total' => $request->total,
                        'lain_lain' => $request->lain_lain,
                        'submited_by' => Auth::id(),
                        'invoice' => $invoice,
                        'order_r1' => $no1,
                        'order_r2' => $no2,
                        'order_r3' => $no3,
                        'tgl_invoice' => date('Y-m-d'),
                        'pengirim' => $request->pengirim
                    ]);

                    OrderTrucking::whereIn('id', $order_id)->update([
                        'tgl_invoice' => date('Y-m-d'),
                        'invoice' => $invoice,
                        'total_invoice' => $request->total,
                    ]);

                    if ($request->tipe == 'R2') {
                        $order = OrderTrucking::whereIn('id', $order_id)->first();
                        $orders = OrderTrucking::whereIn('id', $order_id)->get();

                        $template = TemplateJurnal::find(9);
                        $month = date('m');
                        $month1 = date('m', strtotime($order->tgl_muat));
                        if ($month1 != $month) {
                            $carbon = new Carbon($order->tgl_muat);
                            // $date = '2024-12-31';
                            $date = $carbon->endOfMonth()->toDateString();
                            $no = Jurnal::where('tipe', 'JNL')->whereMonth('created_at', date('m', strtotime($date)))->whereYear('created_at', date('Y', strtotime($date)))->max('no') + 1;
                            $nomor = sprintf('%02d', date('m', strtotime($date))) . '-' . sprintf('%03d', $no) . '/' . date('y', strtotime($date));
                        } else {
                            $no = Jurnal::where('tipe', 'JNL')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->max('no') + 1;
                            $nomor = sprintf('%02d', date('m')) . '-' . sprintf('%03d', $no) . '/' . date('y');
                            $date = date('Y-m-d');
                        }
                        foreach ($template->template_items as $key => $item) {
                            $name = $item->keterangan;
                            $id_job = $order->order ? $order->order->job . '-' . sprintf('%02d', $order->order->no_job) : '-';
                            $cont = $order->container;
                            $seal = $order->seal;
                            // $order_id = $order->order ? $order->order->id : null;
                            $shipment = $order->order ? $order->order->tarif->shipmentInfo->nama : '-';
                            $pembayar = $order->order ? $order->order->tarif->customer->nama : '-';
                            $kapal = $order->order ? $order->order->jadwal_kapal->kapal->nama : '-';
                            $voyage = $order->order ? $order->order->jadwal_kapal->voyage : '-';
                            $customer = $order->customer->nama;
                            $shipment_trucking = $order->tipe;
                            $tujuan_trucking = $order->tarif->tujuan->tujuanInfo->nama;
                            $name = str_replace('[1]', $id_job, $name);
                            $name = str_replace('[2]', $cont, $name);
                            $name = str_replace('[3]', $seal, $name);
                            $name = str_replace('[4]', $kapal, $name);
                            $name = str_replace('[5]', $voyage, $name);
                            $name = str_replace('[6]', $shipment, $name);
                            $name = str_replace('[7]', $pembayar, $name);
                            $name = str_replace('[8]', $customer, $name);
                            $name = str_replace('[9]', $shipment_trucking, $name);
                            $name = str_replace('[10]', $tujuan_trucking, $name);
                            if ($item->coa_debit_id) {
                                $deb = 0;
                                foreach ($orders as $ord) {
                                    $deb += $ord->tarif_nominal;
                                    if ($ord->tagihans->count() > 0) {
                                        foreach ($ord->tagihans as $tag) {
                                            $deb += $tag->jumlah;
                                        }
                                    }
                                }
                                Jurnal::create([
                                    'coa_id' => $item->coa_debit_id,
                                    'order_trucking_id' => $order->id,
                                    'nomor' => $nomor,
                                    'nama' => $name,
                                    'debit' => $deb,
                                    'credit' => 0,
                                    'tipe' => 'JNL',
                                    'no' => $no,
                                    'created_at' => $date,
                                    'relasi' => $nomor,
                                    'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice : null,
                                    'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice : null,
                                ]);
                            }
                            if ($item->coa_credit_id == 87) {
                                foreach ($orders as $ord) {
                                    Jurnal::create([
                                        'coa_id' => $item->coa_credit_id,
                                        'order_trucking_id' => $ord->id,
                                        'nomor' => $nomor,
                                        'nama' => $name,
                                        'credit' => $ord->tarif_nominal,
                                        'debit' => 0,
                                        'tipe' => 'JNL',
                                        'no' => $no,
                                        'created_at' => $date,
                                        'relasi' => $nomor,
                                        'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice : null,
                                        'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice : null,
                                        'container' => $ord->container,
                                        'nopol' => $ord->kendaraan->nopol ?? null,
                                    ]);
                                }
                            }
                            if ($item->coa_credit_id == 28) {
                                foreach ($orders as $ord) {
                                    if ($ord->tagihans->count() > 0) {
                                        foreach ($ord->tagihans as $tag) {

                                            // Cek apakah nama tagihan mengandung "TB/TL"
                                           if (
    str_contains($tag->nama, 'TB/TL') ||
    str_contains($tag->nama, 'Ambil Empty') ||
    str_contains($tag->nama, 'Bongkar Full')
) {
    $coa_id = 87;
} else {
    $coa_id = $item->coa_credit_id;
}


                                            Jurnal::create([
                                                'coa_id' => $coa_id,
                                                'order_trucking_id' => $ord->id,
                                                'nomor' => $nomor,
                                                'nama' => $tag->nama,
                                                'credit' => $tag->jumlah,
                                                'debit' => 0,
                                                'tipe' => 'JNL',
                                                'no' => $no,
                                                'created_at' => $date,
                                                'relasi' => $nomor,
                                                'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice : null,
                                                'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice : null,
                                                'container' => $ord->container,
                                                'nopol' => $ord->kendaraan->nopol ?? null,
                                            ]);
                                        }
                                    }
                                }
                            }

                        }
                        OrderTrucking::whereIn('id', $order_id)->update([
                            'jurnal_piutang' => $nomor,
                        ]);
                        $trx->update([
                            'jurnal_piutang' => $nomor,
                        ]);
                    }

                    if ($request->jurnal_otomatis) {
                        $order = OrderTrucking::whereIn('id', $order_id)->first();
                        $orders = OrderTrucking::whereIn('id', $order_id)->get();

                        $template = TemplateJurnal::find(9);
                        $month = date('m');
                        $month1 = date('m', strtotime($order->tgl_muat));
                        if ($month1 != $month) {
                            $carbon = new Carbon($order->tgl_muat);
                            $date = $carbon->endOfMonth()->toDateString();
                            $no = Jurnal::where('tipe', 'JNL')->whereMonth('created_at', date('m', strtotime($date)))->whereYear('created_at', date('Y', strtotime($date)))->max('no') + 1;
                            $nomor = sprintf('%02d', date('m', strtotime($date))) . '-' . sprintf('%03d', $no) . '/' . date('y', strtotime($date));
                        } else {
                            $no = Jurnal::where('tipe', 'JNL')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->max('no') + 1;
                            $nomor = sprintf('%02d', date('m')) . '-' . sprintf('%03d', $no) . '/' . date('y');
                            $date = date('Y-m-d');
                        }
                        $total = 0;
                        foreach ($orders as $ord) {
                            $shipment = $ord->order ? $ord->order->tarif->shipmentInfo->nama : '-';
                            $pembayar = $ord->order ? $ord->order->tarif->customer->nama : '-';
                            $tujuan_trucking = $ord->tarif->tujuan->tujuanInfo->nama;
                            Jurnal::create([
                                'coa_id' => 31,
                                'order_trucking_id' => $ord->id,
                                'order_id' => $ord->order_id,
                                'nomor' => $nomor,
                                'nama' => 'Biaya Trucking ' . $pembayar . ' ' . $shipment . ' ' . $tujuan_trucking,
                                'debit' => $ord->tarif_nominal,
                                'credit' => 0,
                                'tipe' => 'JNL',
                                'no' => $no,
                                'created_at' => $date,
                                'relasi' => $nomor,
                                'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice : null,
                                'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice : null,
                                'container' => $ord->container,
                                'nopol' => $ord->kendaraan->nopol ?? null,
                            ]);
                            JurnalSample::create([
                                'coa_id' => 31,
                                'order_trucking_id' => $ord->id,
                                'order_id' => $ord->order_id,
                                'nomor' => $nomor,
                                'nama' => 'Pendapatan Trucking ' . $pembayar . ' ' . $shipment . ' ' . $tujuan_trucking,
                                'credit' => $ord->tarif_nominal,
                                'debit' => 0,
                                'tipe' => 'JNL',
                                'no' => $no,
                                'created_at' => $date,
                                'relasi' => $nomor,
                                'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice : null,
                                'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice : null,
                                'container' => $ord->container,
                                'nopol' => $ord->kendaraan->nopol ?? null,
                            ]);

                            foreach ($ord->tagihans as $tag) {
                                Jurnal::create([
                                    'coa_id' => 31,
                                    'order_trucking_id' => $ord->id,
                                    'order_id' => $ord->order_id,
                                    'nomor' => $nomor,
                                    'nama' => $tag->nama,
                                    'debit' => $tag->jumlah,
                                    'credit' => 0,
                                    'tipe' => 'JNL',
                                    'no' => $no,
                                    'created_at' => $date,
                                    'relasi' => $nomor,
                                    'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice : null,
                                    'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice : null,
                                    'container' => $ord->container,
                                    'nopol' => $ord->kendaraan->nopol ?? null,
                                ]);
                                JurnalSample::create([
                                    'coa_id' => 31,
                                    'order_trucking_id' => $ord->id,
                                    'order_id' => $ord->order_id,
                                    'nomor' => $nomor,
                                    'nama' => $tag->nama,
                                    'credit' => $tag->jumlah,
                                    'debit' => 0,
                                    'tipe' => 'JNL',
                                    'no' => $no,
                                    'created_at' => $date,
                                    'relasi' => $nomor,
                                    'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice : null,
                                    'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice : null,
                                    'container' => $ord->container,
                                    'nopol' => $ord->kendaraan->nopol ?? null,
                                ]);
                                $total += $tag->jumlah;
                            }

                            $total += $ord->tarif_nominal;
                        }

                        Jurnal::create([
                            'coa_id' => 131,
                            'order_trucking_id' => $ord->id,
                            'nomor' => $nomor,
                            'nama' => 'Hutang Trucking ' . $request->pengirim . ' INV. ' . $invoice,
                            'credit' => $total,
                            'debit' => 0,
                            'tipe' => 'JNL',
                            'no' => $no,
                            'created_at' => $date,
                            'relasi' => $nomor,
                            'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice : null,
                            'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice : null,
                        ]);
                        JurnalSample::create([
                            'coa_id' => 131,
                            'order_trucking_id' => $ord->id,
                            'nomor' => $nomor,
                            'nama' => 'Piutang Trucking ' . $request->pengirim . ' INV. ' . $invoice,
                            'debit' => $total,
                            'credit' => 0,
                            'tipe' => 'JNL',
                            'no' => $no,
                            'created_at' => $date,
                            'relasi' => $nomor,
                            'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice : null,
                            'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice : null,
                        ]);

                        OrderTrucking::whereIn('id', $order_id)->update([
                            'jurnal_hutang' => $nomor,
                        ]);
                        $trx->update([
                            'jurnal_hutang' => $nomor,
                        ]);
                    }

                    return $invoice;
                });
                $lock->release(); // Lepas kunci jika terjadi kesalahan
                return redirect()->route('trucking.cetak_get.invoice', ['invoice' => $invoice]);
            } catch (\Exception $e) {
                $lock->release(); // Lepas kunci jika terjadi kesalahan
                return back()->with('danger', $e->getMessage());
            }
            $lock->release(); // Lepas kunci setelah selesai
        } else {
            // Tindakan saat kunci sudah dipegang proses lain
            return back()->with('danger', 'Tunggu beberapa menit dan coba lagi!');
        }
    }

    public function monitoring()
    {
        $sj_kembali = OrderTrucking::whereNotNull('sj_kembali')->whereNull('sj_kembali_fa')->orderBy('tgl_muat')->get();
        $orders = OrderTrucking::whereNotNull('sj_kembali_fa')->orderBy('tgl_muat')->get();
        $sj_kembali = OrderTruckingResource::collection($sj_kembali);
        $orders = OrderTruckingResource::collection($orders);
        $kendaraan = Kendaraan::all()->where('is_active', 1)->sortBy('nopol');
        $sopir = Sopir::where('is_active', 1)->orderBy('nama', 'asc')->get();
        $tujuan = SanguSopir::join('lokasi', 'lokasi.id', '=', 'sangu_sopir.tujuan')->select('sangu_sopir.*')->orderBy('lokasi.nama', 'asc')->get();
        $customers = CustomerTrucking::all()->sortBy('nama');
        $update = OrderTrucking::whereNull('order_id')->get();
        foreach ($update as $item) {
            $order = Order::where('container', $item->container)->where('seal', $item->seal)->first();
            if ($order) {
                $item->update(['order_id' => $order->id]);
            }
        }
        return view('admin.trucking.monitoring', compact('sj_kembali', 'orders', 'kendaraan', 'sopir', 'tujuan', 'customers'));
    }

    public function monitoring_invoice()
    {
        $orders = OrderTrucking::whereHas('kendaraan', function ($q) {
            $q->whereIn('milik', ['R1', 'vendor']);
        })->whereNull('invoice')->orderBy('tgl_muat')->get();
        $orders = OrderTruckingResource::collection($orders);
        return view('admin.trucking.monitoring_invoice', compact('orders'));
    }

    public function export_slip_sopir()
    {
        $order = OrderTrucking::where('invoice_sopir', request('invoice'))->first();
        $name = $order->sopir->nama . '_' . date('d-m-y', strtotime($order->tgl_total));
        return Excel::download(new SlipSopirExport(request('invoice')), $name . '.xlsx');
    }

    public function jurnal_hutang_trucking($trx_id, $no)
    {
        $trx = TransaksiTrucking::find($trx_id);
        $orders = OrderTrucking::where('invoice', $trx->invoice)->get();

        $nomor = sprintf('%02d', date('m', strtotime($trx->tgl_invoice))) . '-' . sprintf('%03d', $no) . '/' . date('y');
        $date = date('Y-m-d');
        $total = 0;
        foreach ($orders as $ord) {
            $shipment = $ord->order ? $ord->order->tarif->shipmentInfo->nama : '-';
            $pembayar = $ord->order ? $ord->order->tarif->customer->nama : '-';
            $tujuan_trucking = $ord->tarif->tujuan->tujuanInfo->nama;
            Jurnal::create([
                'coa_id' => 31,
                'order_trucking_id' => $ord->id,
                'nomor' => $nomor,
                'nama' => 'Biaya Trucking ' . $pembayar . ' ' . $shipment . ' ' . $tujuan_trucking,
                'debit' => $ord->tarif_nominal,
                'credit' => 0,
                'tipe' => 'JNL',
                'no' => $no,
                'created_at' => $date,
                'relasi' => $nomor,
                'invoice_vendor' => !str_contains($trx->invoice, 'RAS-LT') ? $trx->invoice : null,
                'invoice_trucking' => str_contains($trx->invoice, 'RAS-LT') ? $trx->invoice : null,
                'container' => $ord->container,
                'nopol' => $ord->kendaraan->nopol ?? null,
            ]);
            JurnalSample::create([
                'coa_id' => 31,
                'order_trucking_id' => $ord->id,
                'nomor' => $nomor,
                'nama' => 'Pendapatan Trucking ' . $pembayar . ' ' . $shipment . ' ' . $tujuan_trucking,
                'credit' => $ord->tarif_nominal,
                'debit' => 0,
                'tipe' => 'JNL',
                'no' => $no,
                'created_at' => $date,
                'relasi' => $nomor,
                'invoice_vendor' => !str_contains($trx->invoice, 'RAS-LT') ? $trx->invoice : null,
                'invoice_trucking' => str_contains($trx->invoice, 'RAS-LT') ? $trx->invoice : null,
                'container' => $ord->container,
                'nopol' => $ord->kendaraan->nopol ?? null,
            ]);

            foreach ($ord->tagihans as $tag) {
                Jurnal::create([
                    'coa_id' => 31,
                    'order_trucking_id' => $ord->id,
                    'nomor' => $nomor,
                    'nama' => $tag->nama,
                    'debit' => $tag->jumlah,
                    'credit' => 0,
                    'tipe' => 'JNL',
                    'no' => $no,
                    'created_at' => $date,
                    'relasi' => $nomor,
                    'invoice_vendor' => !str_contains($trx->invoice, 'RAS-LT') ? $trx->invoice : null,
                    'invoice_trucking' => str_contains($trx->invoice, 'RAS-LT') ? $trx->invoice : null,
                    'container' => $ord->container,
                    'nopol' => $ord->kendaraan->nopol ?? null,
                ]);
                JurnalSample::create([
                    'coa_id' => 31,
                    'order_trucking_id' => $ord->id,
                    'nomor' => $nomor,
                    'nama' => $tag->nama,
                    'credit' => $tag->jumlah,
                    'debit' => 0,
                    'tipe' => 'JNL',
                    'no' => $no,
                    'created_at' => $date,
                    'relasi' => $nomor,
                    'invoice_vendor' => !str_contains($trx->invoice, 'RAS-LT') ? $trx->invoice : null,
                    'invoice_trucking' => str_contains($trx->invoice, 'RAS-LT') ? $trx->invoice : null,
                    'container' => $ord->container,
                    'nopol' => $ord->kendaraan->nopol ?? null,
                ]);
                $total += $tag->jumlah;
            }

            $total += $ord->tarif_nominal;
        }

        Jurnal::create([
            'coa_id' => 131,
            'order_trucking_id' => $ord->id,
            'nomor' => $nomor,
            'nama' => 'Hutang Trucking ' . $trx->pengirim . ' INV. ' . $trx->invoice,
            'credit' => $total,
            'debit' => 0,
            'tipe' => 'JNL',
            'no' => $no,
            'created_at' => $date,
            'relasi' => $nomor,
            'invoice_vendor' => !str_contains($trx->invoice, 'RAS-LT') ? $trx->invoice : null,
            'invoice_trucking' => str_contains($trx->invoice, 'RAS-LT') ? $trx->invoice : null,
        ]);
        JurnalSample::create([
            'coa_id' => 131,
            'order_trucking_id' => $ord->id,
            'nomor' => $nomor,
            'nama' => 'Piutang Trucking ' . $trx->pengirim . ' INV. ' . $trx->invoice,
            'debit' => $total,
            'credit' => 0,
            'tipe' => 'JNL',
            'no' => $no,
            'created_at' => $date,
            'relasi' => $nomor,
            'invoice_vendor' => !str_contains($trx->invoice, 'RAS-LT') ? $trx->invoice : null,
            'invoice_trucking' => str_contains($trx->invoice, 'RAS-LT') ? $trx->invoice : null,
        ]);

        OrderTrucking::where('invoice', $trx->invoice)->update([
            'jurnal_hutang' => $nomor,
        ]);
        $trx->update([
            'jurnal_hutang' => $nomor,
        ]);

        return response('success');
    }
}
