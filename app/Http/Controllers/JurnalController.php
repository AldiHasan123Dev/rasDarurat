<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Imports\JurnalImport;
use App\Models\COA;
use App\Models\Jurnal;
use App\Models\JurnalTampungan;
use App\Models\Order;
use App\Models\OrderTrucking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;

class JurnalController extends Controller
{
    public function index()
    {
        $unbalance = Jurnal::select([DB::raw("SUM(debit) as debit"), DB::raw("SUM(credit) as credit"),'nomor'])->groupBy('nomor')->get()->reject(function ($data) {
            return $data->debit == $data->credit;
        });
        return view('admin.jurnal.index', compact('unbalance'));
    }

    public function order()
    {
        return view('admin.jurnal.order');
    }

    public function order_trucking()
    {
        return view('admin.jurnal.order_trucking');
    }

    public function kolektif()
    {
        $job = Order::pluck('job')->toArray();
        $job = array_unique($job);
        $coa = COA::where('is_active',1)->orderBy('kode')->get();
        return view('admin.jurnal.kolektif', compact('job','coa'));
    }

    public function manual()
    {
        return view('admin.jurnal.manual');
    }

    public function merge()
    {
        $data = Jurnal::pluck('nomor')->toArray();
        $data = array_unique($data);
        return view('admin.jurnal.merge', compact('data'));
    }

    public function tampungan()
    {
        $data = JurnalTampungan::get();
        if (request()->ajax()) {
            $view = view('data.jurnal',compact('data'))->render();
            return response()->json(['html'=>$view]);
        }
        $no_1 = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_2 = Jurnal::where('tipe','BBK')->max('no') + 1;
        $no_3 = Jurnal::where('tipe','BBM')->max('no') + 1;
        $no_4 = Jurnal::where('tipe','BKK')->max('no') + 1;
        $no_5 = Jurnal::where('tipe','BKM')->max('no') + 1;
        $jno_1 = sprintf('%02d',date('m')).'-'.sprintf('%03d',$no_1).'/'.date('y');
        $jno_2 = sprintf('%03d',$no_2).'/BBK-RAS/'.date('y');
        $jno_3 = sprintf('%03d',$no_3).'/BBM-RAS/'.date('y');
        $jno_4 = sprintf('%03d',$no_4).'/BKK-RAS/'.date('y');
        $jno_5 = sprintf('%03d',$no_5).'/BKM-RAS/'.date('y');
        $data = [];
        return view('admin.jurnal.tampungan', compact('no_1','no_2','no_3','no_4','no_5','jno_1','jno_2','jno_3','jno_4','jno_5','data'));
    }

    public function tampungan_store(Request $request)
    {
        $debit = JurnalTampungan::sum('debit');
        $credit = JurnalTampungan::sum('credit');
        $status = true;
        $message = 'Jurnal tampungan berhasil diterbitkan!';
        if($debit!=$credit){
            $status = false;
            $message = 'Debit dan Credit tidak balance!';
        }elseif(!$request->nomor){
            $status = false;
            $message = 'Harap pilih tipe jurnal!';
        }else{
            if($request->tipe=='JNL'){
                $no = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m',strtotime($request->created_at)))->whereYear('created_at',date('Y',strtotime($request->created_at)))->max('no') + 1;
                $nomor = sprintf('%02d',date('m',strtotime($request->created_at))).'-'.sprintf('%03d',$no).'/'.date('y',strtotime($request->created_at));
            }else{
                $no = Jurnal::where('tipe',$request->tipe)->max('no') + 1;
                $nomor = sprintf('%03d',$no).'/'.$request->tipe.'-RAS/'.date('y',strtotime($request->created_at));
            }
            $data = JurnalTampungan::all()->toArray();
            foreach ($data as $item) {
                $jurnal = $item;
                $jurnal['nomor'] = $nomor;
                $jurnal['tipe'] = $request->tipe;
                $jurnal['no'] = $no;
                $jurnal['created_at'] = $request->created_at;
                Jurnal::create($jurnal);
            }
            JurnalTampungan::truncate();
        }
        return response([
            'status' => $status,
            'message' => $message,
        ]);
    }

