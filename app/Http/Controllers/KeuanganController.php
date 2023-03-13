<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function order()
    {
        return view('admin.keuangan.order');
    }
}
