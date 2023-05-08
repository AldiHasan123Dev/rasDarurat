<?php

namespace App\Http\Controllers;

use App\Models\LSS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class LSSController extends Controller
{
    public function index()
    {
        return view('admin.lss.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        LSS::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(LSS $lss, Request $request)
    {
        $data = $request->all();
        $lss->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(LSS $lss)
    {
        $lss->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = LSS::join('lokasi','lokasi.id','=','lss.lokasi_id')
                ->select('lss.*','lokasi.nama')
                ->orderBy('lokasi.nama')
                ->get();

        return Datatables::of($data)
            ->addColumn('lokasi_id', function($data){
                return $data->lokasi->nama;
            })
            ->addColumn('action', function ($data) {
                $view = view('admin.lss.form',['lss'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('lss.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLSSUpdate'.$data->id.'" aria-controls="offcanvasLSSUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasLSSUpdate'.$data->id.'" aria-labelledby="offcanvasLSSUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasLSSUpdate'.$data->id.'Label">Form LSS</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('lss.update',$data).'" method="post">
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
