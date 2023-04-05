<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\TarifTruk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class TarifTrukController extends Controller
{
    public function index()
    {
        $shipments = Shipment::pluck('nama','id');
        return view('admin.tariftruk.index', compact('shipments'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        TarifTruk::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(TarifTruk $tariftruk, Request $request)
    {
        $data = $request->all();
        $tariftruk->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(TarifTruk $tariftruk)
    {
        $tariftruk->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $limit = request('length');
        $start = request('start') * request('length');
        $data = TarifTruk::query()->limit($start)->offset($limit);
        $count = TarifTruk::select('id')->count();
        if (request('truk_id')) {
            $data = TarifTruk::query()->where('truk_id', request('truk_id'))->limit($start)->offset($limit);
            $count = TarifTruk::query()->where('truk_id', request('truk_id'))->count();
        }

        return Datatables::of($data)
            ->addColumn('truk_id', function($data){
                return $data->truk->nama;
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
                $view = view('admin.tariftruk.form',['tariftruk'=>$data,'shipments'=>$shipments])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('tariftruk.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTarifTrukUpdate'.$data->id.'" aria-controls="offcanvasTarifTrukUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasTarifTrukUpdate'.$data->id.'" aria-labelledby="offcanvasTarifTrukUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasTarifTrukUpdate'.$data->id.'Label">Form TarifTruk</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('tariftruk.update',$data).'" method="post">
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
