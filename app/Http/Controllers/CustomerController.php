<?php

namespace App\Http\Controllers;

use App\Imports\CustomerImport;
use App\Models\Customer;
use App\Models\JadwalKapal;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Satuan;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        $users = User::all();
        $jadwal_kapal = JadwalKapal::where('is_active',1)->get();
        $customer = Customer::pluck('nama','id');
        $lokasi = Lokasi::pluck('nama','id');
        $satuan = Satuan::pluck('nama','id');
        $kondisi = Kondisi::pluck('nama','id');
        $shipment = Shipment::pluck('nama','id');

        $kapal = array();
        foreach ($jadwal_kapal as $id => $item ) {
            $kapal[$item->id] = $item->kapal->nama.'('.$item->voyage.') || '.$item->pelayaran->nama.' || ETD '.date('d/m/y',strtotime($item->etd)).' || '.$item->rute;
        }
        return view('admin.customer.index', compact('customers','users','kapal','customer','lokasi','satuan','kondisi','shipment'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        Customer::create($data);
        return back()->with('success','Data berhasil disimpan!');
    }

    public function update(Customer $customer, Request $request)
    {
        $data = $request->all();
        $customer->update($data);
        return back()->with('success','Data berhasil dupdate!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success','Data berhasil dihapus!');
    }

    public function import(Request $request)
    {
        Excel::import(new CustomerImport, $request->file);

        return back()->with('success', 'All good!');
    }
}
