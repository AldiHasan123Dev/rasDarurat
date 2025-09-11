<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JurnalResource;
use App\Models\COA;
use App\Models\Jurnal;
use App\Models\JurnalSample;
use App\Models\Order;
use App\Models\Pelayaran;
use App\Models\OrderTrucking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class JurnalController extends Controller
{
    public function getArrayID()
    {
        $order = Order::where('job','LIKE','%'.request('search').'%')->select('id','job as text')->get();
        return response([
            'items' => $order
        ]);
    }

    public function index()
    {
        $query = Jurnal::query();
        if(request('nomor')){
            $query->where('nomor',request('nomor'));
        }
        if(request('order_id')){
            $query->where('order_id',request('order_id'));
        }
        if(request('order_trucking_id')){
            $query->where('order_trucking_id',request('order_trucking_id'));
        }
        if(request('page') && request('page')>0){
            $data = $query->paginate(10,['*'],'page',request('page'));
        }else{
            $data = $query->get();
        }
        // $query->orderBy('nama');
        $data = JurnalResource::collection($data);
        return response($data);
    }

    public function coa_ras()
    {
        $data = COA::all()->whereNull('coa_id')->sortBy('kode');
        return response($data);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if(!empty($data['invoice_expdc'])){
            $name = $data['nama'];
            $order = Order::find($data['invoice_expdc']);
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
        if(!empty($data['invoice_agen'])){
            $name = $data['nama'];
            $order = Order::find($data['invoice_agen']);
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
            $data['invoice_agen'] = $order->invoice_agen;
            $data['nopol'] = $order->nopol;
            $data['container'] = $order->container;
            $data['nama'] = $name;
        }
        if(!empty($data['invoice_vendor'])){
            $name = $data['nama'];
            $order = OrderTrucking::find($data['invoice_vendor']);
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
            $data['invoice_vendor'] = $order->invoice;
            $data['nopol'] = $order->kendaraan->nopol;
            $data['container'] = $order->container;
            $data['nama'] = $name;
        }
         if(!empty($data['invoice_trucking'])){
            $name = $data['nama'];
            $order = OrderTrucking::find($data['invoice_trucking']);
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
            $data['invoice_trucking'] = $order->invoice;
            $data['nopol'] = $order->kendaraan->nopol;
            $data['container'] = $order->container;
            $data['nama'] = $name;
        }
        $jurnal = Jurnal::create($data);
        return response($jurnal);
    }

    public function destroy()
    {
        $id = request('id');
        Jurnal::find($id)->delete();
        return response('success');
    }

    public function jqgrid()
    {
        $page = request('page'); // get the requested page
        $limit = request('rows') ?? 0; // get how many rows we want to have into the grid
        $sidx = request('sidx'); // get index row - i.e. user click to sort
        $sord = request('sord'); // get the direction
        $search = request('_search'); // get the search
        $is_sample = request('is_sample');
        $kategori = request('kategori');
        $keterangan = request('keterangan');
        $noJob = request('noJob');
        $year_is = request('year_is');
        $tahun = request('tahun');
        $month_is = request('month_is');
        $coa = request('coa_id');
        $nomorS = request('nomorS');
        $nomorE = request('nomorE');
        $tipe = request('tipe');
        $bank = request('bank');
        $kas = request('kas');
        $nomor = request('nomor');
        $jurnal = request('jurnal');
        $bkt = request('bkt');
        $job = request('job');
        $tgl = request('tgl');
        $container = request('container');
        \Log::info('FILTER PARAMS', [
    'coa_id' => $coa,
    'month_is' => $month_is,
    'year_is' => $year_is,
]);

        $is_search = false;
        if($search=='true'){
            $is_search = true;
        }

$jurnal_model = ($kategori === 'sample') ? new JurnalSample() : new Jurnal();
$query = $jurnal_model->newQuery()->with([
    'coa',
    'order',
    'order_trucking.order',
]);

$start = max(0, $limit * $page - $limit);

$hasFilter = false;

if ($keterangan && strlen($keterangan) > 3) {
    $query->where('nama', 'like', '%' . $keterangan . '%');
    $hasFilter = true;
}

if ($nomor) {
    $query->where('nomor', 'like', '%' . $nomor . '%');
    $hasFilter = true;
}

 if (($nomorS) && ($nomorE)) {
        $query->whereBetween('nomor', [$nomorS, $nomorE])
              ->whereYear('created_at', $tahun);

    // Jika hanya $nomorS yang ada
    } elseif (($nomorS)) {
        $query->where('nomor', 'like', '%' . $nomorS . '%')
              ->whereYear('created_at', $tahun);

    // Jika hanya $nomorE yang ada
    } elseif (($nomorE)) {
        $query->where('nomor', 'like', '%' . $nomorE . '%')
              ->whereYear('created_at', $tahun);
    }
if ($noJob) {
    // Jika keduanya ada
    if (!is_null($nomorS) && !is_null($nomorE)) {
        $query->whereBetween('nomor', [$nomorS, $nomorE])
              ->whereNull('order_id')
              ->whereNull('order_trucking_id')
              ->where('coa_id', 31)
              ->whereYear('created_at', $tahun);

    // Jika hanya $nomorS yang ada
    } elseif (!is_null($nomorS)) {
        $query->where('nomor', 'like', '%' . $nomorS . '%')
              ->whereNull('order_id')
              ->whereNull('order_trucking_id')
              ->where('coa_id', 31)
              ->whereYear('created_at', $tahun);

    // Jika hanya $nomorE yang ada
    } elseif (!is_null($nomorE)) {
        $query->where('nomor', 'like', '%' . $nomorE . '%')
              ->whereNull('order_id')
              ->whereNull('order_trucking_id')
              ->where('coa_id', 31)
              ->whereYear('created_at', $tahun);

    // Jika keduanya null → tidak ada where nomor, hanya filter tahun
    } else {
        $tes = $query->whereYear('created_at', $tahun ?? date('Y'))
              ->whereNull('order_id')
              ->whereNull('order_trucking_id')
              ->where('coa_id', 31);
    }
}



if ($container && strlen($container) > 3) {
    $query->where('container', 'like', '%' . $container . '%');
    $hasFilter = true;
}

if ($job) {
    $query->whereHas('order', function ($q) use ($job) {
        $q->where('job', 'like', '%' . $job . '%');
    });
    $hasFilter = true;
}


if ($bank) {
    $query->whereIn('tipe', ['BBK', 'BBM']);
    $hasFilter = true;
} elseif ($bkt) {
    $query->whereIn('tipe', ['BBKT', 'BBMT']);
    $hasFilter = true;
} elseif ($kas) {
    $query->whereIn('tipe', ['BKK', 'BKM']);
    $hasFilter = true;
} elseif ($jurnal) {
   $data = $query->where('tipe', 'JNL');
    $hasFilter = true;
}

if ($tgl && strlen($tgl) > 3) {
    $query->whereDate('created_at', $tgl);
    $hasFilter = true;
}

if ($coa) {
    $query->where('coa_id', $coa)
          ->whereNull('jurnal_balik')
          ->whereMonth('created_at', $month_is)
          ->whereYear('created_at', $year_is);
    $hasFilter = true;
    
if ($tipe === 'debit') {
    $query->where('debit', '>', 0)->orderByDesc('created_at');
} elseif ($tipe === 'credit') {
    $query->where('credit', '>', 0)->orderByDesc('created_at');
}
}

// Hitung total data (tanpa limit)
$count = $query->count();

// Pagination
if ($count > 0 && $limit > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 0;
}

if ($page > $total_pages) {
    $page = $total_pages;
}
$start = max(0, $limit * ($page - 1));

// Ambil data sesuai limit & offset
if ($nomorS && $nomorE) {
    // Jika ada range nomor
    $data = $query->orderBy('nomor', 'asc')
                  ->skip($start)
                  ->take($limit)
                  ->get();

} elseif ($hasFilter) {
    // Jika hanya filter tahun atau filter lain
    $data = $query->orderByDesc('created_at')
                  ->orderBy('nomor')
                  ->skip($start)
                  ->take($limit)
                  ->get();

} else if ($noJob) {
     $data = $query->orderBy('created_at', 'asc')
                  ->skip($start)
                  ->take($limit)
                  ->get();
}else {
    // Jika sama sekali tidak ada filter
    $data = collect();
}

// Format response
$response = JurnalResource::collection($data);
return response([
    'page' => $page,
    'total' => $total_pages,
    'records' => $count,
    'rows' => $response
]);

    }

    public function buku_besar()
    {
        $start = request('start');
        $tipe = request('tipe');
        $saldo_awal = request('saldo_awal');
        $coa = COA::find(request('coa_id'));
        $data =  Jurnal::join('coa','coa.id','=','jurnal.coa_id')
            ->leftJoin('order','order.id','=','jurnal.order_id')
            ->orWhere('order.job','LIKE','%'.request('search').'%')
            ->whereMonth('jurnal.created_at',request('month'))
            ->whereYear('jurnal.created_at',request('year'))
            ->where('jurnal.coa_id', request('coa_id'))
            ->orWhere('coa.kode','LIKE','%'.request('search').'%')
            ->whereMonth('jurnal.created_at',request('month'))
            ->whereYear('jurnal.created_at',request('year'))
            ->where('jurnal.coa_id', request('coa_id'))
            ->orWhere('coa.nama','LIKE','%'.request('search').'%')
            ->whereMonth('jurnal.created_at',request('month'))
            ->whereYear('jurnal.created_at',request('year'))
            ->where('jurnal.coa_id', request('coa_id'))
            ->orWhere('jurnal.nama','LIKE','%'.request('search').'%')
            ->whereMonth('jurnal.created_at',request('month'))
            ->whereYear('jurnal.created_at',request('year'))
            ->where('jurnal.coa_id', request('coa_id'))
            ->orWhere('jurnal.nomor','LIKE','%'.request('search').'%')
            ->whereMonth('jurnal.created_at',request('month'))
            ->whereYear('jurnal.created_at',request('year'))
            ->where('jurnal.coa_id', request('coa_id'))
            ->orWhere('jurnal.created_at','LIKE','%'.request('search').'%')
            ->whereMonth('jurnal.created_at',request('month'))
            ->whereYear('jurnal.created_at',request('year'))
            ->where('jurnal.coa_id', request('coa_id'))
            ->select('jurnal.*')
            ->orderBy('jurnal.created_at')
            ->skip($start)
            ->take(100)
            ->get();

        $count = Jurnal::join('coa','coa.id','=','jurnal.coa_id')
            ->leftJoin('order','order.id','=','jurnal.order_id')
            ->orWhere('order.job','LIKE','%'.request('search').'%')
            ->whereMonth('jurnal.created_at',request('month'))
            ->whereYear('jurnal.created_at',request('year'))
            ->where('jurnal.coa_id', request('coa_id'))
            ->orWhere('coa.kode','LIKE','%'.request('search').'%')
            ->whereMonth('jurnal.created_at',request('month'))
            ->whereYear('jurnal.created_at',request('year'))
            ->where('jurnal.coa_id', request('coa_id'))
            ->orWhere('coa.nama','LIKE','%'.request('search').'%')
            ->whereMonth('jurnal.created_at',request('month'))
            ->whereYear('jurnal.created_at',request('year'))
            ->where('jurnal.coa_id', request('coa_id'))
            ->orWhere('jurnal.nama','LIKE','%'.request('search').'%')
            ->whereMonth('jurnal.created_at',request('month'))
            ->whereYear('jurnal.created_at',request('year'))
            ->where('jurnal.coa_id', request('coa_id'))
            ->orWhere('jurnal.nomor','LIKE','%'.request('search').'%')
            ->whereMonth('jurnal.created_at',request('month'))
            ->whereYear('jurnal.created_at',request('year'))
            ->where('jurnal.coa_id', request('coa_id'))
            ->orWhere('jurnal.created_at','LIKE','%'.request('search').'%')
            ->whereMonth('jurnal.created_at',request('month'))
            ->whereYear('jurnal.created_at',request('year'))
            ->where('jurnal.coa_id', request('coa_id'))
            ->select('jurnal.id')
            ->count();

        $view = view('data.buku_besar',compact('data','tipe','coa','saldo_awal'))->render();
        if ($tipe=='D') {
            $saldo_awal = $data->sum('debit') - $data->sum('credit');
        } else {
            $saldo_awal = $data->sum('credit') - $data->sum('debit');
        }

        $continue = 1;
        if(($start+100)>=$count){
            $continue = 0;
        }
        return response([
            'view' => $view,
            'start' => $start + 100,
            'saldo_awal' => $saldo_awal,
            'continue' => $continue
        ]);
    }

    public function filter()
    {
        $query = Jurnal::query();

        if(request('coa_id')){
            $query->where('coa_id',request('coa_id'));
        }else{
            return response([]);
        }
        if(request('nomor')){
            $query->where('nomor',request('nomor'));
        }
        if(request('nama')){
            $query->where('nama','LIKE',request('nama'));
        }
        if(request('tgl_awal')&&request('tgl_akhir')){
            $query->whereBetween('created_at',[request('tgl_awal'),request('tgl_akhir')]);
        }else{
            return response([]);
        }

        $data = $query->orderBy('created_at')->get();
        $data = JurnalResource::collection($data);
        return response($data);
    }

    public function check_omset()
{
    $order_ids = request('order_id') ?? [];
    if (empty($order_ids)) {
        return response(['status' => 0, 'message' => 'aman']);
    }

    // Ambil semua jurnal yang sesuai dalam satu query
    $jurnals = Jurnal::whereIn('order_id', $order_ids)
        ->where('coa_id', 93)
        ->where('debit', '>', 0)
        ->get()
        ->groupBy('order_id');

    // Ambil semua order sekaligus
    $orders = Order::whereIn('id', $order_ids)->get()->keyBy('id');

    foreach ($jurnals as $orderId => $jurnalList) {
        if (isset($orders[$orderId])) {
            $order = $orders[$orderId];
            return response([
                'status' => 1,
                'message' => $order->job . '-' . sprintf('%02d', $order->no_job) . ' sudah close dari Uang Muka'
            ]);
        }
    }

    return response([
        'status' => 0,
        'message' => 'aman'
    ]);
}


    public function check_omset_trucking()
    {
        $order_id = request('order_id') ?? [];
        $truckid = OrderTrucking::whereIn('id',$order_id)->whereNotNull('order_id')->pluck('order_id')->toArray();
        foreach($truckid as $id){
            $jurnals = Jurnal::where('order_id',$id)->where('coa_id',93)->where('debit','>',0)->get();
            $order = Order::find($id);
            if($jurnals->count()>0 && $order){
                return response([
                    'status' => 1,
                    'message' => $order->job.'-'.sprintf('%02d',$order->no_job).' sudah close dari Uang Muka'
                ]);
            }
        }
        return response([
            'status' => 0,
            'message' => 'aman'
        ]);
    }

    public function render_buku_pembantu()
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
            $query->join('order','order.invoice','=','jurnal.invoice');
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
            $q->whereNull('invoice');
        }
        $no_data = $q->get();
        if($subjek=='pelayaran'){
            $data = Pelayaran::whereHas('hutang_pelayaran', function($q){
                $q->whereNotNull('no_bg_opt');
                $q->orWhereNotNull('no_bg_opp');
                $q->orWhereNotNull('no_bg_ut');
            })->orderBy('nama')->get();
        }

        $res = view('data.buku_besar_pembantu', compact('data','months','coas','year','month','coa_id','tipe','no_data','subjek'))->render();
        return response([
            'data' => $res
        ]);
    }

    public function neraca()
    {
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $month = request('month') ?? date('m');
        $year = request('year') ?? date('Y');
        $m = sprintf('%02d',(int)$month -1);
        $start = $year.'-'.$m.'-01';
        if($month=='01'){
            $start = ((int)$year - 1).'-12-01';
        }
        $start = '2022-12-01';
        $tahunCo = $year;
        $dateCo = now()->create($tahunCo . '-' . '01' . '-01')->startOfMonth()->toDateString();
        $end = Carbon::parse($year . '-' . sprintf('%02d', $month) . '-01')->endOfMonth()->format('Y-m-d 23:59:59');
        $aktiva_lancar = COA::where('kode','not like','1.2%')->where('kode','like','1%')->orderBy('kode')->get();
        $aktiva_tak_lancar = COA::where('kode','like','1.2%')->orderBy('kode')->get();
        $kewajiban = COA::where('kode','like','2.%')->orderBy('kode')->get();
        $modal = COA::where('kode','like','3.%')->orderBy('kode')->get();
        $kel5 = Jurnal::join('coa', 'coa.id', '=', 'jurnal.coa_id')
        ->where('coa.kode', 'like', '5.%')
        ->whereBetween('jurnal.created_at', [$dateCo, $end])
        ->select(DB::raw('SUM(jurnal.debit) AS debit'), DB::raw('SUM(jurnal.credit) AS credit'))->first();
        $kel6 = Jurnal::join('coa','coa.id','=','jurnal.coa_id')
        ->where('coa.kode','like','6.%')
        ->whereBetween('jurnal.created_at',[$dateCo,$end])
        ->select(DB::raw('SUM(jurnal.debit) AS debit'), DB::raw('SUM(jurnal.credit) AS credit'))->first();
        $kel7 = Jurnal::join('coa','coa.id','=','jurnal.coa_id')
        ->where('coa.kode','like','7.%')
        ->whereBetween('jurnal.created_at',[$dateCo,$end])
        ->select(DB::raw('SUM(jurnal.debit) AS debit'), DB::raw('SUM(jurnal.credit) AS credit'))->first();
        $lr = ($kel5->credit - $kel5->debit) - (($kel6->debit - $kel6->credit) + ($kel7->debit - $kel7->credit));

        $res = view('data.neraca', compact('months','year','month','start','end','aktiva_lancar','aktiva_tak_lancar','kewajiban','modal','lr'))->render();
        return response($res);
    }

    public function getLastDay($year, $month)
    {
        $carbon = new Carbon($year.'-'.$month.'-01');
        $last = $carbon->endOfMonth()->toDateString();
        return $last;
    }

    public function getNomor(Request $request)
    {
        $data = Jurnal::where('nomor','like','%'.$request->q.'%')->select('nomor as text')->distinct()->orderBy('nomor', 'desc')->take(10)->get()->map(function ($item) {
            return [
                'id' => $item->text,
                'text' => $item->text
            ];
        });
        return response($data);
    }
}
