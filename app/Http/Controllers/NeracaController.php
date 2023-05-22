<?php

namespace App\Http\Controllers;

use App\Models\Neraca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class NeracaController extends Controller
{
    public function index()
    {
        return view('admin.neraca.index');
    }

    public function store(Request $request)
    {
              $data = $request->all();
        Neraca::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Neraca $neraca, Request $request)
    {
        $data = $request->all();
        $neraca->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Neraca $neraca)
    {
        $neraca->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Neraca::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('is_active', function($data){
                return $data->is_active==1?'ACTIVE':'NON ACTIVE';
            })
            ->addColumn('action', function ($data) {
                $view = view('admin.neraca.form',['neraca'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('neraca.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNeracaUpdate'.$data->id.'" aria-controls="offcanvasNeracaUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNeracaUpdate'.$data->id.'" aria-labelledby="offcanvasNeracaUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasNeracaUpdate'.$data->id.'Label">Form Neraca</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('neraca.update',$data).'" method="post">
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
