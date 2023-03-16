<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function update(Request $request)
    {
        $transaksi = Transaksi::find($request->id);
        $no = $transaksi->order;
        $roman_numerals = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"); // daftar angka Romawi
        $month_number = date("n", strtotime($request->created_at)); // mengambil nomor bulan dari tanggal
        $month_roman = $roman_numerals[$month_number]; // mengambil angka Romawi yang sesuai
        $invoice = sprintf('%04d',$no).'/RAS/'.$month_roman.'/'.date('y', strtotime($request->created_at));

        $transaksi->update([
            'invoice' => $invoice,
            'tanggal_kirim' => $request->tanggal_kirim,
            'created_at' => $request->created_at
        ]);

        return response('success');
    }
}
