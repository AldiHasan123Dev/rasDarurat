<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CetakController extends Controller
{
    public function suratJalan()
    {
        return view('admin.cetak.surat_jalan');
    }

    public function pickOrder()
    {
        $pengirim = Customer::where('tipe','pengirim')->get();
        $penerima = Customer::where('tipe','penerima')->get();
        return view('admin.cetak.pick_order', compact('pengirim','penerima'));
    }
}
