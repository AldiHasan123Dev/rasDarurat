<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class KendaraanController extends Controller
{
    public function index()
    {
        return view('admin.kendaraan.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        Kendaraan::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Kendaraan $kendaraan, Request $request)
    {
        $data = $request->all();
        $kendaraan->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Kendaraan $kendaraan)
    {
        $kendaraan->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Kendaraan::query()->orderBy('is_active','desc')->orderBy('created_at','desc');

        return Datatables::of($data)
            ->addColumn('created_at', function($data){
                return date('d/m/y', strtotime($data->created_at));
            })
            ->addColumn('is_active', function($data){
                return $data->is_active ? 'Aktif' : 'Non Aktif';
            })
            ->addColumn('action', function ($data) {
                $view = view('admin.kendaraan.form',['kendaraan'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('kendaraan.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasKendaraanUpdate'.$data->id.'" aria-controls="offcanvasKendaraanUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasKendaraanUpdate'.$data->id.'" aria-labelledby="offcanvasKendaraanUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasKendaraanUpdate'.$data->id.'Label">Form Kendaraan</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('kendaraan.update',$data).'" method="post">
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
