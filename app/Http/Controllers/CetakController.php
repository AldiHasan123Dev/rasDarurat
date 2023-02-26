<?php

namespace App\Http\Controllers;

use App\Models\BTTB;
use App\Models\Customer;
use App\Models\JadwalKapal;
use App\Models\Order;
use Illuminate\Http\Request;

class CetakController extends Controller
{
    public function suratJalan()
    {
        $penerima = Customer::get();
        return view('admin.cetak.surat_jalan', compact('penerima'));
    }

    public function pickOrder()
    {
        $pengirim = Customer::get();
        $penerima = Customer::get();
        $jadwal_kapal = JadwalKapal::all();
        return view('admin.cetak.pick_order', compact('pengirim','penerima','jadwal_kapal'));
    }

    public function packingList()
    {
        $order = Order::find(request('order_id'));
        if (!$order) {
            return redirect()->route('order.index');
        }

        $data = Order::where('job',$order->job)->get();
        return view('admin.cetak.packing_list', compact('order','data'));
    }

    public function bttb()
    {
        $order = Order::find(request('order_id'));
        if (!$order) {
            return redirect()->route('order.index');
        }

        $data = BTTB::where('order_id',$order->id)->get();
        return view('admin.cetak.bttb', compact('order','data'));
    }

    public function shipment()
    {
        $id = request('jadwal_kapal_id');
        $jadwal_kapal = JadwalKapal::find($id);

        if (!$jadwal_kapal) {
            return redirect()->route('order.index');
        }

        $orders = Order::whereHas('tarif', function($q) use($id){
            $q->where('jadwal_kapal_id', $id);
        })->get();

        return view('admin.cetak.shipment', compact('orders','jadwal_kapal'));
    }
}
