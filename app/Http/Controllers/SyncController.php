<?php

namespace App\Http\Controllers;

use App\Models\JadwalKapal;
use App\Models\Kapal;
use App\Models\Order;
use App\Models\Tarif;
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

    public function sync()
    {
        $data = Order::all();
        $tarif = Tarif::all();
        foreach ($data as $item ) {
            if (substr($item->job,0,2)==23||substr($item->job,0,2)=='23') {
                $job = substr($item->job,2,8);
                $new = '2023'.$job;
                $asuransi = null;
                if(!is_null($item->asuransi)){
                    if($item->asuransi==1||$item->asuransi=='1'){
                        $asuransi = 'ADA';
                    }
                    if($item->asuransi==0||$item->asuransi=='0'){
                        $asuransi = 'TIDAK';
                    }
                }

                $sat = null;
                if ($item->tarif) {
                    $sat =  $item->tarif->satuan ?? null;
                }
                $item->update([
                    'satuan' => $sat,
                    'job' => $new,
                    'asuransi' => $asuransi
                ]);

                foreach ($tarif as $item ) {
                    $tipe = $item->shipmentInfo->nama[0];
                    if($tipe=='F'||$tipe=='f'){
                        $item->update([
                            'satuan' => 1
                        ]);
                    }else{
                        $item->update([
                            'satuan' => 2
                        ]);
                    }
                }
            }

        }


        return response('success');
    }
}
