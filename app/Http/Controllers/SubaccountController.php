<?php

namespace App\Http\Controllers;

use App\Models\Subaccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class SubaccountController extends Controller
{
    public function index()
    {
        return view('admin.subaccount.index');
    }

    public function store(Request $request)
    {
              $data = $request->all();
        Subaccount::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Subaccount $subaccount, Request $request)
    {
        $data = $request->all();
        $subaccount->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Subaccount $subaccount)
    {
        $subaccount->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Subaccount::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('is_active', function($data){
                return $data->is_active==1?'ACTIVE':'NON ACTIVE';
            })
            ->addColumn('action', function ($data) {
                $view = view('admin.subaccount.form',['subaccount'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('subaccount.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSubaccountUpdate'.$data->id.'" aria-controls="offcanvasSubaccountUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSubaccountUpdate'.$data->id.'" aria-labelledby="offcanvasSubaccountUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasSubaccountUpdate'.$data->id.'Label">Form Subaccount</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('subaccount.update',$data).'" method="post">
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
