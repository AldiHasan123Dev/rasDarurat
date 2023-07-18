<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Pelayaran;
use Illuminate\Http\Request;
use App\Models\TarifPelayaran;
use App\Models\HutangPelayaran;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

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

    public function cetak_invoice(Request $request)
    {
        $order_id = explode(',', $request->order_id);
        // // dd($request->all());

        if (count($order_id) <= 1 && $order_id[0] == "") {
            return back()->with('danger', 'Harap checklist terlebih dahulu!');
        }
        $orders = Order::whereIn('id', $order_id)->get()->groupBy('job');
        if ($orders->count() > 1) {
            return back()->with('danger', 'Anda tidak bisa memilih ' . $orders->count() . ' Customer sekaligus!, Harap untuk pilih satu Customer');
        }
        $order = Order::whereIn('id', $order_id)->first();
        // $null_job = Order::whereIn('id', $order_id)->whereNull('id')->count();

        // $tipe = $request->tipe;
        $data = HutangPelayaran::whereIn('order_id', $order_id)->orderBy('created_at')->get()->groupBy('job');

        // $data = HutangPelayaran::whereIn('order_id', $order_id)->orderBy('created_at')->get()->groupBy('job');

        return view('admin.hutangpelayaran.invoice', compact('data', 'order'));
    }

    public function cetak_invoice_get()
    {
        $order_id = request('order_id');
        $order = Order::where('id', $order_id)->first();
        if (!$order) {
            return back()->with('danger', 'Invoice Tidak ditemukan!');
        }
        $hutangpelayaran = HutangPelayaran::where('order_id', request('order_id'))->first();
        $nama = $hutangpelayaran->order->jadwal_kapal->pelayaran->nama;
        $data = Order::where('order_id', $order_id)->orderBy('tgl_muat')->get()->groupBy('job');
        return view('admin.hutangpelayaran.invoice', compact('order', 'data', 'nama'));
    }

    // public function show()
    // {
    //     return view('admin.hutangpelayaran.invoice');
    // }
}
