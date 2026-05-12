<?php

namespace App\Http\Controllers;

use App\Imports\CustomerImport;
use App\Imports\CustomerUpdateImport;
use App\Models\Customer;
use App\Models\JadwalKapal;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Pelayaran;
use App\Models\Satuan;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index()
    {
        $jadwal_kapal = JadwalKapal::whereHas('pelayaran')->where('is_active',1)->get();
        $customer = Customer::pluck('nama','id');
        $lokasi = Lokasi::pluck('nama','id');
        $satuan = Satuan::pluck('nama','id');
        $kondisi = Kondisi::pluck('nama','id');
        $shipment = Shipment::pluck('nama','id');
        $pelayaran = Pelayaran::pluck('nama','id');

        return view('admin.customer.index', compact('pelayaran','customer','lokasi','satuan','kondisi','shipment'));
    }

        public function data_customer()
    {
        $jadwal_kapal = JadwalKapal::whereHas('pelayaran')->where('is_active',1)->get();
        $customer = Customer::pluck('nama','id');
        $lokasi = Lokasi::pluck('nama','id');
        $satuan = Satuan::pluck('nama','id');
        $kondisi = Kondisi::pluck('nama','id');
        $shipment = Shipment::pluck('nama','id');
        $pelayaran = Pelayaran::pluck('nama','id');

        return view('admin.customer.data-customers', compact('pelayaran','customer','lokasi','satuan','kondisi','shipment'));
    }

    public function tarif()
    {
        $jadwal_kapal = JadwalKapal::whereHas('pelayaran')->where('is_active',1)->get();
        $customer = Customer::get(['nama','id']);
        $lokasi = Lokasi::pluck('nama','id');
        $satuan = Satuan::pluck('nama','id');
        $kondisi = Kondisi::pluck('nama','id');
        $shipment = Shipment::pluck('nama','id');
        $pelayaran = Pelayaran::pluck('nama','id');
        $customer_options = [];
        foreach ($customer as $cus) {
            $customer_options[$cus->id] = $cus->nama;
        }
        return view('admin.customer.tarif', compact('customer_options','pelayaran','customer','lokasi','satuan','kondisi','shipment'));
    }

    public function tarifMarketing()
    {
        $idMarketing = Auth::id();
        $jadwal_kapal = JadwalKapal::whereHas('pelayaran')->where('is_active',1)->get();
        $customer = Customer::where('marketing_id',$idMarketing)->get(['nama','id']);
        $lokasi = Lokasi::pluck('nama','id');
        $satuan = Satuan::pluck('nama','id');
        $kondisi = Kondisi::pluck('nama','id');
        $shipment = Shipment::pluck('nama','id');
        $pelayaran = Pelayaran::pluck('nama','id');
        $customer_options = [];
        foreach ($customer as $cus) {
            $customer_options[$cus->id] = $cus->nama;
        }
        return view('admin.customer.tarif_marketing', compact('customer_options','pelayaran','customer','lokasi','satuan','kondisi','shipment','idMarketing'));
    }

    public function create()
    {
        $jadwal_kapal = JadwalKapal::whereHas('pelayaran')->where('is_active',1)->get();
        $customer = Customer::pluck('nama','id');
        $lokasi = Lokasi::pluck('nama','id');
        $satuan = Satuan::pluck('nama','id');
        $kondisi = Kondisi::pluck('nama','id');
        $shipment = Shipment::pluck('nama','id');
        $users = User::get();

        $kapal = array();
        foreach ($jadwal_kapal as $id => $item ) {
            $kapal[$item->id] = $item->pelayaran->nama;
        }

        return view('admin.customer.create', compact('kapal','customer','lokasi','satuan','kondisi','shipment','users'));
    }

    public function edit(Customer $customer)
    {
        $cus = $customer;
        $jadwal_kapal = JadwalKapal::whereHas('pelayaran')->where('is_active',1)->get();
        $customer = Customer::pluck('nama','id');
        $lokasi = Lokasi::pluck('nama','id');
        $satuan = Satuan::pluck('nama','id');
        $kondisi = Kondisi::pluck('nama','id');
        $shipment = Shipment::pluck('nama','id');
        $users = User::get();
        $kapal = array();
        foreach ($jadwal_kapal as $id => $item ) {
            $kapal[$item->id] = $item->pelayaran->nama;
        }
        return view('admin.customer.edit', compact('kapal','customer','lokasi','satuan','kondisi','shipment','users','cus'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        Customer::create($data);
        return redirect()->route('customer.index')->with('success','Data berhasil disimpan!');
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

    public function importUpdate(Request $request)
    {
        Excel::import(new CustomerUpdateImport, $request->file);
        return back()->with('success', 'All good!');
    }

    public function datatable()
    {
        $limit = request('length');
        $start = request('start') * request('length');
        $idMarketing = Auth::id();
        if(request('type')=='tarif'){
            $data = Customer::leftJoin('users as marketing','marketing.id','=','customers.marketing_id')
                ->leftJoin('users as cs','cs.id','=','customers.cs_id')
                ->whereHas('tarif')
                ->select('customers.*')->limit($start)->offset($limit);
            $count = Customer::whereHas('tarif')->select('id')->count();
        }else if(request('type')=='tarif_marketing'){
            $data = Customer::leftJoin('users as marketing','marketing.id','=','customers.marketing_id')
                ->leftJoin('users as cs','cs.id','=','customers.cs_id')
                ->where('customers.marketing_id',$idMarketing)
                ->select('customers.*')->limit($start)->offset($limit);
                $count = Customer::select('id')->where('marketing_id', $idMarketing)->count();
        } else{
            $data = Customer::leftJoin('users as marketing','marketing.id','=','customers.marketing_id')
                ->leftJoin('users as cs','cs.id','=','customers.cs_id')
                ->select('customers.*')->limit($start)->offset($limit);
                $count = Customer::select('id')->count();
        }

        $req = request('filter');
        return Datatables::of($data)
            ->addIndexColumn()
            ->order(function ($query) use($req) {
                if ($req=='keuangan') {
                    // $query->orderBy('customers.nama', 'asc');
                    $query->orderBy('customers.npwp','desc');
                }else{
                    $query->orderBy('customers.nama', 'asc');
                }
            })
            ->addColumn('alamat', function($data){
                return $data->alamat ?? '-';
            })
            ->addColumn('marketing_id', function($data){
                return $data->marketing->name ?? '-';
            })
            ->addColumn('all_in', function($data){
                return $data->all_in==1 ? 'IYA' : 'TIDAK';
            })
            ->addColumn('ba_kembali', function($data){
                return $data->ba_kembali==1 ? 'IYA' : 'TIDAK';
            })
            ->addColumn('cs_id', function($data){
                return $data->cs->name ?? '-';
            })
            ->addColumn('action', function ($data) {
                // $users = User::all();
                // $jadwal_kapal = JadwalKapal::where('is_active',1)->get();
                // $customer = Customer::pluck('nama','id');
                // $lokasi = Lokasi::pluck('nama','id');
                // $satuan = Satuan::pluck('nama','id');
                // $kondisi = Kondisi::pluck('nama','id');
                // $shipment = Shipment::pluck('nama','id');
                // $kapal = array();
                // $cus = $data;
                // foreach ($jadwal_kapal as $id => $item ) {
                //     $kapal[$item->id] = $item->pelayaran->nama;
                // }
                // $view = view('admin.customer.form',compact('users','cus','kapal','customer','lokasi','satuan','kondisi','shipment'))->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('customer.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <a href="'.route('customer.edit',$data).'" class="no-attr text-primary" title="Edit"><i class="fas fa-pencil"></i></a>
                        </div>';
                return $html;
            })
            ->rawColumns(['action'])
            ->setFilteredRecords($count)
            ->toJson();
    }
}
