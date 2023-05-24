<?php

namespace App\Http\Controllers;

use App\Models\TemplateJurnalItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class TemplateJurnalItemController extends Controller
{
    public function index()
    {
        return view('admin.templatejurnalitem.index');
    }

    public function store(Request $request)
    {
              $data = $request->all();
        TemplateJurnalItem::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(TemplateJurnalItem $templatejurnalitem, Request $request)
    {
        $data = $request->all();
        $templatejurnalitem->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(TemplateJurnalItem $templatejurnalitem)
    {
        $templatejurnalitem->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = TemplateJurnalItem::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $view = view('admin.templatejurnalitem.form',['templatejurnalitem'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('templatejurnalitem.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTemplateJurnalItemUpdate'.$data->id.'" aria-controls="offcanvasTemplateJurnalItemUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasTemplateJurnalItemUpdate'.$data->id.'" aria-labelledby="offcanvasTemplateJurnalItemUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasTemplateJurnalItemUpdate'.$data->id.'Label">Form TemplateJurnalItem</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('templatejurnalitem.update',$data).'" method="post">
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
