<?php

namespace App\Services;
use App\Models\Order;
use App\Models\OrderTrucking;

class SyncService {
    public function trucking($id)
    {
        $data = OrderTrucking::whereHas('tarif')->where('id',$id)->first();
        $i = 0;
        if($data){
            $pph_21 = 0;
            $pph_23 = 0;
            $price = $data->tarif->tarif;
            $tujuan = $data->tarif->tujuan->tujuanInfo->nama;
            $tb_tl = 0;
            if($data->customer_id!=2){
                if (($data->kendaraan->milik=='R2'||$data->kendaraan->milik=='vendor'||$data->customer->r2==1)&&$data->customer->pph_23==1) {
                    $pph_23 = $price * 0.02;
                }
            }else{
                if ($data->kendaraan->milik=='R1') {
                    $pph_21 = ($price / 0.97) * 0.03;
                }
            }

            if($data->ambil_empty_tambak_langon==1){
                if($data->tipe=='20'||$data->tipe=='COMBO'){
                    $tb_tl += 50000;
                }
                if($data->tipe=='40'){
                    $tb_tl += 75000;
                }
            }

            if($data->ambil_empty_teluk_langon==1){
                if($data->tipe=='20'||$data->tipe=='COMBO'){
                $tb_tl += 50000;
                }
                if($data->tipe=='40'){
                    $tb_tl += 75000;
                }
            }

            if($data->bongkar_full_teluk_langon==1){
                if($data->tipe=='20'||$data->tipe=='COMBO'){
                    $tb_tl += 50000;
                }
                if($data->tipe=='40'){
                    $tb_tl += 75000;
                }
            }

            $simpanan_kuli = $data->borongan_kuli - $data->kuli;
            $simpanan_sopir = $data->borongan - $data->sangu;
            if($simpanan_kuli < 0){
                $simpanan_kuli = 0;
            }

            $totalan = $simpanan_sopir + $simpanan_kuli + $tb_tl + $data->lain_lain + $data->stappel;
            $margin = $data->tarif->tarif - $data->borongan - $data->borongan_kuli - $data->uang_makan - $data->op - $data->cleaning;

            $data->update([
                'simpanan' => $simpanan_sopir,
                'simpanan_kuli' => $simpanan_kuli,
                'total_sopir' => $totalan,
                'margin' => $margin,
                'pph_21' => $pph_21,
                'pph_23' => $pph_23,
                'tb_tl' => $tb_tl,
            ]);
            $order = Order::where('container',$data->container)->where('seal',$data->seal)->first();
            if($order){
                $data->update(['order_id'=>$order->id]);
            }
            $i++;
        }

        return $data;
    }
}
