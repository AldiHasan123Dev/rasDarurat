<?php

namespace App\Http\Controllers;

use App\Models\Sopir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class SopirController extends Controller
{
    public function index()
    {
        return view('admin.sopir.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        Sopir::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Sopir $sopir, Request $request)
    {
        $data = $request->all();
        $sopir->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Sopir $sopir)
    {
        $sopir->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Sopir::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('is_active', function($data){
                return $data->is_active ? 'Aktif' : 'Non Aktif';
            })
            ->addColumn('action', function ($data) {
                $view = view('admin.sopir.form',['sopir'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('sopir.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSopirUpdate'.$data->id.'" aria-controls="offcanvasSopirUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSopirUpdate'.$data->id.'" aria-labelledby="offcanvasSopirUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasSopirUpdate'.$data->id.'Label">Form Sopir</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('sopir.update',$data).'" method="post">
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
