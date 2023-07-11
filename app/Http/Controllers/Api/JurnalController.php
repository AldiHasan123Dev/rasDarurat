<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JurnalResource;
use App\Models\Jurnal;
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

    public function index()
    {
        $query = Jurnal::query();
        if(request('order_id')){
            $query->where('order_id',request('order_id'));
        }
        if(request('order_trucking_id')){
            $query->where('order_trucking_id',request('order_trucking_id'));
        }
        $data = $query->get();
        $data = JurnalResource::collection($data);
        return response($data);
    }
}
