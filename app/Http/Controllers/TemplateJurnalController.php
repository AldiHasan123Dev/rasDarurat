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
        if(request('template_id')){
            TemplateJurnalItem::where('template_jurnal_id',request('template_id'))->delete();
            $template = TemplateJurnal::find(request('template_id'));
        }
        foreach ($data['debit_coa_id'] as $idx => $item) {
            if(is_null($data['debit_coa_id'][$idx]) && is_null($data['credit_coa_id'][$idx])){

            }else{
                if(is_null($template)){
                    $template = TemplateJurnal::create([
                        'nama' => $data['name'],
                    ]);
                }else{
                    $template->update(['nama'=>$data['name']]);
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

    public function show(TemplateJurnal $templatejurnal)
    {
        $item = $templatejurnal->template_items;
        return response([
            'template' => $templatejurnal,
            'items' => $item
        ]);
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
                            <a href="'.route('templatejurnal.create',['template_id'=>$data->id]).'" class="no-attr text-primary" title="Edit"><i class="fas fa-pencil"></i></a>
                        </div>';
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
