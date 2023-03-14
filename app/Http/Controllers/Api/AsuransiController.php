<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asuransi;
use Illuminate\Http\Request;

class AsuransiController extends Controller
{
    public function getAsuransiByPelayaran($pelayaran_id)
    {
        $data = Asuransi::where('pelayaran_id',$pelayaran_id)->get();
        return response($data);
    }
}
