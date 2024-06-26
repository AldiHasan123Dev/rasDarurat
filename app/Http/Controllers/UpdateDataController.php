<?php

namespace App\Http\Controllers;

use App\Imports\updateJurnal;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UpdateDataController extends Controller
{
    public function jurnal(Request $request)
    {
        $file = $request->file('file');
        Excel::import(new updateJurnal, $file);
        return back()->with('success','Data berhasil di update');
    }
}
