<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class JurnalController extends Controller
{
    public function getArrayID()
    {
        $order = Order::where('job','LIKE','%'.request('search').'%')->select('id','job as text')->get();
        return response([
            'items' => $order
        ]);
    }
}
