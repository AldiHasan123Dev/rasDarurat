<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\OrderTrucking;
use App\Models\TransaksiTrucking;
use Illuminate\Http\Request;

class TransaksiTruckingController extends Controller
{
    public function update(Request $request)
    {
        $roman_numerals = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"); // daftar angka Romawi
        $month_number = date("n",strtotime($request->created_at)); // mengambil nomor bulan dari tanggal
        $month_roman = $roman_numerals[$month_number]; // mengambil angka Romawi yang sesuai

        $trucking = TransaksiTrucking::find($request->transaksi_id);
        if($trucking->tipe=='R1'){
            $no1 = $trucking->order_r1;
            $invoice = sprintf('%03d',$no1).'/'.$month_roman.'/'.date('y', strtotime($request->created_at));
        }else if($trucking->tipe=='R2'){
            $no2 = $trucking->order_r2;
            $invoice = sprintf('%03d',$no2).'/RAS-LT/'.$month_roman.'/'.date('y', strtotime($request->created_at));
        }else{
            $no3= $trucking->order_vendor;
            $invoice = sprintf('%03d',$no3).'/VENDOR-'.$month_roman.'/'.date('y', strtotime($request->created_at));
        }

        $trucking->update([
            'tgl_invoice' => $request->created_at,
            'invoice' => $invoice,
        ]);


        OrderTrucking::whereIn('id',json_decode($trucking->order_id))->update([
            'tgl_invoice' => $request->created_at,
            'invoice' => $invoice,
        ]);

        if($trucking->jurnal_piutang){
            Jurnal::where('nomor',$trucking->jurnal_piutang)->update([
                'invoice' => $invoice
            ]);
        }
        if($trucking->jurnal_hutang){
            Jurnal::where('nomor',$trucking->jurnal_hutang)->update([
                'invoice' => $invoice
            ]);
        }

        return response('Success');
    }
}
