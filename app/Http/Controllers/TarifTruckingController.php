<?php

namespace App\Http\Controllers;

use App\Models\TarifTrucking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class TarifTruckingController extends Controller
{
    public function index()
    {
        return view('admin.tariftrucking.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        TarifTrucking::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(TarifTrucking $tariftrucking, Request $request)
    {
        $data = $request->all();
        $tariftrucking->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(TarifTrucking $tariftrucking)
    {
        $tariftrucking->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = TarifTrucking::query()
                ->join('sangu_sopir','sangu_sopir.id','=','tarif_trucking.tujuan_id')
                ->join('lokasi','lokasi.id','=','sangu_sopir.tujuan')
                ->select('tarif_trucking.*');
        if(request('customer_id')||!is_null(request('customer_id'))){
            $data = TarifTrucking::join('customer_trucking','customer_trucking.id','=','tarif_trucking.customer_id')
            ->join('sangu_sopir','sangu_sopir.id','=','tarif_trucking.tujuan_id')
            ->join('lokasi','lokasi.id','=','sangu_sopir.tujuan')
            ->select('tarif_trucking.*')
            ->where('tarif_trucking.customer_id',request('customer_id'));
        }

        return Datatables::of($data)
            ->addColumn('created_at', function($data){
                return date('d/m/y', strtotime($data->created_at));
            })
            ->addColumn('customer', function($data){
                return $data->customer ? $data->customer->nama : '-';
            })
            ->addColumn('tujuan', function($data){
                return $data->tujuan ? $data->tujuan->tujuanInfo->nama : '-';
            })
            ->addColumn('tarif', function($data){
                return number_format($data->tarif,0,',','.');
            })
            ->addColumn('is_active', function($data){
                $checked = $data->is_active == 1 ? 'checked' : '';
                $label = $data->is_active == 1 ? 'Aktif' : 'Non Aktif';
                $val = $data->is_active == 1 ? 0 : 1;
                $html = '<div class="form-check form-switch">
                            <input class="form-check-input" onchange="changeStatus('.$data->id.','.$val.')" type="checkbox" id="flexSwitchCheckDefault" '.$checked.'>
                            <label class="form-check-label" for="flexSwitchCheckDefault">'.$label.'</label>
                        </div>';
                return $html;
            })
            ->rawColumns(['action','is_active'])
            ->make(true);
    }
}
