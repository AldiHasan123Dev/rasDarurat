<?php

namespace App\Http\Controllers;

use App\Models\COA;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Yajra\Datatables\Datatables;

class COAController extends Controller
{
    public function index()
    {
        $data = COA::all()->whereNull('coa_id')->sortBy('kode');
        $setting = Setting::first();
        $coa_ras = [];
        $is_ras = true;
        if($setting->short_name=='ALB'){
            $is_ras = false;
        }
        return view('admin.coa.index',compact('data','coa_ras','is_ras'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        COA::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(COA $coa, Request $request)
    {
        $data = $request->all();
        if ($request->is_active) {
            $data['is_active'] = $coa->is_active==1?0:1;
        }
        if ($request->is_cont) {
            $data['is_cont'] = $coa->is_cont==1?0:1;
        }
        if ($request->is_nopol) {
            $data['is_nopol'] = $coa->is_nopol==1?0:1;
        }
        if ($request->is_nojob) {
            $data['is_nojob'] = $coa->is_nojob==1?0:1;
        }
        if ($request->is_invoice) {
            $data['is_invoice'] = $coa->is_invoice==1?0:1;
        }
        if ($request->is_invoice_agen) {
            $data['is_invoice_agen'] = $coa->is_invoice_agen==1?0:1;
        }
         if ($request->is_invoice_vendor) {
            $data['is_invoice_vendor'] = $coa->is_invoice_vendor==1?0:1;
        }
         if ($request->is_invoice_external) {
            $data['is_invoice_external'] = $coa->is_invoice_external==1?0:1;
        }
        if ($request->is_invoice_trucking) {
            $data['is_invoice_trucking'] = $coa->is_invoice_trucking==1?0:1;
        }
        if ($request->is_nobg) {
            $data['is_nobg'] = $coa->is_nobg==1?0:1;
        }
        if ($request->is_nobupot) {
            $data['is_nobupot'] = $coa->is_nobupot==1?0:1;
        }
        if ($request->is_tglbupot) {
            $data['is_tglbupot'] = $coa->is_tglbupot==1?0:1;
        }
        $coa->update($data);
        if ($request->response) {
            return response('success');
        }

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(COA $coa)
    {
        $coa->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = COA::orderBy('kode','asc')->orderBy('coa_id','asc')->get();

        return Datatables::of($data)
            ->addColumn('is_active', function($data){
                return $data->is_active==1?'ACTIVE':'NON ACTIVE';
            })
            ->addColumn('coa_id', function($data){
                return $data->coa->kode ?? '-';
            })
            ->addColumn('action', function ($data) {
                $view = view('admin.coa.form',['coa'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('coa.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCOAUpdate'.$data->id.'" aria-controls="offcanvasCOAUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCOAUpdate'.$data->id.'" aria-labelledby="offcanvasCOAUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasCOAUpdate'.$data->id.'Label">Form COA</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('coa.update',$data).'" method="post">
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