    public function tampungan_destroy()
    {
        JurnalTampungan::find(request('id'))->delete();
        return response('success');
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

    public function store_manual(Request $request)
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
            $no = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m',strtotime($data['created_at'])))->whereYear('created_at',date('Y',strtotime($data['created_at'])))->max('no') + 1;
        }

        $jurnal_model = new Jurnal();
        if($data['simpan']=='tampungan'){
            $jurnal_model = new JurnalTampungan();
        }

        for ($i=0; $i < count($data['debit_coa_id']); $i++) {
            if ($data['name'][$i] && $data['amount'][$i]) {
                $name = $data['name'][$i];
                if($data['tipe']=='JNL'){
                    $nomor = sprintf('%02d',date('m',strtotime($data['created_at']))).'-'.sprintf('%03d',$no).'/'.date('y',strtotime($data['created_at']));
                }else{
                    $nomor = sprintf('%03d',$no).'/'.$data['tipe'].'-RAS/'.date('y',strtotime($data['created_at']));
                }
                if ($data['debit_coa_id'][$i] && $data['credit_coa_id'][$i]) {
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'invoice' => $data['invoice'][$i],
                        'nopol' => $data['nopol'][$i],
                        'coa_id' => $data['debit_coa_id'][$i],
                        'nomor' => $nomor,
                        'nama' => $name,
                        'debit' => $data['amount'][$i],
                        'created_at' => $data['created_at'],
                        'no' => $no
                    ]);
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'invoice' => $data['invoice'][$i],
                        'nopol' => $data['nopol'][$i],
                        'coa_id' => $data['credit_coa_id'][$i],
                        'nomor' => $nomor,
                        'nama' => $name,
                        'credit' => $data['amount'][$i],
                        'created_at' => $data['created_at'],
                        'no' => $no
                    ]);
                }else{
                    if($data['debit_coa_id'][$i]){
                        $jurnal_model->create([
                            'tipe' => $data['tipe'],
                            'invoice' => $data['invoice'][$i],
                            'nopol' => $data['nopol'][$i],
                            'coa_id' => $data['debit_coa_id'][$i],
                            'nomor' => $nomor,
                            'nama' => $name,
                            'debit' => $data['amount'][$i],
                            'created_at' => $data['created_at'],
                            'no' => $no
                        ]);
                    }
                    if($data['credit_coa_id'][$i]){
                        $jurnal_model->create([
                            'tipe' => $data['tipe'],
                            'invoice' => $data['invoice'][$i],
                            'nopol' => $data['nopol'][$i],
                            'coa_id' => $data['credit_coa_id'][$i],
                            'nomor' => $nomor,
                            'nama' => $name,
                            'credit' => $data['amount'][$i],
                            'created_at' => $data['created_at'],
                            'no' => $no
                        ]);
                    }
                }
            }
        }

        return back()->with('success','Data berhasil disimpan');
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
            $no = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m',strtotime($data['created_at'])))->whereYear('created_at',date('Y',strtotime($data['created_at'])))->max('no') + 1;
        }

        $jurnal_model = new Jurnal();
        if($data['simpan']=='tampungan'){
            $jurnal_model = new JurnalTampungan();
        }

        for ($i=0; $i < count($data['debit_coa_id']); $i++) {
            if ($data['name'][$i] && $data['amount'][$i]) {
                $name = $data['name'][$i];
                $invoice = null;
                $nopol = null;
                $container = null;
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
                    $tujuan_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tarif->tujuan->tujuanInfo->nama;
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
                    $invoice = $order->invoice;
                    $nopol = $order->nopol;
                    $container = $order->container;
                }
                if($data['tipe']=='JNL'){
                    $nomor = sprintf('%02d',date('m',strtotime($data['created_at']))).'-'.sprintf('%03d',$no).'/'.date('y',strtotime($data['created_at']));
                }else{
                    $nomor = sprintf('%03d',$no).'/'.$data['tipe'].'-RAS/'.date('y',strtotime($data['created_at']));
                }
                if ($data['debit_coa_id'][$i] && $data['credit_coa_id'][$i]) {
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'invoice' => $invoice,
                        'nopol' => $nopol,
                        'container' => $container,
                        'coa_id' => $data['debit_coa_id'][$i],
                        'order_id' => $data['order_id'][$i],
                        'nomor' => $nomor,
                        'nama' => $name,
                        'debit' => $data['amount'][$i],
                        'created_at' => $data['created_at'],
                        'no' => $no
                    ]);
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'invoice' => $invoice,
                        'nopol' => $nopol,
                        'container' => $container,
                        'coa_id' => $data['credit_coa_id'][$i],
                        'order_id' => $data['order_id'][$i],
                        'nomor' => $nomor,
                        'nama' => $name,
                        'credit' => $data['amount'][$i],
                        'created_at' => $data['created_at'],
                        'no' => $no
                    ]);
                }else{
                    if($data['debit_coa_id'][$i]){
                        $jurnal_model->create([
                            'tipe' => $data['tipe'],
                            'invoice' => $invoice,
                            'nopol' => $nopol,
                            'container' => $container,
                            'coa_id' => $data['debit_coa_id'][$i],
                            'order_id' => $data['order_id'][$i],
                            'nomor' => $nomor,
                            'nama' => $name,
                            'debit' => $data['amount'][$i],
                            'created_at' => $data['created_at'],
                            'no' => $no
                        ]);
                    }
                    if($data['credit_coa_id'][$i]){
                        $jurnal_model->create([
                            'tipe' => $data['tipe'],
                            'invoice' => $invoice,
                            'nopol' => $nopol,
                            'container' => $container,
                            'coa_id' => $data['credit_coa_id'][$i],
                            'order_id' => $data['order_id'][$i],
                            'nomor' => $nomor,
                            'nama' => $name,
                            'credit' => $data['amount'][$i],
                            'created_at' => $data['created_at'],
                            'no' => $no
                        ]);
                    }
                }
            }
        }

        return back()->with('success','Data berhasil disimpan');
    }

    public function store_merge(Request $request){
        $tujuan = Jurnal::where('nomor',$request->tujuan)->first();
        Jurnal::where('nomor',$request->awal)->update([
            'nomor' => $tujuan->nomor,
            'no' => $tujuan->no,
            'tipe' => $tujuan->tipe,
        ]);

        return back()->with('success','Merge No. Jurnal berhasil');
    }

    public function store_trucking(Request $request)
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
            $no = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m',strtotime($data['created_at'])))->whereYear('created_at',date('Y',strtotime($data['created_at'])))->max('no') + 1;
        }

        $jurnal_model = new Jurnal();
        if($data['simpan']=='tampungan'){
            $jurnal_model = new JurnalTampungan();
        }

        for ($i=0; $i < count($data['debit_coa_id']); $i++) {
            if ($data['name'][$i] && $data['amount'][$i]) {
                $name = $data['name'][$i];
                $order_id = null;
                $invoice = null;
                $nopol = null;
                $container = null;
                if($data['order_id'][$i]){
                    $order = OrderTrucking::find($data['order_id'][$i]);
                    $id_job = $order->order ? $order->order->job.'-'.sprintf('%02d',$order->order->no_job) : '-';
                    $cont = $order->container;
                    $seal = $order->seal;
                    $order_id = $order->order ? $order->order->id : null;
                    $shipment = $order->order ? $order->order->tarif->shipmentInfo->nama : '-';
                    $pembayar = $order->order ? $order->order->tarif->customer->nama : '-';
                    $kapal = $order->order ? $order->order->jadwal_kapal->kapal->nama : '-';
                    $voyage = $order->order ? $order->order->jadwal_kapal->voyage : '-';
                    $customer = $order->customer->nama;
                    $shipment_trucking = $order->tipe;
                    $tujuan_trucking = $order->tarif->tujuan->tujuanInfo->nama;
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
                    $invoice = $order->invoice;
                    $nopol = $order->kendaraan->nopol;
                    $container = $order->container;
                }
                if($data['tipe']=='JNL'){
                    $nomor = sprintf('%02d',date('m',strtotime($data['created_at']))).'-'.sprintf('%03d',$no).'/'.date('y',strtotime($data['created_at']));
                }else{
                    $nomor = sprintf('%03d',$no).'/'.$data['tipe'].'-RAS/'.date('y',strtotime($data['created_at']));
                }
                if ($data['debit_coa_id'][$i] && $data['credit_coa_id'][$i]) {
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'coa_id' => $data['debit_coa_id'][$i],
                        'invoice' => $invoice,
                        'nopol' => $nopol,
                        'container' => $container,
                        'order_id' => $order_id,
                        'order_trucking_id' => $data['order_id'][$i],
                        'nomor' => $nomor,
                        'nama' => $name,
                        'debit' => $data['amount'][$i],
                        'created_at' => $data['created_at'],
                        'no' => $no
                    ]);
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'coa_id' => $data['credit_coa_id'][$i],
                        'invoice' => $invoice,
                        'nopol' => $nopol,
                        'container' => $container,
                        'order_id' => $order_id,
                        'order_trucking_id' => $data['order_id'][$i],
                        'nomor' => $nomor,
                        'nama' => $name,
                        'credit' => $data['amount'][$i],
                        'created_at' => $data['created_at'],
                        'no' => $no
                    ]);
                }else{
                    if($data['debit_coa_id'][$i]){
                        $jurnal_model->create([
                            'tipe' => $data['tipe'],
                            'coa_id' => $data['debit_coa_id'][$i],
                            'invoice' => $invoice,
                            'nopol' => $nopol,
                            'container' => $container,
                            'order_id' => $order_id,
                            'order_trucking_id' => $data['order_id'][$i],
                            'nomor' => $nomor,
                            'nama' => $name,
                            'debit' => $data['amount'][$i],
                            'created_at' => $data['created_at'],
                            'no' => $no
                        ]);
                    }
                    if($data['credit_coa_id'][$i]){
                        $jurnal_model->create([
                            'tipe' => $data['tipe'],
                            'coa_id' => $data['credit_coa_id'][$i],
                            'invoice' => $invoice,
                            'nopol' => $nopol,
                            'container' => $container,
                            'order_id' => $order_id,
                            'order_trucking_id' => $data['order_id'][$i],
                            'nomor' => $nomor,
                            'nama' => $name,
                            'credit' => $data['amount'][$i],
                            'created_at' => $data['created_at'],
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
        // dd($data);
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
            $no = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m',strtotime($data['created_at'])))->whereYear('created_at',date('Y',strtotime($data['created_at'])))->max('no') + 1;
        }

        $jurnal_model = new Jurnal();
        if($data['simpan']=='tampungan'){
            $jurnal_model = new JurnalTampungan();
        }

        for ($i=0; $i < count($data['debit_coa_id']); $i++) {
            if ($data['name'][$i] && $data['amount'][$i] && $data['job'][$i] && $data['debit_coa_id'][$i] && $data['credit_coa_id'][$i]) {
                $name = $data['name'][$i];
                $jobs = Order::where('job',$data['job'][$i])->get();
                $amount = (int)$data['amount'][$i] / $jobs->count();
                $invoice = null;
                $nopol = null;
                $container = null;
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
                    $tujuan_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tarif->tujuan->tujuanInfo->nama;
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
                    $invoice = $order->invoice;
                    $nopol = $order->nopol;
                    $container = $order->container;

                    if($data['tipe']=='JNL'){
                        $nomor = sprintf('%02d',date('m',strtotime($data['created_at']))).'-'.sprintf('%03d',$no).'/'.date('y',strtotime($data['created_at']));
                    }else{
                        $nomor = sprintf('%03d',$no).'/'.$data['tipe'].'-RAS/'.date('y',strtotime($data['created_at']));
                    }

                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'coa_id' => $data['debit_coa_id'][$i],
                        'order_id' => $order->id,
                        'nomor' => $nomor,
                        'nama' => $name,
                        'debit' => $amount,
                        'invoice' => $invoice,
                        'nopol' => $nopol,
                        'container' => $container,
                        'created_at' => $data['created_at'],
                        'no' => $no
                    ]);
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'coa_id' => $data['credit_coa_id'][$i],
                        'order_id' => $order->id,
                        'nomor' => $nomor,
                        'nama' => $name,
                        'credit' => $amount,
                        'invoice' => $invoice,
                        'nopol' => $nopol,
                        'container' => $container,
                        'created_at' => $data['created_at'],
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

    public function trucking()
    {
        return view('admin.jurnal.trucking');
    }

    public function edit()
    {
        $jurnal = request('jurnal');
        $coa = COA::where('is_active',1)->orderBy('kode')->get();
        $data = Jurnal::where('nomor',$jurnal)->get();
        $orders = Order::select('id','no_job','job','seal')->orderBy('job')->orderBy('no_job')->get();
        $tipe = 'xpdc';
        if($data[0]->order_trucking_id){
            $tipe = 'trucking';
            $orders = OrderTrucking::select('container','seal','id')->orderBy('container')->get();
        }
        $jur = $data[0];
        // return view('admin.jurnal.edit', compact('data','orders','coa','tipe'));
        return view('admin.jurnal.new_edit', compact('data','orders','coa','tipe','jur'));
    }

    public function editOne(Jurnal $jurnal)
    {
        $coa = COA::where('is_active',1)->orderBy('kode')->get();
        $orders = Order::select('id','no_job','job','seal')->orderBy('job')->orderBy('no_job')->get();
        $tipe = 'xpdc';
        if($jurnal->order_trucking_id){
            $tipe = 'trucking';
            $orders = OrderTrucking::select('container','seal','id')->orderBy('container')->get();
        }
        // return view('admin.jurnal.edit', compact('data','orders','coa','tipe'));
        return view('admin.jurnal.form_edit', compact('jurnal','orders','coa','tipe'));
    }

    public function updateOne(Request $request, Jurnal $jurnal)
    {
        $jurnal->update($request->all());
        return back()->with('success','Data berhasil disimpan!');
    }

    public function update(Jurnal $jurnal, Request $request)
    {
        $jurnal_data = Jurnal::where('nomor',$jurnal->nomor)->pluck('id')->toArray();
        $no = Jurnal::where('nomor',$jurnal->nomor)->first()->no;
        $tipe = Jurnal::where('nomor',$jurnal->nomor)->first()->tipe;
        if($tipe=='JNL'){
            $nomor = sprintf('%02d',date('m',strtotime($request->created_at))).'-'.sprintf('%03d',$no).'/'.date('y',strtotime($request->created_at));
        }else{
            $nomor = sprintf('%03d',$no).'/'.$tipe.'-RAS/'.date('y',strtotime($request->created_at));
        }
        foreach ($request->jurnal as $idx => $item) {
            $data = $item;
            $data['nama'] = empty($data['nama']) ? '-' : ($data['nama'] ?? '-');
            $data['nomor'] = $nomor;
            $data['created_at'] = $request->created_at;
            if(!empty($data['order_id'])){
                $name = $data['nama'];
                $order = Order::find($data['order_id']);
                $id_job = $order->job.'-'.sprintf('%02d',$order->no_job);
                $cont = $order->container;
                $seal = $order->seal;
                $shipment = $order->tarif->shipmentInfo->nama;
                $pembayar = $order->tarif->customer->nama ?? '-';
                $kapal = $order->jadwal_kapal->kapal->nama ?? '-';
                $voyage = $order->jadwal_kapal->voyage ?? '-';
                $customer = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->customer->nama;
                $shipment_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tipe;
                $tujuan_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tarif->tujuan->tujuanInfo->nama;
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
                $data['invoice'] = $order->invoice;
                $data['nopol'] = $order->nopol;
                $data['container'] = $order->container;
                $data['nama'] = $name;
            }
            if(!empty($data['order_trucking_id'])){
                $name = $data['nama'];
                $order = OrderTrucking::find($data['order_trucking_id']);
                $id_job = $order->order ? $order->order->job.'-'.sprintf('%02d',$order->order->no_job) : '-';
                $cont = $order->container;
                $seal = $order->seal;
                $order_id = $order->order ? $order->order->id : null;
                $shipment = $order->order ? $order->order->tarif->shipmentInfo->nama : '-';
                $pembayar = $order->order ? $order->order->tarif->customer->nama : '-';
                $kapal = $order->order ? $order->order->jadwal_kapal->kapal->nama : '-';
                $voyage = $order->order ? $order->order->jadwal_kapal->voyage : '-';
                $customer = $order->customer->nama;
                $shipment_trucking = $order->tipe;
                $tujuan_trucking = $order->tarif->tujuan->tujuanInfo->nama;
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
                $data['invoice'] = $order->invoice;
                $data['nopol'] = $order->kendaraan->nopol;
                $data['container'] = $order->container;
                $data['nama'] = $name;
            }
            Jurnal::find($idx)->update($data);
        }

        $ids = array_diff($jurnal_data,array_map('intval',$request->id));
        Jurnal::whereIn('id',$ids)->delete();
        $jurnal = Jurnal::where('nomor',$jurnal->nomor)->first();

        if(!empty($request->jurnal_create)){
            foreach($request->jurnal_create as $idx => $item){
                $data = $item;
                $data['nomor'] = $nomor;
                $data['tipe'] = $jurnal->tipe;
                $data['no'] = $jurnal->no;
                $data['created_at'] = $request->created_at;
                $data['nama'] = empty($data['nama']) ? '-' : ($data['nama'] ?? '-');
                if(!empty($data['order_id'])){
                    $name = $data['nama'];
                    $order = Order::find($data['order_id']);
                    $id_job = $order->job.'-'.sprintf('%02d',$order->no_job);
                    $cont = $order->container;
                    $seal = $order->seal;
                    $shipment = $order->tarif->shipmentInfo->nama;
                    $pembayar = $order->tarif->customer->nama ?? '-';
                    $kapal = $order->jadwal_kapal->kapal->nama ?? '-';
                    $voyage = $order->jadwal_kapal->voyage ?? '-';
                    $customer = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->customer->nama;
                    $shipment_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tipe;
                    $tujuan_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tarif->tujuan->tujuanInfo->nama;
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
                    $data['nama'] = $name;
                    $data['invoice'] = $order->invoice;
                    $data['nopol'] = $order->nopol;
                    $data['container'] = $order->container;
                }
                if(!empty($data['order_trucking_id'])){
                    $name = $data['nama'];
                    $order = OrderTrucking::find($data['order_trucking_id']);
                    $id_job = $order->order ? $order->order->job.'-'.sprintf('%02d',$order->order->no_job) : '-';
                    $cont = $order->container;
                    $seal = $order->seal;
                    $order_id = $order->order ? $order->order->id : null;
                    $shipment = $order->order ? $order->order->tarif->shipmentInfo->nama : '-';
                    $pembayar = $order->order ? $order->order->tarif->customer->nama : '-';
                    $kapal = $order->order ? $order->order->jadwal_kapal->kapal->nama : '-';
                    $voyage = $order->order ? $order->order->jadwal_kapal->voyage : '-';
                    $customer = $order->customer->nama;
                    $shipment_trucking = $order->tipe;
                    $tujuan_trucking = $order->tarif->tujuan->tujuanInfo->nama;
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
                    $data['invoice'] = $order->invoice;
                    $data['nopol'] = $order->kendaraan->nopol;
                    $data['container'] = $order->container;
                    $data['nama'] = $name;
                }

                Jurnal::create($data);
            }
        }

        return redirect()->route('jurnal.edit',['jurnal'=>$nomor])->with('success','Data berhasil diupdate');
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
