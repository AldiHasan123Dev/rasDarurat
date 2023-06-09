<?php

namespace App\Http\Controllers;

use App\Models\TemplateJurnal;
use App\Models\TemplateJurnalItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class TemplateJurnalController extends Controller
{
    public function index()
    {
        return view('admin.templatejurnal.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $template = null;
        foreach ($data['debit_coa_id'] as $idx => $item) {
            if(is_null($data['debit_coa_id'][$idx]) && is_null($data['credit_coa_id'][$idx])){

            }else{
                if(is_null($template)){
                    $template = TemplateJurnal::create([
                        'nama' => $data['name']
                    ]);
                }
                TemplateJurnalItem::create([
                    'template_jurnal_id' => $template->id,
                    'coa_debit_id' => $data['debit_coa_id'][$idx],
                    'coa_credit_id' => $data['credit_coa_id'][$idx],
                    'keterangan' => $data['keterangan'][$idx],
                ]);
            }
        }

        return back()->with('success','Data berhasil disimpan');
    }

    public function create()
    {
        return view('admin.templatejurnal.create');
    }

    public function update(TemplateJurnal $templatejurnal, Request $request)
    {
        $data = $request->all();
        $templatejurnal->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(TemplateJurnal $templatejurnal)
    {
        $templatejurnal->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = TemplateJurnal::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $view = view('admin.templatejurnal.form',['templatejurnal'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('templatejurnal.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTemplateJurnalUpdate'.$data->id.'" aria-controls="offcanvasTemplateJurnalUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasTemplateJurnalUpdate'.$data->id.'" aria-labelledby="offcanvasTemplateJurnalUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasTemplateJurnalUpdate'.$data->id.'Label">Form TemplateJurnal</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('templatejurnal.update',$data).'" method="post">
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
