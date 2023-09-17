<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HutangPelayaran;
use Illuminate\Http\Request;

class HutangPelayaranController extends Controller
{
    public function updateByOrder(Request $request)
    {
        $data = $request->all();
        $hutang = HutangPelayaran::where('order_id',$request->order_id)->first();
        $hutang->update($data);
        return response('success');
    }
}
