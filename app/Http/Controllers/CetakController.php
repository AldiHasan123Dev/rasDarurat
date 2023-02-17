<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CetakController extends Controller
{
    public function suratJalan()
    {
        return view('admin.cetak.surat_jalan');
    }
}
