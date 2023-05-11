<?php

namespace App\Http\Controllers;

use App\Models\JasaKirim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class JasaKirimController extends Controller
{
    public function index()
    {
        return view('admin.jasakirim.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        JasaKirim::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(JasaKirim $jasakirim, Request $request)
    {
        $data = $request->all();
        $jasakirim->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(JasaKirim $jasakirim)
    {
        $jasakirim->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = JasaKirim::join('lokasi','lokasi.id','=','jasa_kirim.lokasi_id')
                ->select('jasa_kirim.*','lokasi.nama')
                ->orderBy('lokasi.nama')
                ->get();

        return Datatables::of($data)
            ->addColumn('lokasi_id', function($data){
                return $data->lokasi->nama;
            })
            ->addColumn('orders', function($data){
                $name = '';
                foreach ($data->orders as $item ) {
                    $name .= $item->job.'-'.sprintf('%02d',$item->no_job).'; ';
                }
                return $name;
            })
            ->addColumn('action', function ($data) {
                $view = view('admin.jasakirim.form',['jasakirim'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('jasakirim.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasJasaKirimUpdate'.$data->id.'" aria-controls="offcanvasJasaKirimUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                            <a href="'.route('cetak.dooring',['jadwal_kapal_id'=>$data->jadwal_kapal_id,'tujuan'=>$data->lokasi_id]).'" class="text-success"><i class="fas fa-print"></i></a>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasJasaKirimUpdate'.$data->id.'" aria-labelledby="offcanvasJasaKirimUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasJasaKirimUpdate'.$data->id.'Label">Form JasaKirim</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('jasakirim.update',$data).'" method="post">
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
