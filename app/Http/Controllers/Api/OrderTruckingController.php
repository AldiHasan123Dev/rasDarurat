<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderTrucking;
use Illuminate\Http\Request;

class OrderTruckingController extends Controller
{
    public function delete(Request $request)
    {
        OrderTrucking::find($request->id)->delete();
    }
}
