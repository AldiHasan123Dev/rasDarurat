<?php

namespace App\Http\Controllers;

use App\Models\Pelayaran;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;


class TarifController extends Controller
{
    public function datatable()
    {
        $data = Pelayaran::all();

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                return '';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

}
