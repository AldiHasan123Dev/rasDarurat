<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JurnalResource;
use App\Models\Jurnal;
use App\Models\JurnalBalik;
use App\Models\Omset;
use App\Models\Order;
use App\Models\PraOmset;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OmsetController extends Controller
{
    public function sync()
    {
        $id = request('id');
        $ids = array_slice($id,request('start'),request('end'));
        $end = request('start') + request('end');
        $data = array();
        $model = new Omset();
        $coa_id = [93,38,133,134,135,140,76,81];
        $orders = Order::whereIn('id',$ids)->where('lock_omset',1)->get();
        if(request('is_pra')){
            $orders = Order::whereIn('id',$ids)->where('lock_omset',0)->get();
            $coa_id = [38,31,133,134,135,140,76,81];
            $model = new PraOmset();
        }
        foreach ($orders as $idx => $order) {
            $cbm = $order->tarif->satuanInfo->nama ?? '-';
            $tarif = $order->tarif->tarif ?? 0;
            if($cbm=='CBM'){
                $tarif *= $order->bttb->sum('vol');
            }
            $data[$idx]['order_id'] = $order->id;
            $data[$idx]['trucking'] = 0;
            $data[$idx]['j_trucking'] = '[]';
            // $data[$idx]['trucking'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%trucking%')->sum('debit');
            // $data[$idx]['j_trucking'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%trucking%')->pluck('id')->toJson();
            $tipe = '';
            if($order->truckingInfo && $order->trucking == 'XPDC'){
                $tipe = $order->truckingInfo->kendaraan->milik;
                if($order->truckingInfo->customer->r1 == 1){
                    $tipe = 'R1';
                }
                if($order->truckingInfo->customer->r2 == 1){
                    $tipe = 'R2';
                }
                if($tipe == 'R2'){
                    $data[$idx]['trucking'] = ($order->truckingInfo->tarif->tarif + $order->truckingInfo->tb_tl + $order->truckingInfo->tambah_isi + $order->truckingInfo->tambah_solar + $order->truckingInfo->stappel + $order->truckingInfo->lain_lain) ?? 0;
                    $data[$idx]['j_trucking'] = '[]';
                }
            }else{
                $data[$idx]['trucking'] = 0;
                $data[$idx]['j_trucking'] = '[]';
            }
            $data[$idx]['job_slip_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% do pod %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% do pod %')->sum('credit');
            $data[$idx]['lolo_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% lolo pod %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% lolo pod %')->sum('credit');
            $data[$idx]['cleaning_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% cleaning pod %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% cleaning pod %')->sum('credit');
            $data[$idx]['ops_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% operasional pod %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% operasional pod %')->sum('credit');
            $data[$idx]['opt_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% pelabuhan / job slip pod')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% pelabuhan / job slip pod')->sum('credit');
            $data[$idx]['truck_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% trucking pod %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% trucking pod %')->sum('credit');
            $data[$idx]['kuli_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% buruh / kuli pod %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% buruh / kuli pod %')->sum('credit');
            $data[$idx]['storage_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% perpanjangan do %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% perpanjangan do %')->sum('credit');
            $data[$idx]['j_job_slip_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% do pod %')->pluck('id')->toJson();
            $data[$idx]['j_lolo_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% lolo pod %')->pluck('id')->toJson();
            $data[$idx]['j_cleaning_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% cleaning pod %')->pluck('id')->toJson();
            $data[$idx]['j_ops_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% operasional pod %')->pluck('id')->toJson();
            $data[$idx]['j_opt_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% pelabuhan / job slip pod')->pluck('id')->toJson();
            $data[$idx]['j_truck_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% trucking pod %')->pluck('id')->toJson();
            $data[$idx]['j_kuli_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% buruh / kuli pod %')->pluck('id')->toJson();
            $data[$idx]['j_storage_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% perpanjangan do %')->pluck('id')->toJson();

            $data[$idx]['opt'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','OPT %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','OPT %')->sum('credit');
            $data[$idx]['j_opt'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','OPT %')->pluck('id')->toJson();
            $data[$idx]['opp'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','OPP %')->orWhere('nama','LIKE','%stamp%')->whereIn('coa_id',$coa_id)->where('order_id',$order->id)->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','OPP %')->orWhere('nama','LIKE','%stamp%')->whereIn('coa_id',$coa_id)->where('order_id',$order->id)->sum('credit');
            $data[$idx]['j_opp'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','OPP %')->orWhere('nama','LIKE','%stamp%')->whereIn('coa_id',$coa_id)->where('order_id',$order->id)->pluck('id')->toJson();
            $data[$idx]['ut'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','UT %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','UT %')->sum('credit');
            $data[$idx]['j_ut'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','UT %')->pluck('id')->toJson();
            $data[$idx]['bl'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','BL %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','BL %')->sum('credit');
            $data[$idx]['j_bl'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','BL %')->pluck('id')->toJson();
            $data[$idx]['apbs'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','APBS %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','APBS %')->sum('credit');
            $data[$idx]['j_apbs'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','APBS %')->pluck('id')->toJson();
            $data[$idx]['cleaning'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%CLEANING %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%CLEANING %')->sum('credit');
            $data[$idx]['j_cleaning'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%CLEANING %')->pluck('id')->toJson();
            $data[$idx]['lss'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','LSS %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','LSS %')->sum('credit');
            $data[$idx]['j_lss'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','LSS %')->pluck('id')->toJson();
            $data[$idx]['storage'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%storage %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%storage %')->sum('credit');
            $data[$idx]['j_storage'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%storage %')->pluck('id')->toJson();
            $data[$idx]['jasa_door'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%dooring %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%dooring %')->sum('credit');
            $data[$idx]['j_jasa_door'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%dooring %')->pluck('id')->toJson();
            $data[$idx]['asuransi'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%asuransi %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%asuransi %')->sum('credit');
            $data[$idx]['j_asuransi'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%asuransi %')->pluck('id')->toJson();
            $data[$idx]['ops'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE',"biaya operasional xpdc %0")->orWhere('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE',"biaya operasional xpdc %'")->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE',"biaya operasional xpdc %0")->orWhere('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE',"biaya operasional xpdc %'")->sum('credit');
            $data[$idx]['j_ops'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE',"biaya operasional xpdc %0")->orWhere('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE',"biaya operasional xpdc %'")->pluck('id')->toJson();
            $data[$idx]['segel'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','pembayaran seal%')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','pembayaran seal%')->sum('credit');
            $data[$idx]['j_segel'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','pembayaran seal%')->pluck('id')->toJson();
            $data[$idx]['ops_seal'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional %, seal')->orWhere('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional % , seal')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional %, seal')->orWhere('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional % , seal')->sum('credit');
            $data[$idx]['j_ops_seal'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional %, seal')->orWhere('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional % , seal')->pluck('id')->toJson();
            $data[$idx]['ops_seal_cleaning'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional %, seal, cleaning')->orWhere('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional % , seal , cleaning')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional %, seal, cleaning')->orWhere('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional % , seal , cleaning')->sum('credit');
            $data[$idx]['j_ops_seal_cleaning'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional %, seal, cleaning')->orWhere('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional % , seal , cleaning')->pluck('id')->toJson();
            $data[$idx]['buruh'] = Jurnal::orWhere('nama','LIKE','Biaya TKBM%')->whereIn('coa_id',$coa_id)->where('order_id',$order->id)->orWhere('nama','LIKE','Biaya Kuli%')->whereIn('coa_id',$coa_id)->where('order_id',$order->id)->orWhere('nama','LIKE','Biaya Buruh%')->whereIn('coa_id',$coa_id)->where('order_id',$order->id)->sum('debit') - Jurnal::orWhere('nama','LIKE','Biaya TKBM%')->whereIn('coa_id',$coa_id)->where('order_id',$order->id)->orWhere('nama','LIKE','Biaya Kuli%')->whereIn('coa_id',$coa_id)->where('order_id',$order->id)->orWhere('nama','LIKE','Biaya Buruh%')->whereIn('coa_id',$coa_id)->where('order_id',$order->id)->sum('credit');
            $data[$idx]['j_buruh'] = Jurnal::orWhere('nama','LIKE','Biaya TKBM%')->whereIn('coa_id',$coa_id)->where('order_id',$order->id)->orWhere('nama','LIKE','Biaya Kuli%')->whereIn('coa_id',$coa_id)->where('order_id',$order->id)->orWhere('nama','LIKE','Biaya Buruh%')->whereIn('coa_id',$coa_id)->where('order_id',$order->id)->pluck('id')->toJson();
            $data[$idx]['checker'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%checker %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%checker %')->sum('credit');
            $data[$idx]['j_checker'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%checker %')->pluck('id')->toJson();
            $data[$idx]['karantina'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%karantina %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%karantina %')->sum('credit');
            $data[$idx]['j_karantina'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%karantina %')->pluck('id')->toJson();
            $data[$idx]['demmurage'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%demmurage %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%demmurage %')->sum('credit');
            $data[$idx]['j_demmurage'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%demmurage %')->pluck('id')->toJson();
            $data[$idx]['kirim_dokumen'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%Pengiriman Dokumen%')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%Pengiriman Dokumen%')->sum('credit');
            $data[$idx]['j_kirim_dokumen'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%Pengiriman Dokumen%')->pluck('id')->toJson();
            $data[$idx]['flexibag'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%flexibag %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%flexibag %')->sum('credit');
            $data[$idx]['j_flexibag'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%flexibag %')->pluck('id')->toJson();
            $data[$idx]['rc'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%rc %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%rc %')->sum('credit');
            $data[$idx]['j_rc'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%rc %')->pluck('id')->toJson();
            $data[$idx]['biaya'] =  $data[$idx]['trucking'] + $data[$idx]['opt'] + $data[$idx]['opp'] + $data[$idx]['ut'] + $data[$idx]['bl'] + $data[$idx]['apbs'] + $data[$idx]['cleaning'] + $data[$idx]['lss'] + $data[$idx]['storage'] + $data[$idx]['jasa_door'] + $data[$idx]['ops'] + $data[$idx]['segel'] + $data[$idx]['ops_seal'] + $data[$idx]['ops_seal_cleaning'] + $data[$idx]['buruh'] + $data[$idx]['checker'] + $data[$idx]['karantina'] + $data[$idx]['demmurage'] + $data[$idx]['kirim_dokumen'] + $data[$idx]['flexibag'] + $data[$idx]['rc'] + $data[$idx]['asuransi'];
            $data[$idx]['biaya_lain'] =  (Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->sum('credit')) - $data[$idx]['biaya'];
            $data[$idx]['biaya'] += $data[$idx]['biaya_lain'] + ($tipe=='R2'?$data[$idx]['trucking']:0);
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
                        json_decode($data[$idx]['j_job_slip_pod'], true),
                        json_decode($data[$idx]['j_lolo_pod'], true),
                        json_decode($data[$idx]['j_cleaning_pod'], true),
                        json_decode($data[$idx]['j_ops_pod'], true),
                        json_decode($data[$idx]['j_opt_pod'], true),
                        json_decode($data[$idx]['j_truck_pod'], true),
                        json_decode($data[$idx]['j_kuli_pod'], true),
                        json_decode($data[$idx]['j_storage_pod'], true),
                        json_decode(Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->pluck('id')->toJson(), true),
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
                if($data[$idx]['biaya_lain'] < 0){
                    $debit = Jurnal::whereIn('id',$this->findUniqueValue(json_decode($biaya_lain,true)))->sum('debit');
                    $data[$idx]['biaya_lain'] = $debit;
                }
        }

        // return response($data);
        $model->upsert($data,['order_id'],[
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
            'ops_seal_cleaning',
            'buruh',
            'checker',
            'karantina',
            'demmurage',
            'job_slip_pod',
            'lolo_pod',
            'cleaning_pod',
            'ops_pod',
            'opt_pod',
            'truck_pod',
            'kuli_pod',
            'storage_pod',
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
            'j_ops_seal_cleaning',
            'j_buruh',
            'j_checker',
            'j_karantina',
            'j_demmurage',
            'j_job_slip_pod',
            'j_lolo_pod',
            'j_cleaning_pod',
            'j_ops_pod',
            'j_opt_pod',
            'j_truck_pod',
            'j_kuli_pod',
            'j_storage_pod',
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
        if(request('is_pra')){
            $omset = Praomset::find($omset_id);
        }else{
            $omset = Omset::find($omset_id);
        }
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

                $rm_col = str_replace(['[',']'],'',$omset_arr['j_biaya']);
                $arr_rm_col = explode(',',$rm_col);
                $new_arr_col = [];
                foreach ($arr_rm_col as $item) {
                    if($item!=$jurnal_id){
                        array_push($new_arr_col,$item);
                    }
                }
                $output_col = json_encode($new_arr_col);
                $update['j_biaya'] = str_replace('"','',$output_col);
            }
            $omset->update($update);
            // $this->syncBiaya($omset);
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

    public function syncJurnalBalik()
    {
        $id = request('id');
        $ids = array_slice($id,request('start'),request('end'));
        $end = request('start') + request('end');
        $orders = Order::whereIn('id',$ids)->where('lock_omset',1)->get();
        $month = request('month');
        $year = request('year');
        $balik = JurnalBalik::where('bulan',$month)->where('tipe','xpdc')->where('tahun',$year)->first();
        if(!$balik){
            $c = new Carbon($year.'-'.sprintf('%02d',$month).'-01');
            $last = $c->endOfMonth()->format('Y-m-d');
            $no = Jurnal::whereNull('jurnal_balik')->where('tipe','TEST')->whereMonth('created_at',$month)->whereYear('created_at',$year)->max('no') + 1;
            $nomor = 'TES-'.sprintf('%02d',$month).'-'.sprintf('%03d',$no).'/'.date('y',strtotime($year.'-'.sprintf('%02d',$month).'-01'));
            $balik = JurnalBalik::create([
                'tanggal' => $last,
                'bulan' => $month,
                'tahun' => $year,
                'nomor' => $nomor,
                'no' => $no,
                'tipe' => 'xpdc',
            ]);
        }
        $res = [];
        $column = ['j_opp','j_opt','j_ut','j_bl','j_apbs','j_cleaning','j_lss','j_storage','j_jasa_door','j_asuransi','j_ops','j_segel','j_ops_seal','j_ops_seal_cleaning','j_buruh','j_checker','j_karantina','j_demmurage','j_kirim_dokumen','j_flexibag','j_rc','j_job_slip_pod','j_lolo_pod','j_cleaning_pod','j_ops_pod','j_opt_pod','j_truck_pod','j_kuli_pod','j_storage_pod','j_biaya_lain'];
        foreach($orders as $order){
            $pra_omset = $order->pra_omset;
            $omset = $order->omset;
            if($pra_omset){
                for($i = 0; $i < count($column); $i++){
                    $col_id = [];
                    $col = $pra_omset[$column[$i]];
                    $j_biaya = json_decode($col);
                    $biaya = Jurnal::whereIn('id',$j_biaya)->get();
                    foreach($biaya as $j_){
                        if($j_->jurnal_balik_data()->count()>0){
                            foreach($j_->jurnal_balik_data as $item){
                                if($item->debit==0){
                                    $item->update([
                                        'credit' => $j_->debit,
                                        'no' => $balik->no,
                                        'nomor' => $balik->nomor
                                    ]);
                                }else{
                                    $item->update([
                                        'debit' => $j_->debit,
                                        'no' => $balik->no,
                                        'nomor' => $balik->nomor
                                    ]);
                                    array_push($col_id,$item->id);
                                }
                            }

                            array_push($res,$j_->id);
                        }else{
                            $data = $j_->toArray();
                            unset($data['id']);
                            if ($j_->coa_id==31) {
                                $data['jurnal_balik'] = $j_->id;
                                $data['coa_id'] = 93;
                                $data['debit'] = $j_->debit;
                                $data['credit'] = 0;
                                $data['tipe'] = 'TEST';
                                $data['nomor'] = $balik->nomor;
                                $data['no'] = $balik->no;
                                $data['created_at'] = $balik->tanggal;
                                $jurnal = Jurnal::create($data);

                                $data['jurnal_balik'] = $j_->id;
                                $data['coa_id'] = $j_->coa_id;
                                $data['credit'] = $j_->debit;
                                $data['debit'] = 0;
                                $data['tipe'] = 'TEST';
                                $data['nomor'] = $balik->nomor;
                                $data['no'] = $balik->no;
                                $data['created_at'] = $balik->tanggal;
                                Jurnal::create($data);

                                array_push($col_id,$jurnal->id);
                            }

                            array_push($res,$j_->id);
                        }
                    }
                    if(!$omset){
                        $omset_data = $pra_omset->toArray();
                        unset($omset_data['id']);
                        $omset_data[$column[$i]] = json_encode($col_id);
                        $omset = Omset::create($omset_data);
                    }else{
                        $omset_data = array();
                        $omset_data[$column[$i]] = json_encode($col_id);
                        $omset->update($omset_data);
                    }
                }
            }

            $this->syncOmset($order->id);

        }

        if(count($id) > $end){
            return response($end);
        }else{
            return response('complete');
        }
    }

    public function jurnalBalikTrucking()
    {
        $month = request('month');
        $year = request('year');
        $tipe = request('tipe');
        $balik = JurnalBalik::where('bulan',$month)->where('tipe','trucking')->where('tahun',$year)->first();
        $jurnal_id = json_decode(request('jurnal_id'));
        if(!$balik){
            $c = new Carbon($year.'-'.sprintf('%02d',$month).'-01');
            $last = $c->endOfMonth()->format('Y-m-d');
            $no = Jurnal::whereNull('jurnal_balik')->where('tipe','TEST')->whereMonth('created_at',$month)->whereYear('created_at',$year)->max('no') + 1;
            $nomor = 'TES-'.sprintf('%02d',$month).'-'.sprintf('%03d',$no).'/'.date('y',strtotime($year.'-'.sprintf('%02d',$month).'-01'));
            $balik = JurnalBalik::create([
                'tanggal' => $last,
                'bulan' => $month,
                'tahun' => $year,
                'nomor' => $nomor,
                'no' => $no,
                'tipe' => 'trucking',
            ]);
        }
        $jurnal = Jurnal::whereIn('id',$jurnal_id)->get();;
        $res = [];
        foreach($jurnal as $j_biaya){
            if($j_biaya->jurnal_balik_data()->count()>0){
                foreach($j_biaya->jurnal_balik_data as $item){
                    if($item->debit==0){
                        $item->update([
                            'credit' => $j_biaya->debit,
                            'no' => $balik->no,
                            'nomor' => $balik->nomor
                        ]);
                    }else{
                        $item->update([
                            'debit' => $j_biaya->debit,
                            'no' => $balik->no,
                            'nomor' => $balik->nomor
                        ]);
                    }
                }

                array_push($res,$j_biaya->id);
            }else{
                $data = $j_biaya->toArray();
                unset($data['id']);
                if ($tipe=='xpdc') {
                    if ($j_biaya->coa_id==61) {
                        $data['jurnal_balik'] = $j_biaya->id;
                        $data['coa_id'] = 100;
                        $data['debit'] = $j_biaya->debit;
                        $data['credit'] = 0;
                        $data['tipe'] = 'TEST';
                        $data['nomor'] = $balik->nomor;
                        $data['no'] = $balik->no;
                        $data['created_at'] = $balik->tanggal;
                        Jurnal::create($data);

                        $data['jurnal_balik'] = $j_biaya->id;
                        $data['coa_id'] = $j_biaya->coa_id;
                        $data['credit'] = $j_biaya->debit;
                        $data['debit'] = 0;
                        $data['tipe'] = 'TEST';
                        $data['nomor'] = $balik->nomor;
                        $data['no'] = $balik->no;
                        $data['created_at'] = $balik->tanggal;
                        Jurnal::create($data);
                    }
                }


                array_push($res,$j_biaya->id);
            }
        }

        return back()->with('success','Data berhasil disimpan dengan nomor jurnal '.$balik->nomor);
    }

    public function syncOmset($order_id)
    {
        $omset =  Omset::where('order_id',$order_id)->first();
        if($omset){
            $data = array();
            $column = ['j_opp','j_opt','j_ut','j_bl','j_apbs','j_cleaning','j_lss','j_storage','j_jasa_door','j_asuransi','j_ops','j_segel','j_ops_seal','j_ops_seal_cleaning','j_buruh','j_checker','j_karantina','j_demmurage','j_kirim_dokumen','j_flexibag','j_rc','j_job_slip_pod','j_lolo_pod','j_cleaning_pod','j_ops_pod','j_opt_pod','j_truck_pod','j_kuli_pod','j_storage_pod','j_biaya_lain'];
            $biaya_id = array();
            for($i = 0; $i < count($column); $i++){
                $data[substr($column[$i],2)] = Jurnal::whereIn('id',json_decode($omset[$column[$i]]))->sum('debit');
                foreach(json_decode($omset[$column[$i]]) as $id){
                    array_push($biaya_id,$id);
                }
            }
            $data['biaya'] = Jurnal::whereIn('id',$biaya_id)->sum('debit');
            $data['j_biaya'] = json_encode($biaya_id);
            $data['laba_kotor'] = $omset['tarif'] - $data['biaya'];
            $data['margin'] = $data['laba_kotor'] / $omset['tarif'];
            $omset->update($data);
        }

        return true;
    }
}
