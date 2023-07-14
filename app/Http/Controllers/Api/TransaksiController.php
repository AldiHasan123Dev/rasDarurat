<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransaksiResource;
use App\Models\Jurnal;
use App\Models\Order;
use App\Models\TemplateJurnal;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
        $start = request('start');
        $limit = request('limit');
        $data = Transaksi::all()->sortBy('invoice')->sortBy('no')->skip($start)->take($limit);
        $count = Transaksi::select('id')->count();
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

        if(request('tanggal_kirim') && is_null($transaksi->tanggal_kirim)){
            $template = TemplateJurnal::find(8);
            $no = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
            $nomor = sprintf('%02d',date('m')).'-'.sprintf('%03d',$no).'/'.date('y');
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
                    Jurnal::create([
                        'coa_id' => $item->coa_debit_id,
                        'order_id' => $transaksi->order_id,
                        'nomor' => $nomor,
                        'nama' => $name,
                        'debit' => round($transaksi->total),
                        'credit' => 0,
                        'tipe' => 'JNL',
                        'no' => $no,
                        'created_at' => $request->tanggal_kirim,
                    ]);
                }
                if($item->coa_credit_id==86){
                    Jurnal::create([
                        'coa_id' => $item->coa_credit_id,
                        'order_id' => $transaksi->order_id,
                        'nomor' => $nomor,
                        'nama' => $name,
                        'credit' => round($transaksi->sub_total),
                        'debit' => 0,
                        'tipe' => 'JNL',
                        'no' => $no,
                        'created_at' => $request->tanggal_kirim,
                    ]);
                }
                if($item->coa_credit_id==56){
                    Jurnal::create([
                        'coa_id' => $item->coa_credit_id,
                        'order_id' => $transaksi->order_id,
                        'nomor' => $nomor,
                        'nama' => $name,
                        'credit' => round($transaksi->ppn),
                        'debit' => 0,
                        'tipe' => 'JNL',
                        'no' => $no,
                        'created_at' => $request->tanggal_kirim,
                    ]);
                }
                if($item->coa_credit_id==25){
                    if($transaksi->asuransi>0){
                        Jurnal::create([
                            'coa_id' => $item->coa_credit_id,
                            'order_id' => $transaksi->order_id,
                            'nomor' => $nomor,
                            'nama' => 'Asuransi '.$transaksi->orderInfo->asuransiInfo->nama,
                            'credit' => round($transaksi->asuransi),
                            'debit' => 0,
                            'tipe' => 'JNL',
                            'no' => $no,
                            'created_at' => $request->tanggal_kirim,
                        ]);
                    }
                }
                if($item->coa_credit_id==28){
                    foreach ($transaksi->jobs as $job) {
                        if($job->tagihan->count()>0){
                            foreach ($job->tagihan as $tagihan) {
                                Jurnal::create([
                                    'coa_id' => $item->coa_credit_id,
                                    'order_id' => $tagihan->order_id,
                                    'nomor' => $nomor,
                                    'nama' => $tagihan->nama,
                                    'credit' => round($tagihan->jumlah),
                                    'debit' => 0,
                                    'tipe' => 'JNL',
                                    'no' => $no,
                                    'created_at' => $request->tanggal_kirim,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return response('success');
    }

    public function updateBupot(Request $request)
    {
        $data = $request->all();
        $data['masa_bupot'] = $request->masa_bupot_bulan.' '.$request->masa_bupot_tahun;
        Transaksi::find($request->id)->update($data);
        return response('success');
    }
}
