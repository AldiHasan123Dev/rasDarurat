<?php

namespace App\Http\Controllers;

use App\Exports\JurnalBatchExport;
use App\Exports\JurnalCoaExport;
use App\Exports\JurnalMonth;
use App\Http\Resources\OrderResource;
use App\Services\SyncService;
use App\Imports\JurnalImport;
use App\Models\COA;
use App\Models\HutangPelayaran;
use App\Models\Jurnal;
use App\Models\JurnalTampungan;
use App\Models\Order;
use App\Models\OrderTrucking;
use App\Models\Pelayaran;
use App\Models\TransaksiSopir;
use App\Models\TransaksiTrucking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
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
        $month = request('month') ?? date('m');
        $year = request('year') ?? date('Y');
        return view('admin.jurnal.index', compact('unbalance','month','year'));
    }

    public function totalan_sopir()
    {
        $data = TransaksiSopir::whereNull('jurnal')->where('jurnal_status',0)->orderBy('tgl_invoice')->get();
        $data1 = TransaksiSopir::whereNotNull('jurnal')->where('jurnal_status',1)->orderBy('jurnal_submit','desc')->get();
        return view('admin.jurnal.totalan_sopir', compact('data','data1'));
    }

    public function slip_totalan_sopir(Request $request)
    {
        $ids = explode(',',$request->ids);
        $data = TransaksiSopir::whereIn('id',$ids)->pluck('order_id');
        $id = '';
        foreach($data as $order_id){
            $id .= str_replace(['[',']'],'',$order_id).',';
        }
        $id = explode(',',$id);
        $orders = OrderTrucking::with('sopir')->whereIn('id',$id)->get();
        return view('admin.jurnal.slip_totalan_sopir', compact('orders'));
    }

    public function submit_slip_totalan_sopir(Request $request)
    {
        if(!$request->nomor){
            return back()->with('danger','Harap pilih nomor jurnal terlebih dahulu!');
        }
        if($request->jurnal_simpanan_sopir){
            foreach($request->jurnal_simpanan_sopir as $js){
                $debit = $js;
                $credit = $js;
                $debit['created_at'] = $request->created_at;
                $credit['coa_id'] = ($credit['tipe']=='BBK' ? 45 : 16);
                $credit['credit'] = $credit['debit'];
                $credit['debit'] = 0;
                $credit['created_at'] = $request->created_at;
                Jurnal::create($debit);
                Jurnal::create($credit);
                TransaksiSopir::where('order_id','LIKE','%'.$debit['order_trucking_id'].'%')->update([
                    'jurnal' => $debit['nomor'],
                    'jurnal_status' => 1,
                    'jurnal_tgl' => $request->created_at,
                    'jurnal_submit' => date('Y-m-d H:i:s')
                ]);
            }
        }
        if($request->jurnal_simpanan_kuli){
            foreach($request->jurnal_simpanan_kuli as $js){
                $debit = $js;
                $credit = $js;
                $debit['created_at'] = $request->created_at;
                $credit['coa_id'] = ($credit['tipe']=='BBK' ? 45 : 16);
                $credit['credit'] = $credit['debit'];
                $credit['debit'] = 0;
                $credit['created_at'] = $request->created_at;
                Jurnal::create($debit);
                Jurnal::create($credit);
                TransaksiSopir::where('order_id','LIKE','%'.$debit['order_trucking_id'].'%')->update([
                    'jurnal' => $debit['nomor'],
                    'jurnal_status' => 1,
                    'jurnal_tgl' => $request->created_at,
                    'jurnal_submit' => date('Y-m-d H:i:s')
                ]);
            }
        }
        if($request->jurnal_tbtl){
            foreach($request->jurnal_tbtl as $js){
                $debit = $js;
                $credit = $js;
                $debit['created_at'] = $request->created_at;
                $credit['coa_id'] = ($credit['tipe']=='BBK' ? 45 : 16);
                $credit['credit'] = $credit['debit'];
                $credit['debit'] = 0;
                $credit['created_at'] = $request->created_at;
                Jurnal::create($debit);
                Jurnal::create($credit);
                TransaksiSopir::where('order_id','LIKE','%'.$debit['order_trucking_id'].'%')->update([
                    'jurnal' => $debit['nomor'],
                    'jurnal_status' => 1,
                    'jurnal_tgl' => $request->created_at,
                    'jurnal_submit' => date('Y-m-d H:i:s')
                ]);
            }
        }
        if($request->jurnal_stappel){
            foreach($request->jurnal_stappel as $js){
                $debit = $js;
                $credit = $js;
                $debit['created_at'] = $request->created_at;
                $credit['coa_id'] = ($credit['tipe']=='BBK' ? 45 : 16);
                $credit['credit'] = $credit['debit'];
                $credit['debit'] = 0;
                $credit['created_at'] = $request->created_at;
                Jurnal::create($debit);
                Jurnal::create($credit);
                TransaksiSopir::where('order_id','LIKE','%'.$debit['order_trucking_id'].'%')->update([
                    'jurnal' => $debit['nomor'],
                    'jurnal_status' => 1,
                    'jurnal_tgl' => $request->created_at,
                    'jurnal_submit' => date('Y-m-d H:i:s')
                ]);
            }
        }

        return redirect()->route('jurnal.totalan_sopir')->with('success','Jurnal berhasil dibuat!');
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
        $no_2 = Jurnal::where('tipe','BBK')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_3 = Jurnal::where('tipe','BBM')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_4 = Jurnal::where('tipe','BKK')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_5 = Jurnal::where('tipe','BKM')->whereYear('created_at',date('Y'))->max('no') + 1;
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
                $no = Jurnal::where('tipe',$request->tipe)->whereYear('created_at',date('Y'))->max('no') + 1;
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
        $no = Jurnal::where('tipe',$data['tipe'])->whereYear('created_at',date('Y', strtotime($data['created_at'])))->max('no') + 1;
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
        $no = Jurnal::where('tipe',$data['tipe'])->whereYear('created_at',date('Y',strtotime($data['created_at'])))->max('no') + 1;
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
                $no_bg = $data['no_bg'][$i] ?? null;
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
                        'no_bg' => $no_bg,
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
                        'no_bg' => $no_bg,
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
                            'no_bg' => $no_bg,
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
                            'no_bg' => $no_bg,
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
            'created_at' => $tujuan->created_at
        ]);

        return back()->with('success','Merge No. Jurnal berhasil');
    }

    public function store_trucking(Request $request)
    {
        $data = $request->all();
        $no = Jurnal::where('tipe',$data['tipe'])->whereYear('created_at',date('Y', strtotime($data['created_at'])))->max('no') + 1;
        if($data['tipe']=='JNL'){
            $no = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m',strtotime($data['created_at'])))->whereYear('created_at',date('Y',strtotime($data['created_at'])))->max('no') + 1;
        }

        $jurnal_model = new Jurnal();
        if($data['simpan']=='tampungan'){
            $jurnal_model = new JurnalTampungan();
        }

        $arr_order = array();
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
                    array_push($arr_order,$order->id);
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

        if($data['simpan']=='tampungan'){

        }else{
            $service = new SyncService();
            foreach($arr_order as $id){
                $sangu_sopir = Jurnal::where('order_trucking_id',$id)->where('nama','LIKE','SANGU SOPIR%')->where('debit','>',0)->sum('debit') ?? 0;
                $sangu_kuli = Jurnal::where('order_trucking_id',$id)->where('nama','LIKE','SANGU KULI%')->where('debit','>',0)->sum('debit') ?? 0;
                $uang_makan = Jurnal::where('order_trucking_id',$id)->where('nama','LIKE','UANG MAKAN%')->where('debit','>',0)->sum('debit') ?? 0;
                $solar = Jurnal::where('order_trucking_id',$id)->where('nama','LIKE','BIAYA TAMBAH SOLAR%')->where('debit','>',0)->sum('debit') ?? 0;
                $op = Jurnal::where('order_trucking_id',$id)->where('nama','LIKE','BIAYA OPERASIONAL TRUCKING%')->where('debit','>',0)->sum('debit') ?? 0;
                $cleaning = Jurnal::where('order_trucking_id',$id)->where('nama','LIKE','BIAYA CLEANING%')->where('debit','>',0)->sum('debit') ?? 0;
                $tally = Jurnal::where('order_trucking_id',$id)->where('nama','LIKE','BIAYA CHECKER%')->where('debit','>',0)->sum('debit') ?? 0;

                if($sangu_sopir>0){
                    OrderTrucking::find($id)->update([
                        'sangu' => $sangu_sopir,
                    ]);
                }
                if($sangu_kuli>0){
                    OrderTrucking::find($id)->update([
                        'kuli' => $sangu_kuli,
                    ]);
                }
                if($solar>0){
                    OrderTrucking::find($id)->update([
                        'tambah_solar' => $solar,
                    ]);
                }
                if($tally>0){
                    OrderTrucking::find($id)->update([
                        'tally' => $tally,
                    ]);
                }
                if($uang_makan>0){
                    OrderTrucking::find($id)->update([
                        'uang_makan' => $uang_makan,
                    ]);
                }
                if($op>0){
                    OrderTrucking::find($id)->update([
                        'op' => $op,
                    ]);
                }
                if($cleaning>0){
                    OrderTrucking::find($id)->update([
                        'cleaning' => $cleaning,
                    ]);
                }

                if($sangu_sopir>0 || $sangu_kuli>0 || $solar>0 || $tally>0 || $uang_makan>0 || $op>0 || $cleaning>0){
                    $service->trucking($id);
                }
            }
        }

        return back()->with('success','Data berhasil disimpan');
    }

    public function store_kolektif(Request $request)
    {
        $data = $request->all();
        // dd($data);
        $no = Jurnal::where('tipe',$data['tipe'])->whereYear('created_at',date('Y', strtotime($data['created_at'])))->max('no') + 1;
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
        $bgs = Jurnal::whereNotNull('no_bg')->orderBy('no_bg')->pluck('no_bg')->toArray();
        $bgs = array_unique($bgs);
        // return view('admin.jurnal.edit', compact('data','orders','coa','tipe'));
        return view('admin.jurnal.form_edit', compact('jurnal','orders','coa','tipe','bgs'));
    }

    public function updateOne(Request $request, Jurnal $jurnal)
    {
        $data = $request->all();
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
        $jurnal->update($data);
        return back()->with('success','Data berhasil disimpan!');
    }

    public function update(Jurnal $jurnal, Request $request)
    {
        $tipe = Jurnal::where('nomor',$jurnal->nomor)->first()->tipe;
        $no = Jurnal::where('nomor',$jurnal->nomor)->first()->no;
        if($tipe=='JNL'){
            $nomor = sprintf('%02d',date('m',strtotime($request->created_at))).'-'.sprintf('%03d',$no).'/'.date('y',strtotime($request->created_at));
        }else{
            $nomor = sprintf('%03d',$no).'/'.$tipe.'-RAS/'.date('y',strtotime($request->created_at));
        }
        Jurnal::where('nomor',$jurnal->nomor)->update([
            'created_at' => $request->created_at,
            'nomor' => $nomor
        ]);

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
        $coas = COA::orderBy('kode')->get(['id','nama','kode']);
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $coa_id = request('coa_id') ?? 45;
        $coa = COA::find($coa_id);
        $year = request('year') ?? date('Y');
        $month = request('month') ?? date('m');
        $tipe = 'D';
        if(substr($coa->kode,0,1)=='2'||substr($coa->kode,0,1)=='3'||substr($coa->kode,0,1)=='5'){
            $tipe = 'C';
        }
        $saldo = array();
        foreach ($months as $idx => $item) {
            $bln = $idx + 1;
            $c = new Carbon($year.'-'.sprintf('%02d',$bln).'-01');
            $now = $c->startOfMonth()->format('Y-m-d');
            $last = $c->endOfMonth()->format('Y-m-d');
            $start = $c->subMonth()->startOfMonth()->format('Y-m-d');
            // $start = '2022-12-01';
            $des = $c->endOfMonth()->format('Y-m-d');
            // dd($start,$des,$last);
            if($idx==0){
                if($tipe=='D'){
                    $saldo_awal = Jurnal::where('coa_id',$coa_id)->whereBetween('created_at',['2022-12-01',$des])->sum('debit') - Jurnal::where('coa_id',$coa_id)->whereBetween('created_at',['2022-12-01',$des])->sum('credit');
                }else{
                    $saldo_awal = Jurnal::where('coa_id',$coa_id)->whereBetween('created_at',['2022-12-01',$last])->sum('credit') - Jurnal::where('coa_id',$coa_id)->whereBetween('created_at',['2022-12-01',$last])->sum('debit');
                }
            }else{
                // if ($tipe=='D') {
                //     $saldo_awal = Jurnal::where('coa_id',$coa_id)->whereBetween('created_at',[$start,$last])->sum('debit') - Jurnal::where('coa_id',$coa_id)->whereBetween('created_at',[$start,$last])->sum('credit');
                // } else {
                //     $saldo_awal = Jurnal::where('coa_id',$coa_id)->whereBetween('created_at',[$start,$last])->sum('credit') - Jurnal::where('coa_id',$coa_id)->whereBetween('created_at',[$start,$last])->sum('debit');
                // }
                // if($saldo_awal>0){
                // }
                $start = $now;
                $saldo_awal =  $saldo['saldo_akhir'][$idx-1];
                // dd($start,$last,$saldo_awal);
            }
            $debit = Jurnal::where('coa_id',$coa_id)->whereBetween('created_at',[$now,$last])->sum('debit');
            $credit = Jurnal::where('coa_id',$coa_id)->whereBetween('created_at',[$now,$last])->sum('credit');
            $saldo['saldo_awal'][$idx] = $saldo_awal;
            if ($tipe=='D') {
                $saldo['saldo_akhir'][$idx] = ($debit + $saldo_awal ) - $credit;
            } else {
                $saldo['saldo_akhir'][$idx] = ($credit + $saldo_awal) - $debit ;
            }
            $saldo['debit'][$idx] = $debit;
            $saldo['credit'][$idx] = $credit;
        }
        $m = (int)$month;
        $saldo_awal = $saldo['saldo_awal'][$m-1];
        $search = null;
        $data = Jurnal::join('coa','coa.id','=','jurnal.coa_id')
                    ->leftJoin('order','order.id','=','jurnal.order_id')
                    ->whereMonth('jurnal.created_at',$month)
                    ->whereYear('jurnal.created_at',$year)
                    ->where('jurnal.coa_id',$coa_id)
                    ->select('jurnal.*')
                    ->orderBy('jurnal.created_at')
                    ->get();
        return view('admin.jurnal.buku_besar', compact('coas','months','month','saldo','saldo_awal','coa','coa_id','data','tipe','year'));
    }

    public function buku_besar_pembantu()
    {
        $year = request('year') ?? date('Y');
        $month = request('month') ?? date('m');
        $coa_id = request('coa_id') ?? 46;
        $subjek = request('subjek') ?? 'customer_xpdc';
        $coa = COA::find($coa_id);
        $coas = COA::orderBy('kode')->get(['id','nama','kode']);
        $tipe = 'D';
        if(substr($coa->kode,0,1)=='2'||substr($coa->kode,0,1)=='3'||substr($coa->kode,0,1)=='5'){
            $tipe = 'C';
        }
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $c = new Carbon($year.'-'.sprintf('%02d',$month).'-01');
        $now = $c->startOfMonth()->format('Y-m-d');
        $last = $c->endOfMonth()->format('Y-m-d');
        $start = '2022-12-01';
        $query = Jurnal::query();
        $query->join('coa','coa.id','=','jurnal.coa_id');
        if($subjek=='customer_xpdc'){
            $query->join('order','order.id','=','jurnal.order_id');
            $query->join('tarif','tarif.id','=','order.tarif_id');
            $query->join('customers','customers.id','=','tarif.customer_id');
            $query->select('jurnal.*','customers.nama as nama_');
        }
        if($subjek=='customer_trucking'){
            $query->join('order_trucking','order_trucking.id','=','jurnal.order_trucking_id');
            $query->join('customer_trucking','customer_trucking.id','=','order_trucking.customer_id');
            $query->select('jurnal.*','customer_trucking.nama as nama_');
        }
        if($subjek=='kendaraan'){
            $query->join('order_trucking','order_trucking.invoice','=','jurnal.invoice');
            $query->join('kendaraan','kendaraan.id','=','order_trucking.kendaraan_id');
            $query->select('jurnal.*','kendaraan.milik as nama_');
        }
        if($subjek=='pelayaran'){
            // $query->join('hutang_pelayaran','hutang_pelayaran.no_bg_ut','=','jurnal.no_bg');
            // $query->join('hutang_pelayaran', function ($join) {
            //     $join->orOn('jurnal.no_bg', '=', 'hutang_pelayaran.no_bg_opp');
            //     $join->orOn('jurnal.no_bg', '=', 'hutang_pelayaran.ut');
            //     $join->orOn('jurnal.no_bg', '=', 'hutang_pelayaran.no_bg_opt');
            // });
            // $query->join('order','order.id','=','hutang_pelayaran.order_id');
            // $query->join('jadwal_kapal','jadwal_kapal.id','=','order.jadwal_kapal_id');
            // $query->join('pelayaran','pelayaran.id','=','jadwal_kapal.pelayaran_id');
            // $query->select('jurnal.*','pelayaran.nama as nama_');
            $query->whereNotNull('jurnal.no_bg');
            // dd($query->get());
        }
        if($subjek=='agen'){
            $query->join('order','order.id','=','jurnal.order_id');
            $query->join('agen','agen.id','=','order.agen_id');
            $query->select('jurnal.*','agen.nama as nama_');
        }
        $query->where('jurnal.coa_id',$coa_id);
        $query->whereBetween('jurnal.created_at',[$start,$last]);
        if($subjek!='pelayaran'){
            $query->orderBy('nama_');
        }
        $data = $query->get();
        // dd($data);
        if($subjek!='pelayaran'){
            $data = $data->groupBy('nama_');
        }
        $q = Jurnal::query();
        $q->where('coa_id',$coa_id);
        $q->whereBetween('created_at',[$start,$last]);
        if($subjek=='customer_trucking'){
            $q->whereNull('order_trucking_id');
        }else if($subjek=='kendaraan'){
            $q->whereNotNull('invoice');
        }else{
            $q->whereNull('order_id');
        }
        $no_data = $q->get();
        if($subjek=='pelayaran'){
            $data = Pelayaran::whereHas('hutang_pelayaran', function($q){
                $q->whereNotNull('no_bg_opt');
                $q->orWhereNotNull('no_bg_opp');
                $q->orWhereNotNull('no_bg_ut');
            })->orderBy('nama')->get();
        }
        return view('admin.jurnal.buku_besar_pembantu',compact('data','months','coas','year','month','coa_id','tipe','no_data','subjek'));
    }

    public function buku_besar_pembantu_detail($year,$month,$coa_id,$pelayaran)
    {
        // dd($year,$month,$coa_id,$pelayaran);
        $pelayaran = Pelayaran::where('nama','like',$pelayaran)->first();
        if(!$pelayaran){
            return back()->with('danger','Mohon maaf sistem ada yang salah!');
        }
        $pelayaran_id = $pelayaran->id;
        $bgs = array();
        $data = HutangPelayaran::where('pelayaran_id',$pelayaran_id)->select('no_bg_opp','no_bg_opt','no_bg_ut')->get();
        foreach ($data as $bg) {
            if(!is_null($bg->no_bg_opp)){
                array_push($bgs,$bg->no_bg_opp);
            }
            if(!is_null($bg->no_bg_opt)){
                array_push($bgs,$bg->no_bg_opt);
            }
            if(!is_null($bg->no_bg_ut)){
                array_push($bgs,$bg->no_bg_ut);
            }
        }
        $bgs = array_unique($bgs);
        $c = new Carbon($year.'-'.sprintf('%02d',$month).'-01');
        $now = $c->startOfMonth()->format('Y-m-d');
        $last = $c->endOfMonth()->format('Y-m-d');
        $start = '2022-12-01';
        $query = Jurnal::query();
        $query->join('coa','coa.id','=','jurnal.coa_id');
        $query->select('jurnal.*');
        $query->where('jurnal.coa_id',$coa_id);
        $query->whereIn('jurnal.no_bg',$bgs);
        $query->whereBetween('jurnal.created_at',[$start,$last]);
        $query->orderBy('created_at');
        $jurnals = $query->get();
        return view('admin.jurnal.buku_besar_pembantu_detail', compact('jurnals','pelayaran_id'));
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

    public function exportJurnalBatch()
    {
        return (new JurnalBatchExport(request('year'),request('month')))->download('jurnal-'.request('month').'-'.request('year').'.xlsx');
    }

    public function exportMonth(Request $request)
    {
        return Excel::download(new JurnalMonth($request->from, $request->to,$request->tipe,$request->year,$request->month),'jurnal.xlsx');
    }

    public function syncJob()
    {
        $data = Jurnal::whereNotNull('order_trucking_id')->whereNull('order_id')->whereBetween('created_at',['2023-07-01',date('Y-m-d')])->get();
        // dd($data->take(10));
        $awal = $data->count();
        $akhir = 0;
        $subs = 0;
        foreach ($data as $item) {
            if(!is_null($item->order_trucking->container ?? null) && !is_null($item->order_trucking->seal ?? null)){
                $order = Order::where('container',$item->order_trucking->container)->where('seal',$item->order_trucking->seal)->first();

                if($order){
                    $item->update([
                        'order_id'=>$order->id,
                        'container' => $item->order_trucking->container ?? null,
                        'nopol' => $item->order_trucking->kendaraan->nopol ?? null,
                    ]);
                    $akhir++;
                }else{
                    $awal--;
                }
            }
        }

        return back()->with('success', $akhir.'/'.$awal.' data berhasil disinkronisasi!');
    }

    public function filter()
    {
        return view('admin.jurnal.filter');
    }

    public function jurnal_bupot_trucking()
    {
        return view('admin.jurnal.bupot_trucking');
    }

    public function jurnal_bupot_trucking_store(Request $request)
    {
        $data = $request->all();
        $no = Jurnal::where('tipe',$data['tipe'])->whereYear('created_at',date('Y', strtotime($data['created_at'])))->max('no') + 1;
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
                            'coa_id' => $data['credit_coa_id'][$i],
                            'nomor' => $nomor,
                            'nama' => $name,
                            'credit' => $data['amount'][$i],
                            'created_at' => $data['created_at'],
                            'no' => $no
                        ]);
                    }
                }

                TransaksiTrucking::where('invoice',$data['invoice'][$i])->update([
                    'bupot' => $data['bupot'][$i],
                    'masa_bupot' => $data['masa_bupot'][$i],
                    'tanggal_bupot' => $data['tanggal_bupot'][$i],
                    'no_bupot' => $data['no_bupot'][$i],
                ]);
            }
        }

        return back()->with('success','Data berhasil disimpan');
    }
}
