<?php

namespace App\Http\Controllers;

use App\Models\TagihanTrucking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class TagihanTruckingController extends Controller
{
    public function index()
    {
        return view('admin.tagihantrucking.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        TagihanTrucking::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(TagihanTrucking $tagihantrucking, Request $request)
    {
        $data = $request->all();
        $tagihantrucking->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(TagihanTrucking $tagihantrucking)
    {
        $tagihantrucking->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = TagihanTrucking::query()->where('order_id',request('order_id'))->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                // $view = view('admin.tagihan.form',['tagihan'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <button type="button" onclick="deleteTagihan('.$data->id.')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                        </div>';
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
