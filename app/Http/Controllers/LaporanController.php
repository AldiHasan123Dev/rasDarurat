<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\COA;
use App\Models\Customer;
use App\Models\Jurnal;
use App\Models\Kendaraan;
use App\Models\Lokasi;
use App\Models\Order;
use App\Models\OrderTrucking;
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
        $count = Order::where('job','LIKE',$year.'%')->count();
        return view('admin.laporan.pelayaran', compact('data','year','count'));
    }
    public function data_rekap_piutang(Request $request){
        // Ambil parameter untuk pagination dari request
      // Filter berdasarkan kolom pencarian
      $searchField = $request->input('searchField');
      $searchString = $request->input('searchString');
  
      // Query data berdasarkan filter dan pagination
      $query = Order::query();
  
      if ($searchField && $searchString) {
          $query->where($searchField, 'like', "%$searchString%");
      }
    // Ambil data dari tabel Jurnals dengan pagination, urutkan berdasarkan 'tgl' descending
    $jurnals = Jurnal::withTrashed()
    ->where('tipe', 'BBM')
    ->whereNull('deleted_at')
    ->where('debit', '!=', 0)
    ->select(
        'invoice',
        \DB::raw('SUM(debit) as total_debit'),
        \DB::raw("GROUP_CONCAT(CONCAT('<div style=\"margin-bottom: 5px; margin-top:5px;\">', DATE_FORMAT(created_at, '%Y-%m-%d'), '</div>') ORDER BY created_at ASC SEPARATOR '') as daftar_tanggal",
        "jurnal.*")
    )
    ->groupBy('invoice')
    ->orderByDesc('invoice')
    ->get();

     $orders = Order::with([
    'order.tarif.customer' => function($query) {
        $query->select('id', 'nama');
    },// Menambahkan relasi transaksi.barang
]);

    }
    public function tujuan()
    {
        $tarif = Tarif::pluck('tujuan')->toArray();
        $id = array_unique($tarif);
        $year = request('year') ?? date('Y');
        $data = Lokasi::whereIn('id',$id)->get();
        $count = Order::where('job','LIKE',$year.'%')->count();
        return view('admin.laporan.tujuan', compact('data','year','count'));
    }
    public function customer()
    {
        $tarif = Tarif::pluck('customer_id')->toArray();
        $id = array_unique($tarif);
        $year = request('year') ?? date('Y');
        $data = Customer::whereIn('id',$id)->get();
        $count = Order::where('job','LIKE',$year.'%')->count();
        return view('admin.laporan.customer', compact('data','year','count'));
    }
    public function omset_customer()
    {
        $tarif = Tarif::pluck('customer_id')->toArray();
        $id = array_unique($tarif);
        $year = request('year') ?? date('Y');
        $data = Customer::whereIn('id',$id)->get();
        return view('admin.laporan.omset_customer', compact('data','year'));
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
            $data = Order::whereMonth('invoice_date',$month)->where('lock_omset','!=',0)->whereYear('invoice_date',$year)->get();
        }else{
            $data = Order::where('job','like',$job.'%')->get();
        }
        $ids = $data->pluck('id')->toArray();
        $coa = COA::where('is_active',1)->get();
        $is_pra = false;
        return view('admin.laporan.omset', compact('is_pra','data','year','months','month','tipe','ids','coa'));
    }
    public function praomset()
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
        $ids = $data->pluck('id')->toArray();
        $coa = COA::where('is_active',1)->get();
        $is_pra = true;
        return view('admin.laporan.pra_omset', compact('is_pra','data','year','months','month','tipe','ids','coa'));
    }
    public function invoice()
    {
        $year = request('year') ?? date('Y');
        $data = Order::whereNull('invoice')->get();
        $data = OrderResource::collection($data);
        return view('admin.laporan.preinvoice', compact('data','year'));
    }
    public function omset_trucking()
    {
        $year = request('year') ?? date('Y');
        $month = request('month') ?? date('m');
        $tipe = request('tipe') ?? 'xpdc';
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        if($tipe=='xpdc'){
            $order_job = Order::whereMonth('invoice_date',$month)->whereYear('invoice_date',$year)->pluck('id')->toArray();
            $orders = OrderTrucking::whereIn('order_id',$order_job)->get();
            $get_id = array();
            foreach($orders as $order_trucking){
                if($order_trucking->order){
                    $tipe_truck = $order_trucking->kendaraan->milik;
                    if($order_trucking->customer->r2 == 1){
                        $tipe_truck = 'R2';
                    }
                    if($order_trucking->customer->r1 == 1){
                        $tipe_truck = 'R1';
                    }
                    if(($order_trucking->order->trucking == 'xpdc' || $order_trucking->order->trucking == 'XPDC') && $tipe_truck == 'R2'){
                        array_push($get_id,$order_trucking->id);
                    }
                }
            }
            $jurnal_id = Jurnal::whereIn('order_trucking_id',$get_id)->whereIn('coa_id',[61,81])->pluck('id')->toArray();
            $data = OrderTrucking::whereIn('id',$get_id)->get()->groupBy('seal');
        }else{
            $order_id = Jurnal::whereNotNull('order_trucking_id')->whereMonth('created_at',$month)->whereYear('created_at',$year)->where('coa_id',87)->pluck('order_trucking_id')->toArray();
            $jurnal_id = Jurnal::whereNotNull('order_trucking_id')->whereMonth('created_at',$month)->whereYear('created_at',$year)->where('coa_id',87)->pluck('id')->toArray();
            $data = OrderTrucking::whereIn('id',$order_id)->get()->groupBy('seal');
        }
        return view('admin.laporan.omset_trucking', compact('data','year','months','month','tipe','jurnal_id'));
    }
}
