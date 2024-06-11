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

            $id = $res->merger ?? $res->id;

            Order::whereIn('id', json_decode($request->order_id))->update([
                'jasa_kirim_id' => $id
            ]);
        }
        return response($res);
    }

    public function unmerge(Request $request)
    {
        $jasakirim = JasaKirim::whereIn('id',$request->id)->get();
        foreach($jasakirim as $item){
            $orders = Order::where('jasa_kirim_id',$item->id)->get();
            foreach ($orders as $order) {
                $agen = Agen::find($order->agen_id);
                if($agen){
                    $lokasi = Lokasi::find($agen->lokasi_id);
                    $no = JasaKirim::max('no') + 1;
                    $res = JasaKirim::create([
                        'jadwal_kapal_id' => $order->jadwal_kapal_id,
                        'no_dooring' => 'SD/'.date('ymd').'/'.sprintf('%03d',$no),
                        'lokasi_id' => $agen->lokasi_id,
                        'agen_id' => $order->agen_id,
                        'no' => $no,
                        'nominal' => $lokasi->harga,
                        'barcode' => $item->barcode,
                        'tgl_kirim' => $item->tgl_kirim,
                        'tgl_terima' => $item->tgl_terima,
                    ]);
                    $order->update([
                        'jasa_kirim_id' => $res->id
                    ]);
                }
            }
        }

        JasaKirim::whereIn('id',$request->id)->delete();
        return response('success');
    }

    public function merge()
    {
        $data = JasaKirim::whereNotNull('barcode')->whereNotNull('tgl_kirim')->whereNotNull('nominal')->get()->groupBy('barcode');
        foreach ($data as $barcode) {
            if($barcode->count()>1){    
                $jasakirim = $barcode->first();
                $group = JasaKirim::where('barcode',$jasakirim->barcode)->where('nominal',$jasakirim->nominal)->get();
                foreach ($group as $idx => $item) {
                    if ($idx==0) {
                        $jasa_kirim_id = $item->id;
                    }
                    Order::where('jasa_kirim_id',$item->id)->update([
                        'jasa_kirim_id' => $jasa_kirim_id
                    ]);
                    if ($idx!=0) {
                        $item->update([
                            'merger' => $jasa_kirim_id
                        ]);
                    }
                }
            }
        }

        return response('success');
    }

    public function addDrafJurnal(Request $request)
    {
        $no = JasaKirim::max('no_draf') + 1;
        $invoice = 'JK/'.date('ymd').'/'.sprintf('%02d',$no);
        JasaKirim::whereIn('id',$request->id)->update([
            'tgl_invoice' => date('Y-m-d'),
            'invoice' => $invoice,
            'no_draf' => $no
        ]);

        return response('success');
    }

}
