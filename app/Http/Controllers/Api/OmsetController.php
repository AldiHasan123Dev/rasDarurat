<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\Omset;
use App\Models\Order;
use Illuminate\Http\Request;

class OmsetController extends Controller
{
    public function sync()
    {
        $month = request('month');
        $year = request('year');
        $job = $year.sprintf('%02d',$month);
        $orders = Order::where('job','like',$job.'%')->get();
        $data = array();
        foreach ($orders as $idx => $order) {
            $data[$idx]['order_id'] = $order->id;
            $data[$idx]['opt'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','OPT %')->sum('debit');
            $data[$idx]['opp'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','OPP %')->sum('debit');
            $data[$idx]['ut'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','UT %')->sum('debit');
            $data[$idx]['bl'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','BL %')->sum('debit');
            $data[$idx]['apbs'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','APBS %')->sum('debit');
            $data[$idx]['cleaning'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%CLEANING %')->sum('debit');
            $data[$idx]['lss'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','LSS %')->sum('debit');
            $data[$idx]['storage'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%storage %')->sum('debit');
            $data[$idx]['jasa_door'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%dooring %')->sum('debit');
            $data[$idx]['ops'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%operasional %')->sum('debit');
            $data[$idx]['segel'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%seal %')->sum('debit');
            $data[$idx]['buruh'] = Jurnal::orWhere('nama','LIKE','%buruh %')->where('coa_id',31)->where('order_id',$order->id)->orWhere('nama','LIKE','%kuli')->where('coa_id',31)->where('order_id',$order->id)->sum('debit');
            $data[$idx]['checker'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%checker %')->sum('debit');
            $data[$idx]['karantina'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%karantina %')->sum('debit');
            $data[$idx]['demmurage'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%demmurage %')->sum('debit');
            $data[$idx]['kirim_dokumen'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%Pengiriman Dokumen%')->sum('debit');
            $data[$idx]['flexibag'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%flexibag %')->sum('debit');
            $data[$idx]['rc'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%rc %')->sum('debit');
            $data[$idx]['biaya'] = $data[$idx]['opt'] + $data[$idx]['opp'] + $data[$idx]['ut'] + $data[$idx]['bl'] + $data[$idx]['apbs'] + $data[$idx]['cleaning'] + $data[$idx]['lss'] + $data[$idx]['storage'] + $data[$idx]['jasa_door'] + $data[$idx]['ops'] + $data[$idx]['segel'] + $data[$idx]['buruh'] + $data[$idx]['checker'] + $data[$idx]['karantina'] + $data[$idx]['demmurage'] + $data[$idx]['kirim_dokumen'] + $data[$idx]['flexibag'] + $data[$idx]['rc'];
            $data[$idx]['biaya_lain'] =  Jurnal::where('order_id',$order->id)->where('coa_id',31)->sum('debit') - $data[$idx]['biaya'];
            $data[$idx]['biaya'] += $data[$idx]['biaya_lain'];
            $data[$idx]['tarif'] = $order->tarif->tarif ?? 0;
            $data[$idx]['laba_kotor'] = $data[$idx]['tarif'] - $data[$idx]['biaya'];
            $data[$idx]['margin'] = $data[$idx]['laba_kotor'] / $data[$idx]['tarif'];
        }

        // return response($data);
        Omset::upsert($data,['order_id'],[
            'opp',
            'opt',
            'ut',
            'bl',
            'apbs',
            'cleaning',
            'lss',
            'storage',
            'jasa_door',
            'asuransi',
            'ops',
            'segel',
            'buruh',
            'checker',
            'karantina',
            'demmurage',
            'kirim_dokumen',
            'biaya_lain',
            'flexibag',
            'rc',
            'biaya',
            'tarif',
            'laba_kotor',
            'margin',
        ]);

        return response('success');
    }
}
