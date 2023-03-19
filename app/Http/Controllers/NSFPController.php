<?php

namespace App\Http\Controllers;

use App\Models\NSFP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class NSFPController extends Controller
{
    public function index()
    {
        return view('admin.nsfp.index');
    }

    public function cancel()
    {
        return view('admin.nsfp.tarik');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        NSFP::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(NSFP $nsfp, Request $request)
    {
        $data = $request->all();
        $nsfp->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(NSFP $nsfp)
    {
        $nsfp->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function revisi(Request $request)
    {
        $nsfp = NSFP::find($request->id);
        if ($nsfp->status=='revisi') {
            return back()->with('danger','Faktur sudah pernah direvisi!');
        }
        $no = substr($nsfp->nomor,3,20);
        $new = '051'.$no;
        NSFP::create([
            'nomor' => $nsfp->nomor,
            'available' => 1
        ]);
        $nsfp->update([
            'nomor' => $new,
            'status' => 'revisi'
        ]);

        return back()->with('success','Revisi Faktur Berhasil di buat!');
    }

    public function tarik(Request $request)
    {
        $nsfp = NSFP::find($request->id);
        $nsfp->update([
            'status' => 'tarik'
        ]);

        return back()->with('success','Faktur Berhasil di tarik!');
    }

    public function datatable()
    {
        $data = NSFP::query();
        if(request('filter')=='available'){
            $data->whereNull('invoice');
        }
        if(request('filter')=='tarik'){
            $data->where('status','tarik');
        }
        if(request('filter')=='invoice'){
            $data->where('status','!=','tarik');
            $data->orWhereNull('status');
            $data->whereNotNull('invoice');
        }

        return Datatables::of($data)
            ->addIndexColumn()
            ->order(function ($data) {
                $data->orderBy('available','desc');
                $data->orderBy('nomor','asc');
            })
            ->addColumn('available',function($data){
                return $data->available==1?'IYA':'TIDAK';
            })
            ->addColumn('action', function ($data) {
                if ($data->available) {
                    $view = view('admin.nsfp.form',['nsfp'=>$data])->render();
                    $html = '<div class="d-flex gap-1">
                                <form action="'.route('nsfp.destroy',$data).'" method="post">
                                    <input type="hidden" name="_token" value="'.csrf_token().'" />
                                    <input type="hidden" name="_method" value="delete" />
                                    <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                                <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNSFPUpdate'.$data->id.'" aria-controls="offcanvasNSFPUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                            </div>

                            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNSFPUpdate'.$data->id.'" aria-labelledby="offcanvasNSFPUpdate'.$data->id.'Label">
                                <div class="offcanvas-header">
                                    <h5 class="offcanvas-title" id="offcanvasNSFPUpdate'.$data->id.'Label">Form NSFP</h5>
                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body">
                                    <form action="'.route('nsfp.update',$data).'" method="post">
                                    <input type="hidden" name="_token" value="'.csrf_token().'" />
                                        <input type="hidden" name="_method" value="PUT" />
                                        '.$view.'
                                    </form>
                                </div>
                            </div>';
                    return $html;
                }
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
