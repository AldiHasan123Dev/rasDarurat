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
        $barang = Barang::where('nama',$request->barang_id)->first();
        $satuan = Satuan::where('nama',$request->satuan_id)->first();
        if (!$satuan) {
            $satuan = Satuan::create(['nama'=>$request->satuan_id]);
        }
        if (!$barang) {
            $barang = Barang::create(['nama'=>$request->barang_id]);
        }
        $data['barang_id'] = $barang->id;
        $data['satuan_id'] = $satuan->id;
        if ($request->id&&$request->id>0) {
            $bttb = BTTB::find($request->id);
            $bttb->update($data);
        }else{
            $bttb = BTTB::create($data);
        }
        return response([
            'status' => 'success',
            'data' => $bttb,
            'message' => 'Data berhasil di simpan'
        ]);
    }

    public function delete()
    {
        $bttb = BTTB::find(request('id'));
        $bttb->delete();
        return response([
            'status' => 'success',
            'data' => $bttb,
            'message' => 'Data berhasil di hapus'
        ]);
    }
}
