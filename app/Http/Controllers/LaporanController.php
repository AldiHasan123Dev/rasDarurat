<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lokasi;
use App\Models\Pelayaran;
use App\Models\Tarif;
use App\Models\User;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function pelayaran()
    {
        $data = Pelayaran::all();
        return view('admin.laporan.pelayaran', compact('data'));
    }
    public function tujuan()
    {
        $tarif = Tarif::pluck('tujuan')->toArray();
        $id = array_unique($tarif);
        $data = Lokasi::whereIn('id',$id)->get();
        return view('admin.laporan.tujuan', compact('data'));
    }
    public function customer()
    {
        $tarif = Tarif::pluck('customer_id')->toArray();
        $id = array_unique($tarif);
        $data = Customer::whereIn('id',$id)->get();
        return view('admin.laporan.customer', compact('data'));
    }
    public function marketing()
    {
        $data = User::where('role_id',2)->whereHas('marketing')->get();
        return view('admin.laporan.marketing', compact('data'));
    }
    public function cs()
    {
        $data = User::where('role_id',2)->whereHas('cs')->get();
        return view('admin.laporan.cs', compact('data'));
    }
}
