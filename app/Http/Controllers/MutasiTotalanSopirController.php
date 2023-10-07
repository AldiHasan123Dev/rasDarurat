<?php

namespace App\Http\Controllers;

use App\Http\Resources\MutasiTotalanSOpirResource;
use App\Models\MutasiTotalanSopir;
use Illuminate\Http\Request;

class MutasiTotalanSopirController extends Controller
{
    public function index()
    {
        $data = MutasiTotalanSopir::all()->whereNull('jurnal');
        $data = MutasiTotalanSOpirResource::collection($data);
        $data1 = MutasiTotalanSopir::all()->whereNotNull('jurnal');
        $data1 = MutasiTotalanSOpirResource::collection($data1);
        return view('admin.mutasitotalansopir.index',compact('data','data1'));
    }
}
