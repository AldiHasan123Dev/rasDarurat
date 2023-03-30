<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SanguSopir;
use Illuminate\Http\Request;

class SanguSopirController extends Controller
{
    public function getSangu(Request $request)
    {
        $data = SanguSopir::find($request->tujuan);
        return response([
            'ukuran_20' => $data->ukuran_20,
            'ukuran_40' => $data->ukuran_40,
            'ukuran_combo' => $data->ukuran_combo,
            'sangu_20' => $data->sangu_20,
            'sangu_40' => $data->sangu_40,
            'sangu_combo' => $data->sangu_combo,
        ]);
    }
}
