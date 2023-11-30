<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Kendaraan;
use App\Models\Lokasi;
use App\Models\Order;
use App\Models\Pelayaran;
use App\Models\Sopir;
use App\Models\Tarif;
use App\Models\User;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function pelayaran()
    {
        $year = request('year') ?? date('Y');
        $data = Pelayaran::all();
        return view('admin.laporan.pelayaran', compact('data','year'));
    }
    public function tujuan()
    {
        $tarif = Tarif::pluck('tujuan')->toArray();
        $id = array_unique($tarif);
        $year = request('year') ?? date('Y');
        $data = Lokasi::whereIn('id',$id)->get();
        return view('admin.laporan.tujuan', compact('data','year'));
    }
    public function customer()
    {
        $tarif = Tarif::pluck('customer_id')->toArray();
        $id = array_unique($tarif);
        $year = request('year') ?? date('Y');
        $data = Customer::whereIn('id',$id)->get();
        return view('admin.laporan.customer', compact('data','year'));
    }
    public function marketing()
    {
        $year = request('year') ?? date('Y');
        $data = User::where('role_id',2)->whereHas('marketing')->get();
        return view('admin.laporan.marketing', compact('data','year'));
    }
    public function cs()
    {
        $year = request('year') ?? date('Y');
        $data = User::where('role_id',2)->whereHas('cs')->get();
        return view('admin.laporan.cs', compact('data','year'));
    }
    public function trucking()
    {
        $year = request('year') ?? date('Y');
        $data = Kendaraan::where('milik','!=','vendor')->where('is_active',1)->get();
        return view('admin.laporan.trucking', compact('data','year'));
    }
    public function sopir()
    {
        $year = request('year') ?? date('Y');
        $data = Sopir::where('milik','!=','vendor')->get();
        return view('admin.laporan.sopir', compact('data','year'));
    }
    public function omset()
    {
        $year = request('year') ?? date('Y');
        $month = request('month') ?? date('m');
        $tipe = request('tipe') ?? 'inv';
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $job = $year.sprintf('%02d',$month);
        if($tipe=='inv'){
            $data = Order::whereMonth('invoice_date',$month)->whereYear('invoice_date',$year)->get();
        }else{
            $data = Order::where('job','like',$job.'%')->get();
        }
        return view('admin.laporan.omset', compact('data','year','months','month','tipe'));
    }
    public function invoice()
    {
        $year = request('year') ?? date('Y');
        $data = Order::whereNull('invoice')->get();
        $data = OrderResource::collection($data);
        return view('admin.laporan.preinvoice', compact('data','year'));
    }
}
