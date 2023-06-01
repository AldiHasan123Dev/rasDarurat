<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class JurnalController extends Controller
{
    public function index()
    {
        return view('admin.jurnal.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        Jurnal::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function create()
    {
        return view('admin.jurnal.create');
    }

    public function update(Jurnal $jurnal, Request $request)
    {
        $data = $request->all();
        $jurnal->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Jurnal $jurnal)
    {
        $jurnal->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Jurnal::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $view = view('admin.jurnal.form',['jurnal'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('jurnal.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasJurnalUpdate'.$data->id.'" aria-controls="offcanvasJurnalUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasJurnalUpdate'.$data->id.'" aria-labelledby="offcanvasJurnalUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasJurnalUpdate'.$data->id.'Label">Form Jurnal</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('jurnal.update',$data).'" method="post">
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
