<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agen;
use App\Models\JasaKirim;
use App\Models\Lokasi;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JasaKirimController extends Controller
{
    public function store(Request $request)
    {
        $res = JasaKirim::where('jadwal_kapal_id',$request->jadwal_kapal_id)->where('lokasi_id',$request->lokasi_id)->where('agen_id',$request->agen_id)->first();
        $agen = Agen::find($request->agen_id);
        if ($agen) {
            $lokasi = Lokasi::find($agen->lokasi_id);
            if(!$res){
                $res = JasaKirim::create([
                    'jadwal_kapal_id' => $request->jadwal_kapal_id,
                    'no_dooring' => $request->no_dooring,
                    'lokasi_id' => $request->lokasi_id,
                    'agen_id' => $request->agen_id,
                    'no' => JasaKirim::max('no') + 1,
                    'nominal' => $lokasi->harga
                ]);
            }else{
                $res->update([
                    'nominal' => $lokasi->harga
                ]);
            }

            Order::whereIn('id', json_decode($request->order_id))->update([
                'jasa_kirim_id' => $res->id
            ]);
        }
        return response($res);
    }
}
