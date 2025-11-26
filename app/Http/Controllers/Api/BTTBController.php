<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BTTB;
use App\Models\Customer;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BTTBController extends Controller
{
    public function store(Request $request)
    {
        auth()->shouldUse('web'); // <-- Penting
        $data = $request->all();
        $barang = Barang::where('nama',$request->barang_id)->first();
        $satuan = Satuan::where('nama',$request->satuan_id)->first();
        if (!$satuan) {
            $satuan = Satuan::create(['nama'=>$request->satuan_id]);
        }
        if (!$barang) {
            $barang = Barang::create(['nama'=>$request->barang_id]);
        }
        $userId = null;
        $userId = auth()->id();
        if (!$userId && $request->user()) {
            $userId = $request->user()->id;
        }
        if (!$userId) {
            try {
                $userId = Auth::guard('sanctum')->id();
            } catch (\Throwable $e) {
                // guard might not exist or throw; ignore and continue
            }
        }
        if (!$userId && $request->filled('created_by')) {
            $userId = $request->input('created_by');
             $userId = $request->input('updated_by');
        }
        $data['barang_id'] = $barang->id;
        $data['satuan_id'] = $satuan->id;
        if ($request->id&&$request->id>0) {
            $bttb = BTTB::find($request->id);
            $data['updated_by'] = $userId;
            $bttb->update($data);
        }else{
            $data['created_by'] = $userId;
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
        auth()->shouldUse('web'); // <-- Penting
        $data = $request->all();

        // determine the current user id from several possible sources so
        // `created_by` can be saved even when this controller is in the API namespace
        $userId = null;
        $userId = auth()->id();
        if (!$userId && $request->user()) {
            $userId = $request->user()->id;
        }
        if (!$userId) {
            try {
                $userId = Auth::guard('sanctum')->id();
            } catch (\Throwable $e) {
                // guard might not exist or throw; ignore and continue
            }
        }
        if (!$userId && $request->filled('created_by')) {
            $userId = $request->input('created_by');
        }
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
                $input['keterangan'] = $data['keterangan-'.$i];
                $input['pengirim_id'] = $customer->id;
                $input['created_by'] = $userId;
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
