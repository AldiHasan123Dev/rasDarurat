<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTrucking;
use App\Models\TarifTrucking;
use Illuminate\Http\Request;

class TarifTruckingController extends Controller
{
    public function createOrUpdate(Request $request)
    {
        $data = $request->all();
        if (!empty($data['tarif'])) {
            $data['tarif'] = str_replace([',','.'],'',$request->tarif);
        }

        try {
            if ($request->tarif_id) {
                TarifTrucking::find($request->tarif_id)->update($data);
                $this->sync_trucking($request->tarif_id);
            }else{
                TarifTrucking::create($data);
            }
            return response('success');
        } catch (\Throwable $th) {
            return response($th);
        }

    }

    public function delete()
    {
        TarifTrucking::find(request('id'))->delete();
        return response('Delete Berhasil');
    }

    public function sync_trucking($tarif_id)
    {
        $data = OrderTrucking::where('tarif_id',$tarif_id)->get();
        $tar = TarifTrucking::find($tarif_id);
        $i = 0;
        foreach($data as $item){
            $pph_21 = 0;
            $pph_23 = 0;
            $price = $item->tarif->tarif;
            $tb_tl = 0;
            if($item->customer_id!=2){
                if ($item->kendaraan->milik=='R2'&&$item->customer->pph_23==1) {
                    $pph_23 = $price * 0.02;
                }
            }else{
                if ($item->kendaraan->milik=='R1') {
                    $pph_21 = ($price / 0.97) * 0.03;
                }
            }

            if($item->ambil_empty_tambak_langon==1){
                if($item->tipe=='20'||$item->tipe=='COMBO'){
                    $tb_tl += 50000;
                }
                if($item->tipe=='40'){
                    $tb_tl += 75000;
                }
            }

            if($item->ambil_empty_teluk_langon==1){
                if($item->tipe=='20'||$item->tipe=='COMBO'){
                $tb_tl += 50000;
                }
                if($item->tipe=='40'){
                    $tb_tl += 75000;
                }
            }

            if($item->bongkar_full_teluk_langon==1){
                if($item->tipe=='20'||$item->tipe=='COMBO'){
                    $tb_tl += 50000;
                }
                if($item->tipe=='40'){
                    $tb_tl += 75000;
                }
            }

            $simpanan_kuli = $item->borongan_kuli - $item->kuli;
            $simpanan_sopir = $item->borongan - $item->sangu;
            if($simpanan_kuli < 0){
                $simpanan_kuli = 0;
            }

            $totalan = $simpanan_sopir + $simpanan_kuli + $tb_tl + $item->lain_lain + $item->stappel;
            $margin = $item->tarif->tarif - $item->borongan - $item->borongan_kuli - $item->uang_makan - $item->op - $item->cleaning;

            $item->update([
                'tujuan' => $tar->tujuan->tujuanInfo->nama,
                'simpanan' => $simpanan_sopir,
                'simpanan_kuli' => $simpanan_kuli,
                'total_sopir' => $totalan,
                'margin' => $margin,
                'pph_21' => $pph_21,
                'pph_23' => $pph_23,
                'tb_tl' => $tb_tl,
            ]);
            $order = Order::where('container',$item->container)->where('seal',$item->seal)->first();
            if($order){
                $item->update(['order_id'=>$order->id]);
            }
            $i++;
        }

        return 'Data berhasil diupdate: '.$i;
    }
}
