<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JurnalResource;
use App\Models\Jurnal;
use App\Models\JurnalBalik;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Omset;
use App\Models\Order;
use App\Models\COA;
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
        $coa_id = COA::whereIn('coa_ras', [93])->pluck('id')->toArray();
        if(count($coa_id) != 8){
            $coa_id = [93];
        }
        $orders = Order::whereIn('id',$ids)->where('lock_omset', 1)->get();
        if(request('is_pra')){
            $orders = Order::whereIn('id',$ids)->where('lock_omset',0)->get();
            $coa_id = COA::whereIn('coa_ras', [38, 31, 133, 134, 135, 140, 76, 81])->pluck('id')->toArray();
            if(count($coa_id) != 8){
                $coa_id = [38,31,133,134,135,140,76,81];
            }
            $model = new PraOmset();
        }
        foreach ($orders as $idx => $order) {
        $cbm = $order->tarif->satuanInfo->nama ?? '-';
        $tarif = $order->tarif->tarif ?? 0;

        if ($cbm == 'CBM') {
            $totalVol = round($order->bttb->sum('vol'), 2);
            // Jika total volume kurang dari 1, set menjadi 1
            if ($totalVol < 1) {
                $totalVol = 1;
            }
            $tarif *= $totalVol;
        }

          $data[$idx]['order_id'] = $order->id;
          $data[$idx]['trucking'] = 0;
          $data[$idx]['j_trucking'] = '[]';

$tipe = '';
if ($order->truckingInfo && $order->trucking == 'XPDC') {
    // Cek apakah customer R1
    if ($order->truckingInfo->customer->r1 == 1) {
        $tipe = 'R1';
    }
    // Jika bukan R1, cek apakah R2
    elseif ($order->truckingInfo->customer->r2 == 1) {
        $tipe = 'R2';
    } else {
        $tipe = $order->truckingInfo->kendaraan->milik ?? '';
    }

    if ($tipe === 'R1' || strtolower($tipe) === 'vendor') {
        $data[$idx]['trucking'] = Jurnal::where('order_id', $order->id)
            ->whereIn('coa_id', $coa_id)
            ->where(function ($q) {
                $q->whereRaw("LOWER(nama) LIKE '%biaya truck luar%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya trucking%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%tb/tl%'");
            })
            ->whereRaw("LOWER(nama) NOT LIKE '%biaya trucking banjarmasin%'") 
            ->whereRaw("LOWER(nama) NOT LIKE '%biaya trucking pod%'") 
            ->sum('debit')
            - Jurnal::where('order_id', $order->id)
            ->whereIn('coa_id', $coa_id)
            ->where(function ($q) {
                $q->whereRaw("LOWER(nama) LIKE '%biaya truck luar%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya trucking%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%tb/tl%'");
            })
            ->whereRaw("LOWER(nama) NOT LIKE '%biaya trucking banjarmasin%'") 
            ->whereRaw("LOWER(nama) NOT LIKE '%biaya trucking pod%'")
            ->sum('credit');

        $data[$idx]['j_trucking'] = Jurnal::where('order_id', $order->id)
            ->whereIn('coa_id', $coa_id)
            ->where(function ($q) {
                $q->whereRaw("LOWER(nama) LIKE '%biaya truck luar%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya trucking%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%tb/tl%'");
            })
            ->whereRaw("LOWER(nama) NOT LIKE '%biaya trucking banjarmasin%'")
            ->whereRaw("LOWER(nama) NOT LIKE '%biaya trucking pod%'")
            ->pluck('id')->toJson();
    }

    if ($tipe == 'R2') {
        $data[$idx]['trucking'] =
            ($order->truckingInfo->tarif->tarif ?? 0) +
            // ($order->truckingInfo->tb_tl ?? 0) +
            // ($order->truckingInfo->tambah_isi ?? 0) +
            // ($order->truckingInfo->tambah_solar ?? 0) +
            ($order->truckingInfo->stappel ?? 0) +
            ($order->truckingInfo->lain_lain ?? 0);
        $data[$idx]['j_trucking'] = '[]';
    }
} else {
    // Kondisi jika tidak ada truckingInfo atau trucking bukan XPDC
    $data[$idx]['trucking'] = 0;
    $data[$idx]['j_trucking'] = '[]';
}

            $data[$idx]['job_slip_pod'] = Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->where(function ($q) {
                    $q->whereRaw("LOWER(nama) LIKE '%do pod%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya do %'");
                })
                ->sum('debit')
                 - Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->where(function ($q) {
                     $q->whereRaw("LOWER(nama) LIKE '%do pod%'")
                     ->orWhereRaw("LOWER(nama) LIKE '%biaya do'");
                })
                ->sum('credit');
            
            $data[$idx]['lolo_pod'] = Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->where(function ($q) {
                    $q->whereRaw("LOWER(nama) LIKE '%lolo pod%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya lolo banjarmasin%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya lolo jayapura%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya turun job empty / lolo banjarmasin%'");
                })
                ->sum('debit')
                - Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->where(function ($q) {
                    $q->whereRaw("LOWER(nama) LIKE '%lolo pod%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya lolo banjarmasin%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya lolo jayapura%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya turun job empty / lolo banjarmasin%'");
                })
                ->sum('credit');

            $data[$idx]['j_lolo_pod'] = Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->where(function ($q) {
                    $q->whereRaw("LOWER(nama) LIKE '%lolo pod%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya lolo banjarmasin%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya lolo jayapura%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya turun job empty / lolo banjarmasin%'");
                })
                ->pluck('id')->toJson();


            $data[$idx]['cleaning_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% cleaning pod %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% cleaning pod %')->sum('credit');
            $data[$idx]['ops_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% operasional pod %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% operasional pod %')->sum('credit');
            $data[$idx]['kuli_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% buruh / kuli pod %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% buruh / kuli pod %')->sum('credit');
            $data[$idx]['storage_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% perpanjangan do %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% perpanjangan do %')->sum('credit');
            $data[$idx]['j_job_slip_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where(function ($q) {
                     $q->whereRaw("LOWER(nama) LIKE '%do pod%'")
                     ->orWhereRaw("LOWER(nama) LIKE '%biaya do %'");
                })->pluck('id')->toJson();
            $data[$idx]['j_cleaning_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% cleaning pod %')->pluck('id')->toJson();
            $data[$idx]['j_ops_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% operasional pod %')->pluck('id')->toJson();
            
            $data[$idx]['opt_pod'] = Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->where(function ($q) {
                    $q->whereRaw("LOWER(nama) LIKE '%pelabuhan / job slip pod%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya lolo stripping dalam banjarmasin%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya job slip banjarmasin%'")
                     ->orWhereRaw("LOWER(nama) LIKE '%job slip jayapura%'");
                })
                ->sum('debit') 
                - Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->where(function ($q) {
                    $q->whereRaw("LOWER(nama) LIKE '%pelabuhan / job slip pod%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya lolo stripping dalam banjarmasin%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya job slip banjarmasin%'")
                     ->orWhereRaw("LOWER(nama) LIKE '%job slip jayapura%'");
                })
                ->sum('credit');

            $data[$idx]['j_opt_pod'] = Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->where(function ($q) {
                    $q->whereRaw("LOWER(nama) LIKE '%pelabuhan / job slip pod%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya lolo stripping dalam banjarmasin%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya job slip banjarmasin%'")
                     ->orWhereRaw("LOWER(nama) LIKE '%job slip jayapura%'");
                })
                ->pluck('id')->toJson();

            
