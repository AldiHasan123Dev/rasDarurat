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
use Yajra\Datatables\Datatables;

class CustomerController extends Controller
{
    public function index()
    {
        $users = User::all();
        $jadwal_kapal = JadwalKapal::where('is_active',1)->get();
        $customer = Customer::pluck('nama','id');
        $lokasi = Lokasi::pluck('nama','id');
        $satuan = Satuan::pluck('nama','id');
        $kondisi = Kondisi::pluck('nama','id');
        $shipment = Shipment::pluck('nama','id');

        $kapal = array();
        foreach ($jadwal_kapal as $id => $item ) {
            $kapal[$item->id] = $item->pelayaran->nama;
        }
        return view('admin.customer.index', compact('users','kapal','customer','lokasi','satuan','kondisi','shipment'));
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

    public function datatable()
    {
        $limit = request('length');
        $start = request('start') * request('length');
        $data = Customer::query()->join('users','users.id','=','customers.marketing_id')
                ->join('users as cs','cs.id','=','customers.cs_id')->select('customers.*','users.name','users.id as usr_id');

        return Datatables::of($data->offset($start)->limit($limit))
            ->addColumn('marketing_id', function($data){
                return $data->name ?? '-';
            })
            ->addColumn('cs_id', function($data){
                return $data->cs->name ?? '-';
            })
            ->addColumn('action', function ($data) {
                $users = User::all();
                $jadwal_kapal = JadwalKapal::where('is_active',1)->get();
                $customer = Customer::pluck('nama','id');
                $lokasi = Lokasi::pluck('nama','id');
                $satuan = Satuan::pluck('nama','id');
                $kondisi = Kondisi::pluck('nama','id');
                $shipment = Shipment::pluck('nama','id');
                $kapal = array();
                $cus = $data;
                foreach ($jadwal_kapal as $id => $item ) {
                    $kapal[$item->id] = $item->pelayaran->nama;
                }
                $view = view('admin.customer.form',compact('users','cus','kapal','customer','lokasi','satuan','kondisi','shipment'))->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('user.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasUserUpdate'.$data->id.'" aria-controls="offcanvasUserUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasUserUpdate'.$data->id.'" aria-labelledby="offcanvasUserUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasUserUpdate'.$data->id.'Label">Form User</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('user.update',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                    <input type="hidden" name="_method" value="PUT" />
                                    '.$view.'
                                </form>
                            </div>
                        </div>';
                return $html;
            })
            ->rawColumns(['action'])
            ->skipTotalRecords()
            ->toJson();
    }
}
