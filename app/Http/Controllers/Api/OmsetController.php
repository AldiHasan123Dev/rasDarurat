<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JurnalResource;
use App\Models\Jurnal;
use App\Models\Omset;
use App\Models\Order;
use Illuminate\Http\Request;

class OmsetController extends Controller
{
    public function sync()
    {
        $id = request('id');
        $ids = array_slice($id,request('start'),request('end'));
        $end = request('start') + request('end');
        $orders = Order::whereIn('id',$ids)->where('lock_omset',0)->get();
        $data = array();
        foreach ($orders as $idx => $order) {
            $cbm = $order->tarif->satuanInfo->nama ?? '-';
            $tarif = $order->tarif->tarif ?? 0;
            if($cbm=='CBM'){
                $tarif *= $order->bttb->sum('vol');
            }
            $data[$idx]['order_id'] = $order->id;
            $data[$idx]['trucking'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%trucking%')->sum('debit');
            $data[$idx]['j_trucking'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%trucking%')->pluck('id')->toJson();
            if($order->truckingInfo){
                $tipe = $order->truckingInfo->kendaraan->milik;
                if($order->truckingInfo->customer->r1 == 1){
                    $tipe = 'R1';
                }
                if($order->truckingInfo->customer->r2 == 1){
                    $tipe = 'R2';
                }
                if($tipe == 'R2'){
                    $data[$idx]['trucking'] = $order->truckingInfo->tarif->tarif ?? 0;
                    $data[$idx]['j_trucking'] = '[]';
                }
            }
            $data[$idx]['opt'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','OPT %')->sum('debit');
            $data[$idx]['j_opt'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','OPT %')->pluck('id')->toJson();
            $data[$idx]['opp'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','OPP %')->orWhere('nama','LIKE','%stamp%')->where('coa_id',31)->where('order_id',$order->id)->sum('debit');
            $data[$idx]['j_opp'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','OPP %')->orWhere('nama','LIKE','%stamp%')->where('coa_id',31)->where('order_id',$order->id)->pluck('id')->toJson();
            $data[$idx]['ut'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','UT %')->sum('debit');
            $data[$idx]['j_ut'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','UT %')->pluck('id')->toJson();
            $data[$idx]['bl'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','BL %')->sum('debit');
            $data[$idx]['j_bl'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','BL %')->pluck('id')->toJson();
            $data[$idx]['apbs'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','APBS %')->sum('debit');
            $data[$idx]['j_apbs'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','APBS %')->pluck('id')->toJson();
            $data[$idx]['cleaning'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%CLEANING %')->sum('debit');
            $data[$idx]['j_cleaning'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%CLEANING %')->pluck('id')->toJson();
            $data[$idx]['lss'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','LSS %')->sum('debit');
            $data[$idx]['j_lss'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','LSS %')->pluck('id')->toJson();
            $data[$idx]['storage'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%storage %')->sum('debit');
            $data[$idx]['j_storage'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%storage %')->pluck('id')->toJson();
            $data[$idx]['jasa_door'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%dooring %')->sum('debit');
            $data[$idx]['j_jasa_door'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%dooring %')->pluck('id')->toJson();
            $data[$idx]['asuransi'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%asuransi %')->sum('debit');
            $data[$idx]['j_asuransi'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%asuransi %')->pluck('id')->toJson();
            $data[$idx]['ops'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE',"biaya operasional xpdc %0")->orWhere('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE',"biaya operasional xpdc %'")->sum('debit');
            $data[$idx]['j_ops'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE',"biaya operasional xpdc %0")->orWhere('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE',"biaya operasional xpdc %'")->pluck('id')->toJson();
            $data[$idx]['segel'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','pembayaran seal%')->sum('debit');
            $data[$idx]['j_segel'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','pembayaran seal%')->pluck('id')->toJson();
            $data[$idx]['ops_seal'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','biaya operasional %, seal')->orWhere('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','biaya operasional % , seal')->sum('debit');
            $data[$idx]['j_ops_seal'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','biaya operasional %, seal')->orWhere('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','biaya operasional % , seal')->pluck('id')->toJson();
            $data[$idx]['ops_seal_cleaning'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','biaya operasional %, seal%')->orWhere('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','biaya operasional % , seal%')->sum('debit');
            $data[$idx]['j_ops_seal_cleaning'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','biaya operasional %, seal%')->orWhere('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','biaya operasional % , seal%')->pluck('id')->toJson();
            $data[$idx]['buruh'] = Jurnal::orWhere('nama','LIKE','Biaya TKBM%')->where('coa_id',31)->where('order_id',$order->id)->orWhere('nama','LIKE','Biaya Kuli%')->where('coa_id',31)->where('order_id',$order->id)->orWhere('nama','LIKE','Biaya Buruh%')->where('coa_id',31)->where('order_id',$order->id)->sum('debit');
            $data[$idx]['j_buruh'] = Jurnal::orWhere('nama','LIKE','Biaya TKBM%')->where('coa_id',31)->where('order_id',$order->id)->orWhere('nama','LIKE','Biaya Kuli%')->where('coa_id',31)->where('order_id',$order->id)->orWhere('nama','LIKE','Biaya Buruh%')->where('coa_id',31)->where('order_id',$order->id)->pluck('id')->toJson();
            $data[$idx]['checker'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%checker %')->sum('debit');
            $data[$idx]['j_checker'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%checker %')->pluck('id')->toJson();
            $data[$idx]['karantina'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%karantina %')->sum('debit');
            $data[$idx]['j_karantina'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%karantina %')->pluck('id')->toJson();
            $data[$idx]['demmurage'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%demmurage %')->sum('debit');
            $data[$idx]['j_demmurage'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%demmurage %')->pluck('id')->toJson();
            $data[$idx]['kirim_dokumen'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%Pengiriman Dokumen%')->sum('debit');
            $data[$idx]['j_kirim_dokumen'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%Pengiriman Dokumen%')->pluck('id')->toJson();
            $data[$idx]['flexibag'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%flexibag %')->sum('debit');
            $data[$idx]['j_flexibag'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%flexibag %')->pluck('id')->toJson();
            $data[$idx]['rc'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%rc %')->sum('debit');
            $data[$idx]['j_rc'] = Jurnal::where('order_id',$order->id)->where('coa_id',31)->where('nama','LIKE','%rc %')->pluck('id')->toJson();
            $data[$idx]['biaya'] = $data[$idx]['trucking'] + $data[$idx]['opt'] + $data[$idx]['opp'] + $data[$idx]['ut'] + $data[$idx]['bl'] + $data[$idx]['apbs'] + $data[$idx]['cleaning'] + $data[$idx]['lss'] + $data[$idx]['storage'] + $data[$idx]['jasa_door'] + $data[$idx]['ops'] + $data[$idx]['segel'] + $data[$idx]['ops_seal'] + $data[$idx]['ops_seal_cleaning'] + $data[$idx]['buruh'] + $data[$idx]['checker'] + $data[$idx]['karantina'] + $data[$idx]['demmurage'] + $data[$idx]['kirim_dokumen'] + $data[$idx]['flexibag'] + $data[$idx]['rc'] + $data[$idx]['asuransi'];
            $data[$idx]['biaya_lain'] =  Jurnal::where('order_id',$order->id)->where('coa_id',31)->sum('debit') - $data[$idx]['biaya'];
            $data[$idx]['biaya'] += $data[$idx]['biaya_lain'];
            $data[$idx]['tarif'] = $tarif;
            $data[$idx]['laba_kotor'] = $data[$idx]['tarif'] - $data[$idx]['biaya'];
            $data[$idx]['margin'] = $data[$idx]['laba_kotor'] / $data[$idx]['tarif'];

            $biaya_lain = json_encode(
                    array_merge(
                        json_decode($data[$idx]['j_trucking'], true),
                        json_decode($data[$idx]['j_opp'], true),
                        json_decode($data[$idx]['j_opt'], true),
                        json_decode($data[$idx]['j_ut'], true),
                        json_decode($data[$idx]['j_bl'], true),
                        json_decode($data[$idx]['j_apbs'], true),
                        json_decode($data[$idx]['j_cleaning'], true),
                        json_decode($data[$idx]['j_lss'], true),
                        json_decode($data[$idx]['j_storage'], true),
                        json_decode($data[$idx]['j_jasa_door'], true),
                        json_decode($data[$idx]['j_asuransi'], true),
                        json_decode($data[$idx]['j_ops'], true),
                        json_decode($data[$idx]['j_segel'], true),
                        json_decode($data[$idx]['j_ops_seal'], true),
                        json_decode($data[$idx]['j_ops_seal_cleaning'], true),
                        json_decode($data[$idx]['j_buruh'], true),
                        json_decode($data[$idx]['j_checker'], true),
                        json_decode($data[$idx]['j_karantina'], true),
                        json_decode($data[$idx]['j_demmurage'], true),
                        json_decode($data[$idx]['j_kirim_dokumen'], true),
                        json_decode($data[$idx]['j_flexibag'], true),
                        json_decode($data[$idx]['j_rc'], true),
                        json_decode(Jurnal::where('order_id',$order->id)->where('coa_id',31)->pluck('id')->toJson(), true),
                    )
                );

                $data[$idx]['j_biaya_lain'] = json_encode($this->findUniqueValue(json_decode($biaya_lain,true)));
                $a = json_decode($biaya_lain,true);
                $b = array_unique($a);
                $arr = [];
                foreach($b as $item){
                    array_push($arr,$item);
                }
                $data[$idx]['j_biaya'] = json_encode($arr);
        }

        // return response($data);
        Omset::upsert($data,['order_id'],[
            'trucking',
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
            'ops_seal',
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
            'j_trucking',
            'j_opp',
            'j_opt',
            'j_ut',
            'j_bl',
            'j_apbs',
            'j_cleaning',
            'j_lss',
            'j_storage',
            'j_jasa_door',
            'j_asuransi',
            'j_ops',
            'j_segel',
            'j_ops_seal',
            'j_buruh',
            'j_checker',
            'j_karantina',
            'j_demmurage',
            'j_kirim_dokumen',
            'j_biaya',
            'j_biaya_lain',
            'j_flexibag',
            'j_rc',
        ]);

        if(count($id) > $end){
            return response($end);
        }else{
            return response('complete');
        }
    }

    public function getJurnal(Request $request)
    {
        $id = str_replace(['[',']','"'],'',$request->id);
        $id = explode(',',$id);
        $data = Jurnal::whereIn('id',$id)->get();
        $res = JurnalResource::collection($data);
        return response($res);
    }

    public function findUniqueValue($array) {
        $counted_values = array_count_values($array);
        $arr = [];
        foreach ($counted_values as $value => $count) {
            if ($count === 1) {
                array_push($arr,$value);
            }
        }
        return $arr;
    }

    public function addJurnal()
    {
        $omset_id = request('omset_id');
        $jurnal_id = request('jurnal_id');
        $type = request('to');
        $before = request('type');
        $col_before = substr($before,2);
        $col = substr($type,2);
        $omset = Omset::find($omset_id);
        $reload = false;
        if($omset){
            $omset_arr = $omset->toArray();
            $rm_col = str_replace(['[',']'],'',$omset_arr[$before]);
            $arr_rm_col = explode(',',$rm_col);
            $new_arr_col = [];
            foreach ($arr_rm_col as $item) {
                if($item!=$jurnal_id){
                    array_push($new_arr_col,$item);
                }
            }
            $output_col = json_encode($new_arr_col);
            $output_col = str_replace('"','',$output_col);

            $rm = json_decode($omset_arr[$type],true);
            if(is_null($rm)){
                $rm = array();
            }
            array_push($rm,$jurnal_id);
            array_unique($rm);
            // $rm = $omset_arr[$type];
            $input = $rm;
            $output = json_encode($rm);
            $output = str_replace('"','',$output);

            $debit_before = Jurnal::whereIn('id',$new_arr_col)->sum('debit');
            $update[$col_before] = $debit_before;
            $update[$before] = $output_col;
            $omset->update($update);
            $debit = Jurnal::whereIn('id',$input)->sum('debit');
            $update = [];
            $update[$col] = $debit;
            $update[$type] = $output;
            if($col=='none'){
                $update['biaya'] = $omset->biaya - $debit;
                $update['laba_kotor'] = $omset->tarif - $update['biaya'];
                $update['margin'] = $update['laba_kotor'] / $omset->tarif;
                $reload = true;
            }
            $omset->update($update);
            return response([
                'message' => 'Data berhasil disimpan!',
                'jurnal' => $output_col,
                'a_debit' => $debit,
                'a_jurnal' => $output,
                'b_debit' => $debit_before,
                'b_jurnal' => $output_col,
                'status' => true,
                'reload' => $reload,
            ]);
        }
        return response([
            'message' => 'Maaf ada yang salah!',
            'jurnal' => "[]",
            'a_debit' => 0,
            'a_jurnal' => "[]",
            'b_debit' => 0,
            'b_jurnal' => "[]",
            'status' => false,
            'reload' => $reload,
        ]);
    }
}
