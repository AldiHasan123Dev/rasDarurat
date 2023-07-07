<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BTTB;
use App\Models\Customer;
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

    public function add(Request $request)
    {
        $data = $request->all();
        for ($i=0; $i < 12; $i++) {
            $input = array();
            $customer = Customer::where('nama',$data['pengirim_id-'.$i])->first();
            if ($data['no_gudang-'.$i] && $data['barang_id-'.$i] && $data['satuan_id-'.$i] && $data['qty-'.$i] && $customer) {
                $barang = Barang::where('nama',$data['barang_id-'.$i])->first();
                $satuan = Satuan::where('nama',$data['satuan_id-'.$i])->first();
                if (!$satuan) {
                    $satuan = Satuan::create(['nama'=>$data['satuan_id-'.$i]]);
                }
                if (!$barang) {
                    $barang = Barang::create(['nama'=>$data['barang_id-'.$i]]);
                }
                $input['order_id'] = $request->order_id;
                $input['barang_id'] = $barang->id;
                $input['satuan_id'] = $satuan->id;
                $input['no_gudang'] = $data['no_gudang-'.$i];
                $input['qty'] = $data['qty-'.$i];
                $input['p'] = $data['p-'.$i];
                $input['l'] = $data['l-'.$i];
                $input['t'] = $data['t-'.$i];
                $input['vol'] = $data['vol-'.$i];
                $input['berat'] = $data['berat-'.$i];
                $input['tgl_masuk'] = $data['tgl_masuk-'.$i];
                $input['pengirim_id'] = $customer->id;
                $bttb = BTTB::create($input);
            }
        }

        $count = BTTB::where('order_id',$data['order_id'])->sum('qty');
        return response($count);
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
