<?php

namespace App\Http\Controllers;

use App\Models\HutangAgen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class HutangAgenController extends Controller
{
    public function index()
    {
        return view('admin.hutangagen.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        HutangAgen::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(HutangAgen $hutangagen, Request $request)
    {
        $data = $request->all();
        $hutangagen->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(HutangAgen $hutangagen)
    {
        $hutangagen->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = HutangAgen::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('agen_id', function ($data) {
                return $data->tarif_agen->agen->nama;
            })
            ->addColumn('order_id', function ($data) {
                return $data->order->job.'-'.sprintf('%02d',$data->no_job);
            })
            ->rawColumns([])
            ->make(true);
    }
}
