<?php

namespace App\Http\Controllers;

use App\Models\BTTB;
use App\Models\Customer;
use App\Models\JadwalKapal;
use App\Models\Lokasi;
use App\Models\NSFP;
use App\Models\Order;
use App\Models\Pengirim;
use App\Models\Tagihan;
use App\Models\Tarif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CetakController extends Controller
{
    public function suratJalan()
    {
        $penerima = Customer::get();
        // $pdf = PDF::loadView('pdf.contoh');
        // return $pdf->stream('document.pdf');
        return view('admin.cetak.surat_jalan', compact('penerima'));
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
        $jadwal_kapal = JadwalKapal::join('kapal','kapal.id','=','jadwal_kapal.kapal_id')->select('jadwal_kapal.*')->where('jadwal_kapal.is_active',1)->get();
        return view('admin.cetak.pick_order', compact('pengirim','penerima','jadwal_kapal'));
    }

    public function packingList()
    {
        $order = Order::find(request('order_id'));
        if (!$order) {
            return redirect()->route('order.index');
        }

        $data = Order::where('job',$order->job)->orderBy('tarif_id')->get();
        return view('admin.cetak.packing_list', compact('order','data'));
    }

    public function packingListKubikasi()
    {
        $order = Order::find(request('order_id'));
        if (!$order) {
            return redirect()->route('order.index');
        }

        $data = Order::where('job',$order->job)->orderBy('tarif_id')->get();
        return view('admin.cetak.packing_list_kubikasi', compact('order','data'));
    }

    public function bttb()
    {
        $order = Order::find(request('order_id'));
        if (!$order) {
            return redirect()->route('order.index');
        }

        if (!$order->tarif) {
            return redirect()->route('order.index')->with('danger','Master Tarif Kosong! Harap di edit terlebih dahulu');
        }

        $data = BTTB::where('order_id',$order->id)->get();
        return view('admin.cetak.bttb', compact('order','data'));
    }

    public function bttbKubikasi()
    {
        $order = Order::find(request('order_id'));
        if (!$order) {
            return redirect()->route('order.index');
        }

        if (!$order->tarif) {
            return redirect()->route('order.index')->with('danger','Master Tarif Kosong! Harap di edit terlebih dahulu');
        }

        $data = BTTB::where('order_id',$order->id)->get();
        return view('admin.cetak.bttb_kubikasi', compact('order','data'));
    }

    public function shipment()
    {
        $id = request('jadwal_kapal_id');
        $jadwal_kapal = JadwalKapal::find($id);
        $lokasi = request('tujuan');
        $tujuan = Lokasi::find($lokasi);
        $pengirim = Pengirim::all();
        $orders = Order::where('jadwal_kapal_id', $id)->get();

        $jadwal_kapals = JadwalKapal::all()->where('is_active',0);
        $pelayaran = $jadwal_kapals->pluck('pelayaran_id')->toArray();
        $lokasi = Tarif::whereIn('pelayaran_id',$pelayaran)->pluck('tujuan')->toArray();
        $data_tarif_lokasi = array_unique($lokasi);
        $data_lokasi = Lokasi::whereIn('id',$data_tarif_lokasi)->get();
        return view('admin.cetak.shipment', compact('orders','jadwal_kapal','tujuan','pengirim','jadwal_kapals','data_lokasi'));
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
        if ($type=='F') {
            $allin = [];
            if ($order->tarif->customer->all_in==1) {
                $allin = $this->allinFCL($order);
            }
            $invoice = $this->FCL($order);
            $validate = $this->FCL($order)['validate'];
        }else{
            $items = array();
            $kategori = 0;
            $jumlah = 0;
            $asuransi = 0;
            $admin = 0;
            $doc = 0;
            $sub_total_s = 0;
            $asuransi_name = '';
            $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
            $validate = array();
            $nama = 'vol';
            $tarif = $order->tarif->tarif;
            $price = $tarif * $kategori * $jumlah;
            $asuransi += $admin;
            foreach ($orders->groupBy('tarif_id') as $idx => $tar) {
                $items[$idx]['keterangan'] = $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama;
                $items[$idx]['koli'] = 0;
                $items[$idx]['jumlah'] = $tar->count();
                $items[$idx]['si'] = 'Cont '.$tar->first()->tarif->shipmentInfo->nama;
                if ($tar->first()->tarif->kondisi==1||$tar->first()->tarif->kondisi==6) {
                    $doc = $tar->count() * 500000;
                }
                if ($order->tarif->customer->all_in==1) {
                    $items[$idx]['tarif'] = (($tar->first()->tarif->tarif * $tar->count()) * 0.011) + $doc;
                    $items[$idx]['sub_total'] = (($tar->first()->tarif->tarif * $tar->count()) * 0.011) + $doc;
                }else{
                    $items[$idx]['tarif'] = $tar->first()->tarif->tarif;
                    $items[$idx]['sub_total'] = $tar->first()->tarif->tarif * $tar->count();
                }
                $sub_total_s = $items[$idx]['sub_total'];
                $price += $tar->first()->tarif->tarif * $tar->count();
                foreach ($tar as $or ) {
                    $items[$idx]['koli'] += $or->bttb->sum('qty');
                    if (is_null($or->berat)||$or->berat<=0) {
                        $kategori+=$or->bttb->sum('vol');
                    }else{
                        $kategori+=$or->bttb->sum('berat');
                    }
                    $jumlah += $or->bttb->sum('qty');
                    if (!is_null($or->asuransi_id)) {
                        $asuransi += ($or->asuransiInfo->rate/100) * $or->pertanggungan;
                        $asuransi_name = $or->asuransiInfo->nama;
                        $admin += $or->asuransiInfo->admin;
                    }
                    if($or->asuransi=='ADA EXC'){
                        if(is_null($or->asuransi_id)){
                            array_push($validate,'Asuransi Job '.$or->job.'-'.sprintf('%02d',$or->no_job).' belum diinput!');
                        }
                    }
                    if(is_null($or->tarif->customer->nik)){
                        array_push($validate,'Customer '.$or->tarif->customer->nama.' NIK Belum diinput!');
                    }
                    if(is_null($or->tarif->customer->npwp)){
                        array_push($validate,'Customer '.$or->tarif->customer->nama.' NPWP Belum diinput!');
                    }
                }
            }
            $nsfp = NSFP::where('available',1)->orderBy('nomor','asc')->first();
            if(is_null($nsfp)){
                array_push($validate,'NSFP belum ada!');
            }
            if ($order->tarif->kondisi==1||$order->tarif->kondisi==6) {
                $doc = $orders->count() * 500000;
            }
        }

        $validate = array_unique($validate);
        $br = Order::with('barang')->where('job',$order->job)->get()->pluck('barang.nama')->toArray();
        $br = array_unique($br);
        $nama_barang = implode(',',$br);
        return view('admin.cetak.invoice',compact('order','orders','cas','validate','nama_barang','allin','invoice'));
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

        $type = strtoupper(strtolower($order->tarif->shipmentInfo->nama[0]));
        if ($type=='F') {
            $nama = 'Cont';
            $kategori = $orders->count().' Cont';
            $jumlah = $orders->count();
            $price = 0;
            $asuransi = 0;
            $admin = 0;
            $doc = 0;
            $asuransi_name = '';
            $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
            $validate = array();
            $items = array();
            foreach ($orders->groupBy('tarif_id') as $idx => $tar ) {
                $items['keterangan'][$idx] = $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama;
                $items['koli'][$idx] = 0;
                $items['jumlah'][$idx] = $tar->count();
                $items['si'][$idx] = 'Cont '.$tar->first()->tarif->shipmentInfo->nama;
                $items['jumlah'][$idx] = $tar->count();
                if ($tar->first()->tarif->kondisi==1||$tar->first()->tarif->kondisi==6) {
                    $doc = $tar->count() * 500000;
                }
                if ($order->tarif->customer->all_in==1) {
                    $items['tarif'][$idx] = (($tar->first()->tarif->tarif * $tar->count()) * 0.011) + $doc;
                    $items['sub_total'][$idx] = (($tar->first()->tarif->tarif * $tar->count()) * 0.011) + $doc;
                }else{
                    $items['tarif'][$idx] = $tar->first()->tarif->tarif;
                    $items['sub_total'][$idx] = $tar->first()->tarif->tarif * $tar->count();
                }
                $price += $tar->first()->tarif->tarif * $tar->count();
                foreach ($tar as $or ) {
                    $items['koli'][$idx] += $or->bttb->sum('qty');
                    if (!is_null($or->asuransi_id)) {
                        $asuransi += ($or->asuransiInfo->rate/100) * $or->pertanggungan;
                        $asuransi_name = $or->asuransiInfo->nama;
                        $admin += $or->asuransiInfo->admin;
                    }
                    if($or->asuransi=='ADA EXC'){
                        if(is_null($or->asuransi_id)){
                            array_push($validate,'Asuransi Job '.$or->job.'-'.sprintf('%02d',$or->no_job).' belum diinput!');
                        }
                    }
                    if(is_null($or->tarif->customer->nik)){
                        array_push($validate,'Customer '.$or->tarif->customer->nama.' NIK Belum diinput!');
                    }
                    if(is_null($or->tarif->customer->npwp)){
                        array_push($validate,'Customer '.$or->tarif->customer->nama.' NPWP Belum diinput!');
                    }
                }
            }
            $nsfp = NSFP::where('available',1)->orderBy('nomor','asc')->first();
            if(is_null($nsfp)){
                array_push($validate,'NSFP belum ada!');
            }
            $asuransi += $admin;
        }else{
            $items = array();
            $kategori = 0;
            $jumlah = 0;
            $asuransi = 0;
            $admin = 0;
            $doc = 0;
            $asuransi_name = '';
            $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
            $validate = array();
            $nama = 'vol';
            $tarif = $order->tarif->tarif;
            $price = $tarif * $kategori * $jumlah;
            $asuransi += $admin;
            foreach ($orders->groupBy('tarif_id') as $idx => $tar) {
                $items['keterangan'][$idx] = $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama;
                $items['koli'][$idx] = 0;
                $items['jumlah'][$idx] = $tar->count();
                $items['si'][$idx] = 'Cont '.$tar->first()->tarif->shipmentInfo->nama;
                $items['jumlah'][$idx] = $tar->count();
                if ($tar->first()->tarif->kondisi==1||$tar->first()->tarif->kondisi==6) {
                    $doc = $tar->count() * 500000;
                }
                if ($order->tarif->customer->all_in==1) {
                    $items['tarif'][$idx] = (($tar->first()->tarif->tarif * $tar->count()) * 0.011) + $doc;
                    $items['sub_total'][$idx] = (($tar->first()->tarif->tarif * $tar->count()) * 0.011) + $doc;
                }else{
                    $items['tarif'][$idx] = $tar->first()->tarif->tarif;
                    $items['sub_total'][$idx] = $tar->first()->tarif->tarif * $tar->count();
                }
                $price += $tar->first()->tarif->tarif * $tar->count();
                foreach ($tar as $or ) {
                    $items['koli'][$idx] += $or->bttb->sum('qty');
                    if (is_null($or->berat)||$or->berat<=0) {
                        $kategori+=$or->bttb->sum('vol');
                    }else{
                        $kategori+=$or->bttb->sum('berat');
                    }
                    $jumlah += $or->bttb->sum('qty');
                    if (!is_null($or->asuransi_id)) {
                        $asuransi += ($or->asuransiInfo->rate/100) * $or->pertanggungan;
                        $asuransi_name = $or->asuransiInfo->nama;
                        $admin += $or->asuransiInfo->admin;
                    }
                    if($or->asuransi=='ADA EXC'){
                        if(is_null($or->asuransi_id)){
                            array_push($validate,'Asuransi Job '.$or->job.'-'.sprintf('%02d',$or->no_job).' belum diinput!');
                        }
                    }
                    if(is_null($or->tarif->customer->nik)){
                        array_push($validate,'Customer '.$or->tarif->customer->nama.' NIK Belum diinput!');
                    }
                    if(is_null($or->tarif->customer->npwp)){
                        array_push($validate,'Customer '.$or->tarif->customer->nama.' NPWP Belum diinput!');
                    }
                }
            }
            $nsfp = NSFP::where('available',1)->orderBy('nomor','asc')->first();
            if(is_null($nsfp)){
                array_push($validate,'NSFP belum ada!');
            }
            if ($order->tarif->kondisi==1||$order->tarif->kondisi==6) {
                $doc = $orders->count() * 500000;
            }
        }

        $validate = array_unique($validate);
        $br = Order::with('barang')->where('job',$order->job)->get()->pluck('barang.nama')->toArray();
        $br = array_unique($br);
        $nama_barang = implode(',',$br);
        return view('admin.cetak.invoice_cont',compact('order','orders','nama','kategori','jumlah','doc','price','asuransi','asuransi_name','cas','validate','admin','nama_barang','items'));
    }

    public function allinFCL(Order $order)
    {
        $orders = Order::where('job',$order->job)->get();
        $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
        $asuransi = 0;
        $admin = 0;
        $doc = 0;
        $koli = 0;
        $sub_total = 0;
        $items = array();
        $asuransi_name = '';
        foreach ($orders->groupBy('tarif_id') as $idx => $tar ) {
            if ($tar->first()->tarif->kondisi==1||$tar->first()->tarif->kondisi==6) {
                $doc = $tar->count() * 500000;
            }
            foreach ($tar as $or ) {
                $koli += $or->bttb->sum('qty');
                if (!is_null($or->asuransi_id)) {
                    $asuransi += ($or->asuransiInfo->rate/100) * $or->pertanggungan;
                    $asuransi_name = $or->asuransiInfo->nama;
                    $admin += $or->asuransiInfo->admin;
                }
            }
            $items[$idx]['keterangan'] = $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama;
            $items[$idx]['koli'] = $koli;
            $items[$idx]['jumlah'] = $tar->count();
            $items[$idx]['si'] = 'Cont '.$tar->first()->tarif->shipmentInfo->nama;
            $items[$idx]['tarif'] = ((($tar->first()->tarif->tarif * $tar->count())) * 0.011)+ ($tar->first()->tarif->tarif*$tar->count());
            $items[$idx]['sub_total'] = $items[$idx]['tarif'];
            $sub_total += $items[$idx]['tarif'];
        }
        $asuransi += $admin;
        if ($asuransi>0&&$order->tipe_asuransi=='job') {
            $asuransi = (($order->asuransiInfo->rate/100) * $order->pertanggungan + $order->asuransiInfo->admin);
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

    public function FCL(Order $order)
    {
        $orders = Order::where('job',$order->job)->get();
        $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
        $asuransi = 0;
        $admin = 0;
        $doc = 0;
        $doc_count = 0;
        $doc_total = 0;
        $koli = 0;
        $sub_total = 0;
        $validate = array();
        $items = array();
        $asuransi_name = '';
        foreach ($orders->groupBy('tarif_id') as $idx => $tar ) {
            if ($tar->first()->tarif->kondisi==1||$tar->first()->tarif->kondisi==6) {
                $doc = $tar->count() * 500000;
                $doc_total += $tar->count() * 500000;
                $doc_count += $tar->count();
            }
            foreach ($tar as $or ) {
                $koli += $or->bttb->sum('qty');
                if (!is_null($or->asuransi_id)) {
                    $asuransi += ($or->asuransiInfo->rate/100) * $or->pertanggungan;
                    $asuransi_name = $or->asuransiInfo->nama;
                    $admin += $or->asuransiInfo->admin;
                }
                if($or->asuransi=='ADA EXC'){
                    if(is_null($or->asuransi_id)){
                        array_push($validate,'Asuransi Job '.$or->job.'-'.sprintf('%02d',$or->no_job).' belum diinput!');
                    }
                }
                if(is_null($or->tarif->customer->nik)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NIK Belum diinput!');
                }
                if(is_null($or->tarif->customer->npwp)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NPWP Belum diinput!');
                }
            }
            $items[$idx]['keterangan'] = $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama;
            $items[$idx]['koli'] = $koli;
            $items[$idx]['jumlah'] = $tar->count();
            $items[$idx]['si'] = 'Cont '.$tar->first()->tarif->shipmentInfo->nama;
            $items[$idx]['tarif'] = ($tar->first()->tarif->tarif) - 500000;
            $items[$idx]['sub_total'] = ($tar->first()->tarif->tarif * $tar->count()) - $doc;
            $sub_total += ($tar->first()->tarif->tarif * $tar->count()) - $doc;
        }
        $sub_total += $doc_total;
        $asuransi += $admin;
        if ($asuransi>0&&$order->tipe_asuransi=='job') {
            $asuransi = (($order->asuransiInfo->rate/100) * $order->pertanggungan + $order->asuransiInfo->admin);
        }
        if($doc_total>0){
            $pph = $doc_total * 0.02;
        }else{
            $pph = $sub_total * 0.02;
        }
        $ppn = $sub_total * 0.011;
        $total = $sub_total + $asuransi + $ppn + $cas->sum('jumlah');
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
        ];
    }
}
