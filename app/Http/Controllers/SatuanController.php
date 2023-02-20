<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class SatuanController extends Controller
{
    public function index()
    {
        return view('admin.satuan.index');
    }

    public function store(Request $request)
    {
              $data = $request->all();
        Satuan::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Satuan $satuan, Request $request)
    {
        $data = $request->all();
        $satuan->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Satuan $satuan)
    {
        $satuan->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Satuan::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $view = view('admin.satuan.form',['satuan'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('satuan.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSatuanUpdate'.$data->id.'" aria-controls="offcanvasSatuanUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSatuanUpdate'.$data->id.'" aria-labelledby="offcanvasSatuanUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasSatuanUpdate'.$data->id.'Label">Form Satuan</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('satuan.update',$data).'" method="post">
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
