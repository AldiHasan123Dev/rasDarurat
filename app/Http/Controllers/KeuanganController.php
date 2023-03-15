<?php

namespace App\Http\Controllers;

use App\Models\NSFP;
use App\Models\Order;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function order()
    {
        return view('admin.keuangan.order');
    }

    public function generateInvoice(Request $request, Order $order)
    {
        $data = $request->all();
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
        Transaksi::create($data);
        Order::where('job',$order->job)->update([
            'invoice' => $invoice,
            'nsfp' => $nsfp->nomor,
            'invoice_date' => date('Y-m-d')
        ]);
        $nsfp->update([
            'available' => 0
        ]);

        return back()->with('success','Invoice berhasil dibuat');
    }

    public function laporanPPn()
    {
        $transaksi = Transaksi::all()->sortBy('created_at');
        return view('admin.keuangan.laporan_ppn', compact('transaksi'));
    }
}
