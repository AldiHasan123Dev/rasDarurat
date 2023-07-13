<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderTruckingResource;
use App\Models\OrderTrucking;
use Illuminate\Http\Request;

class OrderTruckingController extends Controller
{
    public function delete(Request $request)
    {
        OrderTrucking::find($request->id)->delete();
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
        $data = $query->orderBy('tgl_muat','desc')->skip($start)->take($limit)->get();

        // if($is_search){
        //     $count = $query->count();
        // }else{
        // }
        $count = OrderTrucking::get('id')->count();

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

    public function getArrayId(Request $request)
    {
        $id = $request->id;
        $orders = OrderTrucking::whereIn('id',$id)->get();
        $data = OrderTruckingResource::collection($orders);
        return response($data);
    }
}
