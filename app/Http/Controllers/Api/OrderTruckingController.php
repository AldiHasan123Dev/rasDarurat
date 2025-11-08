<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderTruckingResource;
use App\Http\Resources\OrderBiayaTruckResource;
use App\Services\SyncService;
use App\Models\Jurnal;
use App\Models\OrderTrucking;
use App\Models\OrderBiayaTruck;
use Illuminate\Http\Request;

class OrderTruckingController extends Controller
{
    public function delete(Request $request) {
        $orderTrucking = OrderTrucking::find($request->id);
        if (!$orderTrucking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan!'
            ], 404);
        }
        $cekJurnalTerhapus = Jurnal::where('order_trucking_id', $orderTrucking->id)
            ->whereNull('deleted_at')
            ->count();
        if ($cekJurnalTerhapus > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data order trucking tidak bisa dihapus karena biaya sudah tercatat!'
            ], 400);
        }
        $orderTrucking->delete();
          return response()->json([
        'status' => 'success',
        'message' => 'Data order trucking berhasil dihapus!'
    ], 200);
        }

    public function getJurnal()
    {
        $order = OrderTrucking::find(request('id'));
        $sangu_sopir = Jurnal::where('order_trucking_id',request('id'))->where('nama','LIKE','SANGU SOPIR%')->where('debit','>',0)->orWhere('order_id',$order->order_id)->whereNotNull('order_id')->where('nama','LIKE','SANGU SOPIR%')->where('debit','>',0)->sum('debit') ?? 0;
        $sangu_kuli = Jurnal::where('order_trucking_id',request('id'))->where('nama','LIKE','SANGU KULI%')->where('debit','>',0)->orWhere('order_id',$order->order_id)->whereNotNull('order_id')->where('nama','LIKE','SANGU KULI%')->where('debit','>',0)->sum('debit') ?? 0;
        $uang_makan = Jurnal::where('order_trucking_id',request('id'))->where('nama','LIKE','UANG MAKAN%')->where('debit','>',0)->orWhere('order_id',$order->order_id)->whereNotNull('order_id')->where('nama','LIKE','UANG MAKAN%')->where('debit','>',0)->sum('debit') ?? 0;
        $solar = Jurnal::where('order_trucking_id',request('id'))->where('nama','LIKE','BIAYA TAMBAH SOLAR%')->where('debit','>',0)->orWhere('order_id',$order->order_id)->whereNotNull('order_id')->where('nama','LIKE','TAMBAH SOLAR%')->where('debit','>',0)->sum('debit') ?? 0;
        $op = Jurnal::where('order_trucking_id',request('id'))->where('nama','LIKE','BIAYA OPERASIONAL TRUCKING%')->where('debit','>',0)->orWhere('order_id',$order->order_id)->whereNotNull('order_id')->where('nama','LIKE','BIAYA OPERASIONAL TRUCKING%')->where('debit','>',0)->sum('debit') ?? 0;
        $cleaning = Jurnal::where('order_trucking_id',request('id'))->where('nama','LIKE','BIAYA CLEANING%')->where('debit','>',0)->orWhere('order_id',$order->order_id)->whereNotNull('order_id')->where('nama','LIKE','BIAYA CLEANING%')->where('debit','>',0)->sum('debit') ?? 0;
        $tally = Jurnal::where('order_trucking_id',request('id'))->where('nama','LIKE','BIAYA CHECKER%')->where('debit','>',0)->orWhere('order_id',$order->order_id)->whereNotNull('order_id')->where('nama','LIKE','BIAYA CHECKER%')->where('debit','>',0)->sum('debit') ?? 0;
        $tipe = $order->kendaraan->milik;
        if($order->customer->r1 == 1){
            $tipe = 'R1';
        }
        if($order->customer->r2 == 1){
            $tipe = 'R2';
        }
        if($tipe=='R2'){
            $order->update([
                'sangu' => $sangu_sopir,
                'kuli' => $sangu_kuli,
                'tambah_solar' => $solar,
                'tally' => $tally,
                'uang_makan' => $uang_makan,
                'cleaning' => $cleaning,
                'op' => $op
            ]);
        }
        // if($sangu_sopir>0 || $sangu_kuli>0 || $solar>0 || $tally>0 || $uang_makan>0 || $op>0 || $cleaning>0){
        // }
        $service = new SyncService();
        $service->trucking(request('id'));
        return response([
            'sangu_sopir' => $sangu_sopir,
            'sangu_kuli' => $sangu_kuli,
            'uang_makan' => $uang_makan,
            'solar' => $solar,
            'op' => $op,
            'cleaning' => $cleaning,
            'tally' => $tally,
            'tipe' => $tipe
        ]);
    }

    public function jqgrid()
    {
        $page = request('page'); // get the requested page
        $limit = request('rows'); // get how many rows we want to have into the grid
        $sidx = request('sidx'); // get index row - i.e. user click to sort
        $sord = request('sord'); // get the direction
        $search = request('_search'); // get the search
        $is_search = false;
        if($search=='true'){
            $is_search = true;
        }
        $query = OrderTrucking::query();

        $start = $limit * $page - $limit;
        if ($start < 0){
            $start = 0;
        }

        if(request('id')){
            $query->where('id','LIKE','%'.request('id').'%');
        }
        if(request('tgl_muat')){
            $d = substr(request('tgl_muat'),0,2);
            $m = substr(request('tgl_muat'),3,2);
            $y = substr(request('tgl_muat'),6,2);
            $date = '20'.$y.'-'.$m.'-'.$d;
            $query->whereDate('tgl_muat','LIKE','%'.$date.'%');
        }
        if(request('invoice')){
            $query->where('invoice','LIKE','%'.request('invoice').'%');
        }
        if(request('container')){
            $query->where('container','LIKE','%'.request('container').'%');
        }
        if(request('tujuan')){
            $query->where('tujuan','LIKE','%'.request('tujuan').'%');
        }
        if(request('tipe')){
            $query->where('tipe','LIKE','%'.request('tipe').'%');
        }
        if(request('seal')){
            $query->where('seal','LIKE','%'.request('seal').'%');
        }
        if(request('customer')){
            $query->whereHas('customer', function($q){
                $q->where('nama','LIKE','%'.request('customer').'%');
            });
        }
        if(request('trucking')){
            $query->whereHas('order', function($q){
                $q->where('trucking','LIKE','%'.request('trucking').'%');
            });
        }
        if(request('job')){
            $query->whereHas('order', function($q){
                $q->where('job','LIKE','%'.request('job').'%');
            });
        }
       if (request('invNull')) {
    $query->whereNull('invoice')
        ->whereHas('kendaraan', function ($q) {
            $q->where('milik', 'R1');
        })
        ->where('customer_id', '<>', 2); // mengecualikan customer_id = 2
}

        if(request('sopir')){
            $query->whereHas('sopir', function($q){
                $q->where('nama','LIKE','%'.request('sopir').'%');
            });
        }
        if(request('nopol')){
            $query->whereHas('kendaraan', function($q){
                $q->where('nopol','LIKE','%'.request('nopol').'%');
                $q->orWhere('milik','LIKE','%'.request('nopol').'%');
            });
        }
        if(request('pembayar')){
            $query->whereHas('order', function($q){
                $q->whereHas('tarif', function($a){
                    $a->whereHas('customer', function($b){
                        $b->where('nama','LIKE','%'.request('pembayar').'%');
                    });
                });
            });
        }

        // if($sidx){
        //     $data = $query->orderBy($sidx,$sord)->orderBy('no_job')->skip($start)->take($limit)->get();
        // }else{
        // }
        if (request('invNull')) {
    $data = $query->reorder()->orderBy('tgl_muat', 'asc')->skip($start)->take($limit)->get();
} else {
    $data = $query->reorder()->orderBy('tgl_muat', 'desc')->skip($start)->take($limit)->get();
}


        // if($is_search){
        //     $count = $query->count();
        // }else{
        // }
        $count = OrderTrucking::get('id')->count();
         if(request('invNull')){
         $count = OrderTrucking::whereNull('invoice')->whereHas('kendaraan', function($q){
                $q->where('milik', 'R1');
            })->where('customer_id', '<>', 2)->get('id')->count();
        }

        if ($count > 0 && $limit > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages){
            $page = $total_pages;
        }

        $response = OrderTruckingResource::collection($data);
        return response([
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $response
        ]);
    }
    public function jqgrid1()
    {
        $page = request('page'); // get the requested page
        $limit = request('rows'); // get how many rows we want to have into the grid
        $sidx = request('sidx'); // get index row - i.e. user click to sort
        $sord = request('sord'); // get the direction
        $search = request('_search'); // get the search
        $is_search = false;
        if($search=='true'){
            $is_search = true;
        }
        $query = OrderBiayaTruck::query();

        $start = $limit * $page - $limit;
        if ($start < 0){
            $start = 0;
        }
        if(request('customer')){
            $query->whereHas('orderTruck', function ($q) {
            $q->whereHas('customer', function($q2){
                $q->where('nama','LIKE','%'.request('customer').'%');
            });
        });
        }
        if (request('seal')) {
            $query->whereHas('orderTruck', function ($q) {
                $q->where('seal', 'LIKE', '%' . request('seal') . '%');
            });
        } 
        if (request('tgl_muat')) {
            $query->whereHas('orderTruck', function ($q) {
                $q->where('tgl_muat', 'LIKE', '%' . request('tgl_muat') . '%');
            });
        }        
        if (request('container')) {
            $query->whereHas('orderTruck', function ($q) {
                $q->where('container', 'LIKE', '%' . request('container') . '%');
            });
        }      
        if (request('job')) {
            $query->whereHas('orderTruck', function ($q) {
                $q->whereHas('order', function ($q2) {
                    $q2->where('job', 'LIKE', '%' . request('job') . '%');
                });
            });
        }
        
        if (request('sopir')) {
            $query->whereHas('orderTruck', function ($q) {
                $q->whereHas('sopir', function ($q2) {
                    $q2->where('nama', 'LIKE', '%' . request('sopir') . '%');
                });
            });
        }
        if (request('nominal_tb_tl1')) {
            $query->where('nominal_tb_tl1', 'LIKE', '%' . request('nominal_tb_tl1') . '%');
        }
        if (request('order_trucking_id')) {
            $query->where('order_trucking_id', 'LIKE', '%' . request('order_trucking_id') . '%');
        }
        if (request('nominal_stappel1')) {
            $query->where('nominal_stappel1', 'LIKE', '%' . request('nominal_stappel1') . '%');
        }
        if (request('nominal_sangu_kuli1')) {
            $query->where('nominal_sangu_kuli1', 'LIKE', '%' . request('nominal_sangu_kuli1') . '%');
        }
        if (request('nominal_sangu_kuli2')) {
            $query->where('nominal_sangu_kuli2', 'LIKE', '%' . request('nominal_sangu_kuli2') . '%');
        }
        if (request('nominal_sangu_kuli3')) {
            $query->where('nominal_sangu_kuli3', 'LIKE', '%' . request('nominal_sangu_kuli3') . '%');
        }
        if (request('tgl_sangu_kuli1')) {
            $query->where('tgl_sangu_kuli1', 'LIKE', '%' . request('tgl_sangu_kuli1') . '%');
        }
        if (request('tgl_sangu_kuli2')) {
            $query->where('tgl_sangu_kuli2', 'LIKE', '%' . request('tgl_sangu_kuli2') . '%');
        }
        if (request('tgl_sangu_kuli3')) {
            $query->where('tgl_sangu_kuli3', 'LIKE', '%' . request('tgl_sangu_kuli3') . '%');
        }
        if (request('tgl_tb_tl')) {
            $query->where('tgl_tb_tl', 'LIKE', '%' . request('tgl_tb_tl') . '%');
        }
        if (request('tgl_stappel')) {
            $query->where('tgl_stappel', 'LIKE', '%' . request('tgl_stappel') . '%');
        }
        if (request('nopol')) {
            $query->whereHas('orderTruck', function ($q) {
                $q->whereHas('kendaraan', function ($q2) {
                    $q2->where('nopol','LIKE','%'.request('nopol').'%');
                    $q2->orWhere('milik','LIKE','%'.request('nopol').'%');
                });
            });
        }
        
        // if($is_search){
        //     $count = $query->count();
        // }else{
        // }
        $count = OrderBiayaTruck::get('id')->count();

        if ($count > 0 && $limit > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages){
            $page = $total_pages;
        }
        $data = $query->skip($start)->take($limit)->get();

        $response = OrderBiayaTruckResource::collection($data);
        return response([
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $response
        ]);
    }

    public function getArrayId(Request $request)
    {
        $id = $request->id;
        $arr = [];
        foreach ($id as $val) {
            if((int)$val>0){
                array_push($arr,$val);
            }
        }
        if(count($arr)>0){
            $id = array_values(array_filter($arr));
            $ids_ordered = implode(',', $id);
            $orders = OrderTrucking::whereIn('id',$id)->orderByRaw("FIELD(id,$ids_ordered)")->get();
            $data = OrderTruckingResource::collection($orders);
            return response($data);
        }

        return response([]);
    }
}
