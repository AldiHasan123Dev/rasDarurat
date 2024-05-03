<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JurnalResource;
use App\Models\COA;
use App\Models\Jurnal;
use App\Models\JurnalSample;
use App\Models\Order;
use App\Models\OrderTrucking;
use Illuminate\Http\Request;

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
        // $query->orderBy('nama');
        $data = $query->get();
        $data = JurnalResource::collection($data);
        return response($data);
    }

    public function store(Request $request)
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
        $limit = request('rows'); // get how many rows we want to have into the grid
        $sidx = request('sidx'); // get index row - i.e. user click to sort
        $sord = request('sord'); // get the direction
        $search = request('_search'); // get the search
        $is_sample = request('is_sample');
        $is_search = false;
        if($search=='true'){
            $is_search = true;
        }

        $jurnal_model = new Jurnal();
        if ($is_sample=='sample') {
            $jurnal_model = new JurnalSample();
        }
        $query = $jurnal_model->query();


        $start = $limit * $page - $limit;
        if ($start < 0){
            $start = 0;
        }

        if(request('date')){
            $query->whereDate('created_at',request('date'));
        }else{
            if(request('month')){
                $query->whereMonth('created_at',request('month'));
            }
            if(request('year')){
                $query->whereYear('created_at',request('year'));
            }
            if(request('tipe')){
                $query->where('tipe','LIKE','%'.request('tipe').'%');
            }
        }

        if(request('search')){
            $query->search(request('search'));
        }
        $data = $query->orderBy('created_at','desc')->orderBy('nomor','desc')->skip($start)->take($limit)->get();

        $count = $jurnal_model->get('id')->count();
        if (request('date')) {
            $count = $jurnal_model->whereDate('created_at',request('date'))->get('id')->count();
        }else{
            if(request('month') && request('tipe')){
                $count = $jurnal_model->whereMonth('created_at',request('month'))->where('tipe','LIKE','%'.request('tipe').'%')->get('id')->count();
            }
        }


        if ($count > 0 && $limit > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages){
            $page = $total_pages;
        }

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
        $order_id = request('order_id');
        foreach($order_id as $id){
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
}
