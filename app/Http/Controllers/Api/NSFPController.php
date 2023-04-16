<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\NSFP;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class NSFPController extends Controller
{
    public function generate(Request $request)
    {
        $no = str_replace(' ','',$request->nomor);
        $res = explode('.',$no);
        $depan = $res[0].'.'.$res[1].'.'.$res[2].'.';
        $res = (int)end($res);
        for ($i=0; $i < $request->jumlah; $i++) {
            $num = $res + $i;
            NSFP::create([
                'nomor' => $depan.''.sprintf('%08d',$num),
                'available' => 1
            ]);
        }

        return response('success');
    }

    public function store(Request $request)
    {
        $faktur = $request->nsfp;
        $no = '050'.substr($faktur,3,50);
        $nsfp = NSFP::where('nomor',$no)->first();
        $pembayar = Customer::where('nama',$request->pembayar_id)->first();
        if (!$pembayar) {
            return response(false);
        }
        $nsfp->update([
            'invoice' => $request->invoice,
            'nomor' => $faktur,
            'available' => 0
        ]);
        $transaksi = Transaksi::create([
            'tipe_invoice' => 'sewa_gudang',
            'order_id' => null,
            'pembayar_id' => $pembayar->id,
            'job' => null,
            'invoice' => $request->invoice,
            'nsfp' => $faktur,
            'keterangan' => $request->keterangan,
            'tujuan' => $request->tujuan,
            'sub_total' => str_replace([',','.'],'',$request->sub_total),
            'ppn' => str_replace([',','.'],'',$request->ppn),
            'total' => str_replace([',','.'],'',$request->total),
            'pph' => str_replace([',','.'],'',$request->pph),
            'order' => null
        ]);

        return response(true);
    }
}
