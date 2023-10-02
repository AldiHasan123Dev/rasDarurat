<?php

namespace App\Http\Controllers;

use App\Http\Resources\TarifAgenResource;
use App\Models\Shipment;
use App\Models\TarifAgen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class TarifAgenController extends Controller
{
    public function index()
    {
        $shipments = Shipment::pluck('nama','id');
        return view('admin.tarifagen.index', compact('shipments'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if($request->tarif_id){
            TarifAgen::find($request->tarif_id)->update($data);
        }else{
            TarifAgen::create($data);
        }

        return response('Data berhasil disimpan');
    }

    public function update(TarifAgen $tarifagen, Request $request)
    {
        $data = $request->all();
        $tarifagen->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(TarifAgen $tarifagen)
    {
        $tarifagen->delete();

        return response('Data berhasil dihapus');
    }

    public function datatable()
    {
        $limit = request('length');
        $start = request('start') * request('length');
        $data = TarifAgen::query()->limit($start)->offset($limit);
        $count = TarifAgen::select('id')->count();
        if (request('agen_id')) {
            $data = TarifAgen::query()->where('agen_id', request('agen_id'))->limit($start)->offset($limit);
            $count = TarifAgen::query()->where('agen_id', request('agen_id'))->count();
        }

        return Datatables::of($data)
            ->addColumn('agen_id', function($data){
                return $data->agen->nama;
            })
            ->addColumn('dari', function($data){
                return $data->dariInfo->nama;
            })
            ->addColumn('tipe', function($data){
                return $data->shipment->nama;
            })
            ->addColumn('tujuan', function($data){
                return $data->tujuanInfo->nama;
            })
            ->addColumn('is_active', function($data){
                return $data->is_active==1?'AKTIF':'TIDAK AKTIF';
            })
            ->addColumn('tanggal', function($data){
                return date('d/m/y',strtotime($data->tanggal));
            })
            ->addColumn('action', function ($data) {
                $shipments = Shipment::pluck('nama','id');
                $view = view('admin.tarifagen.form',['tarifagen'=>$data,'shipments'=>$shipments])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('tarifagen.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTarifAgenUpdate'.$data->id.'" aria-controls="offcanvasTarifAgenUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasTarifAgenUpdate'.$data->id.'" aria-labelledby="offcanvasTarifAgenUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasTarifAgenUpdate'.$data->id.'Label">Form TarifAgen</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('tarifagen.update',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                    <input type="hidden" name="_method" value="PUT" />
                                    '.$view.'
                                </form>
                            </div>
                        </div>';
                return $html;
            })
            ->rawColumns(['action'])
            ->setFilteredRecords($count)
            ->toJson();
    }

    public function jqgrid()
    {
        $page = request('page'); // get the requested page
        $limit = request('rows'); // get how many rows we want to have into the grid
        $sidx = request('sidx'); // get index row - i.e. user click to sort
        $sord = request('sord'); // get the direction
        $search = request('_search'); // get the search
        $is_search = false;
        if($search=='true'){
            $is_search = true;
        }
        $query = TarifAgen::query();
        $query->join('customers','customers.id','=','tarif_agen.pembayar_id');

        $start = $limit * $page - $limit;
        if ($start < 0){
            $start = 0;
        }

        if(request('agen_id')){
            $query->where('agen_id',request('agen_id'));
        }

        if(request('tanggal')){
            $d = substr(request('tanggal'),0,2);
            $m = substr(request('tanggal'),3,2);
            $y = substr(request('tanggal'),6,2);
            $date = '20'.$y.'-'.$m.'-'.$d;
            $query->whereDate('tanggal','LIKE','%'.$date.'%');
        }

        if(request('dari')){
            $query->whereHas('dariInfo', function($q){
                $q->where('nama','LIKE','%'.request('dari').'%');
            });
        }
        if(request('pembayar')){
            $query->whereHas('pembayar', function($q){
                $q->where('nama','LIKE','%'.request('pembayar').'%');
            });
        }
        if(request('tujuan')){
            $query->whereHas('tujuanInfo', function($q){
                $q->where('nama','LIKE','%'.request('tujuan').'%');
            });
        }
        if(request('tipe')){
            $query->whereHas('shipment', function($q){
                $q->where('nama','LIKE','%'.request('tipe').'%');
            });
        }
        if(request('keterangan')){
            $query->where('keterangan','LIKE','%'.request('tipe').'%');
        }

        $query->select('tarif_agen.*','customers.nama as nama_pembayar');
        $data = $query->orderBy('is_active','desc')->orderBy('tanggal','desc')->orderBy('nama_pembayar')->skip($start)->take($limit)->get();

        $count = TarifAgen::get('id')->count();
        if(request('agen_id')){
            $count = TarifAgen::where('agen_id',request('agen_id'))->get('id')->count();
        }

        if ($count > 0 && $limit > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages){
            $page = $total_pages;
        }

        $response = TarifAgenResource::collection($data);
        return response([
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $response
        ]);
    }
}
