<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class LokasiController extends Controller
{
    public function index()
    {
        return view('admin.lokasi.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        Lokasi::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Lokasi $lokasi, Request $request)
    {
        $data = $request->all();
        $lokasi->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Lokasi $lokasi)
    {
        $lokasi->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Lokasi::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('publis_rate', function($data){
                return number_format($data->publis_rate);
            })
            ->addColumn('diskon', function($data){
                return number_format($data->diskon);
            })
            ->addColumn('harga', function($data){
                return number_format($data->harga);
            })
            ->addColumn('action', function ($data) {
                $view = view('admin.lokasi.form',['lokasi'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('lokasi.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLokasiUpdate'.$data->id.'" aria-controls="offcanvasLokasiUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasLokasiUpdate'.$data->id.'" aria-labelledby="offcanvasLokasiUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasLokasiUpdate'.$data->id.'Label">Form Lokasi</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('lokasi.update',$data).'" method="post">
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
