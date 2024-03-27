<?php

namespace App\Http\Controllers;

use App\Models\HutangAgen;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class HutangAgenController extends Controller
{
    public function index()
    {
        $data = Order::whereHas('agent')->whereYear('created_at',2024)->get()->groupBy('agen_id');
        return view('admin.hutangagen.index', compact('data'));
    }

    public function draf(Request $request)
    {
        $ids = $request->order_id;
        $orders = Order::whereIn('id',$ids)->get()->groupBy('agen_id');
        if(count($ids)==0){
            return back()->with('danger','Harus centang salah satu!');
        }
        if($orders->count()>1){
            return back()->with('danger','Harus centang pada agen yang sama!');
        }

        return view('admin.hutangagen.draf', compact('orders'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        HutangAgen::create($data);

        return back()->with('success', 'Data berhasil disimpan');
    }

    public function update(HutangAgen $hutangagen, Request $request)
    {
        $data = $request->all();
        $hutangagen->update($data);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy(HutangAgen $hutangagen)
    {
        $hutangagen->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = HutangAgen::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('agen_id', function ($data) {
                return $data->tarif_agen->agen->nama;
            })
            ->addColumn('order_id', function ($data) {
                return $data->order->job . '-' . sprintf('%02d', $data->no_job);
            })
            ->rawColumns([])
            ->make(true);
    }
}
