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
        for ($i=0; $i < count($data['debit_coa_id']); $i++) {
            if ($data['name'][$i] && $data['amount'][$i]) {
                $name = $data['name'][$i];
                if($data['order_id'][$i]){
                    $order = Order::find($data['order_id'][$i]);
                    $pembayar = $order->tarif->customer->nama ?? '-';
                    $penerima = $order->penerima->nama ?? '-';
                    $pengirim = $order->pengirim->nama ?? '-';
                    $pelayaran = $order->jadwal_kapal->pelayaran->nama ?? '-';
                    $customer = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->customer->nama;
                    $name = str_replace('[1]',$pembayar,$name);
                    $name = str_replace('[2]',$pengirim,$name);
                    $name = str_replace('[3]',$penerima,$name);
                    $name = str_replace('[4]',$pelayaran,$name);
                    $name = str_replace('[5]',$customer,$name);
                }
                if ($data['debit_coa_id'][$i] && $data['credit_coa_id'][$i]) {
                    Jurnal::create([
                        'coa_id' => $data['debit_coa_id'][$i],
                        'order_id' => $data['order_id'][$i],
                        'nomor' => $data['nomor'],
                        'nama' => $name,
                        'debit' => $data['amount'][$i],
                    ]);
                    Jurnal::create([
                        'coa_id' => $data['credit_coa_id'][$i],
                        'order_id' => $data['order_id'][$i],
                        'nomor' => $data['nomor'],
                        'nama' => $name,
                        'credit' => $data['amount'][$i],
                    ]);
                }else{
                    if($data['debit_coa_id'][$i]){
                        Jurnal::create([
                            'coa_id' => $data['debit_coa_id'][$i],
                            'order_id' => $data['order_id'][$i],
                            'nomor' => $data['nomor'],
                            'nama' => $name,
                            'debit' => $data['amount'][$i],
                        ]);
                    }
                    if($data['credit_coa_id'][$i]){
                        Jurnal::create([
                            'coa_id' => $data['credit_coa_id'][$i],
                            'order_id' => $data['order_id'][$i],
                            'nomor' => $data['nomor'],
                            'nama' => $name,
                            'credit' => $data['amount'][$i],
                        ]);
                    }
                }
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
