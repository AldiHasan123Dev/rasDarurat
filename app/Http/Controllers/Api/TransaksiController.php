<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransaksiResource;
use App\Models\Jurnal;
use App\Models\COA;
use App\Models\Order;
use App\Models\TemplateJurnal;
use App\Models\Transaksi;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{

    protected $sno;
    public function __construct()
    {
        $setting = Setting::find(1);
        $this->sno = $setting->short_name;
    }
    public function index()
    {
        $start = request('start');
        $limit = request('limit');
        $query = Transaksi::query();
        $count = Transaksi::select('id')->count();
        if(request('start_date') && request('end_date')){
            $query->whereBetween('created_at', [request('start_date'), request('end_date')]);
            $count = Transaksi::whereBetween('created_at', [request('start_date'), request('end_date')])->select('id')->count();
        }
        $data = $query->orderBy('invoice')->skip($start)->take($limit)->get();
        $data = TransaksiResource::collection($data);
        return response([
            'start' => $start + $limit,
            'count' => $count,
            'data' => $data
        ]);
    }

    public function update(Request $request)
    {
        $transaksi = Transaksi::find($request->id);
        $no = $transaksi->order;
        $roman_numerals = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"); // daftar angka Romawi
        $month_number = date("n", strtotime($request->created_at)); // mengambil nomor bulan dari tanggal
        $month_roman = $roman_numerals[$month_number]; // mengambil angka Romawi yang sesuai
        $invoice = sprintf('%04d',$no).'/RAS/'.$month_roman.'/'.date('y', strtotime($request->created_at));


        Order::where('job',$transaksi->job)->update([
            'invoice' => $invoice,
            'invoice_date' => $request->created_at
        ]);
        $transaksi->update([
            'invoice' => $invoice,
            'tanggal_kirim' => $request->tanggal_kirim,
            'created_at' => $request->created_at
        ]);

        $is_asuransi_cont = true;
        $is_asuransi_cont_fill = true;

        if(request('tanggal_kirim')){
            if(is_null($transaksi->jurnal_piutang)){
                $month = date('m');
                $month1 = date('m',strtotime($transaksi->created_at));
                if($month1!=$month){
                    $carbon = new Carbon($transaksi->created_at);
                    $date = $carbon->endOfMonth()->toDateString();
                    $no = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m',strtotime($date)))->whereYear('created_at',date('Y',strtotime($date)))->max('no') + 1;
                    $nomor = sprintf('%02d',date('m',strtotime($date))).'-'.sprintf('%03d',$no).'/'.($this->sno == 'ALB' ? 'ALB/' : '').date('y',strtotime($date));
                }else{
                    $no = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
                    $nomor = sprintf('%02d',date('m')).'-'.sprintf('%03d',$no).'/'.($this->sno == 'ALB' ? 'ALB/' : '').date('y');
                    $date = date('Y-m-d');
                }
                $template = TemplateJurnal::find(8);
                foreach ($template->template_items as $key => $item) {
                    $name = $item->keterangan;
                    $order = Order::find($transaksi->order_id);
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
                    if($item->coa_debit_id){
                        $debit = round($transaksi->sub_total) + round($transaksi->ppn);
                        foreach ($transaksi->jobs as $job) {
                            if($job->asuransi=='ADA EXC'){
                                if (!is_null($job->asuransi_id)) {
                                    if($job->tipe_asuransi=='cont'){
                                        $debit += (($job->asuransiInfo->rate/100) * $job->pertanggungan);
                                        $debit += $job->asuransiInfo->admin;
                                    }else{
                                        if($is_asuransi_cont){
                                            $debit += (($job->asuransiInfo->rate/100) * $job->pertanggungan);
                                            $debit += $job->asuransiInfo->admin;
                                            $is_asuransi_cont = false;
                                        }
                                    }
                                }
                            }
                            if($job->tagihan->count()>0){
                                foreach ($job->tagihan as $tagihan) {
                                    $debit += round($tagihan->jumlah);
                                }
                            }
                        }
                        Jurnal::create([
                            'invoice' => $order->invoice ?? null,
                            'nopol' => $order->nopol ?? null,
                            'relasi' => $nomor,
                            'container' => $order->container ?? null,
                            'coa_id' => $item->coa_debit_id,
                            'order_id' => $transaksi->order_id,
                            'nomor' => $nomor,
                            'nama' => $name,
                            'debit' => $debit,
                            'credit' => 0,
                            'tipe' => 'JNL',
                            'no' => $no,
                            'created_at' => $date,
                        ]);
                    }
                    $c86 = COA::where('coa_ras',86)->first()->id ?? 86;
                    $c56 = COA::where('coa_ras',56)->first()->id ?? 56;
                    $c25 = COA::where('coa_ras',25)->first()->id ?? 25;
                    $c28 = COA::where('coa_ras',28)->first()->id ?? 28;
                    if($item->coa_credit_id==$c86){
                        Jurnal::create([
                            'invoice' => $order->invoice ?? null,
                            'nopol' => $order->nopol ?? null,
                            'relasi' => $nomor,
                            'container' => $order->container ?? null,
                            'coa_id' => $item->coa_credit_id,
                            'order_id' => $transaksi->order_id,
                            'nomor' => $nomor,
                            'nama' => $name,
                            'credit' => round($transaksi->sub_total),
                            'debit' => 0,
                            'tipe' => 'JNL',
                            'no' => $no,
                            'created_at' => $date,
                        ]);
                    }
                    if($item->coa_credit_id==$c56){
                        Jurnal::create([
                            'invoice' => $order->invoice ?? null,
                            'relasi' => $nomor,
                            'nopol' => $order->nopol ?? null,
                            'container' => $order->container ?? null,
                            'coa_id' => $item->coa_credit_id,
                            'order_id' => $transaksi->order_id,
                            'nomor' => $nomor,
                            'nama' => $name,
                            'credit' => round($transaksi->ppn),
                            'debit' => 0,
                            'tipe' => 'JNL',
                            'no' => $no,
                            'created_at' => $date,
                        ]);
                    }
                    if($item->coa_credit_id==$c25){
                        foreach ($transaksi->jobs as $job) {
                            if($job->asuransi=='ADA EXC'){
                                if (!is_null($job->asuransi_id)) {
                                    if($job->tipe_asuransi=='cont'){
                                        $asuransi = ($job->asuransiInfo->rate/100) * $job->pertanggungan;
                                        $admin = $job->asuransiInfo->admin;
                                        Jurnal::create([
                                            'invoice' => $order->invoice ?? null,
                                            'nopol' => $order->nopol ?? null,
                                            'relasi' => $nomor,
                                            'container' => $order->container ?? null,
                                            'coa_id' => $item->coa_credit_id,
                                            'order_id' => $job->id,
                                            'nomor' => $nomor,
                                            'nama' => 'Asuransi '.$job->asuransiInfo->nama,
                                            'credit' => round($asuransi + $admin),
                                            'debit' => 0,
                                            'tipe' => 'JNL',
                                            'no' => $no,
                                            'created_at' => $date,
                                        ]);
                                    }else{
                                        if($is_asuransi_cont_fill){
                                            $asuransi = ($job->asuransiInfo->rate/100) * $job->pertanggungan;
                                            $admin = $job->asuransiInfo->admin;
                                            $is_asuransi_cont_fill = false;
                                            Jurnal::create([
                                                'invoice' => $order->invoice ?? null,
                                                'nopol' => $order->nopol ?? null,
                                                'relasi' => $nomor,
                                                'container' => $order->container ?? null,
                                                'coa_id' => $item->coa_credit_id,
                                                'order_id' => $job->id,
                                                'nomor' => $nomor,
                                                'nama' => 'Asuransi '.$job->asuransiInfo->nama,
                                                'credit' => round($asuransi + $admin),
                                                'debit' => 0,
                                                'tipe' => 'JNL',
                                                'no' => $no,
                                                'created_at' => $date,
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if($item->coa_credit_id==$c28){
                        foreach ($transaksi->jobs as $job) {
                            if($job->tagihan->count()>0){
                                foreach ($job->tagihan as $tagihan) {
                                    Jurnal::create([
                                        'invoice' => $order->invoice ?? null,
                                        'nopol' => $order->nopol ?? null,
                                        'container' => $order->container ?? null,
                                        'coa_id' => $item->coa_credit_id,
                                        'order_id' => $tagihan->order_id,
                                        'nomor' => $nomor,
                                        'relasi' => $nomor,
                                        'nama' => $tagihan->nama,
                                        'credit' => round($tagihan->jumlah),
                                        'debit' => 0,
                                        'tipe' => 'JNL',
                                        'no' => $no,
                                        'created_at' => $date,
                                    ]);
                                }
                            }
                        }
                    }
                }


                $transaksi->update([
                    'jurnal_piutang' => $nomor
                ]);
                $transaksi->jobs()->update([
                    'jurnal_piutang' => $nomor
                ]);
            }
        }

        return response('success');
    }

    public function updateBupot(Request $request)
    {
        $data = $request->all();
        $data['masa_bupot'] = $request->masa_bupot_bulan.' '.$request->masa_bupot_tahun;
        $trx = Transaksi::find($request->id);
        $order = Order::where('invoice', $trx->invoice)->get();
        $no = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
        $nomor = sprintf('%02d',date('m')).'-'.sprintf('%03d',$no).'/'. ($this->sno == 'ALB' ? 'ALB/' : '').date('y',strtotime(date('Y').'-'.sprintf('%02d',date('m')).'-01'));

        $c52 = COA::where('coa_ras',52)->first()->id ?? 52;
        $c46 = COA::where('coa_ras',46)->first()->id ?? 46;
        if(is_null($trx->jurnal_bupot)){
            // Cek apakah data order kosong
$isExternal = $order->isEmpty();

// Data jurnal pertama (debit)
$dataJurnalDebit = [
    'coa_id' => $c52,
    'order_id' => $trx->order_id,
    'nomor' => $nomor,
    'relasi' => $nomor,
    'nama' => 'PPh 23 Dibayar Dimuka ' . $trx->pembayar->nama,
    'debit' => $data['bupot'],
    'credit' => 0,
    'tipe' => 'JNL',
    'no' => $no,
    'created_at' => date('Y-m-d'),
];

// Data jurnal kedua (kredit)
$dataJurnalKredit = [
    'coa_id' => $c46,
    'order_id' => $trx->order_id,
    'nomor' => $nomor,
    'relasi' => $nomor,
    'nama' => 'Pelunasan Piutang Ekspedisi/Pph 23 Dibayar Dimuka ' . $trx->pembayar->nama,
    'debit' => 0,
    'credit' => $data['bupot'],
    'tipe' => 'JNL',
    'no' => $no,
    'created_at' => date('Y-m-d'),
];

// Tentukan field invoice atau invoice_external
if ($isExternal) {
    $dataJurnalDebit['invoice_external'] = $trx->invoice;
    $dataJurnalKredit['invoice_external'] = $trx->invoice;
} else {
    $dataJurnalDebit['invoice'] = $trx->invoice;
    $dataJurnalKredit['invoice'] = $trx->invoice;
}

// Simpan ke database
Jurnal::create($dataJurnalDebit);
Jurnal::create($dataJurnalKredit);

        }else{
            Jurnal::where('nomor',$trx->jurnal_bupot)->where('debit','>',0)->first()->update([
                'order_id' => $trx->order_id,
                'debit' => $data['bupot'],
                'nama' => 'PPh 23 Dibayar Dimuka '.$trx->pembayar->nama,
            ]);
            Jurnal::where('nomor',$trx->jurnal_bupot)->where('credit','>',0)->first()->update([
                'order_id' => $trx->order_id,
                'credit' => $data['bupot'],
                'nama' => 'Pelunasan Piutang Ekspedisi/Pph 23 Dibayar Dimuka '.$trx->pembayar->nama,
            ]);
        }

        $data['jurnal_bupot'] = $nomor;
        $trx->update($data);

        return response('success');
    }
}
