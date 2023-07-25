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
        if(request('nomor')){
            $query->where('nomor',request('nomor'));
        }
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
        $query = Jurnal::query();


        $start = $limit * $page - $limit;
        if ($start < 0){
            $start = 0;
        }

        if(request('month')){
            $query->whereMonth('created_at',request('month'));
        }
        if(request('tipe')){
            $query->where('tipe','LIKE','%'.request('tipe').'%');
        }

        if(request('search')){
            $query->search(request('search'));
        }
        $data = $query->orderBy('nomor')->skip($start)->take($limit)->get();

        $count = Jurnal::get('id')->count();
        if(request('month') && request('tipe')){
            $count = Jurnal::whereMonth('created_at',request('month'))->where('tipe','LIKE','%'.request('tipe').'%')->get('id')->count();
        }

        if ($count > 0 && $limit > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages){
            $page = $total_pages;
        }

        $response = JurnalResource::collection($data);
        return response([
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $response
        ]);
    }
}
