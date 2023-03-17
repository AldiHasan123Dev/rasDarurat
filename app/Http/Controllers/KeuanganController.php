<?php

namespace App\Http\Controllers;

use App\Models\NSFP;
use App\Models\Order;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class KeuanganController extends Controller
{
    public function order()
    {
        return view('admin.keuangan.order');
    }

    public function ba_kembali()
    {
        return view('admin.keuangan.ba_kembali');
    }

    public function customer()
    {
        return view('admin.keuangan.customer');
    }

    public function pre_invoice()
    {
        return view('admin.keuangan.pre_invoice');
    }

    public function invoice()
    {
        return view('admin.keuangan.invoice');
    }

    public function generateInvoice(Request $request, Order $order)
    {
        $data = $request->all();
        if ($request->tipe_invoice=='cont') {
            $orders = Order::where('job',$order->job)->get();
            foreach ($orders as $item ) {
                $nsfp = NSFP::where('available',1)->orderBy('nomor','asc')->first();
                $no = Transaksi::max('order') + 1;
                $roman_numerals = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"); // daftar angka Romawi
                $month_number = date("n"); // mengambil nomor bulan dari tanggal
                $month_roman = $roman_numerals[$month_number]; // mengambil angka Romawi yang sesuai
                $invoice = sprintf('%04d',$no).'/RAS/'.$month_roman.'/'.date('y');
                $doc = 0;
                $asuransi = 0;
                $admin = 0;
                if ($item->tarif->kondisi==1||$item->tarif->kondisi==6) {
                    $doc = 500000;
                    $pph = $doc * 0.02;
                }else{
                    $pph = $item->tarif->tarif * 0.02;
                }
                if (!is_null($item->asuransi_id)) {
                    $asuransi = ($item->asuransiInfo->rate/100) * $item->pertanggungan;
                    $admin = $item->asuransiInfo->admin;
                }
                $data['pembayar_id'] = $item->tarif->customer_id;
                $data['invoice'] = $invoice;
                $data['nsfp'] = $nsfp->nomor;
                $data['order'] = $no;
                $data['job'] = $item->job.'-'.sprintf('%02d',$item->no_job);
                $data['order_id'] = $item->id;
                $data['sub_total'] = $item->tarif->tarif + $doc;
                $data['ppn'] = $item->tarif->tarif*0.011;
                $data['asuransi'] = $asuransi;
                $data['admin'] = $admin;
                $data['pph'] = $pph;
                $data['tagihan'] = $item->tagihan->sum('jumlah');
                $data['total'] = $admin + $asuransi + $data['sub_total'] + $data['ppn'] + $data['tagihan'];
                $data['tujuan'] = $item->tarif->tujuan_lokasi->nama;
                $data['keterangan'] = $item->tarif->kondisiInfo->nama.' , '.$item->tarif->dari_lokasi->nama.' - '.$item->tarif->tujuan_lokasi->nama;
                Transaksi::create($data);
                $item->update([
                    'invoice' => $invoice,
                    'nsfp' => $nsfp->nomor,
                    'invoice_date' => date('Y-m-d')
                ]);
                $nsfp->update([
                    'available' => 0,
                    'invoice' => $invoice
                ]);
            }
        }else{
            $nsfp = NSFP::where('available',1)->orderBy('nomor','asc')->first();
            if (!$nsfp) {
                return back();
            }
            $no = Transaksi::max('order') + 1;
            $roman_numerals = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"); // daftar angka Romawi
            $month_number = date("n"); // mengambil nomor bulan dari tanggal
            $month_roman = $roman_numerals[$month_number]; // mengambil angka Romawi yang sesuai
            $invoice = sprintf('%04d',$no).'/RAS/'.$month_roman.'/'.date('y');
            $data['invoice'] = $invoice;
            $data['nsfp'] = $nsfp->nomor;
            $data['order'] = $no;
            $data['order_id'] = $order->id;
            Transaksi::create($data);
            Order::where('job',$order->job)->update([
                'invoice' => $invoice,
                'nsfp' => $nsfp->nomor,
                'invoice_date' => date('Y-m-d')
            ]);
            $nsfp->update([
                'available' => 0,
                'invoice' => $invoice
            ]);
        }

        return back()->with('success','Invoice berhasil dibuat');
    }

    public function laporanPPn()
    {
        $transaksi = Transaksi::all()->sortBy('created_at');
        return view('admin.keuangan.laporan_ppn', compact('transaksi'));
    }

    public function invoiceTable()
    {
        $limit = request('length');
        $start = request('start') * request('length');
        $data = Transaksi::query()
                ->join('customers','customers.id','=','transaksi.pembayar_id')
                ->select('transaksi.*');
        $count = $data->count();
        return Datatables::of($data->offset($start)->limit($limit))
            ->order(function ($query) {
                $query->orderBy('invoice');
            })
            ->addColumn('invoice', function($data){
                return $data->invoice;
            })
            ->addColumn('created_at', function($data){
                return date('d/m/Y', strtotime($data->created_at)) ?? '-';
            })
            ->addColumn('job', function($data){
                return $data->job;
            })
            ->addColumn('no_job', function($data){
                return $data->job.'-01/'.sprintf('%02d',$data->jobs->count());
            })
            ->addColumn('pembayar', function($data){
                return $data->pembayar->nama ?? '-';
            })
            ->addColumn('tanggal_kirim', function($data){
                return is_null($data->tanggal_kirim)?'-': date('d/m/Y', strtotime($data->tanggal_kirim));
            })
            ->addColumn('total', function($data){
                return number_format($data->total) ?? '-';
            })
            ->setTotalRecords($count)
            ->toJson();

    }
}
