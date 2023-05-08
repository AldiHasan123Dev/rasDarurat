<?php

namespace App\Http\Controllers;

use App\Models\THC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class THCController extends Controller
{
    public function index()
    {
        return view('admin.thc.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        THC::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(THC $thc, Request $request)
    {
        $data = $request->all();
        $thc->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(THC $thc)
    {
        $thc->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = THC::join('lokasi','lokasi.id','=','thc.lokasi_id')
                    ->select('thc.*','lokasi.nama')
                    ->orderBy('lokasi.nama')
                    ->get();

        return Datatables::of($data)
            ->addColumn('lokasi_id', function($data){
                return $data->lokasi->nama;
            })
            ->addColumn('action', function ($data) {
                $view = view('admin.thc.form',['thc'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('thc.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTHCUpdate'.$data->id.'" aria-controls="offcanvasTHCUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasTHCUpdate'.$data->id.'" aria-labelledby="offcanvasTHCUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasTHCUpdate'.$data->id.'Label">Form THC</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('thc.update',$data).'" method="post">
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
