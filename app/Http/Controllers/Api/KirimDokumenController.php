<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KirimDokumen;
use App\Models\Order;
use Illuminate\Http\Request;

class KirimDokumenController extends Controller
{
    public function index()
    {
        $jasa_kirim_id = request('jasa_kirim_id');
        $data = KirimDokumen::where('jasa_kirim_id', $jasa_kirim_id)->get();
        return response($data);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $cek = explode(' ',$data['nama']);
        for ($i=0; $i < count($cek); $i++) {
            $name = $cek[$i];
            $order = Order::where('invoice',$name)->first();
            if($order){
                $data['order_id'] = $order->id;
            }else{
                $arr = explode('-',$name);
                if(count($arr)==2){
                    $order = Order::where('job',$arr[0])->where('no_job',(int)$arr[1])->first();
                    if($order){
                        $data['order_id'] = $order->id;
                    }
                }
            }
        }
        $kd = KirimDokumen::create($data);
        return response($kd);
    }

    public function destroy(KirimDokumen $kirim_dokumen){
        $kirim_dokumen->delete();
        return response('success');
    }
}