$data[$idx]['truck_pod'] = Jurnal::where('order_id', $order->id)
    ->whereIn('coa_id', $coa_id)
    ->where(function ($q) {
        $q->whereRaw("LOWER(nama) LIKE '%trucking pod%'")
          ->orWhereRaw("LOWER(nama) LIKE '%biaya trucking banjarmasin%'")
          ->orWhereRaw("LOWER(nama) LIKE '%truck jayapura%'");
    })
    ->sum('debit')
    - Jurnal::where('order_id', $order->id)
    ->whereIn('coa_id', $coa_id)
    ->where(function ($q) {
        $q->whereRaw("LOWER(nama) LIKE '%trucking pod%'")
          ->orWhereRaw("LOWER(nama) LIKE '%biaya trucking banjarmasin%'")
          ->orWhereRaw("LOWER(nama) LIKE '%truck jayapura%'");
    })
    ->sum('credit');

$data[$idx]['j_truck_pod'] = Jurnal::where('order_id', $order->id)
    ->whereIn('coa_id', $coa_id)
    ->where(function ($q) {
        $q->whereRaw("LOWER(nama) LIKE '%trucking pod%'")
          ->orWhereRaw("LOWER(nama) LIKE '%biaya trucking banjarmasin%'")
          ->orWhereRaw("LOWER(nama) LIKE '%truck jayapura%'");
    })
    ->pluck('id')->toJson();

            
            $data[$idx]['j_kuli_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% buruh / kuli pod %')->pluck('id')->toJson();
            $data[$idx]['j_storage_pod'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','% perpanjangan do %')->pluck('id')->toJson();

          $data[$idx]['opt'] = Jurnal::where('order_id', $order->id)
    ->whereIn('coa_id', $coa_id)
    ->where(function ($q) {
        $q->whereRaw("LOWER(nama) LIKE 'opt %'")
          ->orWhereRaw("LOWER(nama) LIKE '%biaya thc lolo%'");
    })
    ->sum('debit')
    - Jurnal::where('order_id', $order->id)
    ->whereIn('coa_id', $coa_id)
    ->where(function ($q) {
        $q->whereRaw("LOWER(nama) LIKE 'opt %'")
          ->orWhereRaw("LOWER(nama) LIKE '%biaya thc lolo%'");
    })
    ->sum('credit');

$data[$idx]['j_opt'] = Jurnal::where('order_id', $order->id)
    ->whereIn('coa_id', $coa_id)
    ->where(function ($q) {
        $q->whereRaw("LOWER(nama) LIKE 'opt %'")
          ->orWhereRaw("LOWER(nama) LIKE '%biaya thc lolo%'");
    })
    ->pluck('id')->toJson();

            $data[$idx]['opp'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','OPP %')->orWhere('nama','LIKE','%stamp%')->whereIn('coa_id',$coa_id)->where('order_id',$order->id)->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','OPP %')->orWhere('nama','LIKE','%stamp%')->whereIn('coa_id',$coa_id)->where('order_id',$order->id)->sum('credit');
            $data[$idx]['j_opp'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','OPP %')->orWhere('nama','LIKE','%stamp%')->whereIn('coa_id',$coa_id)->where('order_id',$order->id)->pluck('id')->toJson();
            
           $data[$idx]['ut'] = Jurnal::where('order_id', $order->id)
    ->whereIn('coa_id', $coa_id)
    ->where(function ($q) {
        $q->whereRaw('LOWER(nama) LIKE ?', ['ut %'])
          ->orWhereRaw('LOWER(nama) LIKE ?', ['%biaya lcl%']);
    })
    ->sum('debit')
    -
    Jurnal::where('order_id', $order->id)
    ->whereIn('coa_id', $coa_id)
    ->where(function ($q) {
        $q->whereRaw('LOWER(nama) LIKE ?', ['ut %'])
          ->orWhereRaw('LOWER(nama) LIKE ?', ['%biaya lcl%']);
    })
    ->sum('credit');

$data[$idx]['j_ut'] = Jurnal::where('order_id', $order->id)
    ->whereIn('coa_id', $coa_id)
    ->where(function ($q) {
        $q->whereRaw('LOWER(nama) LIKE ?', ['ut %'])
          ->orWhereRaw('LOWER(nama) LIKE ?', ['%biaya lcl%']);
    })
    ->pluck('id')
    ->toJson();


            $data[$idx]['bl'] = Jurnal::where('order_id', $order->id)
                                ->whereIn('coa_id', $coa_id)
                                ->where(function ($q) {
                                    $q->whereRaw("LOWER(nama) LIKE '%bl %'")
                                    ->orWhereRaw("LOWER(nama) LIKE '%doc. do meratus%'")
                                    ->orWhereRaw("LOWER(nama) LIKE '%do meratus%'");
                                })->sum('debit') 
                                - Jurnal::where('order_id', $order->id)
                                ->whereIn('coa_id', $coa_id)
                                ->where(function ($q) {
                                    $q->whereRaw("LOWER(nama) LIKE '%bl %'")
                                    ->orWhereRaw("LOWER(nama) LIKE '%doc. do meratus%'")
                                    ->orWhereRaw("LOWER(nama) LIKE '%do meratus%'");
                                })->sum('credit');

            $data[$idx]['j_bl'] = Jurnal::where('order_id', $order->id)
                                ->whereIn('coa_id', $coa_id)
                                ->where(function ($q) {
                                    $q->whereRaw("LOWER(nama) LIKE '%bl %'")
                                    ->orWhereRaw("LOWER(nama) LIKE '%doc. do meratus%'")
                                    ->orWhereRaw("LOWER(nama) LIKE '%do meratus%'");
                                })->pluck('id')->toJson();


            $data[$idx]['apbs'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','APBS %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','APBS %')->sum('credit');
            $data[$idx]['j_apbs'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','APBS %')->pluck('id')->toJson();
            $data[$idx]['cleaning'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%CLEANING %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%CLEANING %')->sum('credit');
            $data[$idx]['j_cleaning'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%CLEANING %')->pluck('id')->toJson();
            $data[$idx]['lss'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','LSS %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','LSS %')->sum('credit');
            $data[$idx]['j_lss'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','LSS %')->pluck('id')->toJson();
            $data[$idx]['storage'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%storage %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%storage %')->sum('credit');
            $data[$idx]['j_storage'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%storage %')->pluck('id')->toJson();
            
            $data[$idx]['jasa_door'] = Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->whereRaw("LOWER(nama) LIKE '%dooring %'")
                ->sum('debit') 
                - Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->whereRaw("LOWER(nama) LIKE '%dooring %'")
                ->sum('credit');

            $data[$idx]['j_jasa_door'] = Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->whereRaw("LOWER(nama) LIKE '%dooring %'")
                ->pluck('id')->toJson();

            $data[$idx]['asuransi'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%asuransi %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%asuransi %')->sum('credit');
            $data[$idx]['j_asuransi'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%asuransi %')->pluck('id')->toJson();
            
            $data[$idx]['ops'] = Jurnal::where('order_id', $order->id)
                                    ->whereIn('coa_id', $coa_id)
                                    ->where(function ($q) {
                                    $q->whereRaw('LOWER(nama) LIKE ?', ['%biaya operasional xpdc%'])
                                    ->orWhereRaw('LOWER(nama) LIKE ?', ['%biaya tally%'])
                                    ->orWhereRaw('LOWER(nama) LIKE ?', ['%biaya operasional ekspedisi%']);
                                })
                                    ->sum('debit')
                                - Jurnal::where('order_id', $order->id)
                                    ->whereIn('coa_id', $coa_id)
                                    ->where(function ($q) {
                                    $q->whereRaw('LOWER(nama) LIKE ?', ['%biaya operasional xpdc%'])
                                    ->orWhereRaw('LOWER(nama) LIKE ?', ['%biaya tally%'])
                                    ->orWhereRaw('LOWER(nama) LIKE ?', ['%biaya operasional ekspedisi%']);
                                })
                                    ->sum('credit');

            $data[$idx]['j_ops'] = Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->where(function ($q) {
                $q->whereRaw('LOWER(nama) LIKE ?', ['%biaya operasional xpdc%'])
                ->orWhereRaw('LOWER(nama) LIKE ?', ['%biaya tally%'])
                ->orWhereRaw('LOWER(nama) LIKE ?', ['%biaya operasional ekspedisi%']);
            })
                ->pluck('id')
                ->toJson();

            $data[$idx]['segel'] = Jurnal::where('order_id', $order->id)
                                    ->whereIn('coa_id', $coa_id)
                                    ->where(function ($q) {
                                    $q->whereRaw('LOWER(nama) LIKE ?', ['%pembayaran seal%'])
                                    ->orWhereRaw('LOWER(nama) LIKE ?', ['%pembelian seal%'])
                                    ->orWhereRaw('LOWER(nama) LIKE ?', ['%biaya seal%']);
                                })

                                ->sum('debit')
                            - Jurnal::where('order_id', $order->id)
                                ->whereIn('coa_id', $coa_id)
                            ->where(function ($q) {
                                $q->whereRaw('LOWER(nama) LIKE ?', ['%pembayaran seal%'])
                                ->orWhereRaw('LOWER(nama) LIKE ?', ['%pembelian seal%'])
                                ->orWhereRaw('LOWER(nama) LIKE ?', ['%biaya seal%']);
                            })
                                ->sum('credit');

            $data[$idx]['j_segel'] = Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->where(function ($q) {
                $q->whereRaw('LOWER(nama) LIKE ?', ['%pembayaran seal%'])
                ->orWhereRaw('LOWER(nama) LIKE ?', ['%pembelian seal%'])
                ->orWhereRaw('LOWER(nama) LIKE ?', ['%biaya seal%']);
            })

                ->pluck('id')
                ->toJson();

            $data[$idx]['ops_seal'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional %, seal')->orWhere('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional % , seal')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional %, seal')->orWhere('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional % , seal')->sum('credit');
            $data[$idx]['j_ops_seal'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional %, seal')->orWhere('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional % , seal')->pluck('id')->toJson();
            $data[$idx]['ops_seal_cleaning'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional %, seal, cleaning')->orWhere('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional % , seal , cleaning')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional %, seal, cleaning')->orWhere('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional % , seal , cleaning')->sum('credit');
            $data[$idx]['j_ops_seal_cleaning'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional %, seal, cleaning')->orWhere('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','biaya operasional % , seal , cleaning')->pluck('id')->toJson();
            $data[$idx]['buruh'] = Jurnal::where('order_id', $order->id)
            ->whereIn('coa_id', $coa_id)
            ->where(function ($q) {
                $q->where('nama', 'LIKE', 'Biaya TKBM%')
                ->orWhere('nama', 'LIKE', 'Biaya Kuli%')
                ->orWhere('nama', 'LIKE', 'Biaya Buruh%');
            })
            ->where(function ($q) {
                $q->where('nama', 'NOT LIKE', '%Buruh POD%')
                ->where('nama', 'NOT LIKE', '%buruh pod%')
                ->where('nama', 'NOT LIKE', '%Kuli POD%')
                ->where('nama', 'NOT LIKE', '%kuli pod%');
            })
            ->sum('debit') 
            - 
            Jurnal::where('order_id', $order->id)
            ->whereIn('coa_id', $coa_id)
            ->where(function ($q) {
                $q->where('nama', 'LIKE', 'Biaya TKBM%')
                ->orWhere('nama', 'LIKE', 'Biaya Kuli%')
                ->orWhere('nama', 'LIKE', 'Biaya Buruh%');
            })
            ->where(function ($q) {
                $q->where('nama', 'NOT LIKE', '%Buruh POD%')
                ->where('nama', 'NOT LIKE', '%buruh pod%')
                ->where('nama', 'NOT LIKE', '%Kuli POD%')
                ->where('nama', 'NOT LIKE', '%kuli pod%');
            })
            ->sum('credit');

            $data[$idx]['j_buruh'] = Jurnal::where('order_id', $order->id)
    ->whereIn('coa_id', $coa_id)
    ->where(function ($q) {
        $q->where('nama', 'LIKE', 'Biaya TKBM%')
          ->orWhere('nama', 'LIKE', 'Biaya Kuli%')
          ->orWhere('nama', 'LIKE', 'Biaya Buruh%');
    })
    ->where(function ($q) {
        $q->where('nama', 'NOT LIKE', '%Buruh POD%')
          ->where('nama', 'NOT LIKE', '%buruh pod%')
          ->where('nama', 'NOT LIKE', '%Kuli POD%')
          ->where('nama', 'NOT LIKE', '%kuli pod%');
    })
            ->pluck('id')->toJson();

            $data[$idx]['checker'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%checker %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%checker %')->sum('credit');
            $data[$idx]['j_checker'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%checker %')->pluck('id')->toJson();
            $data[$idx]['karantina'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%karantina %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%karantina %')->sum('credit');
            $data[$idx]['j_karantina'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%karantina %')->pluck('id')->toJson();
            $data[$idx]['demmurage'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%demmurage %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%demmurage %')->sum('credit');
            $data[$idx]['j_demmurage'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%demmurage %')->pluck('id')->toJson();
            
            $data[$idx]['kirim_dokumen'] = Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->where(function ($q) {
                    $q->whereRaw("LOWER(nama) LIKE '%pengiriman dokumen%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya dokumen%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya kirim dokumen/invoice%'")
                     ->orWhereRaw("LOWER(nama) LIKE '%biaya kirim dokumen%'");
                })
                ->sum('debit')
                - Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->where(function ($q) {
                    $q->whereRaw("LOWER(nama) LIKE '%pengiriman dokumen%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya dokumen%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya kirim dokumen/invoice%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya kirim dokumen%'");
                })
                ->sum('credit');
            $data[$idx]['j_kirim_dokumen'] = Jurnal::where('order_id', $order->id)
                ->whereIn('coa_id', $coa_id)
                ->where(function ($q) {
                    $q->whereRaw("LOWER(nama) LIKE '%pengiriman dokumen%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya dokumen%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya kirim dokumen/invoice%'")
                    ->orWhereRaw("LOWER(nama) LIKE '%biaya kirim dokumen%'");
                })
                ->pluck('id')->toJson();

            $data[$idx]['flexibag'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%flexibag %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%flexibag %')->sum('credit');
            $data[$idx]['j_flexibag'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%flexibag %')->pluck('id')->toJson();
            $data[$idx]['rc'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%rc %')->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%rc %')->sum('credit');
            $data[$idx]['j_rc'] = Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->where('nama','LIKE','%rc %')->pluck('id')->toJson();
            $data[$idx]['biaya'] =  $data[$idx]['truck_pod'] + $data[$idx]['opt_pod'] + $data[$idx]['lolo_pod'] + $data[$idx]['job_slip_pod'] + $data[$idx]['trucking'] + $data[$idx]['opt'] + $data[$idx]['opp'] + $data[$idx]['ut'] + $data[$idx]['bl'] + $data[$idx]['apbs'] + $data[$idx]['cleaning'] + $data[$idx]['lss'] + $data[$idx]['storage'] + $data[$idx]['jasa_door'] + $data[$idx]['ops'] + $data[$idx]['segel'] + $data[$idx]['ops_seal_cleaning'] + $data[$idx]['buruh'] + $data[$idx]['checker'] + $data[$idx]['karantina'] + $data[$idx]['demmurage'] + $data[$idx]['kirim_dokumen'] + $data[$idx]['flexibag'] + $data[$idx]['rc'] + $data[$idx]['asuransi'] + $data[$idx]['ops_pod'] + $data[$idx]['cleaning_pod'] + $data[$idx]['kuli_pod'];
            if(request('is_pra')){
             $data[$idx]['biaya_lain'] =  (Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->sum('credit')) - $data[$idx]['biaya'] + ($tipe=='R2'?$data[$idx]['trucking']:0);
            } else{
                $data[$idx]['biaya_lain'] =  Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->sum('debit') - Jurnal::where('order_id',$order->id)->whereIn('coa_id',$coa_id)->sum('credit') - $data[$idx]['biaya'] + ($tipe=='R2'?$data[$idx]['trucking']:0);
            }
            $data[$idx]['biaya'] += $data[$idx]['biaya_lain'];
            $data[$idx]['tarif'] = $tarif;
            $data[$idx]['laba_kotor'] = $data[$idx]['tarif'] - $data[$idx]['biaya'];
            $tarif = (float) ($data[$idx]['tarif'] ?? 0);
            $laba  = (float) ($data[$idx]['laba_kotor'] ?? 0);
            $data[$idx]['margin'] = $tarif > 0 ? $laba / $tarif : 0;
            $data[$idx]['margin'] =
            $data[$idx]['biaya'] != 0
            ? $data[$idx]['laba_kotor'] / $data[$idx]['biaya']
            : 0;
             if(request('is_pra')){
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
             }else{

             
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
            }

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
        $data = Jurnal::whereIn('id', $id)
              ->where('debit', '>', 0)
              ->get();
        $res = JurnalResource::collection($data);
        return response($res);
    }

    public function getJurnal1(Request $request)
    {
        $id = str_replace(['[',']','"'],'',$request->id);
        $id = explode(',',$id);
        $data = Jurnal::whereIn('id', $id)
              ->get();
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

        public function syncJurnalBalik1()
{
    $ids   = request('id', []);
    $start = (int) request('start', 0);
    $limit = (int) request('end', 10);
    $end   = $start + $limit;

    $month = request('month');
    $year  = request('year');

    // ===============================
    // 1️⃣ AMBIL ORDER
    // ===============================
    $orders = Order::whereIn('id', $ids)
        ->where('lock_omset', 1)
        ->get();

    if ($orders->isEmpty()) {
        return response('complete');
    }

    $orderIds = $orders->pluck('id');

    // ===============================
    // 2️⃣ AMBIL SEMUA JURNAL SEKALI
    // ===============================
    $jurnals = Jurnal::whereIn('order_id', $orderIds)
        ->where('credit', '>', 0)
        ->get()
        ->groupBy('order_id');

    // ===============================
    // 3️⃣ JURNAL BALIK HEADER
    // ===============================
    $balik = JurnalBalik::where('bulan', $month)
        ->where('tahun', $year)
        ->where('tipe', 'xpdc (C)')
        ->first();

    if (!$balik) {
        $last = Carbon::create($year, $month, 1)->endOfMonth();
        $balik = JurnalBalik::create([
            'tanggal' => $last->format('Y-m-d'),
            'bulan'   => $month,
            'tahun'   => $year,
            'no'      => 2,
            'nomor'   => 'OMZ-' . sprintf('%02d', $month) . '-002/' . $last->format('y'),
            'tipe'    => 'xpdc (C)',
        ]);
    }

    $columns = [
        'j_opp','j_opt','j_ut','j_bl','j_apbs','j_cleaning','j_lss','j_storage',
        'j_jasa_door','j_asuransi','j_ops','j_segel','j_ops_seal','j_ops_seal_cleaning',
        'j_buruh','j_checker','j_karantina','j_demmurage','j_kirim_dokumen','j_flexibag',
        'j_rc','j_job_slip_pod','j_lolo_pod','j_cleaning_pod','j_ops_pod','j_opt_pod',
        'j_truck_pod','j_kuli_pod','j_storage_pod','j_biaya_lain'
    ];

    // ===============================
    // 4️⃣ TRANSACTION
    // ===============================
    DB::transaction(function () use ($orders, $jurnals, $balik, $columns) {

        foreach ($orders as $order) {

            $praOmset = $order->pra_omset;
            if (!$praOmset) continue;

            $omset = $order->omset;
            $orderJurnals = $jurnals[$order->id] ?? collect();

            foreach ($columns as $colName) {

                $col_id = [];
                $raw = $praOmset->$colName ?? null;
                $j_biaya = $raw ? json_decode($raw, true) : [];

                if (!$j_biaya || !is_array($j_biaya)) continue;

                // Filter jurnal di memory (TANPA QUERY)
                $biaya = $orderJurnals->whereIn('id', $j_biaya);

                foreach ($biaya as $j_) {

                    if ($j_->coa_id != 31) continue;

                    // ===============================
                    // BELUM ADA JURNAL BALIK
                    // ===============================
                    if (is_null($j_->jurnal_balik)) {

                        $data = $j_->toArray();
                        unset($data['id'], $data['created_at'], $data['updated_at']);

                        // DEBIT
                        $data['jurnal_balik'] = $j_->id;
                        $data['coa_id']       = 31;
                        $data['debit']        = $j_->credit;
                        $data['credit']       = 0;
                        $data['tipe']         = 'OMZ';
                        $data['relasi']       = $balik->nomor;
                        $data['nomor']        = $balik->nomor;
                        $data['no']           = $balik->no;
                        $data['created_at']   = $balik->tanggal;

                        $jurnalDebit = Jurnal::create($data);

                        $j_->update([
                            'jurnal_balik' => $jurnalDebit->id,
                            'is_balik'     => 1
                        ]);

                        // CREDIT
                        $data['coa_id'] = 93;
                        $data['debit']  = 0;
                        $data['credit'] = $j_->credit;
                        Jurnal::create($data);

                        $col_id[] = $jurnalDebit->id;
                    }
                    // ===============================
                    // SUDAH ADA → UPDATE NILAI
                    // ===============================
                    else {
                        foreach ($j_->jurnal_balik_data as $item) {
                            if ($item->credit == 0) {
                                $item->update(['debit' => $j_->credit]);
                                $col_id[] = $item->id;
                            } else {
                                $item->update(['credit' => $j_->credit]);
                            }
                        }
                    }
                }

                // ===============================
                // SIMPAN OMSET
                // ===============================
                if ($col_id) {
                    if (!$omset) {
                        $data = $praOmset->toArray();
                        unset($data['id']);
                        $data[$colName] = json_encode($col_id);
                        $omset = Omset::create($data);
                    } else {
                        $omset->update([
                            $colName => json_encode($col_id)
                        ]);
                    }
                }
            }

            $this->syncOmset($order->id);
        }
    });

    return count($ids) > $end
        ? response($end)
        : response('complete');
}



public function syncJurnalBalik2()
{
    $id = request('id');
    $ids = array_slice($id, request('start'), request('end'));
    $end = request('start') + request('end');

    // Ambil order yang lock_omset 1 atau 2
    $orders = Order::whereIn('id', $id)
        ->whereIn('lock_omset', [1, 2])
        ->get();

    $orderIds = $orders->pluck('id');

    // Ambil jurnal debit tertentu
    $jurnals = Jurnal::whereIn('order_id', $orderIds)
        ->where('debit', '>', 0)
        ->whereIn('coa_id', [133, 134, 135, 140, 76, 81])
        ->pluck('id');

    $month = request('month');
    $year = request('year');

    // Buat atau ambil jurnal balik induk
    $balik = JurnalBalik::where('bulan', $month)
        ->where('tahun', $year)
        ->where('tipe', 'xpdc non 1.6.1 (D)')
        ->first();

    if (!$balik) {
        $c = Carbon::create($year, $month, 1);
        $last = $c->endOfMonth()->format('Y-m-d');

        $no = 5;
        $nomor = "OMZ-" . sprintf('%02d', $month) . "-" . sprintf('%03d', $no) . "/" . date('y', strtotime("$year-$month-01"));

        $balik = JurnalBalik::create([
            'tanggal' => $last,
            'bulan' => $month,
            'tahun' => $year,
            'nomor' => $nomor,
            'no' => $no,
            'tipe' => 'xpdc non 1.6.1 (D)',
        ]);
    }

    $res = [];

    // Semua kolom j_*
    $columns = [
        'j_opp', 'j_opt', 'j_ut', 'j_bl', 'j_apbs', 'j_cleaning', 'j_lss', 'j_storage',
        'j_jasa_door', 'j_asuransi', 'j_ops', 'j_segel', 'j_ops_seal', 'j_ops_seal_cleaning',
        'j_buruh', 'j_checker', 'j_karantina', 'j_demmurage', 'j_kirim_dokumen', 'j_flexibag',
        'j_rc', 'j_job_slip_pod', 'j_lolo_pod', 'j_cleaning_pod', 'j_ops_pod', 'j_opt_pod',
        'j_truck_pod', 'j_kuli_pod', 'j_storage_pod', 'j_biaya_lain'
    ];

    foreach ($orders as $order) {

        $pra_omset = $order->pra_omset;
        $omset = $order->omset;

        if (!$pra_omset) {
            continue;
        }

        // Loop semua kolom j_*
        foreach ($columns as $colName) {

            $col_id = [];

            $col = $pra_omset->$colName ?? null;
            $j_biaya = $col ? json_decode($col, true) : [];

            if (!is_array($j_biaya)) {
                $j_biaya = [];
            }

            // Ambil jurnal debit valid
            $valid_jurnal_ids = array_intersect($j_biaya, $jurnals->toArray());

            $biayaList = Jurnal::whereIn('id', $valid_jurnal_ids)
                ->where('debit', '>', 0)
                ->get();

            foreach ($biayaList as $j_) {

                if (is_null($j_->jurnal_balik)) {

                    if (in_array($j_->coa_id, [133, 134, 135, 140, 76, 81])) {

                        $data = $j_->toArray();
                        unset($data['id']);

                        // Debit
                        $data['jurnal_balik'] = $j_->id;
                        $data['coa_id'] = 93;
                        $data['debit'] = $j_->debit;
                        $data['credit'] = 0;
                        $data['tipe'] = "OMZ";
                        $data['relasi'] = $balik->nomor;
                        $data['nomor'] = $balik->nomor;
                        $data['no'] = $balik->no;
                        $data['created_at'] = $balik->tanggal;

                        $jurnalDebit = Jurnal::create($data);

                        // Tandai jurnal asli
                        $j_->update([
                            'jurnal_balik' => $jurnalDebit->id,
                            'is_balik' => 1
                        ]);

                        // Kredit
                        $data['coa_id'] = $j_->coa_id;
                        $data['credit'] = $j_->debit;
                        $data['debit'] = 0;
                        Jurnal::create($data);

                        $col_id[] = $jurnalDebit->id;
                    }

                    $res[] = $j_->id;
                    continue;
                }
            }

            // --------------------------
            // INSERT / UPDATE OMSET
            // --------------------------

            if (!empty($col_id)) {

                if (!$omset) {

                    $omset_data = $pra_omset->toArray();
                    unset($omset_data['id']);

                    $omset_data[$colName] = json_encode($col_id);

                    $omset = Omset::create($omset_data);

                } else {

                    // Cek duplikasi
                    $isDuplicate = false;

                    foreach ($omset->getAttributes() as $key => $value) {
                        if (str_starts_with($key, 'j_') && $value) {
                            $decoded = json_decode($value, true) ?? [];
                            if (array_intersect($decoded, $col_id)) {
                                $isDuplicate = true;
                                break;
                            }
                        }
                    }

                    if (!$isDuplicate) {
                        $omset->update([
                            $colName => json_encode($col_id)
                        ]);
                    }
                }
            }
        }
    }

    // --- Pagination sync ---
    if (count($id) > $end) {
        return response($end);
    }

    return response('complete');
}





  public function syncJurnalBalik()
{
    $ids   = request('id', []);
    $start = (int) request('start', 0);
    $limit = (int) request('end', 10);
    $slice = array_slice($ids, $start, $limit);
    $end   = $start + $limit;

    $month = request('month');
    $year  = request('year');

    /** ===============================
     * 1️⃣ ORDER VALID
     * =============================== */
    $orders = Order::whereIn('id', $slice)
        ->where('lock_omset', 1)
        ->get();

    if ($orders->isEmpty()) {
        return response('complete');
    }

    /** ===============================
     * 2️⃣ AMBIL JURNAL SEKALI
     * =============================== */
    $jurnals = Jurnal::whereIn('order_id', $orders->pluck('id'))
        ->where('debit', '>', 0)
        ->whereNull('jurnal_balik') // 🔒 anti duplicate
        ->get()
        ->groupBy('order_id');

    /** ===============================
     * 3️⃣ JURNAL BALIK
     * =============================== */
    $balik = JurnalBalik::where('bulan', $month)
    ->where('tahun', $year)
    ->where('tipe', 'xpdc')
    ->first();

if (!$balik) {
    $last = Carbon::create($year, $month, 1)->endOfMonth();

    $balik = JurnalBalik::create([
        'tanggal' => $last->format('Y-m-d'),
        'bulan'   => $month,
        'tahun'   => $year,
        'tipe'    => 'xpdc',
        'no'      => 1,
        'nomor'   => 'OMZ-' . sprintf('%02d', $month) . '-001/' . $last->format('y'),
    ]);
}


    DB::transaction(function () use ($orders, $jurnals, $balik) {

        foreach ($orders as $order) {

            if (!$order->pra_omset) continue;

            $colResult = [];
            $items = $jurnals[$order->id] ?? collect();

            foreach ($items as $j_) {

                if ($j_->coa_id != 31) continue;

                $timestamp = Carbon::parse($balik->tanggal)->startOfDay()->format('Y-m-d H:i:s');

                $base = $j_->toArray();
                unset($base['id'], $base['created_at'], $base['updated_at']);

                /** =====================
                 * DEBIT
                 * ===================== */
                $debit = array_merge($base, [
                    'coa_id'        => 93,
                    'debit'         => $j_->debit,
                    'credit'        => 0,
                    'tipe'          => 'OMZ',
                    'nomor'         => $balik->nomor,
                    'relasi'        => $balik->nomor,
                    'no'            => $balik->no,
                    'jurnal_balik'  => $j_->id,
                    'created_at'    => $timestamp,
                    'updated_at'    => $timestamp,
                ]);

                $jd = Jurnal::create($debit);

                /** =====================
                 * KREDIT
                 * ===================== */
                Jurnal::create(array_merge($debit, [
                    'coa_id' => 31,
                    'debit'  => 0,
                    'credit' => $j_->debit,
                ]));

                /** =====================
                 * UPDATE JURNAL LAMA
                 * ===================== */
                $j_->update([
                    'jurnal_balik' => $jd->id,
                    'is_balik'     => 1
                ]);

                $colResult[] = $jd->id;
            }

            /** =====================
             * SIMPAN OMSET (1x)
             * ===================== */
            if ($colResult) {
                $omset = $order->omset;

                if (!$omset) {
                    $data = $order->pra_omset->toArray();
                    unset($data['id']);
                    $data['j_biaya_lain'] = json_encode($colResult);
                    Omset::create($data);
                } else {
                    $existing = json_decode($omset->j_biaya_lain, true) ?? [];
                    $merged   = array_values(array_unique(array_merge($existing, $colResult)));
                    $omset->update(['j_biaya_lain' => json_encode($merged)]);
                }
            }

            $this->syncOmset($order->id);
        }
    });

    return count($ids) > $end
        ? response($end)
        : response('complete');
}


  public function jurnalBalikTrucking()
{
    try {
        //  Ambil dan validasi request
        $month = request('month');
        $year = request('year');
        $tipe = request('tipe');
        $jurnal_id_raw = request('jurnal_id');

        //  Normalisasi format jurnal_id (bisa JSON, bisa string)
        if (is_string($jurnal_id_raw)) {
            $jurnal_id = json_decode($jurnal_id_raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $jurnal_id = explode(',', $jurnal_id_raw);
            }
        } else {
            $jurnal_id = $jurnal_id_raw;
        }

        //  Logging parameter
        \Log::info('PARAMETER JURNAL BALIK TRUCKING', [
            'month' => $month,
            'year' => $year,
            'tipe' => $tipe,
            'jurnal_id' => $jurnal_id,
        ]);

        //  Validasi awal
        if (empty($month) || empty($year) || empty($jurnal_id)) {
            return back()->with('error', 'Parameter tidak lengkap (bulan, tahun, atau jurnal_id kosong)');
        }

        // Cari atau buat Jurnal Balik
        $balik = \App\Models\JurnalBalik::where('bulan', $month)
            ->whereRaw('LOWER(tipe) = ?', [strtolower('Trucking Expdc')])
            ->where('tahun', $year)
            ->first();

        
            // Buat baru hanya jika belum ada
            $c = new \Carbon\Carbon($year . '-' . sprintf('%02d', $month) . '-01');
            $last = $c->endOfMonth()->format('Y-m-d');
            $no = 3;
            $nomor = 'OMZ-' . sprintf('%02d', $month) . '-' . sprintf('%03d', $no) . '/' . date('y', strtotime($year . '-' . sprintf('%02d', $month) . '-01'));

            $balik = \App\Models\JurnalBalik::create([
                'tanggal' => $last,
                'bulan' => $month,
                'tahun' => $year,
                'nomor' => $nomor,
                'no' => $no,
                'tipe' => 'Trucking Expdc',
            ]);

        // 🔹 Ambil jurnal trucking
        $jurnal = \App\Models\Jurnal::whereIn('order_trucking_id', $jurnal_id)
            ->whereNull('jurnal_balik')
            ->get();

        \Log::info('Jumlah jurnal ditemukan', ['count' => $jurnal->count()]);

        $res = [];

        foreach ($jurnal as $j_biaya) {
            // Jika sudah punya jurnal balik
            if ($j_biaya->jurnal_balik !== null && $j_biaya->jurnal_balik_data) {
                foreach ($j_biaya->jurnal_balik_data as $item) {
                    if ($item->debit == 0) {
                        $item->update(['credit' => $j_biaya->debit]);
                    } else {
                        $item->update(['debit' => $j_biaya->debit]);
                    }
                }
                continue;
            }

            //  Siapkan data baru
            $data = $j_biaya->toArray();
            unset($data['id']);

                // Filter kondisi sesuai requirement
                if ((int) $j_biaya->coa_id === 61 && $j_biaya->jurnal_balik == null) {
                    // --- Debit ---
                    $data['jurnal_balik'] = $j_biaya->id;
                    $data['coa_id'] = 100;
                    $data['debit'] = $j_biaya->debit;
                    $data['credit'] = 0;
                    $data['tipe'] = 'OMZ';
                    $data['nomor'] = $balik->nomor;
                    $data['relasi'] = $balik->nomor;
                    $data['no'] = $balik->no;
                    $data['created_at'] = $balik->tanggal;

                    \Log::info('Membuat jurnal debit', ['coa_id' => $data['coa_id'], 'nomor' => $data['nomor']]);

                    $jurnal_debit = \App\Models\Jurnal::create($data);

                    $j_biaya->update([
                        'jurnal_balik' => $jurnal_debit->id,
                        'is_balik'     => 1,
                    ]);

                    // --- Kredit ---
                    $data['jurnal_balik'] = $j_biaya->id;
                    $data['coa_id'] = $j_biaya->coa_id;
                    $data['credit'] = $j_biaya->debit;
                    $data['debit'] = 0;

                    \Log::info('Membuat jurnal kredit', ['coa_id' => $data['coa_id'], 'nomor' => $data['nomor']]);

                    $jurnal_credit = \App\Models\Jurnal::create($data);
                }

            $res[] = $j_biaya->id;
        }

        return back()->with('success', 'Data berhasil disimpan dengan nomor jurnal ' . $balik->nomor);
    } catch (\Throwable $e) {
        // Tangkap error dan log detail
        \Log::error('Error di jurnalBalikTrucking', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}


    public function jurnalBalikTruckingExt()
{
    $month = request('month');
    $year = request('year');
    $tipe = request('tipe');
    $jurnal_id = json_decode(request('jurnal_id'));

    // cek apakah sudah ada jurnal balik untuk periode & tipe
    $balik = JurnalBalik::where('bulan', $month)
        ->where('tipe', 'Trucking Ext')
        ->where('tahun', $year)
        ->first();

    if (!$balik) {
        $c = new Carbon($year . '-' . sprintf('%02d', $month) . '-01');
        $last = $c->endOfMonth()->format('Y-m-d');
        $no = 4;
        $nomor = 'OMZ-' . sprintf('%02d', $month) . '-' . sprintf('%03d', $no) . '/' . date('y', strtotime($year . '-' . sprintf('%02d', $month) . '-01'));

        $balik = JurnalBalik::create([
            'tanggal' => $last,
            'bulan' => $month,
            'tahun' => $year,
            'nomor' => $nomor,
            'no' => $no,
            'tipe' => 'Trucking Ext',
        ]);
    }

    $jurnal = Jurnal::whereIn('order_trucking_id', $jurnal_id)->whereNull('jurnal_balik')->get();
    $res = [];

    foreach ($jurnal as $j_biaya) {
    // Kalau BELUM ada jurnal balik → bikin baru
    if ($j_biaya->jurnal_balik == null) {
        if ($tipe == 'ext' && (int) $j_biaya->coa_id === 80 || (int) $j_biaya->coa_id === 60) {
            $data = $j_biaya->toArray();
            unset($data['id']);

            // DEBIT
            $data['jurnal_balik'] = $j_biaya->id;
            $data['coa_id'] = 98;
            $data['debit'] = $j_biaya->debit;
            $data['credit'] = 0;
            $data['tipe'] = 'OMZ';
            $data['nomor'] = $balik->nomor;
            $data['relasi'] = $balik->nomor;
            $data['no'] = $balik->no;
            $data['created_at'] = $balik->tanggal;
            $jurnal_debit = Jurnal::create($data);

            // CREDIT
            $data['coa_id'] = $j_biaya->coa_id;
            $data['debit'] = 0;
            $data['credit'] = $j_biaya->debit;
            $jurnal_credit = Jurnal::create($data);

            $j_biaya->update([
                'jurnal_balik' => $jurnal_debit->id,
                'is_balik'     => 1
            ]);
        }
    } else {
        // Kalau SUDAH ada jurnal balik → update yang lama
        foreach ($j_biaya->jurnal_balik_data as $item) {
            if ($item->debit == 0) {
                $item->update([
                    'credit' => $j_biaya->debit
                ]);
            } else {
                $item->update([
                    'debit'  => $j_biaya->debit
                ]);
            }
        }
    }

    $res[] = $j_biaya->id;
}

    return back()->with('success', 'Data berhasil disimpan dengan nomor jurnal ' . $balik->nomor);
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
