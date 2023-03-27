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
}
