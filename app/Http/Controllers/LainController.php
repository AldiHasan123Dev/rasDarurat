<?php

namespace App\Http\Controllers;

use App\Models\Lain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class LainController extends Controller
{
    public function index()
    {
        return view('admin.lain.index');
    }

    public function store(Request $request)
    {
              $data = $request->all();
        Lain::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Lain $lain, Request $request)
    {
        $data = $request->all();
        $lain->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Lain $lain)
    {
        $lain->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Lain::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $view = view('admin.lain.form',['lain'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('lain.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLainUpdate'.$data->id.'" aria-controls="offcanvasLainUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasLainUpdate'.$data->id.'" aria-labelledby="offcanvasLainUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasLainUpdate'.$data->id.'Label">Form Lain</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('lain.update',$data).'" method="post">
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
