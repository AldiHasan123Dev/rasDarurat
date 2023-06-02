<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Jurnal;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class JurnalController extends Controller
{
    public function index()
    {
        return view('admin.jurnal.index');
    }

    public function store(Request $request)
    {
        // [1] pembayar
        // [2] pengirim
        // [3] penerima
        // [4] pelayaran
        // [5] customer
        $data = $request->all();
        if($data['jurnal_id']){
            Jurnal::whereIn('id',json_decode($data['jurnal_id']))->delete();
        }
        if($data['order_id']){
            $order = Order::find($data['order_id']);
            $pembayar = $order->tarif->customer->nama ?? '-';
            $penerima = $order->penerima->nama ?? '-';
            $pengirim = $order->pengirim->nama ?? '-';
            $pelayaran = $order->jadwal_kapal->pelayaran->nama ?? '-';
            $customer = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->customer->nama;
        }
        for ($i=0; $i < count($data['debit_coa_id']); $i++) {
            if ($data['debit_coa_id'][$i] && $data['debit_nomor'][$i] && $data['debit_name'][$i] && $data['debit_amount'][$i]) {
                $name = $data['debit_name'][$i];
                if($data['order_id']){
                    $name = str_replace('[1]',$pembayar,$name);
                    $name = str_replace('[2]',$pengirim,$name);
                    $name = str_replace('[3]',$penerima,$name);
                    $name = str_replace('[4]',$pelayaran,$name);
                    $name = str_replace('[5]',$customer,$name);
                }
                Jurnal::create([
                    'coa_id' => $data['debit_coa_id'][$i],
                    'order_id' => $data['order_id'],
                    'nomor' => $data['debit_nomor'][$i],
                    'nama' => $name,
                    'debit' => $data['debit_amount'][$i],
                ]);
            }
        }
        for ($i=0; $i < count($data['credit_coa_id']); $i++) {
            if ($data['credit_coa_id'][$i] && $data['credit_nomor'][$i] && $data['credit_name'][$i] && $data['credit_amount'][$i]) {
                $name = $data['credit_name'][$i];
                if($data['order_id']){
                    $name = str_replace('[1]',$pembayar,$name);
                    $name = str_replace('[2]',$pengirim,$name);
                    $name = str_replace('[3]',$penerima,$name);
                    $name = str_replace('[4]',$pelayaran,$name);
                    $name = str_replace('[5]',$customer,$name);
                }
                Jurnal::create([
                    'coa_id' => $data['credit_coa_id'][$i],
                    'order_id' => $data['order_id'],
                    'nomor' => $data['credit_nomor'][$i],
                    'nama' => $name,
                    'credit' => $data['credit_amount'][$i],
                ]);
            }
        }

        return back()->with('success','Data berhasil disimpan');
    }

    public function create()
    {
        return view('admin.jurnal.create');
    }

    public function update(Jurnal $jurnal, Request $request)
    {
        $data = $request->all();
        $jurnal->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Jurnal $jurnal)
    {
        $jurnal->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Jurnal::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('debit', function ($data) {
                return $data->debit == 0 ? '-' : number_format($data->debit);
            })
            ->addColumn('credit', function ($data) {
                return $data->credit == 0 ? '-' : number_format($data->credit);
            })
            ->addColumn('coa_id', function ($data) {
                return $data->coa->nama;
            })
            ->addColumn('code', function ($data) {
                return $data->coa->kode;
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
}
