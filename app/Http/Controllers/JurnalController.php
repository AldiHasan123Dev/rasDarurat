<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\COA;
use App\Models\Jurnal;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class JurnalController extends Controller
{
    public function index()
    {
        return view('admin.jurnal.index');
    }

    public function kolektif()
    {
        $job = Order::pluck('job')->toArray();
        $job = array_unique($job);
        $coa = COA::doesnthave('coas')->orderBy('kode')->get();
        return view('admin.jurnal.kolektif', compact('job','coa'));
    }

    public function balik()
    {
        $coa = COA::doesnthave('coas')->orderBy('kode')->get();
        $data = [];
        $coa_debit = null;
        $coa_credit = null;
        $orders = Order::get(['id','job','no_job','seal']);
        if(request('draf')){
            $data = Jurnal::where('is_balik',0)->where('order_id',request('order_id'))->whereIn('coa_id',[request('debit_coa_id_tujuan'),request('credit_coa_id_tujuan')])->get();
            $coa_debit = COA::find(request('debit_coa_id'));
            $coa_credit = COA::find(request('credit_coa_id'));
        }
        return view('admin.jurnal.balik', compact('coa','data','coa_debit','coa_credit','orders'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        for ($i=0; $i < count($data['debit_coa_id']); $i++) {
            if ($data['name'][$i] && $data['amount'][$i]) {
                $name = $data['name'][$i];
                if($data['order_id'][$i]){
                    $order = Order::find($data['order_id'][$i]);
                    $id_job = $order->job.'-'.sprintf('%02d',$order->no_job);
                    $cont = $order->container;
                    $seal = $order->seal;
                    $shipment = $order->tarif->shipmentInfo->nama;
                    $pembayar = $order->tarif->customer->nama ?? '-';
                    $penerima = $order->penerima->nama ?? '-';
                    $pengirim = $order->pengirim->nama ?? '-';
                    $pelayaran = $order->jadwal_kapal->pelayaran->nama ?? '-';
                    $customer = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->customer->nama;
                    $name = str_replace('[1]',$id_job,$name);
                    $name = str_replace('[2]',$cont,$name);
                    $name = str_replace('[3]',$seal,$name);
                    $name = str_replace('[4]',$shipment,$name);
                    $name = str_replace('[5]',$pembayar,$name);
                    $name = str_replace('[6]',$pengirim,$name);
                    $name = str_replace('[7]',$penerima,$name);
                    $name = str_replace('[8]',$pelayaran,$name);
                    $name = str_replace('[9]',$customer,$name);
                }
                if ($data['debit_coa_id'][$i] && $data['credit_coa_id'][$i]) {
                    Jurnal::create([
                        'coa_id' => $data['debit_coa_id'][$i],
                        'order_id' => $data['order_id'][$i],
                        'nomor' => $data['nomor'],
                        'nama' => $name,
                        'debit' => $data['amount'][$i],
                        'created_at' => $data['created_at'][$i],
                    ]);
                    Jurnal::create([
                        'coa_id' => $data['credit_coa_id'][$i],
                        'order_id' => $data['order_id'][$i],
                        'nomor' => $data['nomor'],
                        'nama' => $name,
                        'credit' => $data['amount'][$i],
                        'created_at' => $data['created_at'][$i],
                    ]);
                }else{
                    if($data['debit_coa_id'][$i]){
                        Jurnal::create([
                            'coa_id' => $data['debit_coa_id'][$i],
                            'order_id' => $data['order_id'][$i],
                            'nomor' => $data['nomor'],
                            'nama' => $name,
                            'debit' => $data['amount'][$i],
                            'created_at' => $data['created_at'][$i],
                        ]);
                    }
                    if($data['credit_coa_id'][$i]){
                        Jurnal::create([
                            'coa_id' => $data['credit_coa_id'][$i],
                            'order_id' => $data['order_id'][$i],
                            'nomor' => $data['nomor'],
                            'nama' => $name,
                            'credit' => $data['amount'][$i],
                            'created_at' => $data['created_at'][$i],
                        ]);
                    }
                }
            }
        }

        return back()->with('success','Data berhasil disimpan');
    }

    public function store_kolektif(Request $request)
    {
        $data = $request->all();
        for ($i=0; $i < count($data['debit_coa_id']); $i++) {
            if ($data['name'][$i] && $data['amount'][$i] && $data['job'][$i] && $data['debit_coa_id'][$i] && $data['credit_coa_id'][$i]) {
                $name = $data['name'][$i];
                $jobs = Order::where('job',$data['job'][$i])->get();
                $amount = (int)$data['amount'][$i] / $jobs->count();
                foreach ($jobs as $order) {
                    $id_job = $order->job.'-'.sprintf('%02d',$order->no_job);
                    $cont = $order->container;
                    $seal = $order->seal;
                    $shipment = $order->tarif->shipmentInfo->nama;
                    $pembayar = $order->tarif->customer->nama ?? '-';
                    $penerima = $order->penerima->nama ?? '-';
                    $pengirim = $order->pengirim->nama ?? '-';
                    $pelayaran = $order->jadwal_kapal->pelayaran->nama ?? '-';
                    $customer = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->customer->nama;
                    $name = str_replace('[1]',$id_job,$name);
                    $name = str_replace('[2]',$cont,$name);
                    $name = str_replace('[3]',$seal,$name);
                    $name = str_replace('[4]',$shipment,$name);
                    $name = str_replace('[5]',$pembayar,$name);
                    $name = str_replace('[6]',$pengirim,$name);
                    $name = str_replace('[7]',$penerima,$name);
                    $name = str_replace('[8]',$pelayaran,$name);
                    $name = str_replace('[9]',$customer,$name);

                    Jurnal::create([
                        'coa_id' => $data['debit_coa_id'][$i],
                        'order_id' => $order->id,
                        'nomor' => $data['nomor'],
                        'nama' => $name,
                        'debit' => $amount,
                        'created_at' => $data['created_at'][$i],
                    ]);
                    Jurnal::create([
                        'coa_id' => $data['credit_coa_id'][$i],
                        'order_id' => $order->id,
                        'nomor' => $data['nomor'],
                        'nama' => $name,
                        'credit' => $amount,
                        'created_at' => $data['created_at'][$i],
                    ]);
                }
            }
        }

        return back()->with('success','Data berhasil disimpan');
    }

    public function store_balik(Request $request){
        $data = Jurnal::where('is_balik',0)->where('order_id',request('order_id'))->whereIn('coa_id',[request('debit_coa_id_tujuan'),request('credit_coa_id_tujuan')])->update([
            'is_balik' => 1
        ]);
        foreach ($request->jurnal as $item) {
            $data = $item;
            $data['created_at'] = $request->created_at;
            $data['nomor'] = $request->nomor;
            Jurnal::create($data);
        }
        return redirect()->route('jurnal.balik.create')->with('success','Data berhasil disimpan');
    }

    public function create()
    {
        return view('admin.jurnal.create');
    }

    public function update(Jurnal $jurnal, Request $request)
    {
        $data = $request->all();
        $jurnal->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Jurnal $jurnal)
    {
        $jurnal->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Jurnal::orderBy('created_at','desc')->get();

        return Datatables::of($data)
            ->addColumn('debit', function ($data) {
                return $data->debit == 0 ? '-' : number_format($data->debit,2,'.',',');
            })
            ->addColumn('credit', function ($data) {
                return $data->credit == 0 ? '-' : number_format($data->credit,2,'.',',');
            })
            ->addColumn('coa_id', function ($data) {
                return $data->coa->nama;
            })
            ->addColumn('code', function ($data) {
                return $data->coa->kode;
            })
            ->addColumn('created_at', function ($data) {
                return date('d/m/y', strtotime($data->created_at));
            })
            ->addColumn('order_id', function ($data) {
                $name = '-';
                if($data->order){
                    $name = $data->order->job.'-'.sprintf('%02d',$data->order->no_job);
                }
                return $name;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
