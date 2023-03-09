<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BTTB;
use App\Models\Satuan;
use Illuminate\Http\Request;

class BTTBController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $barang = Barang::find($request->barang_id);
        $satuan = Satuan::find($request->satuan_id);
        if (!$satuan) {
            $satuan = Satuan::create(['nama'=>$request->satuan_id]);
        }
        if (!$barang) {
            $barang = Barang::create(['nama'=>$request->barang_id]);
        }
        $data['barang_id'] = $barang->id;
        $data['satuan_id'] = $satuan->id;
        $bttb = BTTB::create($data);
        return response([
            'status' => 'success',
            'data' => $bttb,
            'message' => 'Data berhasil di simpan'
        ]);
    }
}
