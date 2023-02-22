<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\JadwalKapal;
use Illuminate\Http\Request;

class CetakController extends Controller
{
    public function suratJalan()
    {
        $penerima = Customer::get();
        return view('admin.cetak.surat_jalan', compact('penerima'));
    }

    public function pickOrder()
    {
        $pengirim = Customer::get();
        $penerima = Customer::get();
        $jadwal_kapal = JadwalKapal::all();
        return view('admin.cetak.pick_order', compact('pengirim','penerima','jadwal_kapal'));
    }
}
