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
