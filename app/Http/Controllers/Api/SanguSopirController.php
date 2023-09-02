<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
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
        ]);
    }

    public function createOrUpdate(Request $request)
    {
        $data = $request->all();
        $tujuan = Lokasi::where('nama',$request->tujuan)->first();
        if(!$tujuan){
            $tujuan = Lokasi::create(['nama'=>$request->tujuan]);
        }
        $data['tujuan'] = $tujuan->id;
        $data['ukuran_20'] = str_replace(['.',','],'',$request->ukuran_20);
        $data['ukuran_40'] = str_replace(['.',','],'',$request->ukuran_40);
        $data['ukuran_combo'] = str_replace(['.',','],'',$request->ukuran_combo);
        $data['borongan_kuli_20'] = str_replace(['.',','],'',$request->borongan_kuli_20);
        $data['borongan_kuli_40'] = str_replace(['.',','],'',$request->borongan_kuli_40);
        $data['borongan_kuli_combo'] = str_replace(['.',','],'',$request->borongan_kuli_combo);
        try {
            if ($request->id) {
                SanguSopir::find($request->id)->update($data);
            }else{
                SanguSopir::create($data);
            }
            return response('success');
        } catch (\Throwable $th) {
            return response($th);
        }
    }
}
