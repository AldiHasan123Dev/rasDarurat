<?php

namespace App\Http\Controllers;

use App\Models\HutangPelayaran;
use App\Models\Pelayaran;
use App\Models\TarifPelayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class HutangPelayaranController extends Controller
{
    public function index()
    {
        $data = HutangPelayaran::with('order')->get()->groupBy('order.job');
        return view('admin.hutangpelayaran.index', compact('data'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        HutangPelayaran::create($data);

        return back()->with('success', 'Data berhasil disimpan');
    }

    public function update(HutangPelayaran $hutangpelayaran, Request $request)
    {
        $data = $request->all();
        $hutangpelayaran->update($data);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy(HutangPelayaran $hutangpelayaran)
    {
        $hutangpelayaran->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = HutangPelayaran::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('tarif_pelayaran_id', function ($data) {
                return $data->tarif_pelayaran->pelayaran->nama;
            })
            ->addColumn('order_id', function ($data) {
                return $data->order->job . '-' . sprintf('%02d', $data->no_job);
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
