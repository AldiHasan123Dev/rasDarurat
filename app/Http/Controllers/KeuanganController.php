<?php

namespace App\Http\Controllers;

use App\Models\NSFP;
use App\Models\Order;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function order()
    {
        return view('admin.keuangan.order');
    }

    public function generateInvoice(Request $request, Order $order)
    {
        $nsfp = NSFP::where('available',1)->orderBy('nomor','asc')->first();
        if (!$nsfp) {
            return back();
        }
        $no = 1;
        $month = date('n'); // Nomor bulan dalam format angka
        $roman_numeral = str_repeat('X', intval(($month-1)/10)) . str_repeat('V', intval(($month-1)/5)%2) . str_repeat('I', ($month-1)%5); // Konversi nomor bulan menjadi angka Romawi
        $invoice = $no.'/RAS/'.$roman_numeral.'/'.date('y');
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
}
