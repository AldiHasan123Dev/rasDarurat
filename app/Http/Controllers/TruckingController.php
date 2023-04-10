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
        $data = OrderTrucking::all()->sortByDesc('tgl_muat');
        $data = OrderTruckingResource::collection($data);
        return view('admin.trucking.pre_invoice', compact('data'));
    }

    public function invoice(OrderTrucking $order)
    {
        $orders = OrderTrucking::where('customer_id',$order->customer_id)->whereNull('invoice')->get();
        $total = 0;
        foreach ($orders as $items ) {
            $total += $items->tarif->tarif;
        }
        return view('admin.trucking.invoice', compact('orders','order','total'));
    }
}
