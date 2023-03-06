<?php

namespace App\Http\Controllers;

use App\Models\JadwalKapal;
use App\Models\Kapal;
use App\Models\Order;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function import()
    {
        $data = JadwalKapal::where('id','>',6)->get();
        foreach ($data as $item ) {
            $item->kapal->update([
                'nama' => $item->voyage
            ]);
            $item->update([
                'voyage' => $item->kapal_id
            ]);
        }

        $data = Order::get();
        foreach ($data as $order ) {
            $order->update([
                'trucking' => $order->container,
                'container' => $order->seal,
                'nopol' => $order->trucking
            ]);
        }

        return response('successss');
    }
}
