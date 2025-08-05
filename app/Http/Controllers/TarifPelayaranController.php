<?php

namespace App\Http\Controllers;

use App\Http\Resources\TarifPelayaranResource;
use App\Models\Port;
use App\Models\Shipment;
use App\Models\TarifPelayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class TarifPelayaranController extends Controller
{
    public function index()
    {
        $shipments = Shipment::pluck('nama','id');
        return view('admin.tarifpelayaran.index',compact('shipment'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if($request->tarif_id){
            TarifPelayaran::find($request->tarif_id)->update($data);
        }else{
            TarifPelayaran::create($data);
        }

        return response('Data berhasil disimpan');
    }

    public function update(TarifPelayaran $tarifpelayaran, Request $request)
    {
        $data = $request->all();
        $tarifpelayaran->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(TarifPelayaran $tarifpelayaran)
    {
        $tarifpelayaran->delete();

        return response('Data berhasil dihapus');
    }

    public function datatable()
    {
        $limit = request('length');
        $start = request('start') * request('length');
        $data = TarifPelayaran::query()->limit($start)->offset($limit);
        $count = TarifPelayaran::select('id')->count();
        if (request('pelayaran_id')) {
            $data = TarifPelayaran::query()->where('pelayaran_id', request('pelayaran_id'))->limit($start)->offset($limit);
            $count = TarifPelayaran::query()->where('pelayaran_id', request('pelayaran_id'))->count();
        }

        return Datatables::of($data)
            ->addColumn('pelayaran_id', function($data){
                return $data->pelayaran->nama;
            })
            ->addColumn('dari', function($data){
                return $data->dariInfo->nama;
            })
            ->addColumn('tujuan', function($data){
                return $data->tujuanInfo->nama;
            })
            ->addColumn('tipe', function($data){
                return $data->shipment->nama;
            })
            ->addColumn('is_active', function($data){
                return $data->is_active==1?'AKTIF':'TIDAK AKTIF';
            })
            ->addColumn('tanggal', function($data){
                return date('d/m/y',strtotime($data->tanggal));
            })
            ->addColumn('action', function ($data) {
                $shipments = Shipment::pluck('nama','id');
                $view = view('admin.tarifpelayaran.form',['tarifpelayaran'=>$data,'shipments'=>$shipments])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('tarifpelayaran.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTarifPelayaranUpdate'.$data->id.'" aria-controls="offcanvasTarifPelayaranUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasTarifPelayaranUpdate'.$data->id.'" aria-labelledby="offcanvasTarifPelayaranUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasTarifPelayaranUpdate'.$data->id.'Label">Form TarifPelayaran</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('tarifpelayaran.update',$data).'" method="post">
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
        $query = TarifPelayaran::query();


        $start = $limit * $page - $limit;
        if ($start < 0){
            $start = 0;
        }

        if(request('pelayaran_id')){
            $query->where('pelayaran_id',request('pelayaran_id'));
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
        if(request('tujuan')){
            $query->whereHas('tujuanInfo', function($q){
                $q->where('nama','LIKE','%'.request('tujuan').'%');
            });
        }

          if(request('tujuans')){
            $query->where('is_active', 1)->whereHas('tujuanInfo', function($q){
                $q->where('nama','LIKE','%'.request('tujuans').'%');
            });
        }

        if(request('tipe')){
            $query->whereHas('shipment', function($q){
                $q->where('nama','LIKE','%'.request('tipe').'%');
            });
        }
        if(request('keterangan')){
            $query->where('keterangan','LIKE','%'.request('keterangan').'%');
        }
        if(request('komoditi')){
            $query->where('komoditi','LIKE','%'.request('komoditi').'%');
        }

        $data = $query->orderBy('is_active','desc')->orderBy('tanggal','desc')->skip($start)->take($limit)->get();

        $count = TarifPelayaran::get('id')->count();
        if(request('pelayaran_id')){
            $count = TarifPelayaran::where('pelayaran_id',request('pelayaran_id'))->get('id')->count();
        }

        if ($count > 0 && $limit > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages){
            $page = $total_pages;
        }

        $response = TarifPelayaranResource::collection($data);
        return response([
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $response
        ]);
    }
}
