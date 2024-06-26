<?php

namespace App\Http\Controllers;

use App\Models\CustomerTrucking;
use App\Models\SanguSopir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class CustomerTruckingController extends Controller
{
    public function index()
    {
        $customers = CustomerTrucking::orderBy('nama')->pluck('nama','id');
        $tujuan = SanguSopir::join('lokasi','lokasi.id','=','sangu_sopir.tujuan')
                    ->select('sangu_sopir.id','lokasi.nama','sangu_sopir.is_active')
                    ->where('sangu_sopir.is_active',1)
                    ->orderBy('lokasi.nama')
                    ->pluck('lokasi.nama','sangu_sopir.id');
        return view('admin.customertrucking.index',compact('customers','tujuan'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        CustomerTrucking::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(CustomerTrucking $customertrucking, Request $request)
    {
        $data = $request->all();
        $customertrucking->update($data);
        if($request->api){
            return response('success');
        }

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(CustomerTrucking $customertrucking)
    {
        $customertrucking->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = CustomerTrucking::all()->sortBy('nama');

        return Datatables::of($data)
            ->addColumn('pph_23', function($data){
                $checked = $data->pph_23==1?'checked':'';
                $val = $data->pph_23==1?0:1;
                $html = ' <form action="'.route('customertrucking.update',$data).'" method="post">
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <input type="hidden" name="_method" value="PUT" />
                            <input type="hidden" name="pph_23" value="'.$val.'" />
                            <input type="checkbox" onchange="submit()" '.$checked.'/>
                        </form>';
                return $html;
            })
            ->addColumn('r2', function($data){
                $checked = $data->r2==1?'checked':'';
                $val = $data->r2==1?0:1;
                $html = ' <form action="'.route('customertrucking.update',$data).'" method="post">
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <input type="hidden" name="_method" value="PUT" />
                            <input type="hidden" name="r2" value="'.$val.'" />
                            <input type="checkbox" onchange="submit()" '.$checked.'/>
                        </form>';
                return $html;
            })
            ->addColumn('r1', function($data){
                $checked = $data->r1==1?'checked':'';
                $val = $data->r1==1?0:1;
                $html = ' <form action="'.route('customertrucking.update',$data).'" method="post">
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <input type="hidden" name="_method" value="PUT" />
                            <input type="hidden" name="r1" value="'.$val.'" />
                            <input type="checkbox" onchange="submit()" '.$checked.'/>
                        </form>';
                return $html;
            })
            ->addColumn('is_active', function($data){
                $checked = $data->is_active == 1 ? 'checked' : '';
                $label = $data->is_active == 1 ? 'Aktif' : 'Non Aktif';
                $val = $data->is_active == 1 ? 0 : 1;
                $html = '<div class="form-check form-switch">
                            <input class="form-check-input" onchange="changeStatus('.$data->id.','.$val.',\'customer\')" type="checkbox" id="flexSwitchCheckDefault" '.$checked.'>
                            <label class="form-check-label" for="flexSwitchCheckDefault">'.$label.'</label>
                        </div>';
                return $html;
            })
            ->addColumn('action', function ($data) {
                $view = view('admin.customertrucking.form',['customertrucking'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('customertrucking.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCustomerTruckingUpdate'.$data->id.'" aria-controls="offcanvasCustomerTruckingUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCustomerTruckingUpdate'.$data->id.'" aria-labelledby="offcanvasCustomerTruckingUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasCustomerTruckingUpdate'.$data->id.'Label">Form CustomerTrucking</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('customertrucking.update',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                    <input type="hidden" name="_method" value="PUT" />
                                    '.$view.'
                                </form>
                            </div>
                        </div>';
                return $html;
            })
            ->rawColumns(['action','pph_23','r1','r2','is_active'])
            ->make(true);
    }
}
