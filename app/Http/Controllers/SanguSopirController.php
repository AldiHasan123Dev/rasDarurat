<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\SanguSopir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class SanguSopirController extends Controller
{
    public function index()
    {
        $lokasi = Lokasi::pluck('nama');
        return view('admin.sangusopir.index', compact('lokasi'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $tujuan = Lokasi::find($request->tujuan);
        if(!$tujuan){
            $tujuan = Lokasi::create(['nama'=>$request->tujuan]);
        }
        $data['tujuan'] = $tujuan->id;
        $data['ukuran_20'] = str_replace(['.',','],'',$request->ukuran_20);
        $data['ukuran_40'] = str_replace(['.',','],'',$request->ukuran_40);
        $data['ukuran_combo'] = str_replace(['.',','],'',$request->ukuran_combo);
        $data['borongan_kuli_20'] = str_replace(['.',','],'',$request->borongan_kuli_20);
        $data['borongan_kuli_40'] = str_replace(['.',','],'',$request->borongan_kuli_40);
        $data['borongan_kuli_combo'] = str_replace(['.',','],'',$request->borongan_kuli_combo);
        SanguSopir::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(SanguSopir $sangusopir, Request $request)
    {
        $data = $request->all();
        $tujuan = Lokasi::find($request->tujuan);
        if(!$tujuan){
            $tujuan = Lokasi::create(['nama'=>$request->tujuan]);
        }
        $data['tujuan'] = $tujuan->id;
        $data['ukuran_20'] = str_replace(['.',','],'',$request->ukuran_20);
        $data['ukuran_40'] = str_replace(['.',','],'',$request->ukuran_40);
        $data['ukuran_combo'] = str_replace(['.',','],'',$request->ukuran_combo);
        $data['borongan_kuli_20'] = str_replace(['.',','],'',$request->borongan_kuli_20);
        $data['borongan_kuli_40'] = str_replace(['.',','],'',$request->borongan_kuli_40);
        $data['borongan_kuli_combo'] = str_replace(['.',','],'',$request->borongan_kuli_combo);
        $sangusopir->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(SanguSopir $sangusopir)
    {
        $sangusopir->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = SanguSopir::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('created_at', function($data){
                return date('d/m/y', strtotime($data->created_at));
            })
            ->addColumn('tujuan', function($data){
                return $data->tujuanInfo->nama;
            })
            ->addColumn('ukuran_20', function($data){
                return number_format($data->ukuran_20);
            })
            ->addColumn('ukuran_40', function($data){
                return number_format($data->ukuran_40);
            })
            ->addColumn('ukuran_combo', function($data){
                return number_format($data->ukuran_combo);
            })
            ->addColumn('is_active', function($data){
                return $data->is_active ==1 ? 'Aktif' : 'Non Aktif';
            })
            ->addColumn('action', function ($data) {
                $view = view('admin.sangusopir.form',['sangusopir'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('sangusopir.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSanguSopirUpdate'.$data->id.'" aria-controls="offcanvasSanguSopirUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSanguSopirUpdate'.$data->id.'" aria-labelledby="offcanvasSanguSopirUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasSanguSopirUpdate'.$data->id.'Label">Form Sangu Sopir</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('sangusopir.update',$data).'" method="post">
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
