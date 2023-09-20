<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Pelayaran;
use Illuminate\Http\Request;
use App\Models\TarifPelayaran;
use App\Models\HutangPelayaran;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use Illuminate\Support\Facades\Hash;

class HutangPelayaranController extends Controller
{
    public function index()
    {
        $lists = HutangPelayaran::where('status',0)->pluck('order_id')->toArray();
        $data = Order::join('jadwal_kapal','jadwal_kapal.id','=','order.jadwal_kapal_id')
            ->join('pelayaran','pelayaran.id','=','jadwal_kapal.pelayaran_id')
            ->join('kapal','kapal.id','=','jadwal_kapal.kapal_id')
            ->join('tarif','tarif.id','=','order.tarif_id')
            ->join('lokasi as dari','dari.id','=','tarif.dari')
            ->join('lokasi as tujuan','tujuan.id','=','tarif.tujuan')
            ->join('hutang_pelayaran','hutang_pelayaran.order_id','=','order.id')
            ->whereIn('order.id',$lists)
            ->select('order.job','order.tipe','hutang_pelayaran.is_lock','hutang_pelayaran.ut','dari.nama as dari','tujuan.nama as tujuan','order.tarif_id','order.container','order.seal','order.no_job','order.id','order.jadwal_kapal_id','jadwal_kapal.pelayaran_id','jadwal_kapal.voyage','kapal.nama as nama_kapal','pelayaran.nama')
            ->get()
            ->groupBy('jadwal_kapal.pelayaran_id','order.job');
        return view('admin.hutangpelayaran.index', compact('data'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $ids = array();
        foreach ($data['data'] as $id => $item) {
            $prop = $item;
            $prop['tgl_bg'] = $data['tanggal_bg'];
            $prop['no_bg'] = $data['no_bg'];
            $prop['nominal_bg'] = $data['nominal_bg'];
            $prop['pph'] = $data['pph'];
            $prop['pembulatan'] = $data['pembulatan'];
            HutangPelayaran::find($id)->update($prop);
            array_push($ids,$id);
        }
        return redirect()->route('hutang-pelayaran.index');

        $lists = HutangPelayaran::whereIn('id',$ids)->get();
        foreach ($lists as $item) {
            Jurnal::create([
                'coa_id' => 45,
                'order_id' => $item->order_id,
                'nomor' => '',
                'nama' => ''
            ]);
        }
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

        $cek = HutangPelayaran::with(['order','pelayaran'])->whereIn('order_id', $order_id)->get()->groupBy('tarif_pelayaran.pelayaran_id');
        if(count($cek)>1){
            return back()->with('danger', 'Harap checklist pelayaran yang sama!');
        }
        $data = HutangPelayaran::whereIn('order_id', $order_id)->orderBy('created_at')->get()->groupBy('job');
        $pelayaran = HutangPelayaran::whereIn('order_id', $order_id)->first()->pelayaran;
        // $data = HutangPelayaran::whereIn('order_id', $order_id)->orderBy('created_at')->get()->groupBy('job');

        return view('admin.hutangpelayaran.invoice', compact('data','pelayaran'));
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
