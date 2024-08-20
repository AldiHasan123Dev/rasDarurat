<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPPNExport;
use App\Exports\PajakExport;
use App\Http\Resources\LaporanPPNResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\TransaksiResource;
use App\Imports\InvoiceImport;
use App\Models\Customer;
use App\Models\Lokasi;
use App\Models\NSFP;
use App\Models\Order;
use App\Models\Transaksi;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;

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

    public function fee_cust()
    {
        $data = Order::where('komisi','>',0)->get();
        return view('admin.keuangan.fee_cust', compact('data'));
    }

    public function fee_cust_bayar(Request $request)
    {
        $id = explode(',',$request->order_id);
        if(count($id)==0){
            return back()->with('danger','Harap checklist item!');
        }
        if(request('komisi_print')){
            Order::whereIn('id',$id)->update([
                'komisi_print' => $request->komisi_print
            ]);
        }
        $orders = Order::whereIn('id',$id)->get();
        $order = $orders->first();
        return view('admin.cetak.fee_cust', compact('orders','order','id'));
    }

    public function pre_invoice()
    {
        $data1_id = [];
        $data1 = Order::whereHas('tarif', function($q){
            $q->whereIn('kondisi',[1,6]);
        })->whereHas('jadwal_kapal', function($q){
            $q->whereNotNull('td');
        })->whereNull('invoice')->pluck('id');
        foreach ($data1 as $item ) {
            array_push($data1_id,$item);
        }

        $data2 = Order::whereHas('tarif', function($q){
            $q->whereIn('kondisi',[5,7]);
            $q->whereHas('customer', function($qu){
                $qu->where('ba_kembali',1);
            });
        })->whereHas('jadwal_kapal', function($q){
            $q->whereNotNull('td');
        })->whereNull('invoice')->whereNotNull('ba_kembali')->get();
        foreach ($data2 as $item ) {
            $cek = Order::where('job',$item->job)->whereNotNull('ba_kembali')->count();
            $cek1 = Order::where('job',$item->job)->count();
            if($cek==$cek1){
                array_push($data1_id,$item->id);
            }
        }

        $data3 = Order::whereHas('tarif', function($q){
            $q->whereIn('kondisi',[5,7]);
            $q->whereHas('customer', function($qu){
                $qu->where('ba_kembali',0);
            });
        })->whereHas('jadwal_kapal', function($q){
            $q->whereNotNull('td');
        })->whereNull('invoice')->get();

        $data1 = Order::whereIn('id', $data1_id)->get();
        $data1 = OrderResource::collection($data1);
        $data2 = OrderResource::collection($data3);

        return view('admin.keuangan.pre_invoice2', compact('data1','data2'));
    }

    public function pre_invoice1()
    {
        return view('admin.keuangan.pre_invoice1');
    }

    public function invoice()
    {
        return view('admin.keuangan.invoice');
    }

    public function generateInvoice(Request $request, Order $order)
    {
        $setting = Setting::find(1);
        $data = $request->all();
        $customer_id = $order->tarif->customer->id;
        $nsfp = null;
        if($customer_id!=318){
            $nsfp = NSFP::where('available',1)->orderBy('nomor','asc')->first();
            if (!$nsfp) {
                return back()->with('danger','Tidak ada NSFP yang tersedia! Harap input NSFP terlebih dahulu');
            }
        }
        $no = Transaksi::whereYear('created_at',date('Y'))->max('order') + 1;
        $roman_numerals = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"); // daftar angka Romawi
        $month_number = date("n"); // mengambil nomor bulan dari tanggal
        $month_roman = $roman_numerals[$month_number]; // mengambil angka Romawi yang sesuai
        $invoice = sprintf('%04d',$no).'/'.$setting->short_name.'/'.$month_roman.'/'.date('y');
        $data['invoice'] = $invoice;
        $data['nsfp'] = $nsfp->nomor ?? null;
        $data['order'] = $no;
        $data['order_id'] = $order->id;
        $data['created_at'] = date('Y-m-d');
        Transaksi::create($data);
        Order::where('job',$order->job)->update([
            'invoice' => $invoice,
            'nsfp' => $nsfp->nomor ?? null,
            'invoice_date' => date('Y-m-d'),
            'lock_biaya' => 1
        ]);
        if($nsfp){
            $nsfp->update([
                'available' => 0,
                'invoice' => $invoice
            ]);
        }

        return back()->with('success','Invoice berhasil dibuat');
    }

    public function laporanPPn()
    {
        $start = request('start') ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $end = request('end') ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        $transaksi = Transaksi::whereBetween('created_at',[$start,$end])->orderBy('created_at')->get();
        // dd($transaksi);
        $data = TransaksiResource::collection($transaksi);
        $faktur = NSFP::where('available',1)->first();
        $no = '-';
        if($faktur){
            $no = '010'.substr($faktur->nomor,3,50);
        }
        $customers = Customer::pluck('nama');
        $lokasi = Lokasi::pluck('nama');
        $ppn = $transaksi->sum('ppn');
        $pph = $transaksi->sum('pph');
        $total = $transaksi->sum('total');
        $sub_total = $transaksi->sum('sub_total');
        return view('admin.keuangan.laporan_ppn', compact('transaksi','data','start','end','no','customers','lokasi','ppn','pph','total','sub_total'));
    }

    public function PPNExport()
    {
        return Excel::download(new LaporanPPNExport(request('start'),request('end')), 'laporan.xlsx');
    }

    public function PajakExport()
    {
        return Excel::download(new PajakExport(request('start'),request('end')), 'laporan_pajak.csv',\Maatwebsite\Excel\Excel::CSV,['Content-Type' => 'text/csv',]);
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
                $query->orderBy('order');
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
            ->setFilteredRecords($count)
            ->toJson();

    }

    public function import(Request $request)
    {
        Excel::import(new InvoiceImport, $request->file);

        return back()->with('success', 'All good!');
    }
}
