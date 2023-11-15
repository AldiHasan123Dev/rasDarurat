<?php

namespace App\Http\Controllers;

use App\Models\Agen;
use App\Models\Shipment;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class AgenController extends Controller
{
    public function index()
    {
        $shipments = Shipment::pluck('nama','id');
        return view('admin.suplier.agen.index', compact('shipments'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $kota = $request->kota;
        $lokasi = Lokasi::where('nama','like',$kota)->first();
        if(!$lokasi){
            $lokasi = Lokasi::create([
                'nama' => strtoupper($kota)
            ]);
        }
        $data['lokasi_id'] = $lokasi->id;
        Agen::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Agen $agen, Request $request)
    {
        $data = $request->all();
        $kota = $request->kota;
        $lokasi = Lokasi::where('nama','like',$kota)->first();
        if(!$lokasi){
            $lokasi = Lokasi::create([
                'nama' => strtoupper($kota)
            ]);
        }
        $data['lokasi_id'] = $lokasi->id;
        $agen->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Agen $agen)
    {
        $agen->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Agen::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $view = view('admin.suplier.agen.form',['agen'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('agen.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAgenUpdate'.$data->id.'" aria-controls="offcanvasAgenUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAgenUpdate'.$data->id.'" aria-labelledby="offcanvasAgenUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasAgenUpdate'.$data->id.'Label">Form Agen</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('agen.update',$data).'" method="post">
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
