<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Imports\JurnalImport;
use App\Models\COA;
use App\Models\Jurnal;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
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
        $coa = COA::where('is_active',1)->orderBy('kode')->get();
        return view('admin.jurnal.kolektif', compact('job','coa'));
    }

    public function balik()
    {
        $coa = COA::where('is_active',1)->orderBy('kode')->get();
        $data = [];
        $new = [];
        $coa_debit = null;
        $coa_credit = null;
        $orders = Order::get(['id','job','no_job','seal']);
        if(request('draf')){
            $query = Jurnal::query();
            $query->whereNull('jurnal_balik');
            if (request('order_id')) {
                if(request('tipe')=='job'){
                    $order = Order::find(request('order'));
                    $job = $order->job;
                    $query->whereHas('order', function($q) use($job){
                        $q->where('job',$job);
                    });
                }else if(request('tipe')=='id_job'){
                    $query->where('order_id',request('order_id'));
                }
            }
            if(request('debit_coa_id_tujuan')){
                $query->where('coa_id',request('debit_coa_id_tujuan'));
                $query->where('debit','>',0);
            }
            if(request('credit_coa_id_tujuan')){
                $query->orWhere('coa_id',request('credit_coa_id_tujuan'));
                $query->where('credit','>',0);
                $query->whereNull('jurnal_balik');
                if (request('order_id')) {
                    $query->where('order_id',request('order_id'));
                }
            }
            $data = $query->get();
            $new = array();
            foreach ($data as $idx => $item) {
                if($item['debit']==0){
                    $new[$idx]['debit'] = $item;
                    $new[$idx]['credit'] = Jurnal::where('nomor',$item['nomor'])->where('nama',$item['nama'])->where('debit',$item['credit'])->first();
                }else{
                    $new[$idx]['credit'] = $item;
                    $new[$idx]['debit'] = Jurnal::where('nomor',$item['nomor'])->where('nama',$item['nama'])->where('credit',$item['debit'])->first();
                }
            }
            $coa_debit = COA::find(request('debit_coa_id'));
            $coa_credit = COA::find(request('credit_coa_id'));
        }
        return view('admin.jurnal.balik', compact('coa','new','coa_debit','coa_credit','orders'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $no = Jurnal::where('tipe',$data['tipe'])->max('no') + 1;
        if($no==1 && $data['tipe']=='BBK'){
            $no = 2249;
        }
        if($no==1 && $data['tipe']=='BBM'){
            $no = 751;
        }
        if($no==1 && $data['tipe']=='BKK'){
            $no = 736;
        }
        if($no==1 && $data['tipe']=='BKM'){
            $no = 39;
        }
        if($data['tipe']=='JNL'){
            $no = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
        }
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
                    $kapal = $order->jadwal_kapal->kapal->nama ?? '-';
                    $voyage = $order->jadwal_kapal->voyage ?? '-';
                    $customer = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->customer->nama;
                    $shipment_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tipe;
                    $tujuan_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tujuan;
                    $name = str_replace('[1]',$id_job,$name);
                    $name = str_replace('[2]',$cont,$name);
                    $name = str_replace('[3]',$seal,$name);
                    $name = str_replace('[4]',$kapal,$name);
                    $name = str_replace('[5]',$voyage,$name);
                    $name = str_replace('[6]',$shipment,$name);
                    $name = str_replace('[7]',$pembayar,$name);
                    $name = str_replace('[8]',$customer,$name);
                    $name = str_replace('[9]',$shipment_trucking,$name);
                    $name = str_replace('[10]',$tujuan_trucking,$name);
                }
                if($data['tipe']=='JNL'){
                    $nomor = sprintf('%02d',date('m',strtotime($data['created_at'][$i]))).'-'.sprintf('%03d',$no).'/'.date('y',strtotime($data['created_at'][$i]));
                }else{
                    $nomor = sprintf('%03d',$no).'/'.$data['tipe'].'-RAS/'.date('y',strtotime($data['created_at'][$i]));
                }
                if ($data['debit_coa_id'][$i] && $data['credit_coa_id'][$i]) {
                    Jurnal::create([
                        'coa_id' => $data['debit_coa_id'][$i],
                        'order_id' => $data['order_id'][$i],
                        'nomor' => $nomor,
                        'nama' => $name,
                        'debit' => $data['amount'][$i],
                        'created_at' => $data['created_at'][$i],
                        'no' => $no
                    ]);
                    Jurnal::create([
                        'coa_id' => $data['credit_coa_id'][$i],
                        'order_id' => $data['order_id'][$i],
                        'nomor' => $nomor,
                        'nama' => $name,
                        'credit' => $data['amount'][$i],
                        'created_at' => $data['created_at'][$i],
                        'no' => $no
                    ]);
                }else{
                    if($data['debit_coa_id'][$i]){
                        Jurnal::create([
                            'coa_id' => $data['debit_coa_id'][$i],
                            'order_id' => $data['order_id'][$i],
                            'nomor' => $nomor,
                            'nama' => $name,
                            'debit' => $data['amount'][$i],
                            'created_at' => $data['created_at'][$i],
                            'no' => $no
                        ]);
                    }
                    if($data['credit_coa_id'][$i]){
                        Jurnal::create([
                            'coa_id' => $data['credit_coa_id'][$i],
                            'order_id' => $data['order_id'][$i],
                            'nomor' => $nomor,
                            'nama' => $name,
                            'credit' => $data['amount'][$i],
                            'created_at' => $data['created_at'][$i],
                            'no' => $no
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
        $no = Jurnal::where('tipe',$data['tipe'])->max('no') + 1;
        if($no==1 && $data['tipe']=='BBK'){
            $no = 2249;
        }
        if($no==1 && $data['tipe']=='BBM'){
            $no = 751;
        }
        if($no==1 && $data['tipe']=='BKK'){
            $no = 736;
        }
        if($no==1 && $data['tipe']=='BKM'){
            $no = 39;
        }
        if($data['tipe']=='JNL'){
            $no = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
        }
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
                    $kapal = $order->jadwal_kapal->kapal->nama ?? '-';
                    $voyage = $order->jadwal_kapal->voyage ?? '-';
                    $customer = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->customer->nama;
                    $shipment_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tipe;
                    $tujuan_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tujuan;
                    $name = str_replace('[1]',$id_job,$name);
                    $name = str_replace('[2]',$cont,$name);
                    $name = str_replace('[3]',$seal,$name);
                    $name = str_replace('[4]',$kapal,$name);
                    $name = str_replace('[5]',$voyage,$name);
                    $name = str_replace('[6]',$shipment,$name);
                    $name = str_replace('[7]',$pembayar,$name);
                    $name = str_replace('[8]',$customer,$name);
                    $name = str_replace('[9]',$shipment_trucking,$name);
                    $name = str_replace('[10]',$tujuan_trucking,$name);

                    if($data['tipe']=='JNL'){
                        $nomor = sprintf('%02d',date('m',strtotime($data['created_at'][$i]))).'-'.sprintf('%03d',$no).'/'.date('y',strtotime($data['created_at'][$i]));
                    }else{
                        $nomor = sprintf('%03d',$no).'/'.$data['tipe'].'-RAS/'.date('y',strtotime($data['created_at'][$i]));
                    }

                    Jurnal::create([
                        'coa_id' => $data['debit_coa_id'][$i],
                        'order_id' => $order->id,
                        'nomor' => $nomor,
                        'nama' => $name,
                        'debit' => $amount,
                        'created_at' => $data['created_at'][$i],
                        'no' => $no
                    ]);
                    Jurnal::create([
                        'coa_id' => $data['credit_coa_id'][$i],
                        'order_id' => $order->id,
                        'nomor' => $nomor,
                        'nama' => $name,
                        'credit' => $amount,
                        'created_at' => $data['created_at'][$i],
                        'no' => $no
                    ]);
                }
            }
        }

        return back()->with('success','Data berhasil disimpan');
    }

    public function store_balik(Request $request){
        $no = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
        $nomor = sprintf('%02d',date('m',strtotime($request->created_at))).'-'.sprintf('%03d',$no).'/'.date('y',strtotime($request->created_at));
        foreach ($request->jurnal as $item) {
            $data = $item;
            $data['created_at'] = $request->created_at;
            $data['nomor'] = $nomor;
            $data['jurnal_balik'] = null;
            $data['is_balik'] = 1;
            $data['no'] = $no;
            $j = Jurnal::create($data);
            Jurnal::find($item['jurnal_balik'])->update([
                'jurnal_balik' => $j->id
            ]);
        }
        return redirect()->route('jurnal.balik.create')->with('success','Data berhasil disimpan');
    }

    public function create()
    {
        return view('admin.jurnal.create');
    }

    public function edit(Jurnal $jurnal)
    {
        $coa = COA::where('is_active',1)->orderBy('kode')->get();
        $orders = Order::select('id','no_job','job','seal')->orderBy('job')->orderBy('no_job')->get();
        $data = Jurnal::where('nomor',$jurnal->nomor)->get();
        return view('admin.jurnal.edit', compact('data','orders','coa'));
    }

    public function update(Jurnal $jurnal, Request $request)
    {
        foreach ($request->jurnal as $idx => $item) {
            $data = $item;
            Jurnal::find($idx)->update($data);
        }

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Jurnal $jurnal)
    {
        $jurnal->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function import(Request $request)
    {
        Excel::import(new JurnalImport, $request->file);

        return back()->with('success', 'All good!');
    }

    public function neraca()
    {
        return view('admin.jurnal.neraca');
    }

    public function laba_rugi()
    {
        return view('admin.jurnal.laba_rugi');
    }

    public function buku_besar()
    {
        return view('admin.jurnal.buku_besar');
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
