<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class TruckingController extends Controller
{
    public function order()
    {
        $data = Order::all()->sortByDesc('no')->take(100);
        $data = OrderResource::collection($data);
        return view('admin.trucking.order', compact('data'));
    }
}
