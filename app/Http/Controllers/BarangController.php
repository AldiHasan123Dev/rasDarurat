<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class BarangController extends Controller
{
    public function index()
    {
        return view('admin.barang.index');
    }

    public function store(Request $request)
    {
              $data = $request->all();
        Barang::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Barang $barang, Request $request)
    {
        $data = $request->all();
        $barang->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Barang::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $view = view('admin.barang.form',['barang'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('barang.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBarangUpdate'.$data->id.'" aria-controls="offcanvasBarangUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasBarangUpdate'.$data->id.'" aria-labelledby="offcanvasBarangUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasBarangUpdate'.$data->id.'Label">Form Barang</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('barang.update',$data).'" method="post">
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
