<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EstimasiController extends Controller
{
    public function biaya()
    {
        return view('admin.estimasi.biaya');
    }
}
