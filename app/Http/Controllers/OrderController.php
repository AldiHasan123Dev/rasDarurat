<?php

namespace App\Http\Controllers;

use App\Exports\BAKembaliExport;
use App\Exports\CheiljedangExport;
use App\Exports\ExportSinarrBalado;
use App\Exports\InvoiceRekapExport;
use App\Exports\OrderExport;
use App\Exports\MalindoExport;
use App\Exports\ExportLogised;
use App\Exports\FortunaExport;
use App\Exports\SIExport;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderTruckingResource;
use App\Imports\OrderImport;
use App\Models\Agen;
use App\Models\Jurnal;
use App\Models\Barang;
use App\Models\BTTB;
use App\Models\User;
use App\Models\Customer;
use App\Models\JadwalKapal;
use App\Models\Lokasi;
use App\Models\Order;
use App\Models\OrderTrucking;
use App\Models\Satuan;
use App\Models\Tarif;
use App\Models\Setting;
use App\Models\HutangPelayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index($marketing = null)
    {
        if($marketing){
            $marketing = Auth::id();
        }
        $idMarketing = Auth::id();
        $jadwal_kapal = JadwalKapal::all()->where('is_active',0);
        $user = Auth::id();

        $tarifs = Tarif::join('customers', 'customers.id', '=', 'tarif.customer_id')
            ->join('pelayaran', 'pelayaran.id', '=', 'tarif.pelayaran_id')
            ->join('lokasi as dari', 'dari.id', '=', 'tarif.dari')
            ->join('lokasi as tujuan', 'tujuan.id', '=', 'tarif.tujuan')
            ->join('shipments', 'shipments.id', '=', 'tarif.shipment')
            ->join('kondisi', 'kondisi.id', '=', 'tarif.kondisi')
            ->join('satuan', 'satuan.id', '=', 'tarif.satuan')
            ->where('tarif.is_active', 1)
            ->where(function ($q) use ($user) {
                $q->where('customers.cs_id', $user)
                ->orWhere('customers.marketing_id', $user);
            })
            ->select('tarif.*')
            ->get();
        $barang = Barang::pluck('nama')->toArray();
        $satuan = Satuan::pluck('nama')->toArray();
        $agent = Agen::pluck('nama')->toArray();
        $tarif = array();
        $pelayaran = $jadwal_kapal->pluck('pelayaran_id')->toArray();
        $lokasi = Tarif::whereIn('pelayaran_id',$pelayaran)->pluck('tujuan')->toArray();
        $data_tarif_lokasi = array_unique($lokasi);
        $data_lokasi = Lokasi::whereIn('id',$data_tarif_lokasi)->get();
        $customers = Customer::pluck('nama')->toArray();
        foreach ($tarifs as $id => $item ) {
            $tarif[$item->id] = ($item->customer->nama??'-') .' | '.($item->customer->id??'-').' || '.($item->dari_lokasi->nama??'-') .' || '.($item->tujuan_lokasi->nama??'-') .' || '.($item->kondisiInfo->nama??'-') .' || '.($item->pelayaran->nama??'-') .' || '.($item->shipmentInfo->nama??'-') .' || '.($item->tarif??'-').' || '.$item->stuffing . ' || ' .($item->shipmentInfo->nama??'-');
        }
        return view('admin.order.index', compact('tarif','barang','satuan','agent','jadwal_kapal','data_lokasi','customers','marketing'));
    }

    public function orderMarketing($marketing = null)
    {
        if($marketing){
            $marketing = Auth::id();
        }
        $idMarketing = Auth::id();
        $jadwal_kapal = JadwalKapal::all()->where('is_active',0);
        $tarifs = Tarif::join('customers','customers.id','=','tarif.customer_id')
                    ->join('pelayaran','pelayaran.id','=','tarif.pelayaran_id')
                    ->join('lokasi as dari','dari.id','=','tarif.dari')
                    ->join('lokasi as tujuan','tujuan.id','=','tarif.tujuan')
                    ->join('shipments','shipments.id','=','tarif.shipment')
                    ->join('kondisi','kondisi.id','=','tarif.kondisi')
                    ->join('satuan','satuan.id','=','tarif.satuan')
                    ->select('tarif.*')
                    ->where('customers.marketing_id',$idMarketing)
                    ->where('tarif.is_active',1)
                    ->get();
        $barang = Barang::pluck('nama')->toArray();
        $satuan = Satuan::pluck('nama')->toArray();
        $agent = Agen::pluck('nama')->toArray();
        $tarif = array();
        $pelayaran = $jadwal_kapal->pluck('pelayaran_id')->toArray();
        $lokasi = Tarif::whereIn('pelayaran_id',$pelayaran)->pluck('tujuan')->toArray();
        $data_tarif_lokasi = array_unique($lokasi);
        $data_lokasi = Lokasi::whereIn('id',$data_tarif_lokasi)->get();
        $customers = Customer::pluck('nama')->toArray();
        foreach ($tarifs as $id => $item ) {
            $tarif[$item->id] = ($item->customer->nama??'-') .' | '.($item->customer->id??'-').' || '.($item->dari_lokasi->nama??'-') .' || '.($item->tujuan_lokasi->nama??'-') .' || '.($item->kondisiInfo->nama??'-') .' || '.($item->pelayaran->nama??'-') .' || '.($item->shipmentInfo->nama??'-') .' || '.($item->tarif??'-').' || '.$item->stuffing . ' || ' .($item->shipmentInfo->nama??'-');
        }
        return view('admin.order.order_marketing', compact('tarif','barang','satuan','agent','jadwal_kapal','data_lokasi','customers','marketing','idMarketing'));
    }

    public function order_blum_inv(){
        $lokasi = Lokasi::all();
        $pembayar = Customer::with('marketing','cs')->get();
        $role = User::where('role_id', 2)
            ->where('name', '!=', '#N/A')
            ->get();
        return view ('admin.jurnal.order_blum_inv',compact('lokasi','pembayar','role'));
    }

    public function sj_kembali()
    {
        $data = OrderTrucking::all()->sortByDesc('tgl_muat')->whereNull('sj_kembali');
        $data = OrderTruckingResource::collection($data);
        return view('admin.order.sj_kembali', compact('data'));
    }

    public function baKembali()
    {
        // $data = Order::whereNull('ba_kembali')->whereHas('tarif', function($a){
        //     $a->whereIn('kondisi',[5,7]);
        // })->orderBy('job')->orderBy('no_job')->get();
        // $data = OrderResource::collection($data);
        return view('admin.order.ba_kembali');
    }

    public function baDiantarSBY()
    {
        // $data = Order::whereNull('ba_kembali')->whereHas('tarif', function($a){
        //     $a->whereIn('kondisi',[5,7]);
        // })->orderBy('job')->orderBy('no_job')->get();
        // $data = OrderResource::collection($data);
        return view('admin.order.ba_antar_sby');
    }

        public function baDiantarSBYMakassar()
    {
        // $data = Order::whereNull('ba_kembali')->whereHas('tarif', function($a){
        //     $a->whereIn('kondisi',[5,7]);
        // })->orderBy('job')->orderBy('no_job')->get();
        // $data = OrderResource::collection($data);
        return view('admin.order.ba_antar_sby-makassar');
    }

     public function barangDiantar()
    {
        // $data = Order::whereNull('ba_kembali')->whereHas('tarif', function($a){
        //     $a->whereIn('kondisi',[5,7]);
        // })->orderBy('job')->orderBy('no_job')->get();
        // $data = OrderResource::collection($data);
        return view('admin.order.barang_diantar');
    }

    public function closing()
    {
        $data = Order::whereHas('jadwal_kapal', function($a){
            $a->whereNull('td');
            $a->whereDate('etd','<=',date('Y-m-d'));
        })->orderBy('job')->orderBy('no_job')->get();
        $data = OrderResource::collection($data);
        return view('admin.order.closing', compact('data'));
    }

    public function asuransi()
    {
        $orders = Order::whereIn('asuransi',['ADA','ADA INC','ADA EXC'])->whereNull('asuransi_id')->get();
        return view('admin.order.asuransi',compact('orders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadwal_kapal_id' => 'required|numeric',
            'tarif_id' => 'required|numeric',
            'barang_id' => 'required',
        ],[
            'jadwal_kapal_id.required' => 'Kapal Harus diisi!',
            'jadwal_kapal_id.numeric' => 'Kapal Harus diisi!',
            'tarif_id.required' => 'Pembayar Harus diisi!',
            'tarif_id.numeric' => 'Pembayar Harus diisi!',
            'barang_id.required' => 'Barang Harus diisi!',
        ]);
        $data = $request->all();
        $data['komisi'] = is_null($data['komisi']) ? 0 : $data['komisi'];
        $barang = Barang::find($request->barang_id);
        $data['pengirim_id'] = Customer::where('nama',$request->pengirim_id)->first()->id ?? null;
        $data['penerima_id'] = Customer::where('nama',$request->penerima_id)->first()->id ?? null;
        $data['penerima_bl_id'] = Customer::where('nama',$request->penerima_bl_id)->first()->id ?? null;
        $data['penerimabl'] = Customer::where('nama',$request->penerima_bl_id)->first()->nama ?? null;
        if (!$barang) {
            $barang = Barang::create(['nama'=>$request->barang_id]);
        }
        if($data['agen']=='AGEN'){
            $data['agen_id'] = Agen::where('nama',$request->agen_id)->first()->id ?? null;
            $data['penerimabl'] = Agen::where('nama',$request->agen_id)->first()->nama ?? null;
        }
       $year = date('Y');
       $num = Order::whereRaw('LEFT(job, 4) = ?', [$year])
            ->max('no');
        $setting = Setting::find(1);
        $data['barang_id'] = $barang->id;
        $data['no'] = $num+1;
        if($setting->type_job_year=='y'){
            $data['job'] = date('ym').sprintf('%04d',$num+1);
        }else{
            $data['job'] = date('Ym').sprintf('%04d',$num+1);
        }
        $data['no_job'] = 1;

        $tarif = Tarif::find($request->tarif_id);
        $ceks = Order::where('jadwal_kapal_id',$request->jadwal_kapal_id)->whereHas('tarif', function($q) use($tarif){
            $q->where('customer_id',$tarif->customer_id);
            $q->where('tujuan',$tarif->tujuan);
        })->get();

        if($ceks->count()>0){
            $cek = $ceks->first();
            $data['no'] = $cek->no;
            $data['job'] = $cek->job;
            $data['no_job'] = $ceks->count() + 1;
        }
        $order = Order::create($data);
        // $tarif_pelayaran = TarifPelayaran::where('pelayaran_id',$order->jadwal_kapal->pelayaran_id)
        //                     ->where('dari',$order->tarif->dari)
        //                     ->where('tujuan',$order->tarif->tujuan)
        //                     ->where('tipe',$order->tarif->shipment)
        //                     ->where('is_active',1)
        //                     ->first();
        // if($tarif_pelayaran){
        // }
        HutangPelayaran::create([
            'pelayaran_id' => $order->jadwal_kapal->pelayaran_id,
            'order_id' => $order->id,
            'status' => 0,
        ]);
        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Order $order, Request $request)
    {
        $data = $request->all();
        if($request->komisi){
            $data['komisi'] = is_null($data['komisi']) ? 0 : $data['komisi'];
        }
        if($request->ba){
            if($request->ba==2){
                $data['ba_kembali'] = null;
            }
        }elseif($request->tgl_komisi){

        }elseif($request->asuransi_update){
            $data['pertanggungan'] = str_replace(['.',','],'',$request->pertanggungan);
            $data['asuransi_date'] = date('Y-m-d H:i:s');
            if($request->tipe_asuransi=='job'){
                Order::where('job',$order->job)->update([
                    'pertanggungan' => $data['pertanggungan'],
                    'tipe_asuransi' => 'job',
                    'asuransi_id' => $request->asuransi_id,
                    'asuransi_date' => date('Y-m-d H:i:s')
                ]);
            }
        }else{
            $request->validate([
                'jadwal_kapal_id' => 'required|numeric'
            ]);
            $barang = Barang::where('nama',$request->barang_id)->first();
            if (!$barang) {
                $barang = Barang::create(['nama'=>$request->barang_id]);
            }
            $data['pengirim_id'] = Customer::where('nama',$request->pengirim_id)->first()->id ?? null;
            $data['penerima_id'] = Customer::where('nama',$request->penerima_id)->first()->id ?? null;
            $data['penerima_bl_id'] = Customer::where('nama',$request->penerima_bl_id)->first()->id ?? null;
            $data['penerimabl'] = Customer::where('nama',$request->penerima_bl_id)->first()->nama ?? null;
            if($data['agen']=='AGEN'){
                $data['agen_id'] = Agen::where('nama',$request->agen_id)->first()->id ?? null;
                $data['penerimabl'] = Agen::where('nama',$request->agen_id)->first()->nama ?? null;
            }else{
                $data['agen_id'] = null;
            }


            if ($request->satuan) {
                $satuan = Satuan::find($request->satuan);
                if(!$satuan){
                    $satuan = Satuan::create(['nama'=>$request->satuan]);
                }
                $data['satuan'] = $satuan->id;
            }
            $data['barang_id'] = $barang->id;
        }
        $order->update($data);

        if($request->asuransi_update){
            return back()->with('success','Data berhasil disimpan');
        }
        if ($request->ba_kembali && $request->invoice==1) {
            return redirect()->route('order.ba-kembali',['filter-order'=>'ba_kembali'])->with('success','Data berhasil diupdate');
        }
        return back()->with('success','Data berhasil diupdate');
    }

    public function SIExport()
    {
        return Excel::download(new SIExport(request('attn'),request('to'),request('jadwal_kapal_id'),request('tujuan')),'SI-EXPORT.xlsx');
    }

    public function edit(Order $order)
    {
        if(Auth::user()->role_id!=1){
            if($order->jadwal_kapal){
                if (!is_null($order->jadwal_kapal->td)) {
                    return back()->with('danger','Order tidak bisa di edit');
                }
            }
        }
        $tarifs = Tarif::join('customers','customers.id','=','tarif.customer_id')
                    ->join('pelayaran','pelayaran.id','=','tarif.pelayaran_id')
                    ->join('lokasi as dari','dari.id','=','tarif.dari')
                    ->join('lokasi as tujuan','tujuan.id','=','tarif.tujuan')
                    ->join('shipments','shipments.id','=','tarif.shipment')
                    ->join('kondisi','kondisi.id','=','tarif.kondisi')
                    ->join('satuan','satuan.id','=','tarif.satuan')
                    ->select('tarif.*')
                    ->where('tarif.is_active',1)
                    ->orWhere('tarif.id',$order->tarif_id)
                    ->get();
        $customers = Customer::pluck('nama')->toArray();
        $barang = Barang::pluck('nama')->toArray();
        $tarif = array();
        $agent = Agen::pluck('nama')->toArray();
        foreach ($tarifs as $id => $item ) {
            $tarif[$item->id] = ($item->customer->nama??'-') .' || '.($item->dari_lokasi->nama??'-') .' || '.($item->tujuan_lokasi->nama??'-') .' || '.($item->kondisiInfo->nama??'-') .' || '.($item->pelayaran->nama??'-') .' || '.($item->shipmentInfo->nama??'-') .' || '.($item->tarif??'-').' || '.$item->stuffing .' || '.($item->id??'-') ;
        }
        $pembayar = ($order->customer->nama??'-').' || '.($order->dari_lokasi->nama??'-').' || '.($order->tujuan_lokasi->nama??'-').' || '.($order->kondisiInfo->nama??'-').' || '.($order->pelayaran->nama??'-').' || '.($order->shipmentInfo->nama??'-').' || '.($order->tarif??'-');
        return view('admin.order.edit', compact('order','agent','tarif','customers','barang'));
    }

    public function destroy(Order $order)
    {
        $cekJurnalTakTerhapus = Jurnal::where('order_id', $order->id)->whereNull('deleted_at')->count();
        if ($cekJurnalTakTerhapus > 0) {
            return back()->with('danger','Data job ini tidak bisa dihapus karena biaya sudah tercatat!');
        }
        $order->delete();
        HutangPelayaran::where('order_id',$order->id)->delete();
         return back()->with('success','Data berhasil dihapus');
    }

    public function copy(Order $order)
    {
        $data = $order->toArray();
        $data['no_job'] = Order::where('job',$order->job)->max('no_job') + 1;
        $data['nopol'] = null;
        $data['trucking'] = null;
        $data['container'] = null;
        $data['seal'] = null;
        $data['stuffing'] = null;
        $data['barang_diantar'] = null;
        $data['ba_kembali'] = null;
        $data['full'] = null;
        $data['keterangan'] = null;
        $data['created_at'] = date('Y-m-d');
        $order = Order::create($data);
        HutangPelayaran::create([
            'pelayaran_id' => $order->jadwal_kapal->pelayaran_id,
            'order_id' => $order->id,
            'status' => 0,
        ]);
        return response('copy data berhasil!');
        // return back()->with('success','Copy data berhasil');
    }

    public function import(Request $request)
    {
        Excel::import(new OrderImport, $request->file);

        return back()->with('success', 'All good!');
    }

    public function export()
    {
        return Excel::download(new OrderExport(request('from'),request('to')), 'laporan_order.xlsx');
    }

    public function exportMalindo(Request $request)
    {
        $text = str_replace('-','',$request->month);
        return Excel::download(new MalindoExport($text), 'laporan_order_malindo.xlsx');
    }

    public function exportFortuna(Request $request)
    {
        $text = str_replace('-','',$request->month);
        return Excel::download(new FortunaExport($text), 'laporan_order_fortuna.xlsx');
    }
    
        public function exportSinarBalado(Request $request)
    {
        $text = str_replace('-','',$request->month);
        return Excel::download(new ExportSinarrBalado($text), 'laporan_order_sinar-balado.xlsx');
    }

     public function exportLogisted(Request $request)
    {
        $text = str_replace('-','',$request->month);
        return Excel::download(new ExportLogised($text), 'laporan_order_logisted.xlsx');
    }

    public function exportCheiljedang(Request $request)
    {
        $text = str_replace('-','',$request->month);
        return Excel::download(new CheiljedangExport($text), 'laporan_order_cheiljedang.xlsx');
    }

    public function export_ba_kembali()
    {
        return Excel::download(new BAKembaliExport(), 'laporan_ba_kembali.xlsx');
    }

    public function rekap_invoice()
    {
        return Excel::download(new InvoiceRekapExport(request('invoice')), 'rekap_invoice.xlsx');
    }

    public function pindah_kapal(Request $request)
    {
        $order = Order::find($request->order_id);
        $order_job = $order->job;
        $jasaKirim = $order->jasa_kirim_id ?? null;
        $tarifs = $order->tarif_id;
        $tarif = Tarif::find($tarifs);
        $job_count = Order::where('job',$order->job)->get()->count();
        $setting = Setting::find(1);
        if($job_count <= 1){
            return back()->with('danger','Dilarang pindah kapal dikarenakan cuman ada 1 job! Gunakan Fitur edit');
        }
        $cek = Order::where('jadwal_kapal_id',$request->jadwal_kapal_id)->whereHas('tarif', function($q) use($tarif){
            $q->where('customer_id',$tarif->customer_id);
            $q->where('tujuan',$tarif->tujuan);
        })->get();
        if(count($cek)>0){
            $job = $cek[0]->job;
            $no_job = count($cek) + 1;
            $num = $cek[0]->no;
        }else{
            $year = date('Y');
            $num = Order::whereRaw('LEFT(job, 4) = ?', [$year])
            ->max('no') + 1;
            if($setting->type_job_year=='y'){
                $job = date('ym').sprintf('%04d',$num);
            }else{
                $job = date('Ym').sprintf('%04d',$num);
            }
            $no_job = 1;
        }
        $order->update([
            'jasa_kirim_id' => $jasaKirim,
            'jadwal_kapal_id' => $request->jadwal_kapal_id,
            'job' => $job,
            'no_job' => $no_job,
            'no' => $num,
        ]);

        $jobs = Order::where('job',$order_job)->orderBy('no_job')->get();
        $i = 1;
        foreach($jobs as $idx => $ord){
            $ord->update([
                'no_job' => $i
            ]);

            $i++;
        }

        return back()->with('success','Kapal berhasil dipindahkan! Job berganti '. $job.'-'.sprintf('%02d',$no_job));
    }

    public function monitoring_pembayar()
    {
        return view('admin.keuangan.monitoring_pembayar');
    }

    public function datatable()
    {
        $limit = request('length');
        $start = request('start') * request('length');
        $data = Order::query()->join('tarif','tarif.id','=','order.tarif_id')
                ->join('shipments','shipments.id','=','tarif.shipment')
                ->join('kondisi','kondisi.id','=','tarif.kondisi')
                ->leftJoin('jadwal_kapal','jadwal_kapal.id','=','order.jadwal_kapal_id')
                ->leftJoin('kapal','kapal.id','=','jadwal_kapal.kapal_id')
                ->leftJoin('pelayaran','pelayaran.id','=','jadwal_kapal.pelayaran_id')
                ->leftJoin('customers as pembayar','pembayar.id','=','tarif.customer_id')
                ->leftJoin('customers as penerima','penerima.id','=','order.penerima_id')
                ->leftJoin('customers as pengirim','pengirim.id','=','order.pengirim_id')
                ->leftJoin('customers as penerima_bl','penerima_bl.id','=','order.penerima_bl_id')
                ->leftJoin('users as marketing','marketing.id','=','pembayar.marketing_id')
                ->leftJoin('users as cs','cs.id','=','pembayar.cs_id')
                ->leftJoin('barang','barang.id','=','order.barang_id')
                ->leftJoin('satuan','satuan.id','=','order.satuan')
                ->leftJoin('agen','agen.id','=','order.agen_id')
                ->leftJoin('asuransi','asuransi.id','=','order.asuransi_id')
                ->select('order.*');
        if(request('filter')&&request('filter')=='ba_kembali'){
            $data->whereNull('order.ba_kembali');
            $data->whereNull('order.invoice');
            $data->whereIn('tarif.kondisi',[5,7]);
        }
        if(request('filter')&&request('filter')=='ba_kembali_keuangan'){
            $data->whereNull('order.ba_kembali');
            $data->whereNull('order.invoice');
            $data->whereIn('tarif.kondisi',[5,7]);
            $data->Where('pembayar.ba_kembali',1);
        }
        if(request('filter')&&request('filter')=='pre_invoice'){
            $data->whereNull('order.invoice');
            $data->where(function($q){
                $q->whereNotNull('order.ba_kembali');
                $q->orWhereIn('tarif.kondisi',[1,6]);
                $q->whereNotNull('jadwal_kapal.td');
            });
        }
        if(request('filter')&&request('filter')=='pre_invoice2'){
            $data->whereNull('order.invoice');
            $data->whereIn('tarif.kondisi',[5,7]);
            $data->whereNotNull('jadwal_kapal.td');
            $data->where('pembayar.ba_kembali',0);
        }
        if(request('filter')&&request('filter')=='invoice'){
            $data->whereNotNull('order.invoice');
        }
        if(request('filter')&&request('filter')=='asuransi-before'){
            $data->where('order.asuransi','LIKE','%ADA%');
            $data->where('order.asuransi_cetak',0);
            $data->whereNotNull('asuransi_id');
        }
        if(request('filter')&&request('filter')=='asuransi-after'){
            $data->where('order.asuransi','LIKE','%ADA%');
            $data->where('order.asuransi_cetak',1);
            $data->whereNotNull('asuransi_id');
        }

        $filter = request('filter') ?? null;

        $count = $data->count();

        return Datatables::of($data->offset($start)->limit($limit))
            ->setRowClass(function ($data) {
                $class = '';
                if($data->bttb->count()>0){
                    $class = 'bg-light-success';
                }
                if($data->jadwal_kapal->is_active != 1){
                    $class = 'bg-light-danger';
                }
                if(!is_null($data->invoice)){
                    $class = 'bg-light-warning';
                }

                return $class;
            })
            ->order(function ($data) use($filter){
                if(request('filter')=='asuransi'){
                    $data->orderBy('asuransi_date','desc');
                }else{
                    $data->orderBy('no');
                    $data->orderBy('no_job');
                }
            })
            ->addColumn('updated_at',function ($data) {
                return date('d/m/y H:i', strtotime($data->updated_at)) ?? '-';
            })
            ->addColumn('asuransi_date',function ($data) {
                return $data->asuransi_date ? date('d/m/y H:i', strtotime($data->asuransi_date)) : '-';
            })
            ->addColumn('asuransi_id',function ($data) {
                return $data->asuransiInfo->nama ?? '-';
            })
            ->addColumn('pertanggungan',function ($data) {
                $tipe = '';
                if($data->tipe_asuransi=='job'){
                    $tipe = '(G)';
                }
                return number_format($data->pertanggungan). $tipe ?? '-';
            })
            ->addColumn('tools', function($data){
                $html = '<div class="dropend">
                            <button class="no-attr text-dark text-center dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:.6rem"><i class="fas fa-list"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <form method="POST" action="'.route('order.copy',$data->id).'">
                                        <input type="hidden" name="_token" value="'.csrf_token().'" />
                                        <button onclick="return confirm(\'are you sure\')" type="submit" class="dropdown-item">Copy Order</a>
                                    </form>
                                </li>
                                <li><a class="dropdown-item" href="'.route('cetak.packingList',['order_id'=>$data->id]).'">Packing List</a></li>
                                <li><a class="dropdown-item" href="'.route('cetak.packingList.kubikasi',['order_id'=>$data->id]).'">Packing List Kubikasi</a></li>
                            </ul>
                        </div>

                        <div class="modal fade" id="ba-'.$data->id.'" tabindex="-1" aria-labelledby="ba-'.$data->id.'Label" aria-hidden="true">
                        <form action="'.route('order.update',$data).'" class="modal-dialog" method="post">
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <input type="hidden" name="_method" value="put" />
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ba-'.$data->id.'Label">BA Kembali ('.$data->job.'-'.sprintf('%02d',$data->no_job).')</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col">
                                            <input type="date" name="ba_kembali" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="ba" value="1" class="btn btn-primary" onclick="return confirm(\'are you sure?\')">Simpan</button>
                                </div>
                            </div>
                        </form>
                        </div>';
                return $html;
            })
            ->addColumn('created_at', function($data){
                return date('d/m/y',strtotime($data->created_at));
            })
            ->addColumn('no_job', function($data){
                return $data->job.'-'.sprintf('%02d',$data->no_job);
            })
            ->addColumn('marketing', function($data){
                if(!is_null($data->tarif)){
                    if(!is_null($data->tarif->customer)){
                        return $data->tarif->customer->marketing->name ?? '-';
                    }
                }
                return '-';
            })
            ->addColumn('cs', function($data){
                if(!is_null($data->tarif)){
                    if(!is_null($data->tarif->customer)){
                        return $data->tarif->customer->cs->name ?? '-';
                    }
                }
                return '-';
            })
            ->addColumn('pembayar', function($data){
                if(!is_null($data->tarif)){
                    if(!is_null($data->tarif->customer)){
                        return $data->tarif->customer->nama ?? '-';
                    }
                }
                return '-';
            })
            ->addColumn('pengirim', function($data){
                return $data->pengirim->nama ?? '-';
            })
            ->addColumn('penerima', function($data){
                return $data->penerima->nama ?? '-';
            })
            ->addColumn('dari', function($data){
                return is_null($data->tarif_id)?'-': ($data->tarif->dari_lokasi->nama??'-');
            })
            ->addColumn('tujuan', function($data){
                return is_null($data->tarif_id)?'-': ($data->tarif->tujuan_lokasi->nama??'-');
            })
            ->addColumn('shipment', function($data){
                return is_null($data->tarif_id)?'-': ($data->tarif->shipmentInfo->nama??'-');
            })
            ->addColumn('kondisi', function($data){
                return is_null($data->tarif_id)?'-': ($data->tarif->kondisiInfo->nama??'-');
            })
            ->addColumn('barang', function($data){
                return $data->barang->nama ?? '-';
            })
            ->addColumn('vol', function($data){
                return $data->bttb->sum('vol') ?? '0';
            })
            ->addColumn('berat', function($data){
                return $data->bttb->sum('berat') ?? '0';
            })
            ->addColumn('barang_bttb', function($data){
                $text = '';
                foreach ($data->bttb as $barang ) {
                    $text .= $barang->barang->nama.',';
                }
                return Str::limit($text, 30, '...');
            })
            ->addColumn('pelayaran', function($data){
                return $data->jadwal_kapal->pelayaran->nama ?? '-';
            })
            ->addColumn('kapal', function($data){
                return $data->jadwal_kapal->kapal->nama ?? '-';
            })
            ->addColumn('voyage', function($data){
                return $data->jadwal_kapal->voyage ?? '-';
            })
            ->addColumn('etd', function($data){
                return is_null($data->jadwal_kapal->etd) ? '-' : date('d-m-Y',strtotime($data->jadwal_kapal->etd));
            })
            ->addColumn('td', function($data){
                return is_null($data->jadwal_kapal->td) ? '-' : date('d-m-Y',strtotime($data->jadwal_kapal->td));
            })
            ->addColumn('ba_kirim', function($data){
                return is_null($data->ba_kirim) ? '-' : date('d-m-Y',strtotime($data->ba_kirim));
            })
            ->addColumn('stuffing', function($data){
                return is_null($data->stuffing) ? '-' : date('d-m-Y',strtotime($data->stuffing));
            })
            ->addColumn('full', function($data){
                return is_null($data->full) ? '-' : date('d-m-Y',strtotime($data->full));
            })
            ->addColumn('barang_diantar', function($data){
                return is_null($data->barang_diantar) ? '-' : date('d-m-Y',strtotime($data->barang_diantar));
            })
            ->addColumn('ba_kembali', function($data){
                return is_null($data->ba_kembali) ? '-' : date('d-m-Y',strtotime($data->ba_kembali));
            })
            ->addColumn('satuan', function($data){
                $satuan = '-';
                if ($data->bttb->count()>0) {
                    $satuan = BTTB::where('order_id',$data->id)->orderBy('qty','desc')->first()->satuan->nama;
                }
                return $satuan;
            })
            ->addColumn('koli', function($data){
                $koli = '-';
                if ($data->bttb->count()>0) {
                    $koli = BTTB::where('order_id',$data->id)->sum('qty');
                }
                return $koli;
            })
            ->addColumn('agen_id', function($data){
                return $data->agent->nama ?? '-';
            })
            ->addColumn('unit', function($data){
                return is_null($data->tarif_id)?'-': ($data->tarif->satuanInfo->nama??'-');
            })
            ->addColumn('tarif', function($data){
                return number_format(is_null($data->tarif)?0: ($data->tarif->tarif??'-'));
            })
            ->addColumn('stuffing_t', function($data){
                return is_null($data->tarif_id)?'-': ($data->tarif->stuffing ?? '-');
            })
            ->addColumn('penerima_bl', function($data){
                if ($data->agen=='AGEN') {
                    return $data->agent->nama ?? '-';
                } else {
                    return $data->penerima_bl->nama ?? '-';
                }

                return $data->penerima_bl->nama ?? '-';
            })
            ->rawColumns(['tools'])
            ->setFilteredRecords($count)
            // ->setTotalRecords($count)
            ->toJson();
    }
}
