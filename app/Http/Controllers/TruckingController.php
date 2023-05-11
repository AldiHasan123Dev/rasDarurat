<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderTruckingResource;
use App\Http\Resources\TransaksiSopirResource;
use App\Http\Resources\TransaksiTruckingResource;
use App\Models\CustomerTrucking;
use App\Models\Kendaraan;
use App\Models\Order;
use App\Models\OrderTrucking;
use App\Models\SanguSopir;
use App\Models\Sopir;
use App\Models\TransaksiSopir;
use App\Models\TransaksiTrucking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TruckingController extends Controller
{
    public function order()
    {
        return view('admin.trucking.order');
    }

    public function totalan_sopir()
    {
        $data = OrderTrucking::join('sopir','sopir.id','=','order_trucking.sopir_id')
                ->select('order_trucking.*','sopir.nama')
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
        $order_id = explode(',',$request->order_id);
        if (count($order_id)<=1&&$order_id[0]=="") {
            return back()->with('danger','Harap checklist order!');
        }
        $orders = OrderTrucking::whereIn('id',$order_id)->get();
        $cek = OrderTrucking::whereIn('id',$order_id)->get()->groupBy('sopir_id');
        $order = $orders[0];
        if($cek->count()>1){
            return back()->with('danger','Anda tidak bisa memilih '.$cek->count().' Sopir sekaligus!, Harap untuk pilih satu sopir');
        }
        return view('admin.trucking.totalan_sopir_invoice', compact('orders','order','order_id'));
    }

    public function cetak_invoice_sopir()
    {
        $invoice = request('invoice');
        $order = OrderTrucking::where('invoice_sopir',$invoice)->first();
        if(!$order){
            return back()->with('danger','Invoice tidak ditemukan!');
        }
        $orders = OrderTrucking::where('invoice_sopir',$invoice)->get();
        return view('admin.trucking.totalan_sopir_invoice', compact('orders','order','invoice'));
    }

    public function generate_totalan_sopir(Request $request)
    {
        $order_id = explode(',',$request->order_id);
        if (count($order_id)<=1&&$order_id[0]=="") {
            return back()->with('danger','Harap checklist order!');
        }
        $orders = OrderTrucking::whereIn('id',$order_id)->get()->groupBy('sopir_id');
        if($orders->count()>1){
            return back()->with('danger','Anda tidak bisa memilih '.$orders->count().' Sopir sekaligus!, Harap untuk pilih satu sopir');
        }
        $no = TransaksiSopir::max('order') + 1;
        $invoice = 'RIT/'.date('ymd').'/'.sprintf('%03d',$no);
        OrderTrucking::whereIn('id',$order_id)->update([
            'tgl_total' => date('Y-m-d'),
            'order_sopir' => $no,
            'invoice_sopir' => $invoice
        ]);
        TransaksiSopir::create([
            'tgl_invoice' => date('Y-m-d'),
            'invoice' => $invoice,
            'sopir_id' => $request->sopir_id,
            'order_id' => '['.$request->order_id.']',
            'order_trucking_id' => $request->order_trucking_id,
            'total' => $request->total,
            'order' => $no,
            'submited_by' => Auth::id(),
        ]);
        return redirect()->route('trucking.cetak_invoice.totalan_sopir',['invoice'=>$invoice]);
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
        $data1 = OrderTrucking::join('customer_trucking','customer_trucking.id','=','order_trucking.customer_id')
            ->join('kendaraan','kendaraan.id','=','order_trucking.kendaraan_id')
            ->select('order_trucking.*','customer_trucking.nama as customer','customer_trucking.id as id_customer')
            ->where('kendaraan.milik','R1')
            ->whereNull('order_trucking.invoice')
            ->whereNotNull('order_trucking.tgl_total')
            ->whereNotNull('order_trucking.sj_kembali_fa')
            ->orderBy('customer')
            ->get()
            ->groupBy('customer');

        $data2 = OrderTrucking::join('customer_trucking','customer_trucking.id','=','order_trucking.customer_id')
            ->join('kendaraan','kendaraan.id','=','order_trucking.kendaraan_id')
            ->select('order_trucking.*','customer_trucking.nama as customer','customer_trucking.id as id_customer')
            ->where('kendaraan.milik','R2')
            ->where('order_trucking.customer_id','!=',2)
            ->whereNull('order_trucking.invoice')
            ->whereNotNull('order_trucking.tgl_total')
            ->whereNotNull('order_trucking.sj_kembali_fa')
            ->orderBy('customer')
            ->get()
            ->groupBy('customer');

        $data3 = OrderTrucking::join('customer_trucking','customer_trucking.id','=','order_trucking.customer_id')
            ->join('kendaraan','kendaraan.id','=','order_trucking.kendaraan_id')
            ->select('order_trucking.*','customer_trucking.nama as customer','customer_trucking.id as id_customer')
            ->where('kendaraan.milik','vendor')
            ->where('order_trucking.customer_id','!=',2)
            ->whereNull('order_trucking.invoice')
            ->whereNotNull('order_trucking.sj_kembali_fa')
            ->orderBy('customer')
            ->get()
            ->groupBy('customer');
        return view('admin.trucking.pre_invoice', compact('data1','data2','data3'));
    }

    public function cetak_invoice_get()
    {
        $invoice = request('invoice');
        $order = OrderTrucking::where('invoice',$invoice)->first();
        if(!$order){
            return back()->with('danger','Invoice Tidak ditemukan!');
        }
        $r1s = OrderTrucking::where('invoice',$invoice)->whereHas('kendaraan', function($q){
            $q->where('milik','R1');
            $q->orWhere('milik','vendor');
        })->orderBy('tgl_muat')->get()->groupBy('tarif_id');
        $r2s = OrderTrucking::where('invoice',$invoice)->whereHas('kendaraan', function($q){
            $q->where('milik','R2');
        })->orderBy('tgl_muat')->get()->groupBy('tarif_id');
        return view('admin.trucking.invoice', compact('order','r1s','r2s','invoice'));
    }

    public function cetak_invoice(Request $request)
    {
        $order_id = explode(',',$request->order_id);
        if (count($order_id)<=1&&$order_id[0]=="") {
            return back()->with('danger','Harap checklist terlebih dahulu!');
        }
        $orders = OrderTrucking::whereIn('id',$order_id)->get()->groupBy('customer_id');
        if($orders->count()>1){
            return back()->with('danger','Anda tidak bisa memilih '.$orders->count().' Customer sekaligus!, Harap untuk pilih satu Customer');
        }
        $order = OrderTrucking::whereIn('id',$order_id)->first();
        $null_job = OrderTrucking::whereIn('id',$order_id)->whereNull('order_id')->count();
        $tipe = $order->kendaraan->milik;
        $r1s = OrderTrucking::whereIn('id',$order_id)->whereHas('kendaraan', function($q){
            $q->where('milik','R1');
            $q->orWhere('milik','vendor');
        })->orderBy('tgl_muat')->get()->groupBy('tarif_id');
        $r2s = OrderTrucking::whereIn('id',$order_id)->whereHas('kendaraan', function($q){
            $q->where('milik','R2');
        })->orderBy('tgl_muat')->get()->groupBy('tarif_id');
        if($r1s->count()>0&&$r2s->count()>0){
            return back()->with('danger','Anda tidak bisa memilih 2 Tipe invoice(R1 & R2) sekaligus!');
        }
        return view('admin.trucking.invoice', compact('orders','order','r1s','r2s','order_id','tipe','null_job'));
    }

    public function generate_invoice(Request $request)
    {
        $roman_numerals = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"); // daftar angka Romawi
        $month_number = date("n"); // mengambil nomor bulan dari tanggal
        $month_roman = $roman_numerals[$month_number]; // mengambil angka Romawi yang sesuai
        $order_id = explode(',',$request->order_id);
        $no1 = 0;
        $no2 = 0;
        $no3 = 0;
        if($request->tipe=='R1'){
            $no1 = TransaksiTrucking::max('order_r1') + 1;
            if($no1==1){
                $no1 = 109;
            }
            $invoice = sprintf('%03d',$no1).'/'.$month_roman.'/'.date('y');
        }else if($request->tipe=='R2'){
            $no2 = TransaksiTrucking::max('order_r2') + 1;
            if($no2==1){
                $no2 = 53;
            }
            $invoice = sprintf('%03d',$no2).'/RAS-LT/'.$month_roman.'/'.date('y');
        }else{
            $no3= TransaksiTrucking::max('order_vendor') + 1;
            $invoice = sprintf('%03d',$no3).'/VENDOR-'.$month_roman.'/'.date('y');
        }

        TransaksiTrucking::create([
            'order_trucking_id' => $request->order,
            'order_id' => '['.$request->order_id.']',
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
        ]);

        OrderTrucking::whereIn('id',$order_id)->update([
            'tgl_invoice' => date('Y-m-d'),
            'invoice' => $invoice,
            'total_invoice' => $request->total,
        ]);

        return redirect()->route('trucking.cetak_get.invoice',['invoice'=>$invoice]);
    }

    public function monitoring()
    {
        $sj_kembali = OrderTrucking::whereNull('sj_kembali')->orderBy('tgl_muat')->get();
        $orders = OrderTrucking::whereNotNull('sj_kembali')->orderBy('tgl_muat')->get();
        $sj_kembali = OrderTruckingResource::collection($sj_kembali);
        $orders = OrderTruckingResource::collection($orders);
        $kendaraan = Kendaraan::all()->where('is_active',1)->sortBy('nopol');
        $sopir = Sopir::where('is_active',1)->orderBy('nama','asc')->get();
        $tujuan = SanguSopir::join('lokasi','lokasi.id','=','sangu_sopir.tujuan')->select('sangu_sopir.*')->orderBy('lokasi.nama','asc')->get();
        $customers = CustomerTrucking::all()->sortBy('nama');
        $update = OrderTrucking::whereNull('order_id')->get();
        foreach ($update as $item ) {
            $order = Order::where('container',$item->container)->where('seal',$item->seal)->first();
            if($order){
                $item->update(['order_id'=>$order->id]);
            }
        }
        return view('admin.trucking.monitoring', compact('sj_kembali','orders','kendaraan','sopir','tujuan','customers'));
    }

}
