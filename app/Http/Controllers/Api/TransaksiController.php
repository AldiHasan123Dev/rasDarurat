<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransaksiResource;
use App\Models\Order;
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

        return response('success');
    }
}
