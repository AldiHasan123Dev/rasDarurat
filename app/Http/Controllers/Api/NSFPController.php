<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NSFP;
use Illuminate\Http\Request;

class NSFPController extends Controller
{
    public function generate(Request $request)
    {
        $no = str_replace(' ','',$request->nomor);
        $res = explode('.',$no);
        $depan = $res[0].'.'.$res[1].'.';
        $res = (int)end($res);
        for ($i=0; $i < $request->jumlah; $i++) {
            $num = $res + $i;
            NSFP::create([
                'nomor' => $depan.''.$num,
                'available' => 1
            ]);
        }

        return response('success');
    }
}
