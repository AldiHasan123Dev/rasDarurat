<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TagihanTrucking;
use Illuminate\Http\Request;

class TagihanTruckingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $tagihan = TagihanTrucking::create($data);
        return response($tagihan);
    }

    public function getOne($id)
    {
        $data = TagihanTrucking::find($id);
        return response($data);
    }

    public function destroy(TagihanTrucking $tagihan)
    {
        $tagihan->delete();
        return response($tagihan);
    }
}
