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

    public function create()
    {
        $barang = Barang::pluck('nama')->toArray();
        $satuan = Satuan::pluck('nama')->toArray();
        $pengirim = Customer::pluck('nama')->toArray();
        $data = BTTB::where('order_id',request('order_id'))->get();
        return view('admin.bttb.create', compact('barang','satuan','pengirim','data'));
    }

    public function edit(BTTB $bttb)
    {
        $barang = Barang::pluck('nama','id');
        $satuan = Satuan::pluck('nama','id');
        $pengirim = Customer::pluck('nama','id');
        return view('admin.bttb.edit', compact('bttb','barang','satuan','pengirim'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        foreach ($data['bttb'] as $item ) {
            $bttb = $item;
            if($bttb['no_gudang'] && $bttb['barang_id'] && $bttb['qty'] && $bttb['satuan_id'] && $bttb['pengirim_id']){
                $barang = Barang::find($bttb['barang_id']);
                $satuan = Satuan::find($bttb['satuan_id']);
                if (!$satuan) {
                    $satuan = Satuan::create(['nama'=>$bttb['satuan_id']]);
                }
                if (!$barang) {
                    $barang = Barang::create(['nama'=>$bttb['barang_id']]);
                }
                $bttb['order_id'] = $data['order_id'];
                $bttb['barang_id'] = $barang->id;
                $bttb['satuan_id'] = $satuan->id;
                BTTB::create($bttb);
            }
        }

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(BTTB $bttb, Request $request)
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
        $data = BTTB::join('barang','barang.id','=','bttb.barang_id')
                ->where('bttb.order_id',request('order_id'))
                ->select('bttb.*','barang.nama as nama_barang')
                ->orderBy('bttb.created_at')
                ->get();

        return Datatables::of($data)
            ->addIndexColumn()
            // ->order(function ($data){
            //     $data->orderBy('created_at','asc');
            // })
            ->addColumn('created_at', function($data){
                return date('d/m/y',strtotime($data->created_at));
            })
            ->addColumn('barang_id', function($data){
                return $data->barang->nama ?? '-';
            })
            ->addColumn('satuan_id', function($data){
                return $data->satuan->nama ?? '-';
            })
            ->addColumn('pengirim_id', function($data){
                return $data->pengirim->nama ?? '-';
            })
            ->addColumn('tgl_masuk', function($data){
                return date('d/m/Y',strtotime($data->tgl_masuk));
            })
            ->addColumn('action', function ($data) {
                // $bttb = $data;
                // $barang = Barang::pluck('nama','id');
                // $satuan = Satuan::pluck('nama','id');
                // $pengirim = Customer::pluck('nama','id');
                // $order = Order::find($data->order_id);
                // $view = view('admin.bttb.form', compact('barang','satuan','pengirim','order','bttb'))->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('bttb.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                                <a href="'.route('bttb.edit',$data).'" class="no-attr text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fas fa-pencil"></i></a>
                            </form>
                        </div>';
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
