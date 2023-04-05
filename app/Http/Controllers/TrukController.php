<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Truk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class TrukController extends Controller
{
    public function index()
    {
        $shipments = Shipment::pluck('nama','id');
        return view('admin.suplier.truk.index', compact('shipments'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        Truk::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Truk $truk, Request $request)
    {
        $data = $request->all();
        $truk->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Truk $truk)
    {
        $truk->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Truk::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $view = view('admin.suplier.truk.form',['truk'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('truk.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTrukUpdate'.$data->id.'" aria-controls="offcanvasTrukUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasTrukUpdate'.$data->id.'" aria-labelledby="offcanvasTrukUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasTrukUpdate'.$data->id.'Label">Form Truk</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('truk.update',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                    <input type="hidden" name="_method" value="PUT" />
                                    '.$view.'
                                </form>
                            </div>
                        </div>';
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
