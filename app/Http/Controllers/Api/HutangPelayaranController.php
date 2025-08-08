<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HutangPelayaran;
use Illuminate\Http\Request;

class HutangPelayaranController extends Controller
{
    public function updateByOrder(Request $request)
    {
        $data = $request->all();
        
        $hutang = HutangPelayaran::where('order_id',$request->order_id)->first();
        $hutang->update($data);
        return response('success');
    }

    public function updateOrderId(Request $request)
    {
        $data = $request->data;
        $order_id = explode(',', $data['ids']);
        foreach ($order_id as $order) {
            $prop = $data['data['.$order];

            $prop['tgl_bg_opp'] = $data['tanggal_bg_opp'] ?? null;
            $prop['tgl_bg_opt'] = $data['tanggal_bg_opt'] ?? null;
            $prop['tgl_bg_ut'] = $data['tanggal_bg_ut'] ?? null;
            $prop['no_bg_opp'] = $data['no_bg_opp'] ?? null;
            $prop['no_bg_opt'] = $data['no_bg_opt'] ?? null;
            $prop['no_bg_ut'] = $data['no_bg_ut'] ?? null;
            $prop['nominal_bg_opp'] = $data['nominal_bg_opp'] ?? 0;
            $prop['nominal_bg_opt'] = $data['nominal_bg_opt'] ?? 0;
            $prop['nominal_bg_ut'] = $data['nominal_bg_ut'] ?? 0;
            $prop['pph'] = $data['pph'] ?? 0;
            $prop['pembulatan'] = $data['pembulatan'] ?? 0;
            $prop['penambahan'] = $data['penambahan'] ?? 0;
            $prop['penambahan_nominal'] = $data['penambahan_nominal'] ?? 0;
            // dd($prop);
            HutangPelayaran::where('order_id',$order)->update($prop);
        }
        return response('success');
    }
}
