<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getOrderWithNopol($nopol)
    {
        $orders = Order::where('nopol','LIKE','%'.$nopol.'%')->get(['id','job','no_job','container']);
        return response($orders);
    }

    public function index()
    {
        $start = request('start');
        $limit = request('limit');
        $data = Order::all()->sortBy('job')->sortBy('no')->skip($start)->take($limit);
        $count = Order::select('id')->count();
        $data = OrderResource::collection($data);
        return response([
            'start' => $start + $limit,
            'count' => $count,
            'data' => $data
        ]);
    }

    public function ba_kembali()
    {
        $start = request('start');
        $limit = request('limit');
        $data = Order::whereHas('tarif', function($q){
            $q->whereIn('kondisi',[5,6]);
            $q->whereHas('customer', function($qu){
                $qu->where('ba_kembali',1);
            });
        })->whereHas('jadwal_kapal', function($q){
            $q->whereNotNull('td');
        })->whereNull('invoice')
        ->whereNull('ba_kembali')->orderBy('job','asc')->orderBy('no','asc')->skip($start)->take($limit)->get();
        $count = Order::select('id')->count();
        $data = OrderResource::collection($data);
        return response([
            'start' => $start + $limit,
            'count' => $count,
            'data' => $data
        ]);
    }

    public function pre_invoice()
    {
        $ids = array();
        $data1 = Order::whereHas('tarif', function($q){
            $q->whereIn('kondisi',[1,7]);
        })->whereHas('jadwal_kapal', function($q){
            $q->whereNotNull('td');
        })->whereNull('invoice')->pluck('id');
        foreach ($data1 as $item ) {
            array_push($ids,$item);
        }

        $data2 = Order::whereHas('tarif', function($q){
            $q->whereIn('kondisi',[5,6]);
            $q->whereHas('customer', function($qu){
                $qu->where('ba_kembali',1);
            });
        })->whereHas('jadwal_kapal', function($q){
            $q->whereNotNull('td');
        })->whereNull('invoice')->whereNotNull('ba_kembali')->pluck('id');
        foreach ($data2 as $item ) {
            array_push($ids,$item);
        }

        $data3 = Order::whereHas('tarif', function($q){
            $q->whereIn('kondisi',[5,6]);
            $q->whereHas('customer', function($qu){
                $qu->where('ba_kembali',0);
            });
        })->whereHas('jadwal_kapal', function($q){
            $q->whereNotNull('td');
        })->whereNull('invoice')->pluck('id');
        foreach ($data3 as $item ) {
            array_push($ids,$item);
        }

        $count = count($ids);
        $data = Order::whereIn('id',$ids)->orderBy('job','asc')->orderBy('no','asc')->get();
        $data = OrderResource::collection($data);
        return response([
            'start' => 0,
            'count' => $count,
            'data' => $data
        ]);
    }


}
