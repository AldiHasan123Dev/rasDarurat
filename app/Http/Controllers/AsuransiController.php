<?php

namespace App\Http\Controllers;

use App\Models\Asuransi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class AsuransiController extends Controller
{
    public function index()
    {
        return view('admin.asuransi.index');
    }

    public function store(Request $request)
    {
              $data = $request->all();
        Asuransi::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Asuransi $asuransi, Request $request)
    {
        $data = $request->all();
        $asuransi->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Asuransi $asuransi)
    {
        $asuransi->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Asuransi::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $view = view('admin.asuransi.form',['asuransi'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('asuransi.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAsuransiUpdate'.$data->id.'" aria-controls="offcanvasAsuransiUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAsuransiUpdate'.$data->id.'" aria-labelledby="offcanvasAsuransiUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasAsuransiUpdate'.$data->id.'Label">Form Asuransi</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('asuransi.update',$data).'" method="post">
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
