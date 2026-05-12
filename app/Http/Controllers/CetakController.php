<?php

namespace App\Http\Controllers;

use App\Models\Agen;
use App\Models\BTTB;
use App\Models\Customer;
use App\Models\JadwalKapal;
use App\Models\JasaKirim;
use App\Models\Lokasi;
use App\Models\NSFP;
use App\Models\Order;
use App\Models\Kapal;
use App\Models\Pengirim;
use App\Models\Setting;
use App\Models\Tagihan;
use App\Models\Tarif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CetakController extends Controller
{

    protected $ppn, $pph, $invoice_name, $bank, $no_rek, $bank_name;
    public function __construct()
    {
        $setting = Setting::find(1);
        $this->ppn = $setting->ppn;
        $this->pph = $setting->pph;
        $this->invoice_name = $setting->invoice_name;
        $this->bank = $setting->bank;
        $this->no_rek = $setting->no_rek;
        $this->bank_name = $setting->bank_name;
    }
    public function suratJalan()
    {
        $penerima = Customer::get();
        // $pdf = PDF::loadView('pdf.contoh');
        // return $pdf->stream('document.pdf');
        $tujuan = Lokasi::pluck('nama');
        $jadwal_kapal = JadwalKapal::join('kapal','kapal.id','=','jadwal_kapal.kapal_id')->select('jadwal_kapal.*')->where('jadwal_kapal.is_active',1)->get();
        return view('admin.cetak.surat_jalan', compact('penerima','tujuan','jadwal_kapal'));
        // $mpdf = new PDF();

        // Write some HTML code:
        // $mpdf->WriteHTML('Hello World');

        // Output a PDF file directly to the browser
        // $mpdf->Output();
        // $pdf = PDF::loadView('admin.tagihan.pdf', compact('bills','today'));

        // $content = $mpdf->download()->getOriginalContent();
        // Storage::put('public/bills/bubla.pdf',$content);
    }

    public function pdfSuratJalan(Request $request)
    {
        $customer = Customer::find($request->penerima);
        $data = $request->all();
        $data['penerima'] = $customer->nama;
        $data['kota'] = $customer->kota;
        // dd($data);
        $pdf = Pdf::loadView('pdf.surat_jalan', compact('data'));
        return $pdf->stream('document.pdf');
        return view('pdf.surat_jalan',compact('data'));
    }

    public function pickOrder()
    {
        $pengirim = Customer::get();
        $penerima = Customer::get();
        $tujuan = Lokasi::pluck('nama');
        $jadwal_kapal = JadwalKapal::join('kapal','kapal.id','=','jadwal_kapal.kapal_id')->select('jadwal_kapal.*')->where('jadwal_kapal.is_active',1)->get();
        return view('admin.cetak.pick_order', compact('pengirim','penerima','jadwal_kapal','tujuan'));
    }

    public function packingList()
    {
        $customer_id = request('customer_id') ?? null;
        $kapal_id = request('kapal_id') ?? null;
        $tujuan_id = request('tujuan_id') ?? null;

        if(request('order_id')){
            $order = Order::find(request('order_id'));
        }else{
            $order = Order::whereHas('tarif', function($q){
                $q->where('customer_id',request('customer_id'));
                $q->where('tujuan',request('tujuan_id'));
            })->whereHas('jadwal_kapal', function($q){
                $q->where('kapal_id',request('kapal_id'));
                $q->where('voyage', request('voyage'));
            })->first();
        }
        $customers = Customer::whereHas('tarif')->get(['id','nama']);
        $kapal = Kapal::get(['id','nama']);
        $tujuan = Lokasi::get(['id','nama']);
        $data = [];
        if ($order) {
            $data = Order::where('job',$order->job)->orderBy('job')->orderBy('no_job')->get();
        }
        return view('admin.cetak.packing_list', compact('order','data','customers','kapal','tujuan','customer_id','kapal_id','tujuan_id'));
    }

    public function packingListKubikasi()
    {
        $customer_id = request('customer_id') ?? null;
        $kapal_id = request('kapal_id') ?? null;
        $tujuan_id = request('tujuan_id') ?? null;

        if(request('order_id')){
            $order = Order::find(request('order_id'));
        }else{
            $order = Order::whereHas('tarif', function($q){
                $q->where('customer_id',request('customer_id'));
                $q->where('tujuan',request('tujuan_id'));
            })->whereHas('jadwal_kapal', function($q){
                $q->where('kapal_id',request('kapal_id'));
                $q->where('voyage', request('voyage'));
            })->first();
        }
        $customers = Customer::whereHas('tarif')->get(['id','nama']);
        $kapal = Kapal::get(['id','nama']);
        $tujuan = Lokasi::get(['id','nama']);
        $data = [];
        if ($order) {
            $data = Order::where('job',$order->job)->orderBy('job')->orderBy('no_job')->get();
        }
        return view('admin.cetak.packing_list_kubikasi', compact('order','data','customers','kapal','tujuan','customer_id','kapal_id','tujuan_id'));
    }

    public function bttb()
    {
        $order = Order::find(request('order_id'));
        $orders = Order::where('job',$order->job)->get();
        if (!$order) {
            return redirect()->route('order.index');
        }

        if (!$order->tarif) {
            return redirect()->route('order.index')->with('danger','Master Tarif Kosong! Harap di edit terlebih dahulu');
        }

        $data = BTTB::where('order_id',$order->id)->get();

        $url = base64_encode(file_get_contents(public_path('logo.png')));
        // dd($url);
        // if(request('print')){
        //     $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('admin.cetak.pdf.bttb', compact('order','orders','data','url'));
        //     return $pdf->download('bttb.pdf');
        // }
        return view('admin.cetak.pdf.bttb', compact('order','orders','data','url'));
    }

    public function bttbKubikasi()
    {
        $order = Order::find(request('order_id'));
        $orders = Order::where('job',$order->job)->get();
        if (!$order) {
            return redirect()->route('order.index');
        }

        if (!$order->tarif) {
            return redirect()->route('order.index')->with('danger','Master Tarif Kosong! Harap di edit terlebih dahulu');
        }

        $data = BTTB::where('order_id',$order->id)->get();
        return view('admin.cetak.bttb_kubikasi', compact('order','orders','data'));
    }

    public function shipment()
    {
        $id = request('jadwal_kapal_id');
        $jadwal_kapal = JadwalKapal::find($id);
        $lokasi = request('tujuan');
        $tujuan = Lokasi::find($lokasi);
        $pengirim = Pengirim::all();
        $orders = Order::where('jadwal_kapal_id', $id)->whereHas('tarif', function($q) use($lokasi){
            $q->where('tujuan',$lokasi);
        })->get();

        $jadwal_kapals = JadwalKapal::whereHas('kapal')->whereHas('pelayaran')->where('is_active',0)->get();
        $pelayaran = $jadwal_kapals->pluck('pelayaran_id')->toArray();
        $lokasi = Tarif::whereIn('pelayaran_id',$pelayaran)->pluck('tujuan')->toArray();
        $data_tarif_lokasi = array_unique($lokasi);
        $data_lokasi = Lokasi::whereIn('id',$data_tarif_lokasi)->get();
        return view('admin.cetak.shipment', compact('orders','jadwal_kapal','tujuan','pengirim','jadwal_kapals','data_lokasi'));
    }

    public function dooring()
    {
        $id = request('jadwal_kapal_id');
        $lokasi = request('tujuan');
        $jadwal_kapal = JadwalKapal::find($id);
        $tujuan = Lokasi::find($lokasi);
        $agents = Agen::all();
        $orders = Order::where('agen_id',request('agent'))->where('jadwal_kapal_id', $id)->whereHas('tarif', function($q) use($lokasi){
            $q->where('tujuan',$lokasi);
            $q->whereIn('kondisi',[5,7]);
        })->orderBy('job')->orderBy('no_job')->get();

        $order = $orders->whereNotNull('agen_id')->first();

        $jadwal_kapals = JadwalKapal::whereHas('kapal')->whereHas('pelayaran')->whereNotNull('td')->get();
        $pelayaran = $jadwal_kapals->pluck('pelayaran_id')->toArray();
        $lokasi = Tarif::whereIn('pelayaran_id',$pelayaran)->pluck('tujuan')->toArray();
        $data_tarif_lokasi = array_unique($lokasi);
        $data_lokasi = Lokasi::whereIn('id',$data_tarif_lokasi)->get();

        $no_dooring = '';
        $no = '';
        $order_id = '';
        if($orders->count()>0){
            $no = JasaKirim::max('no') + 1;
            $no_dooring = 'SD/'.date('ymd').'/'.sprintf('%03d',$no);
            $order_id = json_encode($orders->pluck('id')->toArray());
            $cek = JasaKirim::where('lokasi_id',request('tujuan'))->where('jadwal_kapal_id',$id)->first();
            if($cek){
                $no = $cek->no;
                $no_dooring = $cek->no_dooring;
            }
        }

        $url = route('cetak.dooring',
        [
            'jadwal_kapal_id' => request('jadwal_kapal_id'),
            'tujuan'=>request('tujuan'),
            'agent'=>request('agent'),
            'print'=>1
        ]
        );
        if (request('print')) {
            return view('admin.cetak.pdf.doring1', compact('agents','orders','jadwal_kapal','tujuan','jadwal_kapals','data_lokasi','order','order_id','no','no_dooring','url'));
        }
        return view('admin.cetak.doring', compact('agents','orders','jadwal_kapal','tujuan','jadwal_kapals','data_lokasi','order','order_id','no','no_dooring','url'));
    }

    public function draftinvoice()
    {
        if(request('order_id')){
            $order = Order::find(request('order_id'));
            $order->update(['is_draft' => 1]);
        }
        if (request('job')) {
            $order = Order::where('job',request('job'))->first();
        }
        $orders = Order::where('job',$order->job)->get();
        if (!$order) {
            return back()->with('danger','Anda harus memilih job terlebih dahulu!');
        }

        $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
        $type = strtoupper(strtolower($order->tarif->shipmentInfo->nama[0]));
        $is_allin = false;
        if ($type=='F') {
            $allin = [];
            if ($order->tarif->customer->all_in==1) {
                $allin = $this->allinFCL($order);
                $is_allin = true;
                $invoice = $this->FCL($order,1);
            }else{
                $invoice = $this->FCL($order);
            }
            $validate = $this->FCL($order)['validate'];
        }else{
            $allin = [];
            if ($order->tarif->customer->all_in==1) {
                $allin = $this->allinLCL($order);
                $is_allin = true;
                $invoice = $this->LCL($order,1);
            }else{
                $invoice = $this->LCL($order);
            }
            $validate = $this->LCL($order)['validate'];
        }

        $validate = array_unique($validate);
        $br = Order::with('barang')->where('job',$order->job)->get()->pluck('barang.nama')->toArray();
        $br = array_unique($br);
        $nama_barang = implode(',',$br);
        $ppn = $this->ppn;
        $pph = $this->pph;
        $invoice_name = $this->invoice_name;
        $bank = $this->bank;
        $no_rek = $this->no_rek;
        $bank_name = $this->bank_name;
        return view('admin.cetak.draf_invoice',compact('invoice_name','ppn','pph','order','orders','cas','validate','nama_barang','allin','invoice','is_allin','bank','no_rek','bank_name'));
    }

    public function invoice()
    {
        if(request('order_id')){
            $order = Order::find(request('order_id'));
        }
        if (request('job')) {
            $order = Order::where('job',request('job'))->first();
        }
        $orders = Order::where('job',$order->job)->get();
        if (!$order) {
            return back()->with('danger','Anda harus memilih job terlebih dahulu!');
        }

        $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
        $type = strtoupper(strtolower($order->tarif->shipmentInfo->nama[0]));
        $is_allin = false;
        if ($type=='F') {
            $allin = [];
            if ($order->tarif->customer->all_in==1) {
                $allin = $this->allinFCL($order);
                $is_allin = true;
                $invoice = $this->FCL($order,1);
                $inv_tonase = null;
                 $invoice_m3 = null;
                $inv_qty = null;
            }else{
                $invoice = $this->FCL($order);
                $inv_tonase = null;
                 $invoice_m3 = null;
                $inv_qty = null;
            }
            $validate = $this->FCL($order)['validate'];
        }else{
            $allin = [];
            if ($order->tarif->customer->all_in==1) {
                $allin = $this->allinLCL($order);
                $is_allin = true;
                $invoice = $this->LCL($order,1);
                $invoice_m3 = $this->M3LCL($order,1);
                $inv_tonase = $this->tonaseLCL($order,1);
                $inv_qty = $this->qtyLCL($order,1);
            }else{
                $invoice = $this->LCL($order);
                 $invoice_m3 = $this->M3LCL($order,1);
                $inv_tonase = $this->tonaseLCL($order,1);
                $inv_qty = $this->qtyLCL($order,1);
            }
            $validate = $this->LCL($order)['validate'];
        }

        $validate = array_unique($validate);
        $br = Order::with('barang')->where('job',$order->job)->get()->pluck('barang.nama')->toArray();
        $br = array_unique($br);
        $nama_barang = implode(',',$br);
        $ppn = $this->ppn;
        $pph = $this->pph;
        $invoice_name = $this->invoice_name;
        $bank = $this->bank;
        $no_rek = $this->no_rek;
        $bank_name = $this->bank_name;
        return view('admin.cetak.invoice',compact('invoice_name','ppn','pph','order','orders','cas','validate','nama_barang','allin','invoice','is_allin',
        'bank','no_rek','bank_name','inv_tonase', 'inv_qty','invoice_m3'));
    }

    public function invoiceCont()
    {
        if(request('order_id')){
            $order = Order::find(request('order_id'));
        }
        if (request('job')) {
            $order = Order::where('job',request('job'))->first();
        }
        $orders = Order::where('job',$order->job)->get();
        if (!$order) {
            return back()->with('danger','Anda harus memilih job terlebih dahulu!');
        }

        $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
        $type = strtoupper(strtolower($order->tarif->shipmentInfo->nama[0]));
        $is_allin = false;
        if ($type=='F') {
            $allin = $this->allinFCLCount($order);
            $invoice = $this->FCL($order);
            $validate = $this->FCL($order)['validate'];
        }else{
            $allin = [];
            if ($order->tarif->customer->all_in==1) {
                $allin = $this->allinLCL($order);
                $is_allin = true;
            }
            $invoice = $this->LCL($order);
            $validate = $this->LCL($order)['validate'];
        }
        $validate = array_unique($validate);
        $br = Order::with('barang')->where('job',$order->job)->get()->pluck('barang.nama')->toArray();
        $br = array_unique($br);
        $nama_barang = implode(',',$br);
        $ppn = $this->ppn;
        $pph = $this->pph;
        $invoice_name = $this->invoice_name;
        $bank = $this->bank;
        $no_rek = $this->no_rek;
        $bank_name = $this->bank_name;
        return view('admin.cetak.invoice_cont',compact('invoice_name','ppn','pph','order','orders','cas','validate','nama_barang','allin','invoice','is_allin','bank','no_rek','bank_name'));
    }

    public function allinFCL(Order $order)
    {
        $orders = Order::where('job',$order->job)->get();
        $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
        $asuransi = 0;
        $admin = 0;
        $doc = 0;
        $sub_total = 0;
        $items = array();
        $asuransi_name = '';
        foreach ($orders->groupBy('tarif_id') as $idx => $tar ) {
            $koli = 0;
            if ($tar->first()->tarif->kondisi==1||$tar->first()->tarif->kondisi==6) {
                $doc = $tar->count() * 500000;
            }
            foreach ($tar as $or ) {
                $koli += $or->bttb->sum('qty');
                if($or->asuransi=='ADA EXC'){
                    if (!is_null($or->asuransi_id)) {
                        $asuransi += ($or->asuransiInfo->rate/100) * $or->pertanggungan;
                        $asuransi_name = $or->asuransiInfo->nama;
                        $admin += $or->asuransiInfo->admin;
                    }
                }

            }
            $items[$idx]['keterangan'] = $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama;
            $items[$idx]['koli'] = $tar->count();
            $items[$idx]['jumlah'] = $tar->count();
            $items[$idx]['jumlah_cont'] = $tar->count();
            $items[$idx]['si'] = 'Cont '.$tar->first()->tarif->shipmentInfo->nama;
            $items[$idx]['sub_total'] = round(((($tar->first()->tarif->tarif * $tar->count())) * $this->ppn)+ ($tar->first()->tarif->tarif*$tar->count()));
            $items[$idx]['tarif'] = $items[$idx]['sub_total'] / $tar->count();
            $sub_total += $items[$idx]['sub_total'];
        }
        $asuransi += $admin;
        $asuransi = round($asuransi);
        if ($asuransi>0&&$order->tipe_asuransi=='job') {
            $asuransi = round((($order->asuransiInfo->rate/100) * $order->pertanggungan + $order->asuransiInfo->admin));
        }
        $total = $sub_total + $asuransi + $cas->sum('jumlah');
        return [
            'items' => $items,
            'sub_total' => $sub_total,
            'total' => $total,
            'asuransi' => $asuransi_name,
            'asuransi_total' => $asuransi,
        ];
    }

    public function allinFCLCount(Order $order)
    {
        $orders = Order::where('job',$order->job)->get();
        $asuransi = 0;
        $admin = 0;
        $items = array();
        $asuransi_name = '';
        foreach ($orders as $idx => $tar ) {
            $koli = 0;
            $koli += $tar->bttb->sum('qty');
            if($tar->asuransi=='ADA EXC'){
                if (!is_null($tar->asuransi_id)) {
                    $asuransi = ($tar->asuransiInfo->rate/100) * $tar->pertanggungan;
                    $asuransi_name = $tar->asuransiInfo->nama;
                    $admin = $tar->asuransiInfo->admin;
                }
            }

            $items[$idx]['keterangan'] = $tar->tarif->kondisiInfo->nama.', '.$tar->tarif->dari_lokasi->nama.' - '.$tar->tarif->tujuan_lokasi->nama;
            $items[$idx]['invoice'] = $tar->invoice ?? '-';
            $items[$idx]['kapal'] = $tar->jadwal_kapal->kapal->nama.' Voy '.$tar->jadwal_kapal->voyage;
            $items[$idx]['tujuan'] = $tar->tarif->tujuan_lokasi->nama;
            $items[$idx]['barang'] = $tar->barang->nama;
            $items[$idx]['customer'] = $tar->tarif->customer->nama;
            $items[$idx]['alamat'] = $tar->tarif->customer->alamat;
            $items[$idx]['kota'] = $tar->tarif->customer->kota;
            $items[$idx]['koli'] = 1;
            $items[$idx]['container'] = $tar->container;
            $items[$idx]['job'] = $tar->job.'-'.sprintf('%02d',$tar->no_job);
            $items[$idx]['si'] = 'Cont '.$tar->tarif->shipmentInfo->nama;
            $items[$idx]['tarif'] = (round($tar->tarif->tarif * $this->ppn))+ $tar->tarif->tarif;
            $items[$idx]['asuransi'] = $asuransi_name;
            $items[$idx]['asuransi_total'] = $asuransi + $admin;
            $items[$idx]['sub_total'] = $items[$idx]['tarif'];
            $items[$idx]['total'] = $items[$idx]['tarif'] + ($asuransi + $admin) + $tar->tagihan->sum('jumlah');
        }
        return [
            'items' => $items
        ];
    }

    public function allinLCL(Order $order)
    {
        $orders = Order::where('job',$order->job)->get();
        $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
        $asuransi = 0;
        $admin = 0;
        $sub_total = 0;
        $items = array();
        $asuransi_name = '';
        foreach ($orders->groupBy('tarif_id') as $idx => $tar ) {
            $koli = 0;
            $jumlah = 0;
            foreach ($tar as $or ) {
                $koli += $or->bttb->sum('qty');
                $jumlah += $or->bttb->sum('vol');
                if($or->asuransi=='ADA EXC'){
                    if (!is_null($or->asuransi_id)) {
                        $asuransi += ($or->asuransiInfo->rate/100) * $or->pertanggungan;
                        $asuransi_name = $or->asuransiInfo->nama;
                        $admin += $or->asuransiInfo->admin;
                    }
                }
            }
            $items[$idx]['keterangan'] = $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama;
            $items[$idx]['koli'] = $koli;
            $items[$idx]['jumlah'] = round($jumlah,2);
            $items[$idx]['jumlah_cont'] = $tar->count();
            $items[$idx]['si'] = 'Cont '.$tar->first()->tarif->shipmentInfo->nama;
            $items[$idx]['sub_total'] = ((($tar->first()->tarif->tarif * max(1, round($jumlah, 2)))) * $this->ppn) + ($tar->first()->tarif->tarif * round($jumlah,2));
            $items[$idx]['tarif'] = $items[$idx]['sub_total'] / round($jumlah,2);
            $sub_total += $items[$idx]['sub_total'];
        }
        $asuransi += $admin;
        if ($asuransi>0&&$order->tipe_asuransi=='job') {
            $asuransi = round((($order->asuransiInfo->rate/100) * $order->pertanggungan + $order->asuransiInfo->admin));
        }
        $total = $sub_total + $asuransi + $cas->sum('jumlah');
        return [
            'items' => $items,
            'sub_total' => $sub_total,
            'total' => $total,
            'asuransi' => $asuransi_name,
            'asuransi_total' => $asuransi,
        ];
    }

    public function FCL(Order $order, $type = 0)
    {
        $orders = Order::where('job',$order->job)->get();
        $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
        $asuransi = 0;
        $admin = 0;
        $doc = 0;
        $doc_count = 0;
        $doc_total = 0;
        $sub_total = 0;
        $validate = array();
        $items = array();
        $asuransi_name = '';
        $keterangan = '';
        foreach ($orders->groupBy('tarif_id') as $idx => $tar ) {
            $koli = 0;
            $doc = 0;
            if ($tar->first()->tarif->kondisi==1||$tar->first()->tarif->kondisi==6) {
                $tars = $tar->first()->tarif->kondisi;
                $doc = 500000;
                $doc_total += $tar->count() * 500000;
                $doc_count += $tar->count();
            }
            foreach ($tar as $or ) {
                $koli += $or->bttb->sum('qty');
                if($or->asuransi=='ADA EXC'){
                    if(is_null($or->asuransi_id)){
                        array_push($validate,'Asuransi Job '.$or->job.'-'.sprintf('%02d',$or->no_job).' belum diinput!');
                    }
                    if (!is_null($or->asuransi_id)) {
                        $asuransi += ($or->asuransiInfo->rate/100) * $or->pertanggungan;
                        $asuransi_name = $or->asuransiInfo->nama;
                        $admin += $or->asuransiInfo->admin;
                    }
                }
                if(is_null($or->tarif->customer->nik)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NIK Belum diinput!');
                }
                if(is_null($or->tarif->customer->npwp)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NPWP Belum diinput!');
                }
            }
            $keterangan .= $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama.'; ';
            $items[$idx]['keterangan'] = $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama;
            $items[$idx]['koli'] = $tar->count();
            $items[$idx]['jumlah'] = $tar->count();
            $items[$idx]['jumlah_cont'] = $tar->count();
            $items[$idx]['si'] = 'Cont '.$tar->first()->tarif->shipmentInfo->nama;
            $items[$idx]['tarif'] = $tar->first()->tarif->tarif - $doc;
            $items[$idx]['sub_total'] = ($tar->first()->tarif->tarif - $doc) * $tar->count();
            $sub_total += ($tar->first()->tarif->tarif - $doc) * $tar->count();
        }
        $sub_total += $doc_total;
        $asuransi += $admin;
        $asuransi = round($asuransi);
        if ($asuransi>0&&$order->tipe_asuransi=='job') {
            $asuransi = round((($order->asuransiInfo->rate/100) * $order->pertanggungan + $order->asuransiInfo->admin));
        }
        if($doc_total>0){
            $pph = $doc_total * $this->pph;
        }else{
            $pph = $sub_total * $this->pph;
        }
        $ppn = round($sub_total * $this->ppn);
        
        $total = (int)$sub_total + $asuransi + $ppn + $cas->sum('jumlah');
       

        return [
            'items' => $items,
            'sub_total' => $sub_total,
            'doc_count' => $doc_count,
            'doc_total' => $doc_total,
            'ppn' => $ppn,
            'pph' => $pph,
            'admin' => $admin,
            'total' => $total,
            'asuransi' => $asuransi_name,
            'asuransi_total' => $asuransi,
            'validate' => $validate,
            'keterangan' => $keterangan,
        ];
    }

    public function LCL(Order $order)
    {
        $orders = Order::where('job',$order->job)->get();
        $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
        $asuransi = 0;
        $admin = 0;
        $doc = 0;
        $doc_count = 0;
        $doc_total = 0;
        $sub_total = 0;
        $validate = array();
        $items = array();
        $asuransi_name = '';
        $keterangan = '';
        foreach ($orders->groupBy('tarif_id') as $idx => $tar ) {
            $doc = 0;
            $koli = 0;
            $jumlah = 0;
            if ($tar->first()->tarif->kondisi==1||$tar->first()->tarif->kondisi==6) {
                $doc = 500000;
                $doc_total += $tar->count() * 500000;
                $doc_count += $tar->count();
            }
            foreach ($tar as $or ) {
                $koli += $or->bttb->sum('qty');
                $jumlah += $or->bttb->sum('vol');
                if($or->asuransi=='ADA EXC'){
                    if(is_null($or->asuransi_id)){
                        array_push($validate,'Asuransi Job '.$or->job.'-'.sprintf('%02d',$or->no_job).' belum diinput!');
                    }
                    if (!is_null($or->asuransi_id)) {
                        $asuransi += ($or->asuransiInfo->rate/100) * $or->pertanggungan;
                        $asuransi_name = $or->asuransiInfo->nama;
                        $admin += $or->asuransiInfo->admin;
                    }
                }
                if(is_null($or->tarif->customer->nik)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NIK Belum diinput!');
                }
                if(is_null($or->tarif->customer->npwp)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NPWP Belum diinput!');
                }
            }
            $keterangan .= $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama.'; ';
            $items[$idx]['keterangan'] = $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama;
            $items[$idx]['koli'] = $koli;
            $items[$idx]['jumlah'] = round($jumlah,2);
            $items[$idx]['jumlah_cont'] = $tar->count();
            $items[$idx]['si'] = 'M3 '.$tar->first()->tarif->shipmentInfo->nama;
            $items[$idx]['tarif'] = $tar->first()->tarif->tarif;
            $items[$idx]['sub_total'] = $tar->first()->tarif->tarif * max(1, round($jumlah, 2));
            $sub_total += $tar->first()->tarif->tarif * max(1, round($jumlah, 2))    ;
        }
        $sub_total += $doc_total;
        $asuransi += $admin;
        if ($asuransi>0&&$order->tipe_asuransi=='job') {
            $asuransi = round((($order->asuransiInfo->rate/100) * $order->pertanggungan + $order->asuransiInfo->admin));
        }
        if($doc_total>0){
            $pph = $doc_total * $this->pph;
        }else{
            $pph = $sub_total * $this->pph;
        }
        $ppn = round($sub_total * $this->ppn);
        $total = round($sub_total )+ $asuransi + $ppn + $cas->sum('jumlah');
        return [
            'items' => $items,
            'sub_total' => $sub_total,
            'doc_count' => $doc_count,
            'doc_total' => $doc_total,
            'ppn' => $ppn,
            'pph' => $pph,
            'admin' => $admin,
            'total' => $total,
            'asuransi' => $asuransi_name,
            'asuransi_total' => $asuransi,
            'validate' => $validate,
            'keterangan' => $keterangan,
        ];
    }

        public function tonaseLCL(Order $order)
    {
        $orders = Order::where('job',$order->job)->get();
        $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
        $asuransi = 0;
        $admin = 0;
        $doc = 0;
        $doc_count = 0;
        $doc_total = 0;
        $sub_total = 0;
        $validate = array();
        $items = array();
        $asuransi_name = '';
        $keterangan = '';
        foreach ($orders->groupBy('tarif_id') as $idx => $tar ) {
            $doc = 0;
            $koli = 0;
            $jumlah = 0;
            if ($tar->first()->tarif->kondisi==1||$tar->first()->tarif->kondisi==6) {
                $doc = 500000;
                $doc_total += $tar->count() * 500000;
                $doc_count += $tar->count();
            }
            foreach ($tar as $or ) {
                $koli += $or->bttb->sum('qty');
                $jumlah += $or->bttb->sum('berat');
                if($or->asuransi=='ADA EXC'){
                    if(is_null($or->asuransi_id)){
                        array_push($validate,'Asuransi Job '.$or->job.'-'.sprintf('%02d',$or->no_job).' belum diinput!');
                    }
                    if (!is_null($or->asuransi_id)) {
                        $asuransi += ($or->asuransiInfo->rate/100) * $or->pertanggungan;
                        $asuransi_name = $or->asuransiInfo->nama;
                        $admin += $or->asuransiInfo->admin;
                    }
                }
                if(is_null($or->tarif->customer->nik)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NIK Belum diinput!');
                }
                if(is_null($or->tarif->customer->npwp)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NPWP Belum diinput!');
                }
            }
            $keterangan .= $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama.'; ';
            $items[$idx]['keterangan'] = $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama;
            $items[$idx]['koli'] = $koli . ' ' . $or->tarif->satuan_inv;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
            $items[$idx]['jumlah'] = round($jumlah,2);
            $items[$idx]['jumlah_cont'] = $tar->count();
            $items[$idx]['si'] = 'Tonase '.$tar->first()->tarif->shipmentInfo->nama;
            $items[$idx]['tarif'] = $tar->first()->tarif->tarif;
            $items[$idx]['sub_total'] = $tar->first()->tarif->tarif * max(1, round($jumlah, 2));
            $sub_total += $tar->first()->tarif->tarif * max(1, round($jumlah, 2))    ;
        }
        $sub_total += $doc_total;
        $asuransi += $admin;
        if ($asuransi>0&&$order->tipe_asuransi=='job') {
            $asuransi = round((($order->asuransiInfo->rate/100) * $order->pertanggungan + $order->asuransiInfo->admin));
        }
        if($doc_total>0){
            $pph = $doc_total * $this->pph;
        }else{
            $pph = $sub_total * $this->pph;
        }
        $ppn = round($sub_total * $this->ppn);
        $total = round($sub_total )+ $asuransi + $ppn + $cas->sum('jumlah');
        return [
            'items' => $items,
            'sub_total' => $sub_total,
            'doc_count' => $doc_count,
            'doc_total' => $doc_total,
            'ppn' => $ppn,
            'pph' => $pph,
            'admin' => $admin,
            'total' => $total,
            'asuransi' => $asuransi_name,
            'asuransi_total' => $asuransi,
            'validate' => $validate,
            'keterangan' => $keterangan,
        ];
    }

    public function qtyLCL(Order $order)
    {
        $orders = Order::where('job',$order->job)->get();
        $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
        $asuransi = 0;
        $admin = 0;
        $doc = 0;
        $doc_count = 0;
        $doc_total = 0;
        $sub_total = 0;
        $validate = array();
        $items = array();
        $asuransi_name = '';
        $keterangan = '';
        foreach ($orders->groupBy('tarif_id') as $idx => $tar ) {
            $doc = 0;
            $koli = 0;
            $jumlah = 0;
            if ($tar->first()->tarif->kondisi==1||$tar->first()->tarif->kondisi==6) {
                $doc = 500000;
                $doc_total += $tar->count() * 500000;
                $doc_count += $tar->count();
            }
            foreach ($tar as $or ) {
                $koli += $or->bttb->sum('qty');
                $jumlah += $or->bttb->sum('qty');
                if($or->asuransi=='ADA EXC'){
                    if(is_null($or->asuransi_id)){
                        array_push($validate,'Asuransi Job '.$or->job.'-'.sprintf('%02d',$or->no_job).' belum diinput!');
                    }
                    if (!is_null($or->asuransi_id)) {
                        $asuransi += ($or->asuransiInfo->rate/100) * $or->pertanggungan;
                        $asuransi_name = $or->asuransiInfo->nama;
                        $admin += $or->asuransiInfo->admin;
                    }
                }
                if(is_null($or->tarif->customer->nik)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NIK Belum diinput!');
                }
                if(is_null($or->tarif->customer->npwp)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NPWP Belum diinput!');
                }
            }
            $keterangan .= $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama.'; ';
            $items[$idx]['keterangan'] = $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama;
            $items[$idx]['koli'] = $koli . ' ' . $or->tarif->satuan_inv;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
            $items[$idx]['jumlah'] = round($jumlah,2);
            $items[$idx]['jumlah_cont'] = $tar->count();
            $items[$idx]['si'] = 'Koli '.$tar->first()->tarif->shipmentInfo->nama;
            $items[$idx]['tarif'] = $tar->first()->tarif->tarif;
            $items[$idx]['sub_total'] = $tar->first()->tarif->tarif * max(1, round($jumlah, 2));
            $sub_total += $tar->first()->tarif->tarif * max(1, round($jumlah, 2))    ;
        }
        $sub_total += $doc_total;
        $asuransi += $admin;
        if ($asuransi>0&&$order->tipe_asuransi=='job') {
            $asuransi = round((($order->asuransiInfo->rate/100) * $order->pertanggungan + $order->asuransiInfo->admin));
        }
        if($doc_total>0){
            $pph = $doc_total * $this->pph;
        }else{
            $pph = $sub_total * $this->pph;
        }
        $ppn = round($sub_total * $this->ppn);
        $total = round($sub_total )+ $asuransi + $ppn + $cas->sum('jumlah');
        return [
            'items' => $items,
            'sub_total' => $sub_total,
            'doc_count' => $doc_count,
            'doc_total' => $doc_total,
            'ppn' => $ppn,
            'pph' => $pph,
            'admin' => $admin,
            'total' => $total,
            'asuransi' => $asuransi_name,
            'asuransi_total' => $asuransi,
            'validate' => $validate,
            'keterangan' => $keterangan,
        ];
    }

      public function M3LCL(Order $order)
    {
        $orders = Order::where('job',$order->job)->get();
        $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
        $asuransi = 0;
        $admin = 0;
        $doc = 0;
        $doc_count = 0;
        $doc_total = 0;
        $sub_total = 0;
        $validate = array();
        $items = array();
        $asuransi_name = '';
        $keterangan = '';
        foreach ($orders->groupBy('tarif_id') as $idx => $tar ) {
            $doc = 0;
            $koli = 0;
            $jumlah = 0;
            if ($tar->first()->tarif->kondisi==1||$tar->first()->tarif->kondisi==6) {
                $doc = 500000;
                $doc_total += $tar->count() * 500000;
                $doc_count += $tar->count();
            }
            foreach ($tar as $or ) {
                $koli += $or->bttb->sum('qty');
                $jumlah += $or->bttb->sum('vol');
                if($or->asuransi=='ADA EXC'){
                    if(is_null($or->asuransi_id)){
                        array_push($validate,'Asuransi Job '.$or->job.'-'.sprintf('%02d',$or->no_job).' belum diinput!');
                    }
                    if (!is_null($or->asuransi_id)) {
                        $asuransi += ($or->asuransiInfo->rate/100) * $or->pertanggungan;
                        $asuransi_name = $or->asuransiInfo->nama;
                        $admin += $or->asuransiInfo->admin;
                    }
                }
                if(is_null($or->tarif->customer->nik)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NIK Belum diinput!');
                }
                if(is_null($or->tarif->customer->npwp)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NPWP Belum diinput!');
                }
            }
            $keterangan .= $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama.'; ';
            $items[$idx]['keterangan'] = $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama;
            $items[$idx]['koli'] = $koli . ' ' . $or->tarif->satuan_inv;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
            $items[$idx]['jumlah'] = round($jumlah,2);
            $items[$idx]['jumlah_cont'] = $tar->count();
            $items[$idx]['si'] = 'M3 '.$tar->first()->tarif->shipmentInfo->nama;
            $items[$idx]['tarif'] = $tar->first()->tarif->tarif;
            $items[$idx]['sub_total'] = $tar->first()->tarif->tarif * max(1, round($jumlah, 2));
            $sub_total += $tar->first()->tarif->tarif * max(1, round($jumlah, 2))    ;
        }
        $sub_total += $doc_total;
        $asuransi += $admin;
        if ($asuransi>0&&$order->tipe_asuransi=='job') {
            $asuransi = round((($order->asuransiInfo->rate/100) * $order->pertanggungan + $order->asuransiInfo->admin));
        }
        if($doc_total>0){
            $pph = $doc_total * $this->pph;
        }else{
            $pph = $sub_total * $this->pph;
        }
        $ppn = round($sub_total * $this->ppn);
        $total = round($sub_total )+ $asuransi + $ppn + $cas->sum('jumlah');
        return [
            'items' => $items,
            'sub_total' => $sub_total,
            'doc_count' => $doc_count,
            'doc_total' => $doc_total,
            'ppn' => $ppn,
            'pph' => $pph,
            'admin' => $admin,
            'total' => $total,
            'asuransi' => $asuransi_name,
            'asuransi_total' => $asuransi,
            'validate' => $validate,
            'keterangan' => $keterangan,
        ];
    }
}
