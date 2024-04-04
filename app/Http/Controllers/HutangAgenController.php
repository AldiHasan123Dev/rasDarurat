<?php

namespace App\Http\Controllers;

use App\Models\HutangAgen;
use App\Models\Jurnal;
use App\Models\Order;
use App\Models\TagihanAgen;
use App\Models\TarifAgen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class HutangAgenController extends Controller
{
    public function index()
    {
        $data = Order::whereHas('agent')->whereNull('invoice_agen')->whereYear('created_at',2024)->get()->groupBy('agen_id');
        return view('admin.hutangagen.index', compact('data'));
    }

    public function list()
    {
        $data = HutangAgen::all()->groupBy('jurnal');
        // dd($data);
        return view('admin.hutangagen.list', compact('data'));
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

        $orders = Order::whereIn('id',$ids)->get();
        $tarif = TarifAgen::where('agen_id', $orders->first()->agen_id)->where('is_active',1)->orderBy('created_at')->get();
        return view('admin.hutangagen.draf', compact('orders','tarif','ids'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        // dd($data);
        $no = Jurnal::where('tipe','TEST')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
        $nomor = 'HUTAGEN/'.sprintf('%02d',date('m')).'-'.sprintf('%03d',$no).'/'.date('y');
        for ($i=0; $i < count($request->order_id); $i++) {
            $order = Order::find($request->order_id[$i]);
            $cek = Jurnal::where('order_id',$request->order_id[$i])->where('coa_id',93)->where('debit','>',0)->count();
            $jurnal = array();
            $jurnal['order_id'] = $request->order_id[$i];
            $jurnal['nomor'] = $nomor;
            $jurnal['no'] = $no;
            $jurnal['nama'] = 'Biaya Dooring '.$order->tarif->customer->nama.' '.$order->tarif->shipmentInfo->nama;
            $jurnal['container'] = $order->container;
            $jurnal['invoice'] = $request->invoice;
            $jurnal['tipe'] = 'TEST';
            if($cek>0){
                $jurnal['coa_id'] = 134;
                $jurnal['debit'] = $request->tarif[$i];
                $jurnal['credit'] = 0;
                Jurnal::create($jurnal);
                $jurnal['coa_id'] = 63;
                $jurnal['credit'] = $request->tarif[$i];
                $jurnal['debit'] = 0;
                Jurnal::create($jurnal);
            }else{
                $jurnal['coa_id'] = 31;
                $jurnal['debit'] = $request->tarif[$i];
                $jurnal['credit'] = 0;
                Jurnal::create($jurnal);
                $jurnal['coa_id'] = 63;
                $jurnal['credit'] = $request->tarif[$i];
                $jurnal['debit'] = 0;
                Jurnal::create($jurnal);
            }
            Order::find($request->order_id[$i])->update(['invoice_agen' => $request->invoice]);
            HutangAgen::create([
                'order_id' => $request->order_id[$i],
                'tarif' => $request->tarif[$i],
                'invoice' => $request->invoice,
                'jurnal' => $nomor,
                'tanggal' => $request->tanggal
            ]);
        }
        for ($i=0; $i < count($data['tagihan_order_id']); $i++) {
            if($data['nama'][$i]!=null && $data['jumlah'][$i]!=null && $data['tagihan_order_id'][$i]!=null){
                $order = Order::find($data['tagihan_order_id'][$i]);
                if($data['beban'][$i]=='ras'){
                    $cek = Jurnal::where('order_id',$data['tagihan_order_id'][$i])->where('coa_id',93)->where('debit','>',0)->count();
                    if($cek > 0){
                        Jurnal::create([
                            'order_id' => $data['tagihan_order_id'][$i],
                            'nomor' => $nomor,
                            'no' => $no,
                            'nama' => $data['nama'][$i],
                            'container' => $order->container,
                            'invoice' => $request->invoice,
                            'tipe' => 'TEST',
                            'coa_id' => 134,
                            'debit' => $data['jumlah'][$i],
                            'credit' => 0
                        ]);
                    }else{
                        Jurnal::create([
                            'order_id' => $data['tagihan_order_id'][$i],
                            'nomor' => $nomor,
                            'no' => $no,
                            'nama' => $data['nama'][$i],
                            'container' => $order->container,
                            'invoice' => $request->invoice,
                            'tipe' => 'TEST',
                            'coa_id' => 31,
                            'debit' => $data['jumlah'][$i],
                            'credit' => 0
                        ]);
                    }
                    Jurnal::create([
                        'order_id' => $data['tagihan_order_id'][$i],
                        'nomor' => $nomor,
                        'no' => $no,
                        'nama' => $data['nama'][$i],
                        'container' => $order->container,
                        'invoice' => $request->invoice,
                        'tipe' => 'TEST',
                        'coa_id' => 63,
                        'credit' => $data['jumlah'][$i],
                        'debit' => 0
                    ]);
                }else{
                    Jurnal::create([
                        'order_id' => $data['tagihan_order_id'][$i],
                        'nomor' => $nomor,
                        'no' => $no,
                        'nama' => $data['nama'][$i],
                        'container' => $order->container,
                        'invoice' => $request->invoice,
                        'tipe' => 'TEST',
                        'coa_id' => 63,
                        'debit' => $data['jumlah'][$i],
                        'credit' => 0
                    ]);
                    Jurnal::create([
                        'order_id' => $data['tagihan_order_id'][$i],
                        'nomor' => $nomor,
                        'no' => $no,
                        'nama' => $data['nama'][$i],
                        'container' => $order->container,
                        'invoice' => $request->invoice,
                        'tipe' => 'TEST',
                        'coa_id' => 28,
                        'credit' => $data['jumlah'][$i],
                        'debit' => 0
                    ]);
                }

                TagihanAgen::create([
                    'invoice' => $request->invoice,
                    'order_id' => $data['tagihan_order_id'][$i],
                    'nama' => $data['nama'][$i],
                    'jumlah' => $data['jumlah'][$i],
                    'beban' => $data['beban'][$i]
                ]);
            }
        }
        return redirect()->route('hutang-agen.index')->with('success', 'Data berhasil disimpan');
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
