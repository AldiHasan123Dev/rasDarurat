<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $tagihan = Tagihan::create($data);
        return response($tagihan);
    }

    public function getOne($id)
    {
        $data = Tagihan::find($id);
        return response($data);
    }

    public function destroy(Tagihan $tagihan)
    {
        $tagihan->delete();
        return response($tagihan);
    }
}
