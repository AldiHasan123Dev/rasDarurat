<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderTruckingResource;
use App\Models\Order;
use App\Models\OrderTrucking;
use Illuminate\Http\Request;

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
        OrderTrucking::whereIn('id',$order_id)->update([
            'tgl_total' => date('Y-m-d')
        ]);
        return back()->with('success','Data Berhasil disimpan!');
    }

    public function preInvoice()
    {
        $data = OrderTrucking::join('customer_trucking','customer_trucking.id','=','order_trucking.customer_id')
            ->select('order_trucking.*','customer_trucking.nama as customer','customer_trucking.id as id_customer')
            ->whereNull('order_trucking.invoice')
            ->whereNotNull('order_trucking.tgl_total')
            ->whereNotNull('order_trucking.sj_kembali_fa')
            ->orderBy('customer')
            ->get()
            ->groupBy('customer');
        return view('admin.trucking.pre_invoice', compact('data'));
    }

    public function invoice(Request $request)
    {
        $order_id = explode(',',$request->order_id);
        if (count($order_id)<=1&&$order_id[0]=="") {
            return back()->with('danger','Harap checklist terlebih dahulu!');
        }
        $orders = OrderTrucking::whereIn('id',$order_id)->get()->groupBy('customer_id');
        if($orders->count()>1){
            return back()->with('danger','Anda tidak bisa memilih '.$orders->count().' Customer sekaligus!, Harap untuk pilih satu Customer');
        }
        $orders = OrderTrucking::whereIn('id',$order_id)->orderBy('tgl_muat')->get();
        $order = $orders[0];
        $total = 0;
        return view('admin.trucking.invoice', compact('orders','order','total'));
    }
}
