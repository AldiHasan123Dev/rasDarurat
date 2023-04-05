<?php

namespace App\Http\Controllers;

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
        TarifPelayaran::create($data);

        return back()->with('success','Data berhasil disimpan');
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

        return back()->with('success','Data berhasil dihapus');
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
}
