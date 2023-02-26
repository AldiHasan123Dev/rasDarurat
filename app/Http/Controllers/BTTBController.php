<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BTTB;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class BTTBController extends Controller
{
    public function index()
    {
        if(!request('order_id')){
            return redirect()->route('order.index');
        }
        $order = Order::find(request('order_id'));
        $barang = Barang::pluck('nama','id');
        $satuan = Satuan::pluck('nama','id');
        $pengirim = Customer::pluck('nama','id');
        return view('admin.bttb.index', compact('order','barang','satuan','pengirim'));
    }

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
        BTTB::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(BTTB $bttb, Request $request)
    {
        $data = $request->all();
        $bttb->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(BTTB $bttb)
    {
        $bttb->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = BTTB::where('order_id',request('order_id'))->get();

        return Datatables::of($data)
            ->addColumn('barang_id', function($data){
                return $data->barang->nama;
            })
            ->addColumn('satuan_id', function($data){
                return $data->satuan->nama;
            })
            ->addColumn('pengirim_id', function($data){
                return $data->pengirim->nama;
            })
            ->addColumn('tgl_masuk', function($data){
                return date('d/m/Y',strtotime($data->tgl_masuk));
            })
            ->addColumn('action', function ($data) {
                $bttb = $data;
                $barang = Barang::pluck('nama','id');
                $satuan = Satuan::pluck('nama','id');
                $pengirim = Customer::pluck('nama','id');
                $order = Order::find($data->order_id);
                $view = view('admin.bttb.form', compact('barang','satuan','pengirim','order','bttb'))->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('bttb.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBTTBUpdate'.$data->id.'" aria-controls="offcanvasBTTBUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasBTTBUpdate'.$data->id.'" aria-labelledby="offcanvasBTTBUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasBTTBUpdate'.$data->id.'Label">Form BTTB</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('bttb.update',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                    <input type="hidden" name="_method" value="PUT" />
                                    '.$view.'
                                </form>
                            </div>
                        </div>';
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
