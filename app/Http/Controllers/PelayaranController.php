<?php

namespace App\Http\Controllers;

use App\Models\Pelayaran;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class PelayaranController extends Controller
{
    public function index()
    {
        return view('admin.pelayaran.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        Pelayaran::create($data);
        return back()->with('success','Data berhasil disimpan!');
    }

    public function update(Pelayaran $pelayaran, Request $request)
    {
        $data = $request->all();
        $pelayaran->update($data);
        return back()->with('success','Data berhasil dupdate!');
    }

    public function destroy(Pelayaran $pelayaran)
    {
        $pelayaran->delete();
        return back()->with('success','Data berhasil dihapus!');
    }

    public function datatable()
    {
        $data = Pelayaran::all();

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('pelayaran.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasPelayaranUpdate'.$data->id.'" aria-controls="offcanvasPelayaranUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasPelayaranUpdate'.$data->id.'" aria-labelledby="offcanvasPelayaranUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasPelayaranUpdate'.$data->id.'Label">Form Pelayaran</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('pelayaran.update',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                    <input type="hidden" name="_method" value="PUT" />
                                    
                                </form>
                            </div>
                        </div>';
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
